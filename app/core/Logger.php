<?php

/*
 * Archivo: app/core/Logger.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Registra en la base de datos todas las acciones importantes del sistema:
 * inicios de sesión, cierres de sesión, documentos creados, editados
 * o eliminados, y usuarios gestionados.
 *
 * Esto permite a los administradores consultar la bitácora (logs)
 * para saber quién hizo qué y cuándo, lo que es fundamental para
 * la trazabilidad y seguridad del sistema documental.
 */

declare(strict_types=1);

final class Logger
{
    /**
     * Escribe una entrada en la tabla system_logs de la base de datos.
     *
     * Se guarda:
     *  - El ID del usuario que realizó la acción (si está autenticado)
     *  - El nombre corto de la acción (p.ej. 'login', 'document_created')
     *  - Una descripción más detallada de lo que ocurrió
     *  - La dirección IP del visitante
     *  - El User-Agent (navegador/cliente) que hizo la petición
     *  - La fecha y hora exacta del evento (NOW())
     *
     * @param string   $action      Identificador corto de la acción (sin espacios)
     * @param string   $description Texto descriptivo del evento registrado
     * @param int|null $userId      ID del usuario; si es null, se usa el de la sesión activa
     */
    public static function write(string $action, string $description, ?int $userId = null): void
    {
        // Si el log falla (BD caída, tabla inexistente, etc.) no debe interrumpir la operación
        try {
            // Preparamos la consulta SQL con parámetros nombrados para evitar SQL Injection
            $stmt = Database::connection()->prepare(
                'INSERT INTO system_logs (user_id, action, description, ip_address, user_agent, created_at)
                 VALUES (:user_id, :action, :description, :ip, :agent, NOW())'
            );

            $stmt->execute([
                // Si no se pasa un userId explícito, usamos el del usuario con sesión activa
                'user_id' => $userId ?? Auth::id(),
                'action'  => $action,
                'description' => $description,
                // Dirección IP del cliente; puede ser null si no está disponible
                'ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
                // Cadena del navegador; se limita a 255 caracteres para no exceder el campo
                'agent'   => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (\Throwable) {
            // Fallo silencioso: el log no debe bloquear ninguna operación del sistema
        }
    }
}
