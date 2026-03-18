# 🚀 Guía de Implementación en Hostinger
## Golden Cabo Transportation — Laravel 10

---

## PASO 1 — Preparar el Hosting en Hostinger

1. Ingresa a **hPanel** → "Administrar" en tu hosting
2. Verifica que el hosting tenga **PHP 8.1 o superior**
   - hPanel → PHP Configuration → Selecciona PHP 8.1 o 8.2
3. Habilita las extensiones: `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `bcmath`, `tokenizer`

---

## PASO 2 — Crear la Base de Datos MySQL

1. En hPanel → **MySQL Databases**
2. Crea una base de datos: `goldencabo_db` (o el nombre que prefieras)
3. Crea un usuario con contraseña segura
4. Asigna todos los permisos al usuario sobre la base de datos
5. Anota: `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

### Importar la estructura SQL:
1. hPanel → **phpMyAdmin**
2. Selecciona tu base de datos
3. Pestaña **Import** → sube el archivo `database/goldencabo_structure.sql`
4. Clic en "Go" para importar

---

## PASO 3 — Subir los Archivos del Proyecto

### Opción A — File Manager (recomendado para principiantes):
1. hPanel → **File Manager**
2. Descomprime `goldencabo_laravel.zip` en tu computadora
3. Sube **todo el contenido** (excepto la carpeta `vendor/`) a `/home/tu_usuario/`
4. El contenido de la carpeta `public/` debe ir a `public_html/`

### Opción B — FTP/SFTP con FileZilla:
```
Host: ftp.tudominio.com
Usuario: tu_usuario_ftp
Puerto: 21 (FTP) o 22 (SFTP)
```
Sube los archivos siguiendo la misma estructura del Paso A.

### Estructura de archivos en el servidor:
```
/home/u123456789/          ← archivos Laravel (app/, bootstrap/, config/, etc.)
/home/u123456789/public_html/  ← contenido de la carpeta public/
```

---

## PASO 4 — Instalar Dependencias con Composer

### En Hostinger Terminal (SSH):
1. hPanel → **SSH Access** (actívalo si no está habilitado)
2. Conéctate:
   ```bash
   ssh u123456789@tudominio.com
   ```
3. Navega al directorio del proyecto:
   ```bash
   cd /home/u123456789
   ```
4. Instala las dependencias:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
   > Si Composer no está disponible, descárgalo:
   > `curl -sS https://getcomposer.org/installer | php`
   > `php composer.phar install --optimize-autoloader --no-dev`

---

## PASO 5 — Configurar el Archivo .env

1. En el servidor, copia el archivo de configuración:
   ```bash
   cp .env.example .env
   ```
2. Edita el archivo `.env`:
   ```bash
   nano .env
   ```
3. Configura estos valores:
   ```env
   APP_NAME="Golden Cabo Transportation"
   APP_ENV=production
   APP_KEY=        ← (se genera en el siguiente paso)
   APP_DEBUG=false
   APP_URL=https://www.goldencabotransportation.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tu_base_de_datos
   DB_USERNAME=tu_usuario_db
   DB_PASSWORD=tu_contraseña_db

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=587
   MAIL_USERNAME=reservaciones@tudominio.com
   MAIL_PASSWORD=tu_contraseña_email
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=reservaciones@tudominio.com
   MAIL_FROM_NAME="Golden Cabo Transportation"

   ADMIN_PASSWORD=TuContraseñaAdmin2025!
   ADMIN_EMAIL=goldencabotransportation@gmail.com
   ```
4. Guarda: `Ctrl+X` → `Y` → `Enter`

---

## PASO 6 — Comandos de Artisan

Ejecuta estos comandos en SSH:

```bash
# Generar la clave de aplicación (¡obligatorio!)
php artisan key:generate

# Ejecutar migraciones (si no importaste el SQL)
php artisan migrate --force

# Limpiar y optimizar cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace de almacenamiento
php artisan storage:link

# Limpiar cachés si hay problemas
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## PASO 7 — Configurar el Document Root

1. hPanel → **Website** → "Manage"
2. **Domain** → asegúrate que el Document Root apunte a `public_html`
3. Si usas subdominio, configúralo también

---

## PASO 8 — Verificar Permisos de Archivos

En SSH:
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
```

---

## PASO 9 — Configurar Email en Hostinger

1. hPanel → **Email** → "Create Email Account"
2. Crea: `reservaciones@tudominio.com`
3. En `.env` actualiza las credenciales de correo
4. Datos SMTP de Hostinger:
   - Host: `smtp.hostinger.com`
   - Puerto: `587` (TLS) o `465` (SSL)
   - Usuario: tu dirección de correo completa

---

## PASO 10 — Acceso al Panel Administrativo

- URL: `https://tudominio.com/admin`
- Contraseña: la que configuraste en `ADMIN_PASSWORD` en `.env`
- Por defecto: `GoldenCabo2025!` *(cámbiala antes de ir a producción)*

---

## 🔧 Solución de Problemas Comunes

| Problema | Solución |
|---|---|
| Error 500 | Revisar `storage/logs/laravel.log`, verificar `APP_DEBUG=true` temporalmente |
| Página en blanco | Permisos de `storage/` y `bootstrap/cache/` |
| Error de Base de Datos | Verificar credenciales en `.env`, probar conexión en phpMyAdmin |
| Emails no se envían | Verificar SMTP en `.env`, revisar logs |
| Assets no cargan | Verificar que `public/` esté como document root |

---

## 📞 Notas Finales

- El panel admin está en `/admin` con contraseña simple (sin login de usuario)
- El PDF del voucher se genera en el **cliente** (JavaScript/jsPDF) — no requiere configuración especial
- El PDF del servidor (DomPDF) se usa en la ruta `/reservation/{order}/pdf`
- Asegúrate de tener SSL activado en Hostinger para HTTPS

