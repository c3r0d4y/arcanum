-- ============================================================
-- Archivo: database/rename_to_arcanum.sql
-- Autor:   C3r0d4y
--
-- ¿Qué hace este script?
-- Renombra la base de datos de la aplicación: mueve todas las
-- tablas de `archivo_documental` a la nueva base `arcanum`,
-- otorga los mismos permisos al usuario de la aplicación y
-- elimina la base antigua (que queda vacía).
--
-- Cómo ejecutarlo (requiere el usuario root de MySQL):
--   sudo mysql < /var/www/html/arcanum/database/rename_to_arcanum.sql
-- ============================================================

-- Se crea la nueva base con la misma codificación que la original
CREATE DATABASE IF NOT EXISTS arcanum
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Se mueven las tablas a la nueva base (RENAME TABLE conserva
-- datos, índices y llaves foráneas; es una operación instantánea)
RENAME TABLE
    archivo_documental.users            TO arcanum.users,
    archivo_documental.document_types   TO arcanum.document_types,
    archivo_documental.document_senders TO arcanum.document_senders,
    archivo_documental.documents        TO arcanum.documents,
    archivo_documental.system_logs      TO arcanum.system_logs;

-- El usuario de la aplicación recibe sobre `arcanum` los mismos
-- permisos que tenía sobre la base anterior (solo datos, sin DDL total)
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER, TRIGGER
    ON arcanum.* TO 'archivo_app'@'localhost';
FLUSH PRIVILEGES;

-- Se elimina la base antigua, que ya quedó sin tablas
DROP DATABASE archivo_documental;
