<?php

/*
 * Archivo: config/secrets.example.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Es la PLANTILLA de los secretos del sistema. No contiene valores
 * reales: sirve para que quien instale ARCANUM sepa qué datos debe
 * proporcionar.
 *
 * CÓMO USARLO
 *   1. Copia este archivo:
 *        cp config/secrets.example.php config/secrets.local.php
 *   2. Rellena los valores reales en la copia.
 *   3. En producción, mueve la copia FUERA del directorio web:
 *        sudo mv config/secrets.local.php /var/www/arcanum-secrets.php
 *        sudo chown www-data:www-data /var/www/arcanum-secrets.php
 *        sudo chmod 600 /var/www/arcanum-secrets.php
 *
 * IMPORTANTE
 *   El archivo con los valores reales NUNCA debe subirse al
 *   repositorio: el .gitignore ya lo excluye. Quien obtenga la clave
 *   maestra puede descifrar todos los expedientes.
 */

declare(strict_types=1);

return [

    /*
     * Clave maestra de cifrado AES-256-GCM.
     * Deben ser exactamente 64 caracteres hexadecimales (256 bits).
     * Genera una nueva con:
     *     php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
     *
     * ADVERTENCIA: si cambias esta clave, los expedientes ya cifrados
     * dejan de poder leerse. Rotarla obliga a re-cifrar todo el archivo.
     */
    'encryption_key' => 'PON_AQUI_64_CARACTERES_HEXADECIMALES',

    /*
     * Credenciales de MySQL.
     * El script database/schema.sql crea un usuario con permisos
     * mínimos (solo SELECT, INSERT, UPDATE y DELETE, sin DDL).
     */
    'db' => [
        'host'     => 'localhost',
        'database' => 'arcanum',
        'username' => 'arcanum_app',
        'password' => 'PON_AQUI_LA_CONTRASENA_DE_LA_BASE_DE_DATOS',
        'charset'  => 'utf8mb4',
    ],
];
