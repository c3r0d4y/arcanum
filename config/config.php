<?php

/*
 * Archivo: config/config.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Define las constantes globales de la aplicación: nombre, URL base,
 * rutas de directorios y zona horaria. Es lo primero que se carga al
 * arrancar el sistema para que todos los demás archivos puedan usar
 * estas constantes sin preocuparse de dónde viven los archivos.
 */

declare(strict_types=1);

// Nombre del sistema (nivel máximo de clasificación)
define('APP_NAME', 'ARCANUM');

// URL base de instalación
define('BASE_URL', '/arcanum');

// Raíz absoluta del proyecto
define('APP_ROOT', dirname(__DIR__));

// Directorio donde se almacenan los archivos cifrados
define('STORAGE_PATH', APP_ROOT . '/storage/pdfs');

/*
 * Carga de secretos (clave de cifrado y credenciales de base de datos).
 * Se buscan primero FUERA del directorio web (/var/www/arcanum-secrets.php),
 * que es la ubicación correcta: así una fuga del código fuente no expone
 * las llaves. Si aún no se ha movido, se usa la copia local de respaldo.
 */
$rutaSecretos = '/var/www/arcanum-secrets.php';
if (!is_file($rutaSecretos)) {
    $rutaSecretos = __DIR__ . '/secrets.local.php';
}
if (!is_file($rutaSecretos)) {
    // Sin secretos no se puede operar: se detiene con un mensaje genérico
    http_response_code(500);
    exit('Error de configuración del sistema. Contacte al administrador.');
}
$secretos = require $rutaSecretos;

// Clave maestra AES-256-GCM — 64 caracteres hexadecimales = 256 bits
// NUNCA exponer esta clave; rotarla requiere re-cifrar todo el archivo
define('ENCRYPTION_KEY', (string) $secretos['encryption_key']);

// Credenciales de MySQL; las usa config/database.php
define('DB_CONFIG', (array) $secretos['db']);

// Se libera la variable para que los secretos no queden en el ámbito global
unset($secretos, $rutaSecretos);

// Tiempo máximo de inactividad antes de cerrar la sesión (en segundos)
define('SESSION_TIMEOUT', 300); // 5 minutos

date_default_timezone_set('America/Mexico_City');
