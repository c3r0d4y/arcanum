# ARCANUM — Versión DEMO

**Autor: C3r0d4y**

Sistema de gestión de expedientes clasificados. Los documentos se almacenan
**cifrados con AES-256-GCM** en disco, y los campos sensibles de la base de
datos también viajan cifrados. El acceso exige contraseña más un **PIN de
segundo factor**, con bloqueo automático de la cuenta tras varios intentos
fallidos y registro de auditoría de toda actividad.

> ### 🔗 Versión demo publicada
> **https://arcanum.ciberdefensa.com.mx/login**
>
> Esta es la instancia de demostración, en **modo solo lectura**. Puedes
> navegar por toda la aplicación —expedientes, usuarios, catálogos,
> bitácora— pero ninguna operación de escritura se ejecuta.

---

## ⚠️ Este repositorio es la versión DEMO

Este código tiene `DEMO_MODE` activo en `config/config.php`. **No es la
versión operativa** y no debe usarse para material clasificado real.

### Cómo funciona el modo demo

El bloqueo se aplica en el **front controller** (`public/index.php:71`), antes
de que la petición llegue a cualquier controlador: se rechaza **todo `POST`**
salvo las tres rutas necesarias para iniciar sesión (`login`, `login/pin`,
`pin/verify`).

Es una barrera de servidor, no un aviso de navegador: aunque alguien
manipule el HTML o envíe la petición directamente con `curl`, la operación
nunca se ejecuta. Las peticiones AJAX reciben `403` con JSON; las normales,
un aviso y redirección.

Para convertir esta instancia en operativa hay que poner `DEMO_MODE` en
`false` — y entonces aplican todas las advertencias de despliegue de más
abajo.

## Funciones

| Función | Descripción |
|---|---|
| Expedientes | Alta, consulta, edición y baja de documentos con archivo PDF cifrado |
| Cifrado en reposo | Los PDF se guardan cifrados; los metadatos sensibles, cifrados por campo |
| Autenticación en dos pasos | Contraseña (bcrypt, coste 12) + PIN de acceso |
| Bloqueo de cuenta | Tras intentos fallidos consecutivos; solo un administrador desbloquea |
| Roles | `admin` (control total) y `operador` (solo expedientes) |
| Bitácora | Registro de auditoría de accesos y operaciones |
| Catálogos | Tipos de documento y remitentes configurables |
| Perfil | Cambio de contraseña, PIN y fotografía |

## Arquitectura (MVC)

```
arcanum_demo/
├── config/
│   ├── config.php               → constantes globales, carga de secretos, DEMO_MODE
│   ├── database.php             → conexión PDO
│   ├── secrets.example.php      → plantilla de secretos (copiar a secrets.local.php)
│   └── .htaccess                → acceso web denegado
├── public/
│   ├── index.php                → front controller: cabeceras, guardián demo, rutas
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
| `/ping` | GET | Mantiene viva la sesión |

En modo demo, todas las rutas `POST` de esta tabla quedan bloqueadas salvo
las de inicio de sesión.

## Criptografía

- **Algoritmo:** AES-256-GCM (`openssl`), IV de 96 bits único por operación y
  tag de autenticación de 128 bits.
- **Archivos:** `[IV 12][TAG 16][CIFRADO]` guardado como `.enc`.
- **Campos de base de datos:** `ENC:<base64(IV + TAG + CIFRADO)>`.
- **Contraseñas:** bcrypt con coste 12 (`password_hash`).
- **Clave maestra:** 256 bits en hexadecimal, fuera del directorio web.

## Seguridad

- **Sesión:** expira a los 300 segundos de inactividad (`SESSION_TIMEOUT`).
- **CSRF:** token verificado en todas las operaciones de escritura.
- **Cabeceras:** CSP, `X-Frame-Options: DENY`, `X-Content-Type-Options:
  nosniff`, `Referrer-Policy: no-referrer`, `Permissions-Policy` restrictiva.
- **Base de datos:** el usuario de MySQL solo tiene `SELECT`, `INSERT`,
  `UPDATE` y `DELETE` — sin permisos DDL, para limitar el daño ante un
  compromiso.
- **Directorios protegidos:** `config/` y `storage/` con `Require all denied`.

## Requisitos

- PHP 8.0 o superior con las extensiones `openssl`, `pdo_mysql` y `mbstring`.
- MySQL 5.7+ o MariaDB 10.3+.
- Apache con `mod_rewrite` habilitado.

## Instalación

```bash
git clone <url-del-repositorio> arcanum_demo
cd arcanum_demo

# 1. Base de datos
mysql -u root -p < database/schema.sql

# 2. Secretos
cp config/secrets.example.php config/secrets.local.php
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"   # clave maestra
# Edita config/secrets.local.php con esa clave y tus credenciales de MySQL

# 3. Permisos de escritura
mkdir -p storage/pdfs storage/avatars
chown -R www-data:www-data storage
chmod 750 storage/pdfs storage/avatars
```

Apunta el `DocumentRoot` a la carpeta del proyecto con `AllowOverride All`, y
ajusta `BASE_URL` en `config/config.php` a la ruta donde lo sirvas (`""` si es
la raíz del dominio).

### Credenciales de la demo

El `database/schema.sql` crea dos usuarios de prueba, documentados dentro del
propio archivo. **Son credenciales públicas de demostración**: en una
instalación operativa hay que cambiarlas antes de exponer el sistema.

## Despliegue en producción

Si vas a desactivar `DEMO_MODE`, esto es obligatorio:

1. **Mueve los secretos fuera del directorio web:**
   ```bash
   sudo mv config/secrets.local.php /var/www/arcanum-secrets.php
   sudo chown www-data:www-data /var/www/arcanum-secrets.php
   sudo chmod 600 /var/www/arcanum-secrets.php
   ```
   `config/config.php` los busca primero ahí.
2. **Genera una clave maestra nueva** — nunca reutilices la de la demo.
3. **Cambia las contraseñas y los PIN** de los usuarios semilla.
4. **Sirve solo por HTTPS** y activa HSTS.
5. **Respalda la clave maestra** en un lugar seguro y separado: si se pierde,
   los expedientes cifrados son irrecuperables.

## Limitaciones que el usuario debe conocer

- **Clave maestra perdida = expedientes irrecuperables.** No hay mecanismo de
  recuperación por diseño. Rotar la clave obliga a re-cifrar todo el archivo.
- **El cifrado protege el disco, no la sesión.** Un usuario con credenciales
  válidas ve los expedientes en claro; la protección real ante el robo del
  servidor o de un respaldo es el cifrado en reposo.
- **La demo es solo lectura, pero es pública.** No cargues en ella ningún
  material que no puedas mostrar abiertamente.
- **Sin auditoría externa formal.** El proyecto se entrega tal cual. Si
  detectas una vulnerabilidad, repórtala de forma privada al autor antes de
  divulgarla.

---

**ARCANUM — desarrollado por C3r0d4y.**
