<?php

/*
 * Archivo: config/database.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Devuelve los datos de conexión a MySQL para la clase Database.
 *
 * Las credenciales reales ya NO viven aquí: se cargan desde el archivo
 * de secretos (fuera del directorio web) en config/config.php, que las
 * expone mediante la constante DB_CONFIG. Así este archivo puede
 * versionarse o respaldarse sin riesgo de fugar contraseñas.
 */

declare(strict_types=1);

return DB_CONFIG;
