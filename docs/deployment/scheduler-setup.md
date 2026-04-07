# Scheduler Setup Documentation

## 1. Cron Job Entry
To run the Laravel Scheduler on your server, you need to add a single cron entry that runs the `schedule:run` command every minute.

Edit your server's crontab:
```bash
crontab -e
```

Add the following line (replace `/path-to-your-project` with the actual path):
```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 2. Scheduled Tasks in POS Laravel
Current scheduled tasks configured in `routes/console.php` or `app/Console/Kernel.php` (for older Laravel versions):
- **Daily Sales Summary**: `GenerateDailySalesSummaryJob` (Runs daily at midnight).
- **Marketplace Sync Retries**: `RetryFailedMarketplaceSyncJob` (Runs every hour).
- **Marketplace Order Check**: `ImportMarketplaceOrdersJob` (Runs every 15 minutes of each connected shop).
- **Stock Level Notifications**: `LowStockCheckJob` (if configured; runs hourly).

## 3. Local Testing
To test the scheduler locally, use:
```bash
php artisan schedule:work
```
This will run the tasks without setting up a cron entry.

## 4. Monitoring
Check the scheduled task list:
```bash
php artisan schedule:list
```
View the results of the last run:
```bash
php artisan schedule:show
```
(Note: Command availability depends on Laravel version.)
