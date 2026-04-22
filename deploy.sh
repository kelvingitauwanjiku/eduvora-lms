#!/usr/bin/env bash
set -euo pipefail

# Eduvora deploy script for cPanel (SSH)
# Adjust SSH_USER, SSH_HOST, SSH_PORT, APP_DIR and BRANCH as needed.

SSH_USER="ypforum"
SSH_HOST="ypforum.org"
SSH_PORT="20198"
APP_DIR="${APP_DIR:-~/deveint.ypforum.org}"
BRANCH="${1:-main}"
ENV_FILE="${ENV_FILE:-.env}"

echo "Deploying branch $BRANCH to $SSH_USER@$SSH_HOST:$APP_DIR"

action() {
  echo "-- Connecting to remote server"
  ssh -p "$SSH_PORT" "$SSH_USER"@"$SSH_HOST" "bash -s" <<'REMOTE'
set -euo pipefail

# Remote variables passed via SSH environment
: "BRANCH=${BRANCH}"
: "APP_DIR=${APP_DIR}"

echo "Entering $APP_DIR"
cd "$APP_DIR" || { echo "App dir not found: $APP_DIR"; exit 2; }

# Backup (quick tar of current release) - keeps in home for rollback
TIMESTAMP=$(date +%F_%H%M%S)
BACKUP_FILE=~/eduvora-backup-$TIMESTAMP.tar.gz
echo "Creating quick tar backup: $BACKUP_FILE"
tar -czf "$BACKUP_FILE" . || echo "Backup failed or tar not available"

# Maintenance mode
if command -v php >/dev/null 2>&1; then
  php artisan down --message="Deploying $BRANCH" --retry=60 || true
else
  echo "php not found on remote. Aborting."; exit 3
fi

# Update code
if [ -d .git ]; then
  git fetch origin --quiet
  git checkout "$BRANCH" || git checkout -b "$BRANCH" origin/"$BRANCH" || true
  git pull origin "$BRANCH" --quiet || true
else
  echo "No git repo found in $APP_DIR. Ensure code is deployed or use git clone.";
fi

# Composer
if command -v composer >/dev/null 2>&1; then
  composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress || true
else
  echo "composer not found, skipping PHP dependencies install"
fi

# Node build (optional)
if command -v npm >/dev/null 2>&1; then
  npm ci --silent || echo "npm ci failed"
  npm run build --silent || echo "npm build failed"
else
  echo "npm not found, skipping frontend build"
fi

# Key & storage
php artisan key:generate --force || true
php artisan storage:link --force || true

# Migrate DB (ensure backup done)
php artisan migrate --force || { echo "Migration failed"; exit 4; }

# Cache
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Restart queue
php artisan queue:restart || true

# Permissions
if command -v whoami >/dev/null 2>&1; then
  OWNER=$(whoami)
  chown -R "$OWNER":"$OWNER" storage bootstrap/cache || true
fi
find storage -type d -exec chmod 775 {} \; || true
find storage -type f -exec chmod 664 {} \; || true
chmod -R 775 bootstrap/cache || true

# Bring app up
php artisan up || true

echo "Remote deploy script finished"
REMOTE
}

# Confirm before running
read -p "Proceed with deploy to $SSH_USER@$SSH_HOST (branch: $BRANCH)? [y/N]: " CONFIRM
if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
  echo "Aborted by user."; exit 1
fi

action

echo "Deploy finished. Check logs with: ssh -p $SSH_PORT $SSH_USER@$SSH_HOST 'tail -n 200 $APP_DIR/storage/logs/laravel.log'"
