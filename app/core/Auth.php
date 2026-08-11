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
     * Si el usuario no ha iniciado sesión, o lleva más de SESSION_TIMEOUT
     * segundos sin actividad, lo cierra y lo manda al login.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('login');
        }

        // Cierra la sesión si superó el tiempo máximo sin actividad
        if (isset($_SESSION['last_activity']) &&
            (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            self::logout();
            flash('error', 'Sesión cerrada por inactividad. Ingresa de nuevo.');
            redirect('login');
        }

        $_SESSION['last_activity'] = time();

        // Verifica si el usuario debe completar la configuración inicial obligatoria
        $user  = $_SESSION['user'] ?? [];
        $route = self::currentRoute();

        // ── Paso 1: cambiar la contraseña provisional antes de cualquier otra acción ──
        if (($user['must_change_password'] ?? 0) === 1) {
            // Solo puede estar en la página de perfil o el formulario de contraseña
            $permitidos = ['profile', 'profile/password', 'logout', 'ping'];
            if (!in_array($route, $permitidos, true)) {
                flash('warning', 'Debes cambiar tu contraseña de acceso antes de continuar.');
                redirect('profile');
            }
            return; // Mientras no cambie la contraseña, no se evalúa el paso 2
        }

        // ── Paso 2: configurar el PIN de seguridad (si es el predeterminado o no existe) ──
        if (($user['has_default_pin'] ?? false) || !($user['has_pin'] ?? false)) {
            // Solo puede estar en la página de perfil o el formulario de PIN
            $permitidos = ['profile', 'profile/pin', 'logout', 'ping'];
            if (!in_array($route, $permitidos, true)) {
                flash('warning', 'Configura tu PIN de seguridad antes de continuar.');
                redirect('profile');
            }
        }
    }

    /**
     * Verifica que el PIN haya sido confirmado recientemente (máx. 90 s).
     * Si no, redirige de regreso con un error. Uso de un solo consumo.
     */
    public static function requirePin(): void
    {
        $verified = (int) ($_SESSION['pin_verified_at'] ?? 0);
        if ((time() - $verified) > 90) {
            flash('error', 'PIN de seguridad no verificado. Inténtalo de nuevo.');
            $back = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/documents';
            header('Location: ' . $back);
            exit;
        }
        unset($_SESSION['pin_verified_at']);
    }

    /**
     * Obtiene la ruta actual relativa a BASE_URL para comparaciones internas.
     */
    private static function currentRoute(): string
    {
        $route = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '', '/');
        $base  = trim(BASE_URL, '/');
        if ($base !== '' && str_starts_with($route, $base)) {
            $route = trim(substr($route, strlen($base)), '/');
        }
        return $route;
    }

    /**
     * Actualiza el registro de última actividad sin pasar por
     * la verificación completa de requireLogin().
     * Se usa en el endpoint de ping del temporizador JS.
     */
    public static function refreshActivity(): void
    {
        if (self::check()) {
            $_SESSION['last_activity'] = time();
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

        // Guarda los campos necesarios del usuario en sesión (sin contraseña)
        $_SESSION['user'] = [
            'id'                   => (int) $user['id'],
            'name'                 => $user['name'],
            'email'                => $user['email'],
            'role'                 => $user['role'],
            'must_change_password' => (int) ($user['must_change_password'] ?? 0),
            'has_pin'              => !empty($user['pin_hash']),
            // Marca si el PIN sigue siendo el predeterminado del sistema (123456)
            'has_default_pin'      => password_verify('123456', (string) ($user['pin_hash'] ?? '')),
        ];

        // Marca el momento de inicio para el temporizador de inactividad
        $_SESSION['last_activity'] = time();
    }

    /**
     * Cierra la sesión del usuario de forma segura.
     * Limpia los datos de sesión, elimina la cookie del navegador
     * y destruye la sesión en el servidor.
     */
    /**
     * Refresca los datos del usuario en sesión después de cambios en perfil.
     */
    public static function refreshSession(array $updated): void
    {
        if (isset($updated['must_change_password'])) {
            $_SESSION['user']['must_change_password'] = (int) $updated['must_change_password'];
        }
        if (array_key_exists('pin_hash', $updated)) {
            $_SESSION['user']['has_pin'] = !empty($updated['pin_hash']);
        }
        if (isset($updated['name'])) {
            $_SESSION['user']['name'] = $updated['name'];
        }
        // Actualiza el flag de PIN predeterminado cuando el usuario cambia su PIN
        if (array_key_exists('has_default_pin', $updated)) {
            $_SESSION['user']['has_default_pin'] = (bool) $updated['has_default_pin'];
        }
    }

    /**
     * Bloquea la operación si el usuario aún usa el PIN predeterminado (123456).
     * Se debe llamar antes de cualquier acción que modifique datos del sistema.
     */
    public static function blockIfDefaultPin(): void
    {
        if ($_SESSION['user']['has_default_pin'] ?? false) {
            flash('error', 'Debes cambiar tu PIN predeterminado (123456) en Mi Perfil antes de realizar esta operación.');
            $back = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/documents';
            header('Location: ' . $back);
            exit;
        }
    }

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
