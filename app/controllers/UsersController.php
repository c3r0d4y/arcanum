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

        // Valida todos los campos (null = nuevo usuario, true = contraseña obligatoria)
        $data = $this->validatedData(null, true);

        if (!$data) {
            redirect('users/create');
        }

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

        $user = $this->findOrFail($id);

        // Valida los datos (con el ID para excluirlo en la validación de correo único)
        $data = $this->validatedData($id, false);

        if (!$data) {
            redirect('users/' . $id . '/edit');
        }

        /* Protección especial: un administrador no puede quitarse sus propios permisos.
         * Esto evita que queden sin administrador por accidente al editar el propio perfil. */
        if ($id === Auth::id() && ($data['role'] !== 'admin' || (int) $data['active'] !== 1)) {
            flash('error', 'No puede quitar sus propios permisos de administrador.');
            redirect('users/' . $id . '/edit');
        }

        /* Protección especial: no se puede dejar el sistema sin administradores activos. */
        if ($this->wouldRemoveLastAdmin($user, $data)) {
            flash('error', 'Debe quedar al menos un administrador activo.');
            redirect('users/' . $id . '/edit');
        }

        $this->users->update($id, $data);
        Logger::write('user_updated', 'Usuario actualizado: ' . $data['email']);
        flash('success', 'Usuario actualizado correctamente.');
        redirect('users');
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
        $password = (string) ($_POST['password'] ?? '');
        $data     = [
            'name'   => trim((string) ($_POST['name']   ?? '')),
            'email'  => trim((string) ($_POST['email']  ?? '')),
            'role'   => trim((string) ($_POST['role']   ?? '')),
            // active vale 1 si el checkbox fue marcado, 0 si no
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        // Guarda los datos en sesión para recuperarlos si el formulario falla
        $_SESSION['old'] = $data;

        // Verifica que los campos obligatorios no estén vacíos
        if ($data['name'] === '' || $data['email'] === '' || $data['role'] === '') {
            flash('error', 'Nombre, correo y rol son obligatorios.');
            return null;
        }

        // Verifica que el correo tenga un formato válido
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Correo electronico invalido.');
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
