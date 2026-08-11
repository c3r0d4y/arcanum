-- ══════════════════════════════════════════════════════════════════
-- Archivo: database/fix_users_columns.sql
-- Autor:   C3r0d4y
--
-- ¿Qué hace este archivo?
-- Repara la tabla `users` en servidores donde la base de datos no
-- tiene las columnas que el login en dos pasos (contraseña + PIN) y
-- el bloqueo por intentos fallidos necesitan; sin ellas la página
-- /login/pin responde con error 500.
--
-- ¿Cómo se usa?
-- En el panel del hosting (phpMyAdmin), selecciona la base de datos de
-- ARCANUM, abre la pestaña SQL, pega TODO este contenido y ejecútalo.
--
-- Cada instrucción usa "IF NOT EXISTS": si la columna ya está en la
-- tabla, simplemente se omite sin marcar error (MariaDB 10.0.2+, que
-- es lo que usan la mayoría de los hostings compartidos).
--
-- Si tu servidor fuera MySQL puro y marcara error de sintaxis (#1064),
-- primero revisa qué columnas ya existen con:
--
--     SHOW COLUMNS FROM users;
--
-- y ejecuta una por una SOLO las instrucciones de las columnas que
-- falten, quitando las palabras "IF NOT EXISTS".
-- ══════════════════════════════════════════════════════════════════

-- Intentos fallidos de login acumulados (contraseña o PIN)
ALTER TABLE users ADD COLUMN IF NOT EXISTS failed_attempts INT UNSIGNED NOT NULL DEFAULT 0
    COMMENT 'Intentos fallidos de login acumulados' AFTER active;

-- Momento en que la cuenta fue bloqueada
ALTER TABLE users ADD COLUMN IF NOT EXISTS locked_at DATETIME NULL
    COMMENT 'Momento en que la cuenta fue bloqueada' AFTER failed_attempts;

-- Fin del bloqueo temporal; NULL = permanente
ALTER TABLE users ADD COLUMN IF NOT EXISTS lock_expires_at DATETIME NULL
    COMMENT 'Fin del bloqueo temporal; NULL = permanente' AFTER locked_at;

-- Nombre de archivo de la foto de perfil
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL
    COMMENT 'Nombre de archivo de la foto de perfil' AFTER lock_expires_at;

-- PIN de seguridad cifrado (segundo factor del login)
ALTER TABLE users ADD COLUMN IF NOT EXISTS pin_hash VARCHAR(255) NULL
    COMMENT 'PIN de seguridad cifrado (segundo factor)' AFTER avatar;

-- 1 = el usuario debe cambiar su contraseña provisional
ALTER TABLE users ADD COLUMN IF NOT EXISTS must_change_password TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '1 = debe cambiar su contraseña provisional' AFTER pin_hash;

-- Al terminar, verifica que las 6 columnas existan:
-- SHOW COLUMNS FROM users;
