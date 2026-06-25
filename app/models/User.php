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
            'SELECT id, name, email, role, active, created_at
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
            'SELECT id, name, email, role, active, created_at
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
            'INSERT INTO users (name, email, password_hash, role, active, created_at)
             VALUES (:name, :email, :password_hash, :role, :active, NOW())'
        );
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
}
