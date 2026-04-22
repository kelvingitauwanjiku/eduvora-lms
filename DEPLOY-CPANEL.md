# Eduvora cPanel Deployment Guide

## Quick Deploy Steps

### Option 1: Pull from GitHub (Recommended)

SSH to your server and run:

```bash
cd /home/ypforum/deveint.ypforum.org

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and optimize cache
php artisan optimize:clear
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 775 storage bootstrap/cache public
chmod 644 .env
```

### Option 2: Manual Upload via cPanel File Manager

1. Download the project from GitHub
2. Extract and upload via cPanel File Manager to `/home/ypforum/deveint.ypforum.org/`

### Option 3: Use .cpanel.yml Auto-Deploy

cPanel's AutoDeploy feature will automatically deploy on git push (if configured).

---

## Post-Deploy Checklist

- [ ] Verify `.env` has correct production settings
- [ ] Check database connection works
- [ ] Test frontend loads at https://deveint.ypforum.org
- [ ] Test login/registration flow
- [ ] Check error logs: `tail -f storage/logs/laravel.log`

---

## Rollback Steps

```bash
# Check recent commits
git log --oneline -5

# Revert to previous commit
git revert HEAD

# Or reset to specific commit
git reset --hard a5b80c2
git push --force origin main
```

---

## Troubleshooting

### Build not found
```bash
cd /home/ypforum/public_html/public
ls -la build/
```

### Permission errors
```bash
chown -R ypforum:ypforum /home/ypforum/public_html
chmod -R 775 storage bootstrap/cache public
```

### Cache issues
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
```

### Database connection
```bash
php artisan migrate:status
```