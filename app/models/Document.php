<?php

/*
 * Archivo: app/models/Document.php
 * Autor:   C3r0d4y
 *
 * Modelo de documento. El archivo PDF se guarda cifrado (AES-256-GCM),
 * pero los metadatos de búsqueda viven en claro para que los filtros
 * se resuelvan directamente en SQL sin descifrar nada.
 *
 * Campos cifrados en BD (prefijo "ENC:"):
 *   original_file_name
 *
 * Campos en claro (necesarios para búsquedas y filtros SQL):
 *   number, subject, sender, type, document_date, file_name,
 *   file_size, created_by
 *
 * El PDF solo se descifra al visualizarlo completo o al editarlo;
 * durante las búsquedas no se realiza ninguna operación de descifrado.
 */

declare(strict_types=1);

final class Document
{
    // Lee los tipos activos desde la tabla de catálogo en vez del array estático
    public function types(): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT name FROM document_types WHERE active = 1 ORDER BY name ASC'
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(), 'name');
    }

    // Devuelve los remitentes autorizados activos desde el catálogo
    public function senders(): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT name FROM document_senders WHERE active = 1 ORDER BY name ASC'
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(), 'name');
    }

    /*
     * Busca documentos aplicando filtros. Todos los campos de búsqueda
     * (number, subject, type, date) están en claro, así que los filtros
     * se resuelven completos en SQL sin descifrar nada: la búsqueda es
     * mucho más rápida porque la base de datos hace todo el trabajo.
     */
    public function search(array $filters): array
    {
        $sql    = 'SELECT d.*, u.name AS created_by_name
                   FROM documents d
                   LEFT JOIN users u ON u.id = d.created_by
                   WHERE 1=1';
        $params = [];

        // Coincidencia parcial sobre el folio (en claro)
        if (($filters['number'] ?? '') !== '') {
            $sql .= ' AND d.number LIKE :number';
            $params['number'] = '%' . $filters['number'] . '%';
        }
        // Coincidencia parcial sobre el asunto (en claro)
        if (($filters['subject'] ?? '') !== '') {
            $sql .= ' AND d.subject LIKE :subject';
            $params['subject'] = '%' . $filters['subject'] . '%';
        }
        if (($filters['date'] ?? '') !== '') {
            $sql .= ' AND d.document_date = :date';
            $params['date'] = $filters['date'];
        }
        if (($filters['type'] ?? '') !== '') {
            $sql .= ' AND d.type = :type';
            $params['type'] = $filters['type'];
        }

        $sql .= ' ORDER BY d.document_date DESC, d.id DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        // No se descifra nada en el listado: los metadatos ya están en claro
        return $stmt->fetchAll();
    }

    // Busca un documento por ID y descifra sus campos
    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT d.*, u.name AS created_by_name
             FROM documents d
             LEFT JOIN users u ON u.id = d.created_by
             WHERE d.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->decrypt($row) : null;
    }

    // Crea un nuevo documento cifrando los campos sensibles antes de guardar
    public function create(array $data): int
    {
        $data = $this->encrypt($data);

        $stmt = Database::connection()->prepare(
            'INSERT INTO documents
                (number, subject, document_date, sender, type,
                 file_name, original_file_name, mime_type, file_size,
                 created_by, created_at, updated_at)
             VALUES
                (:number, :subject, :document_date, :sender, :type,
                 :file_name, :original_file_name, :mime_type, :file_size,
                 :created_by, NOW(), NOW())'
        );
        $stmt->execute($data);

        return (int) Database::connection()->lastInsertId();
    }

    // Actualiza un documento cifrando los campos sensibles antes de guardar
    public function update(int $id, array $data): void
    {
        $data = $this->encrypt($data);

        $sets = [
            'number = :number',
            'subject = :subject',
            'document_date = :document_date',
            'sender = :sender',
            'type = :type',
            'updated_at = NOW()',
        ];

        if (isset($data['file_name'])) {
            $sets[] = 'file_name = :file_name';
            $sets[] = 'original_file_name = :original_file_name';
            $sets[] = 'mime_type = :mime_type';
            $sets[] = 'file_size = :file_size';
        }

        $data['id'] = $id;

        Database::connection()
            ->prepare('UPDATE documents SET ' . implode(', ', $sets) . ' WHERE id = :id')
            ->execute($data);
    }

    // Elimina el registro de la BD (el archivo físico se borra desde el controlador)
    public function delete(int $id): void
    {
        Database::connection()
            ->prepare('DELETE FROM documents WHERE id = :id')
            ->execute(['id' => $id]);
    }

    /*
     * Cifra únicamente el nombre original del archivo.
     * Los metadatos de búsqueda (number, subject, sender) se guardan
     * en claro para que las búsquedas SQL sean rápidas.
     */
    private function encrypt(array $data): array
    {
        if (isset($data['original_file_name'])) {
            $data['original_file_name'] = Crypto::encryptField($data['original_file_name']);
        }
        return $data;
    }

    /*
     * Descifra únicamente el nombre original del archivo.
     * Se usa solo al consultar un documento individual (ver o editar);
     * el listado y las búsquedas nunca pasan por aquí.
     */
    private function decrypt(array $row): array
    {
        $row['original_file_name'] = Crypto::decryptField((string) ($row['original_file_name'] ?? ''));
        return $row;
    }
}
