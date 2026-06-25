-- =====================================================================
-- Archivo: database/schema.sql
-- Autor:   C3r0d4y
--
-- ¿Qué hace este archivo?
-- Define la estructura completa de la base de datos del sistema
-- de archivo documental. Contiene:
--   1. Creación de la base de datos
--   2. Definición de las 3 tablas principales
--   3. Usuarios iniciales de prueba/acceso
--   4. Usuario de base de datos de la aplicación y sus permisos
--
-- Para ejecutarlo:
--   mysql -u root -p < database/schema.sql
-- =====================================================================

-- ---------------------------------------------------------------------
-- Creación de la base de datos
-- IF NOT EXISTS evita error si ya existe; útil al ejecutar el script
-- varias veces en distintos entornos.
-- utf8mb4 soporta español, caracteres especiales y emojis.
-- utf8mb4_unicode_ci es la colación (reglas de comparación/orden).
-- ---------------------------------------------------------------------
-- ARCANUM — Sistema de Documentación Clasificada
-- Campos sensibles (number, subject, sender, original_file_name) se almacenan
-- cifrados con AES-256-GCM. Formato: ENC:<base64(IV+TAG+CT)>

CREATE DATABASE IF NOT EXISTS archivo_documental
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Selecciona la base de datos para que las instrucciones siguientes
-- se ejecuten dentro de ella.
USE archivo_documental;

-- =====================================================================
-- TABLA: users
-- Guarda los usuarios que pueden acceder al sistema.
-- Hay dos tipos de rol: 'admin' (acceso total) y 'operador' (solo documentos).
-- =====================================================================
CREATE TABLE IF NOT EXISTS users (
    -- Identificador único del usuario, se incrementa automáticamente
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Nombre completo del usuario (máximo 120 caracteres)
    name          VARCHAR(120) NOT NULL,

    -- Correo electrónico del usuario; es único (no puede repetirse)
    -- y se usa como nombre de usuario para iniciar sesión
    email         VARCHAR(160) NOT NULL UNIQUE,

    -- Contraseña cifrada con bcrypt (nunca se guarda en texto plano)
    password_hash VARCHAR(255) NOT NULL,

    -- Rol del usuario: 'admin' puede gestionar usuarios y ver logs;
    -- 'operador' solo puede gestionar documentos
    role          ENUM('admin', 'operador') NOT NULL DEFAULT 'operador',

    -- Indica si el usuario puede iniciar sesión (1 = activo, 0 = bloqueado)
    active        TINYINT(1) NOT NULL DEFAULT 1,

    -- Fecha y hora de creación del registro, se rellena automáticamente
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: documents
-- Guarda los documentos oficiales del archivo: oficios, memorandos
-- y cartas. Cada documento tiene sus metadatos y la referencia al
-- archivo PDF almacenado en el servidor.
-- =====================================================================
CREATE TABLE IF NOT EXISTS documents (
    -- Identificador único del documento
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Folio cifrado AES-256-GCM (MEDIUMTEXT para datos cifrados en base64)
    number             MEDIUMTEXT NOT NULL,

    -- Asunto cifrado AES-256-GCM
    subject            MEDIUMTEXT NOT NULL,

    -- Fecha oficial del documento (no la de registro en el sistema)
    document_date      DATE         NOT NULL,

    -- Remitente cifrado AES-256-GCM
    sender             MEDIUMTEXT NOT NULL,

    -- Tipo de documento: solo se aceptan estas tres categorías
    type               ENUM('oficio', 'memorandum', 'carta') NOT NULL,

    -- Nombre del archivo con el que se guarda el PDF en el servidor
    -- (nombre aleatorio generado para mayor seguridad)
    file_name          VARCHAR(120) NOT NULL,

    -- Nombre original del archivo (cifrado AES-256-GCM)
    original_file_name MEDIUMTEXT NOT NULL,

    -- Tipo MIME del archivo (siempre 'application/pdf')
    mime_type          VARCHAR(80)  NOT NULL,

    -- Tamaño del archivo en bytes
    file_size          INT UNSIGNED NOT NULL,

    -- ID del usuario que registró el documento.
    -- NULL si el usuario fue eliminado posteriormente (ON DELETE SET NULL)
    created_by         INT UNSIGNED NULL,

    -- Fecha y hora de registro del documento en el sistema
    created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Fecha y hora de la última modificación (se actualiza automáticamente)
    updated_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Relación con la tabla users: si el usuario se elimina,
    -- created_by se pone en NULL (no se elimina el documento)
    CONSTRAINT fk_documents_created_by
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,

    -- Índices para acelerar las búsquedas por estos campos frecuentes
    INDEX idx_documents_number  (number),
    INDEX idx_documents_subject (subject),
    INDEX idx_documents_date    (document_date),
    INDEX idx_documents_type    (type)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: system_logs
-- Bitácora de todas las acciones importantes del sistema.
-- Cada vez que alguien inicia sesión, crea, edita o elimina
-- un documento o usuario, se guarda un registro aquí.
-- Solo los administradores pueden consultar esta tabla.
-- =====================================================================
CREATE TABLE IF NOT EXISTS system_logs (
    -- Identificador único del registro de log (BIGINT porque puede haber muchos)
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- ID del usuario que realizó la acción.
    -- NULL si fue una acción anónima (p.ej. intento de login fallido)
    user_id     INT UNSIGNED NULL,

    -- Nombre corto de la acción realizada (p.ej. 'login', 'document_created')
    action      VARCHAR(80)  NOT NULL,

    -- Descripción detallada de lo que ocurrió
    description VARCHAR(500) NOT NULL,

    -- Dirección IP del usuario que realizó la acción
    ip_address  VARCHAR(45)  NULL,

    -- Información del navegador o cliente HTTP usado
    user_agent  VARCHAR(255) NULL,

    -- Fecha y hora exacta del evento
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Relación con usuarios: si el usuario se elimina, user_id queda en NULL
    CONSTRAINT fk_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,

    -- Índices para filtrar logs por fecha y por tipo de acción eficientemente
    INDEX idx_logs_created_at (created_at),
    INDEX idx_logs_action     (action)
) ENGINE=InnoDB;

-- =====================================================================
-- DATOS INICIALES
-- Inserta los usuarios de acceso por defecto del sistema.
-- Las contraseñas están cifradas con bcrypt (password_hash de PHP).
--   admin@c3r0d4y.com    => Admin123!
--   operador@c3r0d4y.com => Operador123!
--
-- ON DUPLICATE KEY UPDATE evita error si ya existen estos registros
-- al volver a ejecutar el script.
-- =====================================================================
INSERT INTO users (name, email, password_hash, role, active)
VALUES
    -- Usuario administrador con acceso total al sistema
    ('Administrador', 'admin@c3r0d4y.com',    '$2y$12$7hsplKp4PkoWUrvraA7XLesT3sN0tWcp9qqa5HU0qu4Fval2ntBiu', 'admin',    1),
    -- Usuario operador con acceso solo a documentos
    ('Operador',      'operador@c3r0d4y.com', '$2y$12$HM/AWI4UK1RlPAKL9DXgNucXZB/gYB6LtZNPT046xn1DEn9HlyniG', 'operador', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- =====================================================================
-- USUARIO DE BASE DE DATOS PARA LA APLICACIÓN
-- Se crea un usuario de MySQL con permisos mínimos necesarios
-- (solo SELECT, INSERT, UPDATE y DELETE, sin DDL).
-- Esto limita el daño en caso de que la aplicación sea comprometida.
-- =====================================================================

-- Crea el usuario 'archivo_app' si no existe todavía
CREATE USER IF NOT EXISTS 'archivo_app'@'localhost'
    IDENTIFIED BY '2d069c72b3f4958de6542120ce82b25b';

-- Le da permisos solo para las 4 operaciones básicas de datos
-- sobre todas las tablas de esta base de datos
GRANT SELECT, INSERT, UPDATE, DELETE
    ON archivo_documental.*
    TO 'archivo_app'@'localhost';

-- Aplica los cambios de permisos de forma inmediata
FLUSH PRIVILEGES;
