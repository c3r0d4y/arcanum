# ARCANUM

**Autor: C3r0d4y**

Sistema de gestión de expedientes clasificados. Los documentos se almacenan
**cifrados con AES-256-GCM** en disco, y los campos sensibles de la base de
datos también viajan cifrados. El acceso exige contraseña más un **PIN de
segundo factor**, con bloqueo automático de la cuenta tras intentos fallidos
y registro de auditoría de toda la actividad.

> ### 🔗 Versión demo publicada
> **https://arcanum.ciberdefensa.com.mx/login**
>
> Ahí puedes ver el sistema funcionando en **modo solo lectura**: se navega
> por toda la aplicación —expedientes, usuarios, catálogos, bitácora— pero
> ninguna operación de escritura se ejecuta. Este repositorio contiene la
> **versión completa y operativa**, con la escritura habilitada.

---

## Funciones

| Función | Descripción |
|---|---|
| Expedientes | Alta, consulta, edición y baja de documentos con archivo PDF cifrado |
| Cifrado en reposo | Los PDF se guardan cifrados; los metadatos sensibles, cifrados por campo |
| Autenticación en dos pasos | Contraseña (bcrypt, coste 12) + PIN de acceso numérico |
| Re-verificación de PIN | Las operaciones críticas sobre expedientes vuelven a pedir el PIN |
| Bloqueo de cuenta | Tras intentos fallidos consecutivos; solo un administrador desbloquea |
| Roles | `admin` (control total) y `operador` (solo expedientes) |
| Bitácora | Registro de auditoría de accesos y operaciones |
| Catálogos | Tipos de documento y remitentes configurables |
| Perfil | Cambio de contraseña, PIN y fotografía |

## Arquitectura (MVC)

```
arcanum/
├── config/
│   ├── config.php               → constantes globales y carga de secretos
│   ├── database.php             → conexión PDO
│   ├── secrets.example.php      → plantilla de secretos (copiar a secrets.local.php)
│   └── .htaccess                → acceso web denegado
├── public/
│   ├── index.php                → front controller: cabeceras de seguridad y rutas
│   ├── presentacion.html        → página de presentación del producto
│   └── assets/                  → css · js · img
├── app/
│   ├── core/                    → Auth · Crypto · Csrf · Database · Logger · Controller
│   ├── controllers/             → Auth · Documents · Users · Logs · Catalogs · Profile
│   ├── models/                  → Document · User · Catalog · SystemLog
│   └── views/                   → auth · documents · users · logs · catalogs · profile
├── database/                    → schema.sql y migraciones
└── storage/                     → pdfs/ (cifrados) · avatars/; acceso web denegado
```

## Rutas

| Ruta | Método | Función |
|---|---|---|
| `/login` | GET · POST | Inicio de sesión con contraseña |
| `/login/pin` | GET · POST | Segundo paso: PIN de acceso |
| `/logout` | GET | Cierre de sesión |
| `/documents` | GET | Listado de expedientes |
| `/documents/{id}` | GET | Ficha del expediente |
| `/documents/{id}/file` | GET | Descarga del PDF (se descifra al vuelo) |
| `/documents/create` · `/documents/store` | GET · POST | Alta de expediente |
| `/documents/{id}/edit` · `/update` · `/delete` | GET · POST | Edición y baja |
| `/users` … | GET · POST | Gestión de usuarios (solo `admin`) |
| `/logs` | GET | Bitácora de auditoría (solo `admin`) |
| `/catalogs` … | GET · POST | Tipos de documento y remitentes |
| `/profile` … | GET · POST | Contraseña, PIN y fotografía |
| `/pin/verify` | POST | Re-verificación de PIN para operaciones críticas |
| `/ping` | GET | Mantiene viva la sesión |

## Criptografía

- **Algoritmo:** AES-256-GCM (`openssl`), IV de 96 bits único por operación y
  tag de autenticación de 128 bits. Dos expedientes idénticos producen
  criptogramas distintos.
- **Archivos:** `[IV 12][TAG 16][CIFRADO]` guardado como `.enc` en `storage/pdfs/`.
- **Campos de base de datos:** `ENC:<base64(IV + TAG + CIFRADO)>` en número,
  asunto, remitente y nombre original del archivo.
- **Contraseñas y PIN:** bcrypt con coste 12 (`password_hash`).
- **Clave maestra:** 256 bits en hexadecimal, cargada desde fuera del
  directorio web.

Los registros anteriores al cifrado (sin el prefijo `ENC:`) se siguen leyendo
en claro, así que el sistema puede operar con contenido mixto durante una
migración.

## Seguridad

- **Sesión:** expira a los 300 segundos de inactividad (`SESSION_TIMEOUT`);
  cookie `HttpOnly` con `SameSite=Strict`.
- **CSRF:** token verificado en todas las operaciones de escritura.
- **Cabeceras:** CSP, `X-Frame-Options: DENY`, `X-Content-Type-Options:
  nosniff`, `Referrer-Policy: no-referrer`, `Permissions-Policy` restrictiva.
- **Base de datos:** el usuario de MySQL solo tiene `SELECT`, `INSERT`,
  `UPDATE` y `DELETE` — sin permisos DDL, para limitar el daño ante un
  compromiso.
- **Directorios protegidos:** `config/` y `storage/` con `Require all denied`.
- **Sin credenciales en pantalla:** el login no muestra usuarios de ejemplo.

## Requisitos

- PHP 8.0 o superior con las extensiones `openssl`, `pdo_mysql` y `mbstring`.
- MySQL 5.7+ o MariaDB 10.3+.
- Apache con `mod_rewrite` habilitado.

## Instalación

```bash
git clone <url-del-repositorio> arcanum
cd arcanum

# 1. Base de datos
mysql -u root -p < database/schema.sql

# 2. Columnas de autenticación añadidas después del esquema inicial
php database/migrate_users_columns.php    # idempotente

# 3. Secretos
cp config/secrets.example.php config/secrets.local.php
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"   # genera la clave maestra
# Edita config/secrets.local.php con esa clave y tus credenciales de MySQL

# 4. Permisos de escritura
mkdir -p storage/pdfs storage/avatars
chown -R www-data:www-data storage
chmod 750 storage/pdfs storage/avatars
```

Apunta el `DocumentRoot` a la carpeta del proyecto con `AllowOverride All`, y
ajusta `BASE_URL` en `config/config.php` a la ruta donde lo sirvas (`""` si es
la raíz del dominio).

> **Nota sobre el esquema:** `database/schema.sql` no incluye las columnas de
> autenticación agregadas después (`failed_attempts`, `locked_at`,
> `lock_expires_at`, `avatar`, `pin_hash`, `must_change_password`). Una base
> creada solo con ese script da error 500 al verificar el PIN. El paso 2 de
> la instalación lo resuelve.

## Puesta en producción

Este repositorio contiene la versión operativa. Antes de exponerla:

1. **Mueve los secretos fuera del directorio web:**
   ```bash
   sudo mv config/secrets.local.php /var/www/arcanum-secrets.php
   sudo chown www-data:www-data /var/www/arcanum-secrets.php
   sudo chmod 600 /var/www/arcanum-secrets.php
   ```
   `config/config.php` los busca primero ahí y solo usa la copia local como
   respaldo.
2. **Genera una clave maestra nueva y exclusiva** de esa instalación.
3. **Cambia las contraseñas y los PIN** de los usuarios semilla que crea
   `schema.sql`. El PIN predeterminado obliga a cambiarse en el primer acceso.
4. **Sirve solo por HTTPS** y activa HSTS.
5. **Respalda la clave maestra** en un lugar seguro y separado del servidor.
6. **Respalda la base de datos y `storage/pdfs/` juntos**: sin ambos, los
   expedientes no se recuperan.

## Limitaciones que el usuario debe conocer

- **Clave maestra perdida = expedientes irrecuperables.** No hay mecanismo de
  recuperación por diseño. Rotar la clave obliga a re-cifrar todo el archivo.
- **El cifrado protege el disco, no la sesión.** Un usuario con credenciales
  válidas ve los expedientes en claro; la protección real es ante el robo del
  servidor, del disco o de un respaldo.
- **Las búsquedas sobre campos cifrados se filtran en PHP** después de
  descifrar, no en SQL. Con volúmenes grandes conviene revisar el rendimiento.
- **Sin auditoría externa formal.** El proyecto se entrega tal cual. Si
  detectas una vulnerabilidad, repórtala de forma privada al autor antes de
  divulgarla.

---

**ARCANUM — desarrollado por C3r0d4y.**
