# Despliegue en Hostinger

Este proyecto está preparado para producción, pero antes de publicarlo en Hostinger revisa estos puntos:

## Requisitos del servidor

- PHP **8.4.1 o superior**
- Extensiones activas: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `session`
- Base de datos MySQL creada
- El document root debe apuntar a la carpeta `public`

## Variables de entorno

Copia `.env.example` a `.env` en el servidor y completa estos valores:

- `APP_NAME=Electoral`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://tu-dominio.com`
- `APP_TIMEZONE=America/Bogota`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Si el sitio ya usa HTTPS, activa también:

- `SESSION_SECURE_COOKIE=true`

## Pasos recomendados

1. Ejecuta `composer install --no-dev --optimize-autoloader`
2. Ejecuta `php artisan key:generate` si el `.env` todavía no tiene clave
3. Ejecuta `php artisan migrate --force`
4. Ejecuta `php artisan storage:link`
5. Ejecuta `php artisan optimize`
6. Compila los assets con `npm run build` y sube `public/build`

## Archivos importantes

- Las fotos de votantes se guardan en `storage/app/public/votantes/certificados`
- Para que se vean en navegador, el enlace `public/storage` debe existir

## Verificación final

- Abre la app y confirma que `APP_DEBUG` está en `false`
- Revisa que las imágenes carguen correctamente
- Verifica que el login y el registro sigan funcionando con la nueva URL
