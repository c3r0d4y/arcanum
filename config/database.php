<?php

/*
 * Archivo: config/database.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Guarda los datos de conexión a la base de datos MySQL.
 * Devuelve un arreglo con host, nombre de la base, usuario, contraseña
 * y juego de caracteres. Lo usa la clase Database para abrir la conexión.
 *
 * IMPORTANTE: nunca subas este archivo a un repositorio público,
 * ya que contiene la contraseña de acceso a la base de datos.
 */

declare(strict_types=1);

return [
    // Servidor donde corre MySQL (en este caso el mismo servidor web)
    'host' => 'localhost',

    // Nombre de la base de datos que usa la aplicación
    'database' => 'archivo_documental',

    // Usuario de MySQL con permisos solo sobre esta base de datos
    'username' => 'archivo_app',

    // Contraseña del usuario anterior
    'password' => '2d069c72b3f4958de6542120ce82b25b',

    // Codificación de caracteres. utf8mb4 permite español, emojis, etc.
    'charset' => 'utf8mb4',
];
