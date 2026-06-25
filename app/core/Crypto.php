<?php

/*
 * Archivo: app/core/Crypto.php
 * Autor:   C3r0d4y
 *
 * Cifrado AES-256-GCM para archivos y campos de base de datos.
 * Garantiza confidencialidad e integridad: si alguien altera el
 * archivo cifrado, la autenticación GCM falla y no se sirve nada.
 *
 * Formato de archivo cifrado (binario):
 *   [ IV 12 bytes ][ TAG 16 bytes ][ texto cifrado ]
 *
 * Formato de campo de BD (texto):
 *   ENC:<base64(IV + TAG + texto cifrado)>
 *
 * Los campos sin prefijo "ENC:" se devuelven tal cual (compatibilidad
 * con registros anteriores al cifrado).
 */

declare(strict_types=1);

final class Crypto
{
    private const ALGO      = 'aes-256-gcm';
    private const IV_LEN    = 12;
    private const TAG_LEN   = 16;
    private const FIELD_PFX = 'ENC:';

    // Cifra el contenido binario de un archivo (PDF → binario cifrado)
    public static function encryptFile(string $plaintext): string
    {
        $iv  = random_bytes(self::IV_LEN);
        $tag = '';
        $ct  = openssl_encrypt(
            $plaintext, self::ALGO, self::key(),
            OPENSSL_RAW_DATA, $iv, $tag, '', self::TAG_LEN
        );

        if ($ct === false) {
            throw new \RuntimeException('Error al cifrar el archivo.');
        }

        return $iv . $tag . $ct;
    }

    // Descifra el contenido binario de un archivo (.enc → PDF original)
    public static function decryptFile(string $ciphertext): string
    {
        $iv = substr($ciphertext, 0, self::IV_LEN);
        $tag = substr($ciphertext, self::IV_LEN, self::TAG_LEN);
        $ct  = substr($ciphertext, self::IV_LEN + self::TAG_LEN);

        $pt = openssl_decrypt(
            $ct, self::ALGO, self::key(),
            OPENSSL_RAW_DATA, $iv, $tag
        );

        if ($pt === false) {
            throw new \RuntimeException('Autenticación GCM fallida: el archivo puede estar corrompido o alterado.');
        }

        return $pt;
    }

    // Cifra un campo de texto para guardar cifrado en la base de datos
    public static function encryptField(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $iv  = random_bytes(self::IV_LEN);
        $tag = '';
        $ct  = openssl_encrypt(
            $value, self::ALGO, self::key(),
            OPENSSL_RAW_DATA, $iv, $tag, '', self::TAG_LEN
        );

        if ($ct === false) {
            throw new \RuntimeException('Error al cifrar campo de base de datos.');
        }

        return self::FIELD_PFX . base64_encode($iv . $tag . $ct);
    }

    // Descifra un campo de base de datos; si no tiene prefijo ENC: lo retorna tal cual
    public static function decryptField(string $value): string
    {
        if ($value === '' || !str_starts_with($value, self::FIELD_PFX)) {
            // Dato previo al cifrado: se devuelve sin modificar
            return $value;
        }

        $raw = base64_decode(substr($value, strlen(self::FIELD_PFX)), true);
        if ($raw === false) {
            return $value;
        }

        $iv  = substr($raw, 0, self::IV_LEN);
        $tag = substr($raw, self::IV_LEN, self::TAG_LEN);
        $ct  = substr($raw, self::IV_LEN + self::TAG_LEN);

        $pt = openssl_decrypt(
            $ct, self::ALGO, self::key(),
            OPENSSL_RAW_DATA, $iv, $tag
        );

        // Si la autenticación falla retorna el valor cifrado para no exponer nada
        return $pt !== false ? $pt : $value;
    }

    private static function key(): string
    {
        return hex2bin(ENCRYPTION_KEY);
    }
}
