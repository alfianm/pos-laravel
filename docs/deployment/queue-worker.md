# Queue Worker Documentation

## 1. Supervisor Configuration
Use Supervisor to monitor and restart queue workers automatically.

Create a new file: `/etc/supervisor/conf.d/pos-worker.conf`

```ini
[program:pos-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pos-laravel/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pos-laravel/storage/logs/worker.log
stopwaitsecs=3600
```

## 2. Dedicated Marketplace Queue
For critical marketplace syncs, consider running a separate worker for the `marketplace-sync` queue.

Add to `/etc/supervisor/conf.d/pos-marketplace-worker.conf`:

```ini
[program:pos-marketplace-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pos-laravel/artisan queue:work redis --queue=marketplace-sync,marketplace-imports --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
stdout_logfile=/var/www/pos-laravel/storage/logs/marketplace-worker.log
```

## 3. Reloading Supervisor
After updating configurations:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```
To restart workers after code deployment:
```bash
php artisan queue:restart
```
