# Backup Checklist

## 1. Database Backups (PostgreSQL)
- **Automatic Backups**: Setup a daily cron for `pg_dump`.
- **Retention**: Keep 7 days of daily backups, 4 weeks of weekly backups, and 12 months of monthly backups.
- **Storage**: Store backups in an off-site storage like Amazon S3, Google Cloud Storage, or Wasabi.
- **Tools**: Use `spatie/laravel-backup` for integrated backups.

### Spatie Laravel Backup CLI:
```bash
php artisan backup:run
php artisan backup:list
```

## 2. File Backups
- **Public Uploads**: `storage/app/public`.
- **Private Files**: `storage/app/private`.
- **Configuration Files**: `.env` and `config/`.

## 3. Disaster Recovery Plan
- **Pre-requisites**: Tested restoration process on a staging server.
- **Steps**:
  1. Setup clear server (OS, PHP, DB).
  2. Restore database from S3.
  3. Restore uploaded media.
  4. Redeploy code via Git.
  5. Configure environment variables.
  6. Verify connectivity.
