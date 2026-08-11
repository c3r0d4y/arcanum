<?php

/*
 * Archivo: database/migrate_plaintext_metadata.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este script?
 * Convierte a texto plano los campos de búsqueda de la tabla documents
 * (number, subject y sender) que estaban cifrados con AES-256-GCM.
 *
 * ¿Por qué?
 * Para optimizar las búsquedas: con los metadatos en claro, los filtros
 * se resuelven directamente en SQL y ya no hay que descifrar cada fila.
 * El archivo PDF sigue cifrado; solo se descifra al verlo o editarlo.
 *
 * Cómo ejecutarlo (una sola vez, desde la terminal):
 *   php /var/www/html/arcanum/database/migrate_plaintext_metadata.php
 *
 * Es seguro ejecutarlo más de una vez: los valores que ya están en
 * claro (sin prefijo "ENC:") se dejan tal cual.
 */

declare(strict_types=1);

// Carga la configuración y las clases necesarias de la aplicación
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../app/core/Crypto.php';
require __DIR__ . '/../app/core/Database.php';

$pdo = Database::connection();

// Lee todos los documentos con sus campos de búsqueda actuales
$rows = $pdo->query('SELECT id, number, subject, sender FROM documents')->fetchAll();

$actualizados = 0;

foreach ($rows as $row) {
    // Descifra cada campo; si ya estaba en claro se devuelve sin cambios
    $number  = Crypto::decryptField((string) $row['number']);
    $subject = Crypto::decryptField((string) $row['subject']);
    $sender  = Crypto::decryptField((string) $row['sender']);

    // Solo escribe en la BD si algún campo realmente cambió
    if ($number === $row['number'] && $subject === $row['subject'] && $sender === $row['sender']) {
        continue;
    }

    $pdo->prepare('UPDATE documents SET number = :n, subject = :s, sender = :r WHERE id = :id')
        ->execute(['n' => $number, 's' => $subject, 'r' => $sender, 'id' => $row['id']]);

    $actualizados++;
}

echo "Documentos revisados: " . count($rows) . PHP_EOL;
echo "Documentos convertidos a texto plano: {$actualizados}" . PHP_EOL;
