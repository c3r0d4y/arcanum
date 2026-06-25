<?php

/*
 * Archivo: app/core/Database.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Gestiona la conexión a la base de datos MySQL usando PDO
 * (PHP Data Objects), que es la forma moderna y segura de
 * comunicarse con bases de datos en PHP.
 *
 * Usa el patrón Singleton: sin importar cuántas veces se llame
 * a connection(), solo se crea UNA conexión por petición.
 * Esto evita abrir múltiples conexiones innecesarias al servidor
 * de base de datos.
 */

declare(strict_types=1);

final class Database
{
    /**
     * Almacena la única instancia de la conexión PDO.
     * Es null mientras no se haya conectado todavía.
     * Se declara estática para que persista entre llamadas.
     */
    private static ?PDO $pdo = null;

    /**
     * Devuelve la conexión a la base de datos.
     *
     * Si ya hay una conexión abierta, la devuelve directamente
     * (Singleton). Si no existe, la crea usando los datos de
     * config/database.php y la guarda para futuras llamadas.
     *
     * @return PDO Objeto de conexión listo para ejecutar consultas
     */
    public static function connection(): PDO
    {
        // Si ya existe una conexión activa, la devolvemos directamente
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        // Cargamos los datos de conexión desde el archivo de configuración
        $config = require APP_ROOT . '/config/database.php';

        // Construimos el DSN (Data Source Name): cadena que identifica la BD
        // Formato: mysql:host=SERVIDOR;dbname=BASE_DE_DATOS;charset=CODIFICACION
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['database'],
            $config['charset']
        );

        // Abrimos la conexión con las opciones recomendadas de seguridad:
        self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
            // Lanza excepciones en lugar de errores silenciosos
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Devuelve filas como arreglos asociativos (clave => valor)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Desactiva la emulación de prepared statements para mayor seguridad
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$pdo;
    }
}
