# Multi-Vendor eCommerce Platform - Setup Guide

## Quick Start

This guide will help you set up the multi-vendor eCommerce platform from scratch.

## Prerequisites

Before you begin, ensure you have:
- PHP 8.2 or higher
- Composer installed
- Node.js 18+ and NPM
- MySQL/MariaDB database
- GD or Imagick PHP extension

## Step-by-Step Installation

### 1. Clone or Download the Project

```bash
cd /path/to/your/web/directory
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install JavaScript dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
# Run all migrations to create database tables
php artisan migrate
```

### 6. Publish Package Assets

```bash
# Publish Spatie Media Library assets
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"

# Publish Laravel Fortify assets  
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"

# Publish Activity Log assets
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
```

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### 9. Create Admin User

```bash
php artisan make:filament-user
```

Follow the prompts to create your admin account.

### 10. Seed Initial Data (Optional)

To add some sample data for testing:

```bash
php artisan db:seed
```

## Configuration

### Payment Gateways

Edit `.env` and add your payment gateway credentials:

```env
# bKash
BKASH_APP_KEY=your_app_key
BKASH_APP_SECRET=your_app_secret
BKASH_USERNAME=your_username
BKASH_PASSWORD=your_password
BKASH_SANDBOX=true

# SSLCommerz
SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_password
SSLCOMMERZ_SANDBOX=true

# Stripe
STRIPE_KEY=your_publishable_key
STRIPE_SECRET=your_secret_key

# PayPal
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_SECRET=your_secret
PAYPAL_MODE=sandbox
```

### Courier Services

Configure courier services for shipping:

```env
# Pathao
PATHAO_CLIENT_ID=your_client_id
PATHAO_CLIENT_SECRET=your_client_secret
PATHAO_STORE_ID=your_store_id
PATHAO_SANDBOX=true

# Steadfast
STEADFAST_API_KEY=your_api_key
STEADFAST_SECRET_KEY=your_secret_key
STEADFAST_SANDBOX=true
```

### SMS Gateway

Configure SMS gateway for notifications:

```env
SMS_GATEWAY=your_gateway_name
SMS_API_KEY=your_api_key
SMS_SENDER_ID=your_sender_id
```

## Running the Application

### Development

```bash
# Run development server, queue worker, and vite simultaneously
composer dev
```

This will start:
- Laravel development server (http://localhost:8000)
- Queue worker
- Vite development server

### Production

For production, use your web server (Apache/Nginx) to serve the `public` directory.

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/project/public

    <Directory /path/to/project/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/project/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Hostinger Shared Hosting Setup

### 1. Upload Files

Upload all project files to your hosting directory (usually `public_html`).

### 2. Update .env

Update `.env` file with your Hostinger database credentials:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_hostinger_db
DB_USERNAME=your_hostinger_user
DB_PASSWORD=your_hostinger_password
```

### 3. Run Migrations

Use Hostinger's terminal or SSH to run:

```bash
cd public_html
php artisan migrate --force
```

### 4. Setup Cron Job

In Hostinger control panel, add a cron job:

**Command:**
```bash
cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**Frequency:** Every minute (*/1)

### 5. Queue Worker Cron

Add another cron job for queue processing:

**Command:**
```bash
cd /home/username/public_html && php artisan queue:work --stop-when-empty
```

**Frequency:** Every 5 minutes (*/5)

### 6. Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 7. Set Permissions

Ensure storage and bootstrap/cache directories are writable:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Access the Platform

### Admin Panel
- URL: `https://yourdomain.com/admin`
- Use the credentials created in step 9

### Vendor Panel
- URL: `https://yourdomain.com/vendor`
- Vendors can register and login here

### Frontend
- URL: `https://yourdomain.com`
- Customer-facing store

## Initial Configuration

After logging into the admin panel:

1. **System Settings**
   - Go to Settings → General
   - Configure site name, logo, contact details

2. **Payment Gateways**
   - Go to Settings → Payments
   - Enable and configure payment gateways

3. **Shipping Methods**
   - Go to Shipping → Methods
   - Set up shipping zones and rates

4. **Email Templates**
   - Go to Settings → Email Templates
   - Customize notification templates

5. **Tax Configuration**
   - Go to Settings → Tax
   - Configure tax rates for your regions

## Testing

### Run Tests

```bash
php artisan test
```

### Code Formatting

```bash
composer lint
```

## Troubleshooting

### Migration Errors

If you encounter migration errors:

```bash
php artisan migrate:fresh
```

**Warning:** This will drop all tables and recreate them.

### Permission Issues

If you see permission errors:

```bash
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

### Cache Issues

Clear all caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Support

For issues or questions:
1. Check the documentation in `ECOMMERCE_IMPLEMENTATION.md`
2. Review service classes in `app/Services/`
3. Check migration files in `database/migrations/`

## Security Checklist

Before going live:

- [x] Set `APP_DEBUG=false` in `.env`
- [x] Set `APP_ENV=production` in `.env`
- [x] Use HTTPS with valid SSL certificate
- [x] Set strong `APP_KEY`
- [x] Secure database credentials
- [x] Enable two-factor authentication
- [x] Set up regular backups
- [x] Configure rate limiting
- [x] Review file upload settings
- [x] Enable CSRF protection
- [x] Configure CORS properly

## Performance Optimization

For better performance:

1. Enable OPcache in PHP
2. Use database query caching
3. Configure CDN for static assets
4. Enable Gzip compression
5. Optimize images before upload
6. Use queue workers for heavy tasks
7. Enable Laravel's route caching
8. Use eager loading in queries

## Backup Strategy

Set up automated backups:

```bash
# Database backup
mysqldump -u username -p database_name > backup.sql

# Files backup
tar -czf backup.tar.gz /path/to/project
```

Consider using Laravel Backup package for automated backups:

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

## Next Steps

1. Customize the frontend design
2. Add product categories
3. Create test products
4. Set up shipping zones
5. Configure payment gateways
6. Test the complete purchase flow
7. Invite vendors to register
8. Configure marketing tools

## Additional Resources

- Laravel Documentation: https://laravel.com/docs
- Filament Documentation: https://filamentphp.com/docs
- Livewire Documentation: https://livewire.laravel.com/docs

---

**Need Help?** Check the implementation documentation or review the source code for detailed comments and examples.
