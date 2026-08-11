<?php

/*
 * Archivo: app/controllers/AuthController.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Controla el proceso de autenticación del usuario:
 *  - Mostrar el formulario de ingreso (login)
 *  - Procesar el formulario y verificar credenciales
 *  - Cerrar la sesión del usuario (logout)
 *
 * Usa la clase User para buscar al usuario en la base de datos,
 * la clase Auth para gestionar la sesión, y Logger para registrar
 * los intentos de inicio y cierre de sesión en la bitácora.
 */

declare(strict_types=1);

final class AuthController extends Controller
{
    /**
     * Muestra la página de inicio de sesión.
     *
     * Si el usuario ya tiene sesión activa, lo redirige directamente
     * a la lista de documentos para que no vea el login innecesariamente.
     */
    public function showLogin(): void
    {
        // Si ya está autenticado, lo mandamos a documentos
        if (Auth::check()) {
            redirect('documents');
        }

        // Carga la vista del formulario de login
        $this->view('auth/login', ['title' => 'Ingresar']);
    }

    /**
     * Procesa el formulario de inicio de sesión (petición POST).
     *
     * Pasos que sigue:
     *  1. Verifica el token CSRF para proteger contra ataques externos.
     *  2. Lee el correo y contraseña enviados por el formulario.
     *  3. Busca al usuario activo con ese correo en la base de datos.
     *  4. Verifica que la contraseña coincida con el hash guardado.
     *  5. Si todo es correcto, inicia la sesión y redirige a documentos.
     *  6. Si algo falla, registra el intento fallido y vuelve al login.
     */
    public function login(): void
    {
        // Protección CSRF: verifica que el formulario sea legítimo
        Csrf::verify();

        // Lee y limpia los datos enviados por el formulario
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        // Guarda el correo en sesión para rellenarlo si hay error
        $_SESSION['old'] = ['email' => $email];

        $userModel = new User();

        // Busca al usuario activo con ese correo
        $user = $userModel->findByEmail($email);

        // Si el usuario no existe, mensaje genérico para no revelar datos
        if (!$user) {
            flash('error', 'Credenciales incorrectas.');
            Logger::write('login_failed', "Intento fallido — correo no encontrado: {$email}", null);
            redirect('login');
        }

        // Verifica si la cuenta está bloqueada antes de validar la contraseña
        if ($user['locked_at'] !== null) {
            $expira = $user['lock_expires_at'] ?? null;

            // Bloqueo permanente (sin fecha de expiración): solo lo quita un administrador
            if ($expira === null) {
                flash('error', 'Acceso suspendido. Cuenta bloqueada por múltiples intentos fallidos. Contacte al administrador del sistema.');
                Logger::write('login_blocked', "Intento en cuenta bloqueada: {$email}", null);
                redirect('login');
            }

            // Bloqueo temporal aún vigente: informa cuánto falta para reintentarlo
            if (strtotime((string) $expira) > time()) {
                $resta = strtotime((string) $expira) - time();
                flash('error', 'Cuenta bloqueada temporalmente por intentos fallidos. Intenta de nuevo en ' . $this->formatoEspera($resta) . '.');
                Logger::write('login_blocked', "Intento en cuenta con bloqueo temporal: {$email}", null);
                redirect('login');
            }

            // El bloqueo temporal ya expiró: se permite intentar de nuevo
            // (el contador de intentos se conserva para escalar la espera)
        }

        // Verifica que la contraseña coincida con el hash almacenado
        if (!password_verify($password, $user['password_hash'])) {
            // Incrementa el contador; el modelo bloquea cada 3 intentos fallidos
            // con espera creciente (1 min, 5 min, 30 min)
            $attempts = $userModel->incrementFailedAttempts((int) $user['id']);

            // Posición del intento dentro de la ventana actual de 3
            $enVentana = (($attempts - 1) % 3) + 1;
            $remaining = 3 - $enVentana;

            if ($remaining <= 0) {
                // Relee el registro para saber qué tipo de bloqueo se aplicó:
                // temporal (con fecha de expiración) o permanente (sin ella,
                // p. ej. si la migración de bloqueos temporales no se ha corrido)
                $bloqueado = $userModel->findByEmail($email);
                $expira    = $bloqueado['lock_expires_at'] ?? null;

                if ($expira !== null) {
                    $espera = $this->formatoEspera(max(1, strtotime((string) $expira) - time()));
                    flash('error', "Intento {$enVentana} de 3 — Cuenta bloqueada temporalmente. Podrás intentar de nuevo en {$espera}.");
                    Logger::write('account_locked', "Cuenta bloqueada temporalmente ({$attempts} intentos acumulados): {$email}", null);
                } else {
                    flash('error', "Intento {$enVentana} de 3 — Cuenta bloqueada por exceder el número máximo de intentos. Contacte al administrador del sistema.");
                    Logger::write('account_locked', "Cuenta bloqueada por intentos fallidos: {$email}", null);
                }
            } else {
                $label = $remaining === 1 ? 'intento' : 'intentos';
                flash('error', "Intento {$enVentana} de 3 — Credenciales incorrectas. Quedan {$remaining} {$label} antes del bloqueo.");
                Logger::write('login_failed', "Intento fallido ({$enVentana}/3) para: {$email}", null);
            }

            redirect('login');
        }

        // Si el usuario no tiene PIN configurado, asignamos 123456 como predeterminado
        if (empty($user['pin_hash'])) {
            $defaultHash      = password_hash('123456', PASSWORD_DEFAULT);
            $userModel->updatePin((int) $user['id'], $defaultHash);
            $user['pin_hash'] = $defaultHash;
            Logger::write('pin_default_assigned', 'PIN predeterminado asignado al usuario: ' . $user['email']);
        }

        // Contraseña correcta: guarda al usuario en sesión temporal y pide el PIN
        // El contador de intentos NO se resetea aún; se resetea al completar el PIN
        $_SESSION['pending_auth'] = [
            'user'       => $user,
            'created_at' => time(),
        ];

        redirect('login/pin');
    }

    /**
     * Muestra la pantalla de verificación de PIN (segundo factor de autenticación).
     * Solo accesible si existe una sesión pendiente válida (contraseña ya verificada).
     */
    public function showLoginPin(): void
    {
        // Si ya tiene sesión completa, va directo a documentos
        if (Auth::check()) {
            redirect('documents');
        }

        // Sin sesión pendiente, de vuelta al login
        if (empty($_SESSION['pending_auth'])) {
            redirect('login');
        }

        // La sesión pendiente expira a los 2 minutos por seguridad
        if ((time() - ($_SESSION['pending_auth']['created_at'] ?? 0)) > 120) {
            unset($_SESSION['pending_auth']);
            flash('error', 'La verificación expiró. Ingresa de nuevo.');
            redirect('login');
        }

        $pendingUser = $_SESSION['pending_auth']['user'];

        $this->view('auth/login_pin', [
            'title'     => 'Verificación de PIN',
            'userName'  => $pendingUser['name'],
            'userEmail' => $pendingUser['email'],
        ]);
    }

    /**
     * Procesa el PIN enviado en el segundo paso de autenticación.
     * Un PIN incorrecto cuenta como intento fallido de login (mismo contador).
     * Solo el PIN correcto completa la sesión con Auth::login().
     */
    public function verifyLoginPin(): void
    {
        Csrf::verify();

        // Sin sesión pendiente, de vuelta al login
        if (empty($_SESSION['pending_auth'])) {
            redirect('login');
        }

        // La sesión pendiente expiró
        if ((time() - ($_SESSION['pending_auth']['created_at'] ?? 0)) > 120) {
            unset($_SESSION['pending_auth']);
            flash('error', 'La verificación expiró. Ingresa de nuevo.');
            redirect('login');
        }

        $user      = $_SESSION['pending_auth']['user'];
        $pin       = (string) ($_POST['pin'] ?? '');
        $userModel = new User();

        // Verifica el PIN contra el hash almacenado
        if (!password_verify($pin, (string) ($user['pin_hash'] ?? ''))) {
            // PIN incorrecto: cuenta como intento fallido. El bloqueo aquí es
            // PERMANENTE porque la contraseña ya fue verificada: si alguien
            // llega a esta etapa y falla el PIN repetidamente, es señal de que
            // la contraseña pudo haber sido robada.
            $attempts = $userModel->incrementFailedAttempts((int) $user['id'], true);

            // Posición del intento dentro de la ventana actual de 3
            $enVentana = (($attempts - 1) % 3) + 1;
            $remaining = 3 - $enVentana;

            if ($remaining <= 0) {
                // Límite alcanzado: destruye la sesión pendiente y bloquea
                unset($_SESSION['pending_auth']);
                flash('error', "Intento {$enVentana} de 3 — PIN incorrecto. Cuenta bloqueada por exceder el número máximo de intentos. Contacte al administrador del sistema.");
                Logger::write('account_locked', "Cuenta bloqueada por intentos fallidos de PIN: {$user['email']}", null);
                redirect('login');
            }

            $label = $remaining === 1 ? 'intento' : 'intentos';
            flash('error', "Intento {$enVentana} de 3 — PIN incorrecto. Quedan {$remaining} {$label} antes del bloqueo.");
            Logger::write('login_failed', "PIN incorrecto ({$enVentana}/3) para: {$user['email']}", null);
            redirect('login/pin');
        }

        // PIN correcto: destruye la sesión temporal, resetea el contador y completa el login
        unset($_SESSION['pending_auth']);
        $userModel->resetFailedAttempts((int) $user['id']);
        Auth::login($user);
        Logger::write('login', 'Inicio de sesion correcto (PIN verificado).');
        redirect('documents');
    }

    /**
     * Endpoint AJAX llamado por el temporizador de inactividad del navegador.
     * Renueva el registro de última actividad en la sesión si el usuario
     * sigue autenticado. Devuelve JSON para que el JS lo consuma.
     */
    public function ping(): void
    {
        // Solo renueva si hay sesión válida y no expirada
        Auth::requireLogin();
        header('Content-Type: application/json');
        echo '{"ok":true}';
        exit;
    }

    /**
     * Cierra la sesión del usuario actual.
     *
     * Si hay un usuario autenticado, registra el cierre en la bitácora
     * antes de destruir la sesión. Luego redirige al login.
     */
    public function logout(): void
    {
        // Solo registramos si hay alguien con sesión activa
        if (Auth::check()) {
            Logger::write('logout', 'Cierre de sesion.');
        }

        // Destruye la sesión y elimina la cookie del navegador
        Auth::logout();
        redirect('login');
    }

    /**
     * Convierte una cantidad de segundos en un texto legible en español
     * para los mensajes de bloqueo temporal (p. ej. "5 minutos", "1 minuto").
     */
    private function formatoEspera(int $segundos): string
    {
        if ($segundos < 60) {
            return $segundos === 1 ? '1 segundo' : "{$segundos} segundos";
        }

        // Redondea hacia arriba para no prometer menos tiempo del real
        $minutos = (int) ceil($segundos / 60);
        return $minutos === 1 ? '1 minuto' : "{$minutos} minutos";
    }
}
