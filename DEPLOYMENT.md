# Server Deployment Guide
### Bitaqati — Laravel 13.4 · Multi-Tenant SaaS

---

## Table of Contents

1. [Server Requirements](#1-server-requirements)
2. [DNS & Domain Setup](#2-dns--domain-setup)
3. [Nginx Configuration](#3-nginx-configuration)
4. [Wildcard SSL Certificate](#4-wildcard-ssl-certificate)
5. [Uploading Project Files](#5-uploading-project-files)
6. [Environment File `.env`](#6-environment-file-env)
7. [Installing the Application](#7-installing-the-application)
8. [Database Setup](#8-database-setup)
9. [Supervisor — Persistent Processes](#9-supervisor--persistent-processes)
10. [Cron Job](#10-cron-job)
11. [File Permissions](#11-file-permissions)
12. [Final Verification Checklist](#12-final-verification-checklist)
13. [Useful Commands After Deployment](#13-useful-commands-after-deployment)

---

## 1. Server Requirements

| Requirement | Minimum Version | Notes |
|-------------|----------------|-------|
| PHP | **8.3+** | With extensions listed below |
| MySQL | 8.0+ | or MariaDB 10.6+ |
| Nginx | 1.18+ | Preferred over Apache |
| Composer | 2.x | PHP dependency manager |
| Node.js | 18+ | For building frontend assets |
| npm | 9+ | |
| Supervisor | any | Manages persistent background processes |
| Certbot | any | For SSL certificate |

### Required PHP Extensions

```bash
# Verify all are loaded
php -m | grep -E "bcmath|ctype|curl|dom|fileinfo|json|mbstring|openssl|pcre|pdo|pdo_mysql|tokenizer|xml|zip"

# Install on Ubuntu / Debian
sudo apt install -y php8.3-bcmath php8.3-ctype php8.3-curl php8.3-dom \
  php8.3-fileinfo php8.3-mbstring php8.3-mysql php8.3-xml php8.3-zip \
  php8.3-redis php8.3-intl php8.3-gd
```

---

## 2. DNS & Domain Setup

### Why Does This Project Need Wildcard DNS?

This project uses a **Subdomain-per-Tenant** architecture:

```
yourapp.com/superadmin      →  Super Admin panel
network1.yourapp.com        →  Tenant 1 admin & client panel
network2.yourapp.com        →  Tenant 2 admin & client panel
```

Every new network registered on the platform gets its own subdomain. Because you can't know these subdomains in advance, you need a single **Wildcard DNS record** that routes any `*.yourapp.com` request to the same server. Nginx + the `ResolveTenant` middleware then figure out which tenant to load based on the subdomain.

### Required DNS Records

Go to your domain registrar's DNS panel (Cloudflare, GoDaddy, Namecheap, etc.) and add:

```
Type  | Name  | Value            | TTL
------|-------|-----------------|------
A     | @     | YOUR_SERVER_IP   | Auto
A     | www   | YOUR_SERVER_IP   | Auto
A     | *     | YOUR_SERVER_IP   | Auto   ← This is the Wildcard
```

**Real example:**
```
A    @       185.100.200.50    Auto
A    www     185.100.200.50    Auto
A    *       185.100.200.50    Auto
```

> **Cloudflare users:** Set the Wildcard record to **DNS only** (grey cloud) while setting up the SSL certificate. You can enable the proxy (orange cloud) afterwards.

---

## 3. Nginx Configuration

### Create the Config File

```bash
sudo nano /etc/nginx/sites-available/yourapp.com
```

Paste the following — replace `yourapp.com` and `/var/www/yourapp` with your own values:

```nginx
# ============================================================
# Redirect all HTTP → HTTPS (main domain + all subdomains)
# ============================================================
server {
    listen 80;
    listen [::]:80;
    server_name yourapp.com www.yourapp.com *.yourapp.com;

    return 301 https://$host$request_uri;
}

# ============================================================
# Main HTTPS server — handles the root domain AND every
# subdomain thanks to the wildcard in server_name
# ============================================================
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourapp.com www.yourapp.com *.yourapp.com;

    root  /var/www/yourapp/public;
    index index.php;

    # ── SSL ─────────────────────────────────────────────────
    ssl_certificate     /etc/letsencrypt/live/yourapp.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourapp.com/privkey.pem;
    ssl_protocols       TLSv1.2 TLSv1.3;
    ssl_ciphers         HIGH:!aNULL:!MD5;

    # ── Security Headers ────────────────────────────────────
    add_header X-Frame-Options          "SAMEORIGIN";
    add_header X-Content-Type-Options   "nosniff";
    add_header X-XSS-Protection         "1; mode=block";
    add_header Referrer-Policy          "strict-origin-when-cross-origin";

    # ── Static Asset Caching ────────────────────────────────
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|svg)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    # ── Laravel Front Controller ────────────────────────────
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # ── Laravel Reverb WebSocket Proxy ──────────────────────
    # Reverb listens on port 8080 internally.
    # Nginx forwards /app/ and /apps/ WebSocket traffic to it
    # so clients connect on the standard HTTPS port (443).
    location /app/ {
        proxy_pass         http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header   Upgrade $http_upgrade;
        proxy_set_header   Connection "Upgrade";
        proxy_set_header   Host $host;
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_read_timeout 86400;
    }

    location /apps/ {
        proxy_pass         http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header   Upgrade $http_upgrade;
        proxy_set_header   Connection "Upgrade";
        proxy_set_header   Host $host;
    }

    # ── Block Access to Sensitive Files ─────────────────────
    location ~ /\.env      { deny all; }
    location ~ /\.ht       { deny all; }
    location ~ /storage/.*\.php$ { deny all; }

    # ── Logs ────────────────────────────────────────────────
    access_log /var/log/nginx/yourapp.access.log;
    error_log  /var/log/nginx/yourapp.error.log;

    client_max_body_size 20M;
}
```

### Enable & Test

```bash
# Create symlink to enable the site
sudo ln -s /etc/nginx/sites-available/yourapp.com /etc/nginx/sites-enabled/

# Test configuration syntax
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

## 4. Wildcard SSL Certificate

A standard Let's Encrypt certificate covers only one domain. You need a **Wildcard certificate** that covers both `yourapp.com` and `*.yourapp.com` so every tenant subdomain is secured automatically.

### Install Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Request the Wildcard Certificate

Wildcard certificates require **DNS challenge** verification (not HTTP):

```bash
sudo certbot certonly \
  --manual \
  --preferred-challenges dns \
  -d yourapp.com \
  -d "*.yourapp.com"
```

### DNS Verification Steps

Certbot will ask you to add a TXT record in your DNS:

```
Type  | Name                   | Value
------|------------------------|----------------------------------
TXT   | _acme-challenge        | <value provided by Certbot>
```

1. Add the TXT record in your DNS panel
2. Wait **2–5 minutes** for propagation
3. Verify it propagated: `nslookup -type=TXT _acme-challenge.yourapp.com`
4. Press **Enter** in the Certbot terminal to continue

### Auto-Renewal

```bash
# Test renewal (dry run)
sudo certbot renew --dry-run

# Certbot registers a systemd timer automatically — verify it
sudo systemctl status certbot.timer
```

> **Cloudflare users:** Use the [certbot-dns-cloudflare](https://certbot-dns-cloudflare.readthedocs.io/) plugin to automate DNS challenge renewals without any manual steps.

---

## 5. Uploading Project Files

### Option A — Git (Recommended)

```bash
# On the server
cd /var/www/
sudo git clone https://github.com/your-username/your-repo.git yourapp
cd yourapp

# Set ownership
sudo chown -R www-data:www-data /var/www/yourapp
sudo chmod -R 755 /var/www/yourapp
```

### Option B — rsync from Your Local Machine

```bash
rsync -avz \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='.env' \
  /path/to/local/project/ \
  user@YOUR_SERVER_IP:/var/www/yourapp/
```

---

## 6. Environment File `.env`

```bash
cd /var/www/yourapp
sudo cp .env.example .env
sudo nano .env
```

Fill in every value below — comments explain why each one matters:

```dotenv
# ── Application ─────────────────────────────────────────────
APP_NAME="Bitaqati"
APP_ENV=production
APP_KEY=                          # Generated in Step 7
APP_DEBUG=false                   # MUST be false in production
APP_URL=https://yourapp.com

# ── Domain — the core of the subdomain routing system ───────
# Used by ResolveTenant middleware to extract the subdomain
# from the Host header. No https://, no trailing slash.
APP_DOMAIN=yourapp.com

# ── Database ─────────────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitaqati
DB_USERNAME=bitaqati_user
DB_PASSWORD=YOUR_STRONG_DB_PASSWORD

# ── Sessions & Cache ─────────────────────────────────────────
SESSION_DRIVER=database
# The leading dot is CRITICAL — it allows the session cookie
# to be shared across yourapp.com AND all *.yourapp.com subdomains.
# Without it, users are logged out when switching subdomains.
SESSION_DOMAIN=.yourapp.com
SESSION_SECURE_COOKIE=true        # HTTPS only
SESSION_LIFETIME=120
CACHE_STORE=database

# ── Queue ────────────────────────────────────────────────────
QUEUE_CONNECTION=database

# ── Reverb WebSocket ─────────────────────────────────────────
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=my-app              # Any unique string
REVERB_APP_KEY=my-app-key         # Any unique string
REVERB_APP_SECRET=my-app-secret   # Strong random secret
REVERB_HOST=0.0.0.0               # Listen on all interfaces
REVERB_PORT=8080                  # Internal port (Nginx proxies it)
REVERB_SCHEME=https

# ── Vite (sent to the browser) ───────────────────────────────
# The browser connects to wss://yourapp.com/app/KEY via Nginx,
# NOT directly to port 8080.
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${APP_DOMAIN}"
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https

# ── Super Admin Account ──────────────────────────────────────
# Migration #13 reads these values to seed the super admin user.
# Set them BEFORE running php artisan migrate.
SUPER_ADMIN_NAME="Platform Owner"
SUPER_ADMIN_EMAIL=admin@yourapp.com
SUPER_ADMIN_PHONE=0599000000
SUPER_ADMIN_PASSWORD=StrongPassword123!

# ── Default Tenant ───────────────────────────────────────────
# The subdomain for the first / default network.
# Migration #13 also uses this to create the initial tenant.
DEFAULT_TENANT_SUBDOMAIN=demo

# ── Logging ──────────────────────────────────────────────────
LOG_CHANNEL=daily
LOG_LEVEL=error
```

---

## 7. Installing the Application

Run these commands **in order**:

```bash
cd /var/www/yourapp

# 1. Install PHP dependencies (no dev packages in production)
composer install --no-dev --optimize-autoloader

# 2. Generate the application encryption key
php artisan key:generate

# 3. Build frontend assets
npm ci
npm run build

# 4. Cache config, routes, views, and events for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Create the public storage symlink
php artisan storage:link
```

---

## 8. Database Setup

### Create the Database and User

```bash
mysql -u root -p
```

```sql
CREATE DATABASE bitaqati CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bitaqati_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_DB_PASSWORD';
GRANT ALL PRIVILEGES ON bitaqati.* TO 'bitaqati_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Run Migrations

```bash
# Make sure .env is fully configured before this step
php artisan migrate --force
```

### Migration Execution Order

| # | Migration File | What It Does |
|---|---------------|-------------|
| 1 | `create_users_table` | Core users table |
| 2–7 | `create_*_table` | Cards, packages, invoices, transactions, tickets, etc. |
| 8 | `create_plans_table` | Subscription plans |
| 9 | `create_tenants_table` | Tenant (network) records |
| 10 | `create_system_config_table` | Global key-value config |
| 11 | `add_tenant_id_to_all_tables` | Adds `tenant_id` column to every table |
| 12 | `update_users_role_enum` | Adds `super_admin` and `network_admin` roles |
| 13 | `seed_default_tenant_and_super_admin` | **Creates Super Admin user + default tenant from `.env`** |
| 14 | `create_homepage_sections_table` | Public homepage section management |
| 15 | `add_pending_to_tenants_status` | Adds `pending` status for tenant registration flow |

> **Important:** Migration #13 reads from `.env` to create the Super Admin account. These three values **must be set before running migrate**:
> ```dotenv
> SUPER_ADMIN_EMAIL=admin@yourapp.com
> SUPER_ADMIN_PASSWORD=StrongPassword123!
> DEFAULT_TENANT_SUBDOMAIN=demo
> ```

---

## 9. Supervisor — Persistent Processes

The application requires **two long-running processes** that must always be online:

| Process | Command | Purpose |
|---------|---------|---------|
| Queue Worker | `php artisan queue:work` | Processes notifications and background jobs |
| Reverb Server | `php artisan reverb:start` | WebSocket server for real-time chat |

### Install Supervisor

```bash
sudo apt install -y supervisor
```

### Queue Worker Config

```bash
sudo nano /etc/supervisor/conf.d/yourapp-queue.conf
```

```ini
[program:yourapp-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/yourapp/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/yourapp-queue.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=5
stopwaitsecs=3600
```

### Reverb WebSocket Config

```bash
sudo nano /etc/supervisor/conf.d/yourapp-reverb.conf
```

```ini
[program:yourapp-reverb]
process_name=%(program_name)s
command=php /var/www/yourapp/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/yourapp-reverb.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=3
```

### Start Everything

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start yourapp-queue:*
sudo supervisorctl start yourapp-reverb

# Verify all processes are running
sudo supervisorctl status
```

Expected output:
```
yourapp-queue:yourapp-queue_00   RUNNING   pid 12345, uptime 0:00:10
yourapp-queue:yourapp-queue_01   RUNNING   pid 12346, uptime 0:00:10
yourapp-reverb                   RUNNING   pid 12347, uptime 0:00:10
```

---

## 10. Cron Job

```bash
sudo crontab -u www-data -e
```

Add this single line:

```cron
* * * * * cd /var/www/yourapp && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. File Permissions

```bash
cd /var/www/yourapp

# Set owner to the web server user
sudo chown -R www-data:www-data .

# Standard permissions
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;

# These two directories need write access by the application
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Quick verification
ls -la storage/
```

---

## 12. Final Verification Checklist

### DNS

```bash
# Both should resolve to your server IP
nslookup network1.yourapp.com
nslookup network2.yourapp.com
nslookup random-test.yourapp.com
```

### SSL

```bash
# Certificate should be valid on the root domain and any subdomain
curl -vI https://yourapp.com 2>&1 | grep -E "SSL|subject|expire"
curl -vI https://test123.yourapp.com 2>&1 | grep "SSL"
```

### Application

```bash
# Routes compile without errors
php artisan route:list | tail -5

# Database connection works
php artisan tinker --execute="echo DB::connection()->getPdo() ? 'DB OK' : 'FAIL';"

# Super Admin was seeded
php artisan tinker --execute="echo \App\Models\User::where('role','super_admin')->count() . ' super admin(s) found';"

# Default tenant was created
php artisan tinker --execute="echo \App\Models\Tenant::count() . ' tenant(s) found';"
```

### Pages

| URL | Expected Result |
|-----|----------------|
| `https://yourapp.com` | Public platform homepage |
| `https://yourapp.com/superadmin/login` | Super Admin login page |
| `https://demo.yourapp.com/admin/login` | Default network admin login |
| `https://yourapp.com/register` | New network registration form |

### Background Processes

```bash
sudo supervisorctl status
# All processes must show RUNNING
```

---

## 13. Useful Commands After Deployment

### Deploying an Update

Run this sequence every time you push new code:

```bash
cd /var/www/yourapp

# 1. Pull latest code
git pull origin main

# 2. Install any new PHP packages
composer install --no-dev --optimize-autoloader

# 3. Rebuild frontend assets if changed
npm ci && npm run build

# 4. Run any new migrations
php artisan migrate --force

# 5. Clear old cache and rebuild
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart background processes to pick up code changes
sudo supervisorctl restart yourapp-queue:*
sudo supervisorctl restart yourapp-reverb
```

### Monitoring

```bash
# Stream application logs live
tail -f /var/www/yourapp/storage/logs/laravel.log

# Stream queue worker logs
tail -f /var/log/yourapp-queue.log

# Stream Reverb WebSocket logs
tail -f /var/log/yourapp-reverb.log

# Count pending jobs in the queue
php artisan tinker --execute="echo DB::table('jobs')->count() . ' jobs pending';"

# System resource usage
htop
```

### Manually Create a New Tenant

```bash
php artisan tinker
```

```php
$plan = \App\Models\Plan::first();

$tenant = \App\Models\Tenant::create([
    'name'      => 'Test Network',
    'subdomain' => 'testnet',       // → testnet.yourapp.com
    'plan_id'   => $plan->id,
    'status'    => 'active',
]);

$admin = \App\Models\User::withoutGlobalScopes()->create([
    'tenant_id' => $tenant->id,
    'name'      => 'Network Admin',
    'email'     => 'admin@testnet.com',
    'phone'     => '0500000001',
    'password'  => bcrypt('password123'),
    'role'      => 'network_admin',
    'balance'   => 0,
    'status'    => 'active',
]);

$tenant->update(['owner_id' => $admin->id]);

echo "Done — visit: https://testnet.yourapp.com/admin/login";
```

---

## Key Concepts to Remember

> **`APP_DOMAIN` vs `APP_URL`**
> - `APP_URL=https://yourapp.com` — used by Laravel to generate absolute URLs
> - `APP_DOMAIN=yourapp.com` — used by the `ResolveTenant` middleware to strip the subdomain from the `Host` header
> - Both are required. `APP_DOMAIN` must not include `https://` or a trailing slash.

> **`SESSION_DOMAIN` leading dot**
> - `SESSION_DOMAIN=.yourapp.com` — the leading dot is required
> - It tells the browser to send the session cookie for `yourapp.com` and every `*.yourapp.com` subdomain
> - Without it, users must log in separately on every subdomain

> **Reverb & Nginx**
> - Reverb listens internally on port `8080`
> - Nginx acts as a reverse proxy and forwards WebSocket connections from port `443`
> - Clients connect to `wss://yourapp.com/app/KEY` — they never touch port `8080` directly
> - This is why `VITE_REVERB_PORT=443` and `VITE_REVERB_HOST="${APP_DOMAIN}"` are set that way

---

*Last updated: 2026-04-19 | Laravel 13.4 · PHP 8.3 · Laravel Reverb 1.10*
