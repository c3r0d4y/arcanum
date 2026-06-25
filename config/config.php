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

// Clave maestra AES-256-GCM — 64 caracteres hexadecimales = 256 bits
// NUNCA exponer esta clave; rotarla requiere re-cifrar todo el archivo
define('ENCRYPTION_KEY', '8d88bf22f5435b750a9b39044e7c239aea611547096baafc1ac7fe6ed0d5ac19');

date_default_timezone_set('America/Mexico_City');
