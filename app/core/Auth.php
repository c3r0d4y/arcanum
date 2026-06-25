<?php

/*
 * Archivo: app/core/Auth.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Gestiona todo lo relacionado con la autenticación del usuario:
 * saber si hay alguien con sesión activa, obtener sus datos,
 * iniciar sesión, cerrar sesión y proteger páginas que requieren
 * estar autenticado o ser administrador.
 */

declare(strict_types=1);

final class Auth
{
    /**
     * Verifica si hay un usuario con sesión iniciada.
     * Devuelve true si hay sesión activa, false si no.
     */
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Devuelve los datos del usuario autenticado como arreglo,
     * o null si no hay nadie con sesión activa.
     * Los datos incluyen: id, name, email y role.
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Devuelve solo el ID numérico del usuario autenticado,
     * o null si no hay sesión activa.
     * Útil para guardar quién creó o modificó un registro.
     */
    public static function id(): ?int
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    /**
     * Protege páginas que requieren sesión iniciada.
     * Si el usuario no ha iniciado sesión, lo redirige al login.
     * Se llama al inicio de cualquier acción que requiera autenticación.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('login');
        }
    }

    /**
     * Protege páginas exclusivas para administradores.
     * Primero verifica que haya sesión iniciada, y luego
     * que el usuario tenga el rol 'admin'. Si no cumple,
     * muestra la página de error 403 (acceso denegado).
     */
    public static function requireAdmin(): void
    {
        // Primero aseguramos que el usuario esté autenticado
        self::requireLogin();

        // Luego verificamos que sea administrador
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            (new Controller())->view('errors/403');
            exit;
        }
    }

    /**
     * Inicia la sesión del usuario guardando sus datos en $_SESSION.
     * Se regenera el ID de sesión para prevenir ataques de fijación
     * de sesión (session fixation).
     *
     * @param array $user Arreglo con los datos del usuario autenticado.
     */
    public static function login(array $user): void
    {
        // Genera un nuevo ID de sesión para mayor seguridad
        session_regenerate_id(true);

        // Guarda solo los campos necesarios del usuario (no la contraseña)
        $_SESSION['user'] = [
            'id'    => (int) $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
    }

    /**
     * Cierra la sesión del usuario de forma segura.
     * Limpia los datos de sesión, elimina la cookie del navegador
     * y destruye la sesión en el servidor.
     */
    public static function logout(): void
    {
        // Borra todos los datos guardados en la sesión
        $_SESSION = [];

        // Si la sesión usa cookies, se elimina la cookie del navegador
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000, // Fecha en el pasado para que el navegador la elimine
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destruye la sesión en el servidor
        session_destroy();
    }
}
