# Production Environment Setup

## 1. System Requirements
- PHP 8.3+ (PHP 8.4 recommended)
- PostgreSQL 15+
- Redis (for Queue & Cache)
- Nginx / Apache
- Supervisor (for Queue Workers)

## 2. Environment Variables (.env)
Key variables to configure for production:
```env
APP_NAME="POS Multi Cabang"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pos_laravel
DB_USERNAME=pos_user
DB_PASSWORD=your_secure_password

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

FILESYSTEM_DISK=public
```

## 3. Deployment Steps
1. **Clone & Install**:
   ```bash
   git clone <repo-url>
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```
2. **Setup Cache**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. **Run Migrations**:
   ```bash
   php artisan migrate --force
   ```
4. **Seed Initial Data** (if needed):
   ```bash
   php artisan db:seed --force
   ```

## 4. File Permissions
Ensure the web server user has write access to:
- `storage`
- `bootstrap/cache`
