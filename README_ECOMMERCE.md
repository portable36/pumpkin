# Multi-Vendor eCommerce Platform

A comprehensive, modern multi-vendor eCommerce platform built with Laravel 12, Livewire v4, and Filament v5. Optimized for Hostinger shared hosting environments.

## Features

### ✅ Vendor Management
- Complete vendor onboarding workflow
- Store configuration and settings
- Flexible commission rules (global, category, vendor-specific)
- Automated payout tracking and processing
- Comprehensive vendor analytics
- PDF report generation
- Compliance checks and verification
- Performance metrics tracking

### ✅ Product Management
- Products with unlimited variants and attributes
- Auto-generated & manual SKU (configurable)
- Auto-generated & manual Barcode (configurable)
- Cost price and selling price management
- Multi-level categories & tags
- SEO metadata (title, description, keywords)
- Bulk import/export (Excel, CSV)
- Full-text search with indexing
- Multiple images & videos support
- Complete category taxonomy

### ✅ Inventory System
- Multi-warehouse support
- Real-time stock tracking
- Stock reservations during checkout (prevents overselling)
- Low-stock alerts with notifications
- Complete inventory transaction logging
- Supplier management
- Purchase order system
- Event-driven inventory sync
- PDF inventory reports

### ✅ Cart & Checkout
- Guest and authenticated user carts
- Automatic cart expiration
- Coupon system with preview
- Real-time price recalculation
- Session-based cart for guests
- Cart merging on login

### ✅ Order Management
- Multi-vendor order splitting
- Complete order lifecycle tracking
- Status management (Pending → Processing → Shipped → Delivered)
- Comprehensive order history
- Automated invoice generation (PDF)
- Returns & refunds workflow
- Status change notifications
- Internal notes and customer notes

### ✅ Payment Integration
- **bKash** - Bangladesh's leading mobile wallet
- **SSLCommerz** - Leading payment gateway in Bangladesh
- **Stripe** - International cards and wallets
- **PayPal** - Global payment solution
- Payment intent management
- Webhook handling & verification
- Automated refund processing
- Fraud detection checks
- Payment reports (PDF)

### ✅ Shipping & Logistics
- **Pathao** courier integration
- **Steadfast** courier integration with one-click dispatch
- Multiple shipping zones configuration
- Various shipping methods (flat rate, free, weight-based, courier)
- Real-time tracking IDs
- Delivery status updates
- Automated courier rate calculation
- Shipping label generation
- Smart warehouse selection
- Shipping reports (PDF)

### ✅ Accounting & Finance
- Vendor payout tracking and processing
- Platform commission calculation
- Double-entry ledger system
- Tax/VAT calculation and reporting
- Comprehensive financial reports
- Profit/Loss statements
- Expense tracking (marketing, delivery, product cost, operational)
- Invoice management system
- Financial reports (PDF)

### ✅ Reviews & Ratings
- Product reviews with 1-5 star ratings
- Verified purchase badges
- Review moderation system
- Multi-criteria vendor ratings
  - Product quality
  - Shipping speed
  - Communication
- Helpful/Not helpful voting
- Abuse prevention and reporting
- Review images support

### ✅ Wishlist
- Multiple wishlists per user
- Public/private wishlist options
- Stock back-in notifications
- Price-drop alerts
- Easy product saving

### ✅ Notification System
- Email notifications with templates
- SMS notifications (with gateway integration)
- Web push notifications
- Real-time order updates
- OTP delivery for authentication
- Customizable notification preferences
- Event-driven notification system

### ✅ Search & Discovery
- Advanced full-text search
- Multiple filters & sorting options
- Auto-suggest functionality
- Smart ranking algorithms
- Search analytics and tracking
- Popular searches tracking

### ✅ Analytics & Reporting
- Sales reports (daily, weekly, monthly, yearly)
- Complete funnel tracking
- Vendor performance analytics
- Customer behavior tracking
- Comprehensive accounting reports
- Inventory level reports
- Shipping performance reports
- Product view tracking
- Conversion tracking
- Campaign performance

### ✅ Marketing Tools
- Campaign tracking (UTM parameters)
- Google Tag Manager integration
- Facebook Pixel integration
- Google Analytics support
- Conversion analytics
- Customer segmentation
- Campaign ROI tracking

## System Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer 2.x
- Node.js 18+ & NPM
- GD or Imagick extension

### Recommended for Production
- 2GB RAM minimum
- SSL certificate
- CDN for media files (optional)

## Installation

### 1. Clone and Install Dependencies

```bash
# Clone the repository
git clone <repository-url>
cd project

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Configuration

Update `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Configure Services

Add your API credentials to `.env`:

```env
# Payment Gateways
BKASH_APP_KEY=your_key
BKASH_APP_SECRET=your_secret
BKASH_USERNAME=your_username
BKASH_PASSWORD=your_password
BKASH_SANDBOX=true

SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_password
SSLCOMMERZ_SANDBOX=true

STRIPE_KEY=your_key
STRIPE_SECRET=your_secret

PAYPAL_CLIENT_ID=your_client_id
PAYPAL_SECRET=your_secret
PAYPAL_MODE=sandbox

# Courier Services
PATHAO_CLIENT_ID=your_client_id
PATHAO_CLIENT_SECRET=your_client_secret
PATHAO_STORE_ID=your_store_id
PATHAO_SANDBOX=true

STEADFAST_API_KEY=your_api_key
STEADFAST_SECRET_KEY=your_secret_key
STEADFAST_SANDBOX=true

# SMS Gateway
SMS_GATEWAY=your_gateway
SMS_API_KEY=your_api_key
SMS_SENDER_ID=your_sender_id
```

### 6. Build Assets

```bash
npm run build
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Create Admin User

```bash
php artisan make:filament-user
```

## Hostinger Deployment

### 1. File Upload
Upload all files to your public_html directory (or subdirectory)

### 2. Environment Configuration
- Rename `.env.example` to `.env`
- Update database credentials
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Update `APP_URL` to your domain

### 3. Cron Job Setup
Add to crontab in Hostinger control panel:

```cron
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 5. Queue Configuration
For Hostinger, use database queue driver:

```env
QUEUE_CONNECTION=database
```

Then setup a cron job:
```cron
*/5 * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty
```

## Performance Optimization

### Caching
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### Database Indexing
All foreign keys and frequently queried columns are automatically indexed.

### Image Optimization
Images are automatically compressed and resized using Intervention Image.

### CDN Integration
Configure CDN for static assets in `.env`:

```env
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket
AWS_URL=your_cdn_url
MEDIA_DISK=s3
```

## Admin Panel Access

Visit: `your-domain.com/admin`

## Vendor Panel Access

Visit: `your-domain.com/vendor`

## API Documentation

API endpoints are available at: `/api/v1/`

## Scheduled Tasks

The following tasks run automatically via Laravel scheduler:

- **Every minute**: Process queue jobs
- **Hourly**: 
  - Update product views analytics
  - Process price drop alerts
  - Send low stock alerts
- **Daily**:
  - Update vendor metrics
  - Generate financial summaries
  - Clean expired carts
  - Clean old logs

## Security Features

- CSRF protection
- SQL injection prevention (via Eloquent ORM)
- XSS protection
- Rate limiting
- Two-factor authentication
- Encrypted sensitive credentials
- Activity logging

## Support & Documentation

For detailed implementation guides, see:
- `ECOMMERCE_IMPLEMENTATION.md` - Complete feature documentation
- Service classes in `app/Services/` - Business logic documentation
- Migration files in `database/migrations/` - Database structure

## License

This project is open-sourced software licensed under the MIT license.

## Credits

Built with:
- Laravel 12
- Livewire v4
- Filament v5
- Spatie packages (Media Library, Permissions, Activity Log)
- Intervention Image
- DomPDF for report generation

---

For support or questions, please open an issue in the repository.
