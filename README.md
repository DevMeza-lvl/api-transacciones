# API Transactions (Laravel + Sanctum + Scribe)

API de ejemplo para gestión de usuarios y transacciones, con autenticación por token (Sanctum) y documentación interactiva (Scribe).

## Requisitos

- PHP 8.2+
- Composer 2.x
- MySQL 8 (o MariaDB compatible)
- Node 18+ (solo si vas a compilar assets con Vite; para esta API no es obligatorio)
- Git

## Instalación (local)

```bash
# 1) Clonar
git clone https://github.com/DevMeza-lvl/api-transacciones.git
cd api-transacciones

# 2) Dependencias
composer install

# 3) Variables de entorno
Para linux/MacOs
cp .env.example .env
Para Windows
copy .env.example .env
php artisan key:generate

# 4) Configurar base de datos
# Edita .env con tus credenciales (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5) Migraciones 
php artisan migrate

# (Opcional) Seeders
php artisan db:seed

# 6) (Recomendado) Ajustes de CORS (config/cors.php)
#   'allowed_origins' => ['http://127.0.0.1:8000'],

# 7) Ajustes de Scribe (docs)
# En .env ya vienen:
#   SCRIBE_BASE_URL=http://127.0.0.1:8000
#   SCRIBE_TRY_BASE_URL=http://127.0.0.1:8000
# Luego genera la doc:
php artisan scribe:generate

# 8) Levantar el servidor
php artisan serve
