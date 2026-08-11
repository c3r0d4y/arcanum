-- =====================================================================
-- Archivo: database/migrate_lockout.sql
-- Autor:   C3r0d4y
--
-- ¿Qué hace este archivo?
-- Agrega la columna 'lock_expires_at' a la tabla de usuarios para
-- soportar bloqueos TEMPORALES de cuenta con espera creciente
-- (1 min, 5 min, 30 min) en lugar de bloqueos permanentes.
--
--   - lock_expires_at con fecha  => bloqueo temporal: la cuenta se
--     libera sola cuando pasa esa fecha.
--   - lock_expires_at en NULL con locked_at con fecha => bloqueo
--     permanente: solo un administrador puede desbloquear.
--
-- Para ejecutarlo (requiere usuario root de MySQL):
--   sudo mysql arcanum < database/migrate_lockout.sql
--
-- Nota: si se ejecuta dos veces, la segunda dará el error
-- "Duplicate column name", que es inofensivo (la columna ya existe).
-- =====================================================================

ALTER TABLE arcanum.users
    ADD COLUMN lock_expires_at DATETIME NULL
    COMMENT 'Fecha en que expira el bloqueo temporal; NULL = sin expiración'
    AFTER locked_at;
