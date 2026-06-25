<?php

/*
 * Archivo: app/models/Document.php
 * Autor:   C3r0d4y
 *
 * Modelo de documento con cifrado AES-256-GCM en campos sensibles.
 *
 * Campos cifrados en BD (prefijo "ENC:"):
 *   number, subject, sender, original_file_name
 *
 * Campos en claro (necesarios para índices y filtros SQL):
 *   type, document_date, file_name, file_size, created_by
 *
 * La búsqueda por campos cifrados se hace en PHP tras descifrar,
 * garantizando que la BD nunca exponga metadatos en claro.
 */

declare(strict_types=1);

final class Document
{
    private array $types = ['oficio', 'memorandum', 'carta'];

    public function types(): array
    {
        return $this->types;
    }

    /*
     * Busca documentos aplicando filtros. Los campos cifrados (number, subject)
     * se filtran en PHP después de descifrar. Los campos en claro (type, date)
     * se filtran en SQL para mayor eficiencia.
     */
    public function search(array $filters): array
    {
        $sql    = 'SELECT d.*, u.name AS created_by_name
                   FROM documents d
                   LEFT JOIN users u ON u.id = d.created_by
                   WHERE 1=1';
        $params = [];

        // Filtros SQL sobre campos no cifrados
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
        $rows = $stmt->fetchAll();

        // Descifra y filtra en PHP los campos sensibles
        $results = [];
        foreach ($rows as $row) {
            $row = $this->decrypt($row);

            if (($filters['number'] ?? '') !== '' &&
                stripos($row['number'], $filters['number']) === false) {
                continue;
            }
            if (($filters['subject'] ?? '') !== '' &&
                stripos($row['subject'], $filters['subject']) === false) {
                continue;
            }

            $results[] = $row;
        }

        return $results;
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

    // Cifra todos los campos sensibles de un documento
    private function encrypt(array $data): array
    {
        $data['number']             = Crypto::encryptField($data['number']);
        $data['subject']            = Crypto::encryptField($data['subject']);
        $data['sender']             = Crypto::encryptField($data['sender']);
        $data['original_file_name'] = Crypto::encryptField($data['original_file_name']);
        return $data;
    }

    // Descifra todos los campos sensibles de un documento leído de BD
    private function decrypt(array $row): array
    {
        $row['number']             = Crypto::decryptField((string) ($row['number'] ?? ''));
        $row['subject']            = Crypto::decryptField((string) ($row['subject'] ?? ''));
        $row['sender']             = Crypto::decryptField((string) ($row['sender'] ?? ''));
        $row['original_file_name'] = Crypto::decryptField((string) ($row['original_file_name'] ?? ''));
        return $row;
    }
}
