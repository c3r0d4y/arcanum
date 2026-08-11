<?php

/*
 * Archivo: app/models/Catalog.php
 * Autor:   C3r0d4y
 *
 * Modelo genérico para gestionar los catálogos editables del sistema:
 * tipos de documento y remitentes autorizados.
 *
 * Solo se permiten las tablas declaradas en TABLES para evitar
 * que se manipulen otras tablas por error o inyección.
 */

declare(strict_types=1);

final class Catalog
{
    // Tablas permitidas — única fuente de verdad para la whitelist
    private const TABLES = ['document_types', 'document_senders'];

    // Lanza excepción si el nombre de tabla no está en la lista permitida
    private function validarTabla(string $tabla): void
    {
        if (!in_array($tabla, self::TABLES, true)) {
            throw new \InvalidArgumentException("Catálogo no permitido: {$tabla}");
        }
    }

    // Devuelve todos los registros activos para poblar selectores en formularios
    public function activos(string $tabla): array
    {
        $this->validarTabla($tabla);
        $stmt = Database::connection()->prepare(
            "SELECT id, name FROM {$tabla} WHERE active = 1 ORDER BY name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Devuelve todos los registros (activos e inactivos) para la vista de administración
    public function todos(string $tabla): array
    {
        $this->validarTabla($tabla);
        $stmt = Database::connection()->prepare(
            "SELECT id, name, active, created_at FROM {$tabla} ORDER BY name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Busca un registro por ID; devuelve null si no existe
    public function buscar(string $tabla, int $id): ?array
    {
        $this->validarTabla($tabla);
        $stmt = Database::connection()->prepare(
            "SELECT id, name, active FROM {$tabla} WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    // Agrega un nuevo elemento al catálogo
    public function crear(string $tabla, string $nombre): int
    {
        $this->validarTabla($tabla);
        $stmt = Database::connection()->prepare(
            "INSERT INTO {$tabla} (name, active, created_at) VALUES (:name, 1, NOW())"
        );
        $stmt->execute(['name' => $nombre]);
        return (int) Database::connection()->lastInsertId();
    }

    // Actualiza el nombre de un elemento del catálogo
    public function actualizar(string $tabla, int $id, string $nombre): void
    {
        $this->validarTabla($tabla);
        Database::connection()
            ->prepare("UPDATE {$tabla} SET name = :name WHERE id = :id")
            ->execute(['name' => $nombre, 'id' => $id]);
    }

    // Elimina un elemento del catálogo de forma permanente
    public function eliminar(string $tabla, int $id): void
    {
        $this->validarTabla($tabla);
        Database::connection()
            ->prepare("DELETE FROM {$tabla} WHERE id = :id")
            ->execute(['id' => $id]);
    }

    // Verifica si ya existe un nombre igual en el catálogo (para evitar duplicados)
    public function existeNombre(string $tabla, string $nombre, ?int $excludeId = null): bool
    {
        $this->validarTabla($tabla);
        $sql    = "SELECT COUNT(*) FROM {$tabla} WHERE name = :name";
        $params = ['name' => $nombre];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
