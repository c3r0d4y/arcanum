<?php

/*
 * Archivo: app/controllers/ProfileController.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Gestiona el perfil personal del usuario autenticado:
 *  - Ver y actualizar la foto de perfil
 *  - Cambiar la contraseña de acceso
 *  - Configurar o cambiar el PIN de seguridad
 *  - Verificar PIN vía AJAX (endpoint JSON)
 */

declare(strict_types=1);

final class ProfileController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    /**
     * Muestra la página de perfil personal del usuario.
     */
    public function index(): void
    {
        Auth::requireLogin();
        $id   = Auth::id();
        $user = $this->users->find($id);

        $this->view('profile/index', [
            'title'      => 'Mi Perfil',
            'userRecord' => $user,
        ]);
    }

    /**
     * Cambia la contraseña del usuario autenticado.
     * Requiere la contraseña actual para confirmar identidad.
     */
    public function updatePassword(): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $id       = Auth::id();
        $current  = (string) ($_POST['current_password'] ?? '');
        $new      = (string) ($_POST['new_password'] ?? '');
        $confirm  = (string) ($_POST['confirm_password'] ?? '');

        // Obtiene el hash actual para verificar la contraseña vigente
        $full = $this->users->findByEmail(Auth::user()['email']);
        if (!$full || !password_verify($current, $full['password_hash'])) {
            flash('error', 'La contraseña actual es incorrecta.');
            redirect('profile');
        }

        if (strlen($new) < 8) {
            flash('error', 'La nueva contraseña debe tener al menos 8 caracteres.');
            redirect('profile');
        }

        if ($new !== $confirm) {
            flash('error', 'Las contraseñas nuevas no coinciden.');
            redirect('profile');
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $this->users->updatePassword($id, $hash);
        $this->users->clearMustChangePassword($id);

        // Refresca la sesión para quitar el bloqueo de contraseña obligatoria
        Auth::refreshSession(['must_change_password' => 0]);

        Logger::write('password_changed', 'Usuario cambió su contraseña.');
        flash('success', 'Contraseña actualizada correctamente.');
        redirect('profile');
    }

    /**
     * Configura o cambia el PIN de seguridad del usuario.
     * Si ya tiene PIN, requiere confirmar el PIN actual.
     */
    public function updatePin(): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $id      = Auth::id();
        $newPin  = trim((string) ($_POST['new_pin'] ?? ''));
        $confirm = trim((string) ($_POST['confirm_pin'] ?? ''));

        // Verifica que el PIN tenga entre 4 y 8 dígitos numéricos
        if (!preg_match('/^\d{4,8}$/', $newPin)) {
            flash('error', 'El PIN debe ser numérico y tener entre 4 y 8 dígitos.');
            redirect('profile');
        }

        if ($newPin !== $confirm) {
            flash('error', 'Los PIN no coinciden.');
            redirect('profile');
        }

        // Si ya tenía PIN, requiere el actual para cambiarlo
        $currentPinHash = $this->users->findPin($id);
        if ($currentPinHash !== null) {
            $current = trim((string) ($_POST['current_pin'] ?? ''));
            if (!password_verify($current, $currentPinHash)) {
                flash('error', 'El PIN actual es incorrecto.');
                redirect('profile');
            }
        }

        $this->users->updatePin($id, password_hash($newPin, PASSWORD_DEFAULT));

        // Refresca la sesión: activa has_pin y desactiva el bloqueo de PIN predeterminado
        Auth::refreshSession(['pin_hash' => $newPin, 'has_default_pin' => false]);

        Logger::write('pin_changed', 'Usuario configuró su PIN de seguridad.');
        flash('success', 'PIN de seguridad configurado correctamente.');
        redirect('profile');
    }

    /**
     * Actualiza la foto de perfil del usuario autenticado.
     */
    public function updateAvatar(): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $id   = Auth::id();
        $user = $this->users->find($id);

        $filename = $this->saveAvatar($user['avatar'] ?? null);
        if ($filename === null && !empty($_FILES['avatar']['tmp_name'])) {
            redirect('profile');
        }

        if ($filename !== null) {
            $this->users->updateAvatar($id, $filename);
            Logger::write('avatar_changed', 'Usuario actualizó su foto de perfil.');
            flash('success', 'Foto de perfil actualizada.');
        }

        redirect('profile');
    }

    /**
     * Endpoint AJAX para verificar el PIN antes de operaciones críticas.
     * Devuelve JSON y almacena en sesión la marca de tiempo de verificación.
     */
    public function verifyPin(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        header('Content-Type: application/json');

        $pin  = trim((string) ($_POST['pin'] ?? ''));
        $id   = Auth::id();
        $hash = $this->users->findPin($id);

        // Usuario sin PIN configurado
        if ($hash === null) {
            echo json_encode(['ok' => false, 'setup' => true,
                'error' => 'No has configurado tu PIN. Ve a Mi Perfil primero.']);
            exit;
        }

        if (!password_verify($pin, $hash)) {
            // PIN incorrecto: cuenta como intento fallido igual que en el login.
            // Sin este contador, una sesión robada podría probar los 10,000
            // PIN posibles por AJAX hasta dar con el correcto.
            // El bloqueo es PERMANENTE porque el usuario ya está autenticado:
            // fallar el PIN repetidamente aquí sugiere una sesión secuestrada.
            $attempts = $this->users->incrementFailedAttempts($id, true);

            // Posición del intento dentro de la ventana actual de 3
            $enVentana = (($attempts - 1) % 3) + 1;
            $restantes = 3 - $enVentana;

            if ($restantes <= 0) {
                // Límite alcanzado: registra el evento, bloquea y cierra la sesión
                Logger::write('account_locked', 'Cuenta bloqueada: 3 PIN incorrectos en verificación de operación crítica.', $id);
                Auth::logout();
                echo json_encode([
                    'ok'     => false,
                    'locked' => true,
                    'error'  => 'Cuenta bloqueada por exceder los intentos de PIN. La sesión fue cerrada. Contacte al administrador del sistema.',
                ]);
                exit;
            }

            Logger::write('pin_failed', "PIN incorrecto en operación crítica ({$enVentana}/3).", $id);
            $label = $restantes === 1 ? 'intento' : 'intentos';
            echo json_encode(['ok' => false, 'error' => "PIN incorrecto. Quedan {$restantes} {$label} antes del bloqueo."]);
            exit;
        }

        // PIN correcto: limpia el contador de intentos fallidos
        $this->users->resetFailedAttempts($id);

        // Si el PIN verificado es el predeterminado, se bloquea la operación
        if ($_SESSION['user']['has_default_pin'] ?? false) {
            echo json_encode([
                'ok'          => false,
                'default_pin' => true,
                'error'       => 'Tu PIN aún es el predeterminado (123456). Cámbialo en Mi Perfil antes de continuar.',
            ]);
            exit;
        }

        // Marca la verificación en sesión (válida 90 segundos)
        $_SESSION['pin_verified_at'] = time();
        echo json_encode(['ok' => true]);
        exit;
    }

    /* ── Métodos privados ── */

    /**
     * Guarda la imagen de avatar subida y elimina la anterior.
     * Misma lógica que en UsersController pero para el perfil propio.
     */
    private function saveAvatar(?string $oldAvatar): ?string
    {
        if (empty($_FILES['avatar']['tmp_name'])) {
            return null;
        }

        $file = $_FILES['avatar'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            flash('error', 'La imagen no debe superar 2 MB.');
            return null;
        }

        $info = @getimagesize($file['tmp_name']);
        if (!$info) {
            flash('error', 'El archivo debe ser una imagen válida.');
            return null;
        }

        $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
        if (!in_array($info[2], $allowed, true)) {
            flash('error', 'Formato no permitido. Use JPG, PNG, GIF o WEBP.');
            return null;
        }

        $ext      = image_type_to_extension($info[2], false);
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest     = APP_ROOT . '/storage/avatars/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            flash('error', 'No se pudo guardar la imagen.');
            return null;
        }

        if ($oldAvatar) {
            $old = APP_ROOT . '/storage/avatars/' . basename($oldAvatar);
            if (is_file($old)) {
                @unlink($old);
            }
        }

        return $filename;
    }
}
