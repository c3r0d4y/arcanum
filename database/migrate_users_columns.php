<?php

/*
 * Archivo: database/migrate_users_columns.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Repara la tabla `users` en instalaciones donde la base de datos se creó
 * solo con schema.sql, que no incluye las columnas que el código actual
 * necesita para el login en dos pasos (contraseña + PIN) y el bloqueo
 * por intentos fallidos.
 *
 * Sin estas columnas, al verificar el PIN el sistema intenta actualizar
 * campos que no existen y la base de datos responde con un error, lo que
 * el servidor muestra como "Error 500".
 *
 * ¿Cómo se usa?
 * Desde la terminal del servidor, parado en la carpeta del proyecto:
 *
 *     php database/migrate_users_columns.php
 *
 * El script es seguro de ejecutar varias veces: revisa columna por
 * columna y solo agrega las que faltan, sin tocar los datos existentes.
 */

declare(strict_types=1);

// Solo se permite ejecutar desde la terminal, nunca desde el navegador
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo puede ejecutarse desde la terminal.');
}

// Carga la configuración real del sistema (misma conexión que usa la app)
require __DIR__ . '/../config/config.php';

$db  = DB_CONFIG;
$pdo = new PDO(
    "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
    $db['username'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Columnas que el código actual espera en la tabla users.
// Para cada una se indica la instrucción exacta para crearla.
$columnasRequeridas = [
    'failed_attempts'      => "ALTER TABLE users ADD COLUMN failed_attempts INT UNSIGNED NOT NULL DEFAULT 0
                               COMMENT 'Intentos fallidos de login acumulados' AFTER active",
    'locked_at'            => "ALTER TABLE users ADD COLUMN locked_at DATETIME NULL
                               COMMENT 'Momento en que la cuenta fue bloqueada' AFTER failed_attempts",
    'lock_expires_at'      => "ALTER TABLE users ADD COLUMN lock_expires_at DATETIME NULL
                               COMMENT 'Fin del bloqueo temporal; NULL = permanente' AFTER locked_at",
    'avatar'               => "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL
                               COMMENT 'Nombre de archivo de la foto de perfil' AFTER lock_expires_at",
    'pin_hash'             => "ALTER TABLE users ADD COLUMN pin_hash VARCHAR(255) NULL
                               COMMENT 'PIN de seguridad cifrado (segundo factor)' AFTER avatar",
    'must_change_password' => "ALTER TABLE users ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0
                               COMMENT '1 = debe cambiar su contraseña provisional' AFTER pin_hash",
];

echo "Revisando la tabla users de '{$db['database']}'...\n\n";

// Obtiene las columnas que ya existen en la tabla
$existentes = array_column(
    $pdo->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_ASSOC),
    'Field'
);

$agregadas = 0;
foreach ($columnasRequeridas as $columna => $sql) {
    if (in_array($columna, $existentes, true)) {
        echo "  [ok]     {$columna} ya existe\n";
        continue;
    }
    // La columna falta: se agrega sin modificar los datos existentes
    $pdo->exec($sql);
    echo "  [creada] {$columna}\n";
    $agregadas++;
}

echo "\nListo. Columnas agregadas: {$agregadas}.\n";
echo $agregadas > 0
    ? "Prueba de nuevo el inicio de sesión con PIN.\n"
    : "La tabla ya estaba completa; el error 500 tiene otra causa (revisa el log de errores de PHP del servidor).\n";
