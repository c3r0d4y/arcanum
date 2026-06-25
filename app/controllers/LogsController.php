<?php

/*
 * Archivo: app/controllers/LogsController.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Controla la sección de Logs (bitácora) del sistema.
 * Solo los administradores pueden acceder a esta sección.
 *
 * Muestra el historial de acciones registradas con filtros
 * por rango de fechas y por rol del usuario que realizó la acción.
 */

declare(strict_types=1);

final class LogsController extends Controller
{
    /**
     * Muestra la lista de registros de log del sistema.
     *
     * Verifica que el usuario sea administrador antes de continuar.
     * Lee los filtros de la URL (parámetros GET) y los pasa al modelo
     * para obtener solo los logs que coincidan con el criterio.
     *
     * Los valores por defecto de los filtros son:
     *  - 'from': primer día del mes actual
     *  - 'to':   hoy
     *  - 'role': todos los roles (sin filtro)
     */
    public function index(): void
    {
        // Solo administradores pueden ver la bitácora
        Auth::requireAdmin();

        // Lee los filtros de búsqueda enviados por la URL
        // Si no se envía un filtro, se usa el valor por defecto indicado
        $filters = [
            'from' => trim((string) ($_GET['from'] ?? date('Y-m-01'))), // Primer día del mes actual
            'to'   => trim((string) ($_GET['to']   ?? date('Y-m-d'))), // Hoy
            'role' => trim((string) ($_GET['role']  ?? '')),           // Todos los roles
        ];

        // Carga la vista de logs pasando los registros y los filtros activos
        $this->view('logs/index', [
            'title'   => 'Logs del sistema',
            'logs'    => (new SystemLog())->search($filters),
            'filters' => $filters,
        ]);
    }
}
