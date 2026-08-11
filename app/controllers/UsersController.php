<?php

/*
 * Archivo: app/controllers/UsersController.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Controla todas las operaciones sobre usuarios del sistema:
 *  - Listar todos los usuarios
 *  - Crear un usuario nuevo
 *  - Editar los datos de un usuario existente
 *  - Actualizar (guardar cambios) de un usuario
 *  - Eliminar un usuario
 *
 * Solo los administradores pueden acceder a estas funciones.
 * Incluye validaciones de seguridad para evitar que:
 *  - Un admin se quite sus propios permisos
 *  - Se elimine o desactive al último administrador activo
 */

declare(strict_types=1);

final class UsersController extends Controller
{
    /** Instancia del modelo User para acceder a la base de datos */
    private User $users;

    /**
     * Constructor: crea la instancia del modelo User al iniciar el controlador.
     */
    public function __construct()
    {
        $this->users = new User();
    }

    /**
     * Muestra la lista completa de usuarios del sistema.
     * Solo accesible para administradores.
     */
    public function index(): void
    {
        Auth::requireAdmin();
        $this->view('users/index', [
            'title' => 'Usuarios',
            'users' => $this->users->all(),
        ]);
    }

    /**
     * Muestra el formulario vacío para crear un usuario nuevo.
     * La contraseña es obligatoria al crear un usuario nuevo.
     */
    public function create(): void
    {
        Auth::requireAdmin();
        $this->view('users/form', [
            'title'           => 'Nuevo usuario',
            'userRecord'      => null,              // null indica creación, no edición
            'action'          => url('users/store'),
            'requiresPassword' => true,             // Contraseña obligatoria al crear
        ]);
    }

    /**
     * Procesa el formulario de creación y guarda el nuevo usuario.
     *
     * Valida los datos, crea el usuario y registra la acción en la bitácora.
     */
    public function store(): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        // Valida todos los campos (null = nuevo usuario, true = contraseña obligatoria)
        $data = $this->validatedData(null, true);

        if (!$data) {
            redirect('users/create');
        }

        // Procesa el avatar si se subió uno
        $data['avatar'] = $this->saveAvatar(null);
        // El nuevo usuario debe cambiar su contraseña al primer ingreso
        $data['must_change_password'] = 1;

        $this->users->create($data);
        Logger::write('user_created', 'Usuario creado: ' . $data['email']);
        flash('success', 'Usuario registrado correctamente.');
        redirect('users');
    }

    /**
     * Muestra el formulario con los datos del usuario para editarlo.
     * La contraseña es opcional al editar (se puede dejar la actual).
     *
     * @param int $id ID del usuario a editar
     */
    public function edit(int $id): void
    {
        Auth::requireAdmin();
        $user = $this->findOrFail($id);
        $this->view('users/form', [
            'title'           => 'Editar usuario',
            'userRecord'      => $user,
            'action'          => url('users/' . $id . '/update'),
            'requiresPassword' => false, // Contraseña opcional al editar
        ]);
    }

    /**
     * Procesa el formulario de edición y guarda los cambios del usuario.
     *
     * Incluye protecciones especiales:
     *  - El administrador no puede quitarse sus propios permisos de admin
     *  - No se puede dejar el sistema sin ningún administrador activo
     *
     * @param int $id ID del usuario a actualizar
     */
    public function update(int $id): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $user = $this->findOrFail($id);

        // Valida los datos principales (nombre, correo, rol, contraseña opcional)
        $data = $this->validatedData($id, false);

        if (!$data) {
            redirect('users/' . $id . '/edit');
        }

        /* Protección especial: un administrador no puede quitarse sus propios permisos. */
        if ($id === Auth::id() && ($data['role'] !== 'admin' || (int) $data['active'] !== 1)) {
            flash('error', 'No puede quitar sus propios permisos de administrador.');
            redirect('users/' . $id . '/edit');
        }

        /* Protección especial: no se puede dejar el sistema sin administradores activos. */
        if ($this->wouldRemoveLastAdmin($user, $data)) {
            flash('error', 'Debe quedar al menos un administrador activo.');
            redirect('users/' . $id . '/edit');
        }

        // Procesa el avatar si se subió uno nuevo (reemplaza y elimina el anterior)
        $newAvatar = $this->saveAvatar($user['avatar'] ?? null);
        if ($newAvatar !== null) {
            $data['avatar'] = $newAvatar;
        }

        // Si el administrador asignó una nueva contraseña, obliga al operador a renovarla
        // en su primer ingreso y a reconfigurar su PIN desde cero
        $passwordChanged = isset($data['password_hash']);
        if ($passwordChanged) {
            $data['must_change_password'] = 1;
        }

        // Guarda los datos del operador
        $this->users->update($id, $data);

        // Restablece el PIN al predeterminado para forzar que el operador lo reconfigure
        if ($passwordChanged) {
            $this->users->updatePin($id, password_hash('123456', PASSWORD_DEFAULT));
            Logger::write('password_reset_by_admin', 'Contraseña restablecida por administrador: ' . $data['email']);
        }

        Logger::write('user_updated', 'Operador actualizado: ' . $data['email']);
        flash('success', 'Operador actualizado correctamente.');
        redirect('users');
    }

    /**
     * Sirve la imagen de avatar de un usuario.
     * La imagen se almacena fuera del directorio público y se sirve a través de este método.
     *
     * @param int $id ID del usuario
     */
    public function avatar(int $id): void
    {
        Auth::requireLogin();
        $user = $this->findOrFail($id);

        if (empty($user['avatar'])) {
            http_response_code(404);
            exit;
        }

        $path = APP_ROOT . '/storage/avatars/' . basename($user['avatar']);
        if (!is_file($path)) {
            http_response_code(404);
            exit;
        }

        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            default       => 'application/octet-stream',
        };

        header('Content-Type: ' . $mime);
        header('Cache-Control: private, max-age=86400');
        readfile($path);
        exit;
    }

    /**
     * Elimina un usuario del sistema.
     *
     * Protecciones:
     *  - No se puede eliminar al propio usuario con sesión activa
     *  - No se puede eliminar al último administrador activo
     *
     * @param int $id ID del usuario a eliminar
     */
    public function delete(int $id): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $user = $this->findOrFail($id);

        // Protección: no se puede eliminar a uno mismo
        if ($id === Auth::id()) {
            flash('error', 'No puede eliminar su propio usuario.');
            redirect('users');
        }

        // Protección: no se puede eliminar al último administrador activo
        if ($user['role'] === 'admin' && (int) $user['active'] === 1
            && $this->users->countActiveAdmins($id) === 0) {
            flash('error', 'Debe quedar al menos un administrador activo.');
            redirect('users');
        }

        $this->users->delete($id);
        Logger::write('user_deleted', 'Usuario eliminado: ' . $user['email']);
        flash('success', 'Usuario eliminado.');
        redirect('users');
    }

    /**
     * Desbloquea una cuenta de usuario bloqueada por intentos fallidos.
     * Solo accesible para administradores. Requiere verificación de PIN.
     *
     * @param int $id ID del usuario a desbloquear
     */
    public function unlock(int $id): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $user = $this->findOrFail($id);

        // Verifica que la cuenta esté efectivamente bloqueada
        if ($user['locked_at'] === null) {
            flash('error', 'La cuenta no está bloqueada.');
            redirect('users');
        }

        $this->users->unlock($id);
        Logger::write('account_unlocked', 'Cuenta desbloqueada por administrador: ' . $user['email']);
        flash('success', 'Cuenta de ' . $user['name'] . ' desbloqueada correctamente.');
        redirect('users');
    }

    /* ---------------------------------------------------------------
     * Métodos privados de validación
     * Solo se usan internamente en este controlador.
     * --------------------------------------------------------------- */

    /**
     * Valida y limpia los datos del formulario de usuario.
     *
     * Verifica que:
     *  - Nombre, correo y rol sean obligatorios y válidos
     *  - El correo tenga formato correcto
     *  - El rol sea 'admin' o 'operador'
     *  - El correo no esté ya registrado por otro usuario
     *  - La contraseña tenga al menos 6 caracteres si se proporciona
     *
     * @param int|null $id              ID del usuario al editar (null si es nuevo)
     * @param bool     $requiresPassword true si la contraseña es obligatoria
     * @return array|null Los datos validados o null si hay errores
     */
    private function validatedData(?int $id, bool $requiresPassword): ?array
    {
        $password  = (string) ($_POST['password'] ?? '');
        // Solo recibimos la parte local; el dominio siempre es @c3r0d4y.com
        $emailUser = strtolower(trim((string) ($_POST['email_user'] ?? '')));
        $data     = [
            'name'   => trim((string) ($_POST['name']  ?? '')),
            'email'  => $emailUser . '@c3r0d4y.com',
            'role'   => trim((string) ($_POST['role']  ?? '')),
            // active vale 1 si el checkbox fue marcado, 0 si no
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        // Guarda los datos en sesión para recuperarlos si el formulario falla
        $_SESSION['old'] = array_merge($data, ['email_user' => $emailUser]);

        // Verifica que los campos obligatorios no estén vacíos
        if ($data['name'] === '' || $emailUser === '' || $data['role'] === '') {
            flash('error', 'Nombre, correo y rol son obligatorios.');
            return null;
        }

        // Valida que la parte local del correo solo tenga caracteres permitidos
        if (!preg_match('/^[a-zA-Z0-9._%+\-]+$/', $emailUser)) {
            flash('error', 'El identificador solo puede contener letras, números y los caracteres . _ % + -');
            return null;
        }

        // Verifica que el rol sea uno de los dos permitidos
        if (!in_array($data['role'], ['admin', 'operador'], true)) {
            flash('error', 'Rol invalido.');
            return null;
        }

        // Verifica que el correo no esté ya registrado por otro usuario
        if ($this->users->emailExists($data['email'], $id)) {
            flash('error', 'Ya existe un usuario con ese correo.');
            return null;
        }

        // Valida la contraseña si es obligatoria o si se proporcionó una
        if ($requiresPassword && $password === '') {
            flash('error', 'La contrasena es obligatoria.');
            return null;
        }
        if ($password !== '') {
            if (strlen($password) < 6) {
                flash('error', 'La contrasena debe tener al menos 6 caracteres.');
                return null;
            }
            // Hashea la contraseña con bcrypt para guardarla segura en la BD
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $data;
    }

    /**
     * Verifica si cambiar un usuario causaría quedarse sin administradores activos.
     *
     * Devuelve true si el usuario era admin activo, los nuevos datos lo cambiarían
     * (quita el rol admin o lo desactiva), y no quedan otros admins activos.
     *
     * @param array $currentUser Los datos actuales del usuario antes del cambio
     * @param array $data        Los nuevos datos que se quieren guardar
     * @return bool true si el cambio dejaría el sistema sin admins
     */
    private function wouldRemoveLastAdmin(array $currentUser, array $data): bool
    {
        return $currentUser['role'] === 'admin'              // El usuario era admin
            && (int) $currentUser['active'] === 1            // Estaba activo
            && ($data['role'] !== 'admin' || (int) $data['active'] !== 1) // Se le quita el rol o se desactiva
            && $this->users->countActiveAdmins((int) $currentUser['id']) === 0; // No hay otros admins
    }

    /**
     * Busca un usuario por ID o muestra la página 404 si no existe.
     *
     * @param int $id ID del usuario a buscar
     * @return array Los datos del usuario si existe
     */
    /**
     * Guarda el avatar subido y elimina el anterior si existía.
     * Devuelve el nombre del archivo guardado, o null si no se subió ninguno.
     *
     * @param string|null $oldAvatar Nombre del archivo anterior (para borrarlo)
     * @return string|null Nombre del nuevo archivo o null si no hubo imagen
     */
    private function saveAvatar(?string $oldAvatar): ?string
    {
        // Si no hay archivo o está vacío, no hay nada que guardar
        if (empty($_FILES['avatar']['tmp_name'])) {
            return null;
        }

        $file = $_FILES['avatar'];

        // Verifica que la subida fue exitosa
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Limita el tamaño a 2 MB
        if ($file['size'] > 2 * 1024 * 1024) {
            flash('error', 'La imagen no debe superar 2 MB.');
            return null;
        }

        // Verifica que sea realmente una imagen por su contenido (no solo la extensión)
        $info = @getimagesize($file['tmp_name']);
        if (!$info) {
            flash('error', 'El archivo de avatar debe ser una imagen válida.');
            return null;
        }

        $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
        if (!in_array($info[2], $allowed, true)) {
            flash('error', 'Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.');
            return null;
        }

        $ext      = image_type_to_extension($info[2], false);
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest     = APP_ROOT . '/storage/avatars/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            flash('error', 'No se pudo guardar la imagen de perfil.');
            return null;
        }

        // Elimina el avatar anterior si existía
        if ($oldAvatar) {
            $oldPath = APP_ROOT . '/storage/avatars/' . basename($oldAvatar);
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $filename;
    }

    private function findOrFail(int $id): array
    {
        $user = $this->users->find($id);
        if (!$user) {
            http_response_code(404);
            $this->view('errors/404');
            exit;
        }
        return $user;
    }
}
