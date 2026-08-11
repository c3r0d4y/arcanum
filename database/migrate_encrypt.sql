-- =====================================================================
-- Migración: ARCANUM — Campos cifrados AES-256-GCM
-- Autor: C3r0d4y
--
-- Amplía los campos sensibles a TEXT para almacenar valores cifrados
-- en base64 (formato: ENC:<base64(IV+TAG+CT)>).
--
-- Ejecutar UNA SOLA VEZ sobre la BD existente:
--   mysql -u root -p arcanum < database/migrate_encrypt.sql
-- =====================================================================

USE arcanum;

-- Los índices BTREE no funcionan en columnas TEXT sin prefijo;
-- se eliminan ya que la búsqueda ahora se hace en PHP tras descifrar.
ALTER TABLE documents
    DROP INDEX IF EXISTS idx_documents_number,
    DROP INDEX IF EXISTS idx_documents_subject;

-- Amplía los campos sensibles para almacenar datos cifrados
ALTER TABLE documents
    MODIFY COLUMN number             MEDIUMTEXT NOT NULL,
    MODIFY COLUMN subject            MEDIUMTEXT NOT NULL,
    MODIFY COLUMN sender             MEDIUMTEXT NOT NULL,
    MODIFY COLUMN original_file_name MEDIUMTEXT NOT NULL;

-- Renombra la base de datos a arcanum_documental (opcional)
-- ALTER DATABASE arcanum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
