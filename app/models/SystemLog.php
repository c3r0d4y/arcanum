<?php

/*
 * Archivo: app/models/SystemLog.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Es el Modelo de Logs del Sistema. Se encarga de consultar la tabla
 * 'system_logs' para obtener el historial de acciones registradas.
 *
 * No crea ni borra registros de log desde aquí; eso lo hace Logger.php.
 * Este modelo solo sirve para leer y filtrar la bitácora existente,
 * que es lo que se muestra en la sección de Logs del panel de administración.
 */

declare(strict_types=1);

final class SystemLog
{
    /**
     * Busca registros de log aplicando filtros opcionales.
     *
     * Construye la consulta dinámicamente según los filtros recibidos.
     * Se hace un JOIN con la tabla users para mostrar el nombre y rol
     * del usuario que realizó cada acción.
     *
     * El resultado está limitado a 500 registros para evitar sobrecargar
     * el servidor con consultas muy grandes.
     *
     * @param array $filters Arreglo con los filtros opcionales:
     *                       'from' => fecha inicial (YYYY-MM-DD)
     *                       'to'   => fecha final (YYYY-MM-DD)
     *                       'role' => rol del usuario ('admin' o 'operador')
     * @return array Lista de registros de log encontrados
     */
    public function search(array $filters): array
    {
        // Consulta base: trae todos los logs con nombre y rol del usuario
        $sql    = 'SELECT l.*, u.name AS user_name, u.role AS user_role
                   FROM system_logs l
                   LEFT JOIN users u ON u.id = l.user_id
                   WHERE 1=1';
        $params = [];

        // Filtra logs desde una fecha inicial (inicio del día: 00:00:00)
        if (($filters['from'] ?? '') !== '') {
            $sql .= ' AND l.created_at >= :from_date';
            $params['from_date'] = $filters['from'] . ' 00:00:00';
        }

        // Filtra logs hasta una fecha final (fin del día: 23:59:59)
        if (($filters['to'] ?? '') !== '') {
            $sql .= ' AND l.created_at <= :to_date';
            $params['to_date'] = $filters['to'] . ' 23:59:59';
        }

        // Filtra por rol del usuario si fue especificado
        if (($filters['role'] ?? '') !== '') {
            $sql .= ' AND u.role = :role';
            $params['role'] = $filters['role'];
        }

        // Ordena del más reciente al más antiguo; limita a 500 filas
        $sql .= ' ORDER BY l.created_at DESC LIMIT 500';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
