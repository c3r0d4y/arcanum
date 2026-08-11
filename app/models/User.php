<?php

/*
 * Archivo: app/models/User.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Es el Modelo de Usuario. Contiene toda la lógica de acceso a la
 * tabla 'users' de la base de datos: listar, buscar, crear, actualizar
 * y eliminar usuarios del sistema.
 *
 * También incluye validaciones de negocio como comprobar si un correo
 * ya está en uso o contar cuántos administradores activos quedan,
 * lo cual es importante para no dejar el sistema sin administrador.
 */

declare(strict_types=1);

final class User
{
    /**
     * Devuelve todos los usuarios del sistema ordenados por nombre.
     * Se excluye el campo password_hash por seguridad.
     *
     * @return array Lista con todos los usuarios registrados
     */
    public function all(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, name, email, role, active, failed_attempts, locked_at,
                    avatar, must_change_password, created_at
             FROM users
             ORDER BY name ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Busca un usuario por su ID.
     * Se excluye el campo password_hash por seguridad.
     *
     * @param int $id ID del usuario a buscar
     * @return array|null Los datos del usuario, o null si no existe
     */
    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, name, email, role, active, failed_attempts, locked_at,
                    avatar, must_change_password, created_at, pin_hash
             FROM users
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        // fetch() devuelve false si no encontró nada; lo convertimos a null
        return $user ?: null;
    }

    /**
     * Busca un usuario activo por su correo electrónico.
     * Se usa durante el proceso de inicio de sesión.
     * Devuelve TODOS los campos incluyendo password_hash para poder
     * verificar la contraseña con password_verify().
     *
     * @param string $email Correo del usuario a buscar
     * @return array|null Los datos completos del usuario, o null si no existe o está inactivo
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM users WHERE email = :email AND active = 1 LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Verifica si ya existe un usuario con un correo determinado.
     * Se usa para evitar correos duplicados al crear o editar usuarios.
     *
     * @param string   $email    Correo a verificar
     * @param int|null $exceptId Si se está editando un usuario, su ID se excluye de la búsqueda
     *                           para que no choque consigo mismo
     * @return bool true si el correo ya está en uso, false si está disponible
     */
    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql    = 'SELECT id FROM users WHERE email = :email';
        $params = ['email' => $email];

        // Al editar, excluimos el propio usuario de la búsqueda
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }

        $stmt = Database::connection()->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }

    /**
     * Cuenta cuántos administradores activos hay en el sistema.
     * Se usa para asegurarse de que siempre quede al menos uno
     * antes de eliminar o desactivar a un administrador.
     *
     * @param int|null $exceptId Si se especifica, excluye a ese usuario del conteo
     *                           (útil cuando se está editando al propio administrador)
     * @return int Número de administradores activos
     */
    public function countActiveAdmins(?int $exceptId = null): int
    {
        $sql    = "SELECT COUNT(*) FROM users WHERE role = 'admin' AND active = 1";
        $params = [];

        // Excluye al usuario actual del conteo si se está evaluando si puede cambiar su propio rol
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     *
     * @param array $data Arreglo con los datos del usuario:
     *                    name, email, password_hash, role, active
     * @return int ID del usuario recién creado
     */
    public function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (name, email, password_hash, role, active, avatar, must_change_password, created_at)
             VALUES (:name, :email, :password_hash, :role, :active, :avatar, :must_change_password, NOW())'
        );
        $data['avatar']                = $data['avatar'] ?? null;
        $data['must_change_password']  = $data['must_change_password'] ?? 0;
        $stmt->execute($data);
        return (int) Database::connection()->lastInsertId();
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * Si el arreglo $data incluye 'password_hash', también se
     * actualiza la contraseña. Esto permite editar sin cambiar
     * la contraseña si el campo viene vacío.
     *
     * @param int   $id   ID del usuario a actualizar
     * @param array $data Nuevos datos del usuario
     */
    public function update(int $id, array $data): void
    {
        // Campos que siempre se actualizan
        $sets = [
            'name = :name',
            'email = :email',
            'role = :role',
            'active = :active',
        ];

        // Solo se actualiza la contraseña si se proporcionó una nueva
        if (isset($data['password_hash'])) {
            $sets[] = 'password_hash = :password_hash';
        }

        // Solo se actualiza el avatar si se indica explícitamente
        if (array_key_exists('avatar', $data)) {
            $sets[] = 'avatar = :avatar';
        }

        // Permite forzar el cambio de contraseña en el próximo ingreso
        if (isset($data['must_change_password'])) {
            $sets[] = 'must_change_password = :must_change_password';
        }

        $data['id'] = $id;

        Database::connection()
            ->prepare('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id')
            ->execute($data);
    }

    /**
     * Elimina un usuario de la base de datos por su ID.
     * Sus documentos quedan huérfanos (created_by = NULL)
     * gracias a la restricción ON DELETE SET NULL definida en el esquema.
     *
     * @param int $id ID del usuario a eliminar
     */
    public function delete(int $id): void
    {
        Database::connection()
            ->prepare('DELETE FROM users WHERE id = :id')
            ->execute(['id' => $id]);
    }

    /**
     * Devuelve el pin_hash del usuario para verificarlo al operar.
     * Se usa solo en el endpoint de verificación de PIN.
     */
    public function findPin(int $id): ?string
    {
        $stmt = Database::connection()->prepare(
            'SELECT pin_hash FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? ($row['pin_hash'] ?? null) : null;
    }

    /**
     * Guarda el hash del PIN del usuario.
     */
    public function updatePin(int $id, string $pinHash): void
    {
        Database::connection()
            ->prepare('UPDATE users SET pin_hash = :hash WHERE id = :id')
            ->execute(['hash' => $pinHash, 'id' => $id]);
    }

    /**
     * Marca que el usuario ya cambió su contraseña obligatoria.
     */
    public function clearMustChangePassword(int $id): void
    {
        Database::connection()
            ->prepare('UPDATE users SET must_change_password = 0 WHERE id = :id')
            ->execute(['id' => $id]);
    }

    /**
     * Actualiza la contraseña del usuario.
     */
    public function updatePassword(int $id, string $passwordHash): void
    {
        Database::connection()
            ->prepare('UPDATE users SET password_hash = :hash WHERE id = :id')
            ->execute(['hash' => $passwordHash, 'id' => $id]);
    }

    /**
     * Actualiza el avatar del perfil propio del usuario.
     */
    public function updateAvatar(int $id, ?string $filename): void
    {
        Database::connection()
            ->prepare('UPDATE users SET avatar = :avatar WHERE id = :id')
            ->execute(['avatar' => $filename, 'id' => $id]);
    }

    /**
     * Guarda si la tabla users ya tiene la columna lock_expires_at.
     * Se consulta una sola vez por petición para no repetir la verificación.
     * Si la migración migrate_lockout.sql aún no se ejecuta, el sistema
     * sigue funcionando con el comportamiento anterior (bloqueo permanente).
     */
    private static ?bool $tieneExpiracionBloqueo = null;

    /**
     * Verifica si existe la columna lock_expires_at en la tabla users.
     */
    private static function hasLockExpiry(): bool
    {
        if (self::$tieneExpiracionBloqueo === null) {
            try {
                $stmt = Database::connection()
                    ->query("SHOW COLUMNS FROM users LIKE 'lock_expires_at'");
                self::$tieneExpiracionBloqueo = (bool) $stmt->fetch();
            } catch (\Throwable) {
                self::$tieneExpiracionBloqueo = false;
            }
        }
        return self::$tieneExpiracionBloqueo;
    }

    /**
     * Calcula cuántos segundos dura el bloqueo temporal según el número
     * de intentos fallidos acumulados. La espera crece con cada bloqueo
     * para frenar ataques de fuerza bruta sin castigar de por vida a un
     * usuario legítimo que se equivocó:
     *   1er bloqueo (3 intentos)  => 1 minuto
     *   2do bloqueo (6 intentos)  => 5 minutos
     *   3ro en adelante           => 30 minutos
     */
    public static function lockDuration(int $attempts): int
    {
        return match (intdiv($attempts, 3)) {
            1       => 60,
            2       => 300,
            default => 1800,
        };
    }

    /**
     * Incrementa el contador de intentos fallidos de autenticación y
     * devuelve el total acumulado. Cada 3 intentos fallidos la cuenta
     * se bloquea:
     *
     *  - Bloqueo temporal (predeterminado): la cuenta se libera sola al
     *    pasar la espera calculada por lockDuration(). Se usa en la etapa
     *    de contraseña, donde el atacante aún no ha probado nada.
     *
     *  - Bloqueo permanente ($permanente = true): solo lo quita un
     *    administrador. Se usa cuando falla el PIN, porque en ese punto
     *    la contraseña ya fue verificada y el riesgo es mayor.
     */
    public function incrementFailedAttempts(int $id, bool $permanente = false): int
    {
        $pdo = Database::connection();

        // Suma un intento fallido al contador
        $pdo->prepare('UPDATE users SET failed_attempts = failed_attempts + 1 WHERE id = :id')
            ->execute(['id' => $id]);

        // Lee el total acumulado para decidir si toca bloquear
        $stmt = $pdo->prepare('SELECT failed_attempts FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $attempts = (int) $stmt->fetchColumn();

        // Cada múltiplo de 3 intentos dispara un nuevo bloqueo
        if ($attempts >= 3 && $attempts % 3 === 0) {
            if ($permanente || !self::hasLockExpiry()) {
                // Bloqueo sin fecha de expiración: requiere un administrador
                $sql = self::hasLockExpiry()
                    ? 'UPDATE users SET locked_at = NOW(), lock_expires_at = NULL WHERE id = :id'
                    : 'UPDATE users SET locked_at = NOW() WHERE id = :id';
                $pdo->prepare($sql)->execute(['id' => $id]);
            } else {
                // Bloqueo temporal con espera creciente
                $pdo->prepare(
                    'UPDATE users SET locked_at = NOW(),
                            lock_expires_at = DATE_ADD(NOW(), INTERVAL :seg SECOND)
                     WHERE id = :id'
                )->execute(['seg' => self::lockDuration($attempts), 'id' => $id]);
            }
        }

        return $attempts;
    }

    /**
     * Restablece el contador de intentos fallidos y desbloquea la cuenta.
     * Se llama al autenticarse correctamente para limpiar el historial.
     */
    public function resetFailedAttempts(int $id): void
    {
        $campos = 'failed_attempts = 0, locked_at = NULL';
        if (self::hasLockExpiry()) {
            $campos .= ', lock_expires_at = NULL';
        }
        Database::connection()
            ->prepare("UPDATE users SET {$campos} WHERE id = :id")
            ->execute(['id' => $id]);
    }

    /**
     * Desbloquea manualmente una cuenta bloqueada.
     * Solo puede ejecutarlo un administrador desde el panel de control.
     */
    public function unlock(int $id): void
    {
        // Misma limpieza que al autenticarse correctamente
        $this->resetFailedAttempts($id);
    }
}
