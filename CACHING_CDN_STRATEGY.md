## Caching & CDN Strategy for Hostinger Shared Hosting

### Overview
Hostinger shared hosting has memory and CPU limits, so aggressive caching is essential. Strategy is:
1. **HTTP Cache Headers** — static assets + frequently accessed pages (browser + CDN)
2. **Redis/File Cache** — dynamic data (products, categories, settings)
3. **CDN Integration** — Cloudflare or Hostinger CDN for asset delivery
4. **Query Caching** — Eloquent model result caching
5. **API Response Caching** — JSON endpoints cached per user/role

### Implementation

#### 1. Static Asset Caching (Public Frontend)
**Files:** CSS, JS, images, fonts
- Served with `Cache-Control: public, max-age=31536000` (1 year)
- By default Vite/Webpack includes content hashes, so versioning is automatic.

**Middleware** — update in `app/Http/Middleware/SetCacheHeaders.php`:
```php
Route::middleware('SetCacheHeaders')->group(function () {
    Route::get('/css/{file}', ...)->name('css');
    Route::get('/js/{file}', ...)->name('js');
    Route::get('/images/{file}', ...)->name('images');
});
```

#### 2. Product/Catalog Caching
Cache product lists, categories, and attributes in Redis/File cache:
```php
// Cache for 1 hour
Cache::remember('products.all', 3600, fn() => Product::with('category')->get());
Cache::remember("category.{$id}.products", 3600, fn() => Category::find($id)->products()->get());
```

#### 3. Settings Caching
Settings loaded on every request — cache aggressively:
```php
Cache::remember('settings.all', 86400, fn() => Setting::all());  // 24h
```

#### 4. CDN Integration
- **Cloudflare** (free tier):
  - Caches static assets, HTML pages (via cache rules)
  - Provides DDoS protection
  - Automatic HTTPS
  - Works seamlessly with Laravel
  - Setup: Point DNS to Cloudflare; configure cache rules in dashboard

- **Hostinger CDN** (if available):
  - Purchased as add-on via panel
  - Automatically mirrors assets from `/public` folder

**Configuration in Laravel:**
```php
// config/filesystems.php
'disks' => [
    'cdn' => [
        'driver' => 's3',
        'key' => env('CDN_KEY'),
        'secret' => env('CDN_SECRET'),
        'region' => env('CDN_REGION'),
        'bucket' => env('CDN_BUCKET'),
        'url' => env('CDN_URL', 'https://cdn.example.com'),
    ],
];
```

#### 5. Database Query Caching
Use Laravel Query Caching:
```php
// Cache expensive queries
Product::where('active', true)
    ->rememberForever()  // Cache indefinitely until invalidated
    ->get();
```

#### 6. API Response Caching
Cache JSON API responses per user/role:
```php
// Cache personalized product list per role
$cacheKey = "products.list.{$request->user()?->role ?: 'guest'}";
return Cache::remember($cacheKey, 3600, fn() => ProductResource::collection(Product::active()->get()));
```

#### 7. Cache Invalidation Strategy
- **Settings changed** → invalidate `settings.all` cache
- **Product updated** → invalidate `products.all` and category caches
- **Order placed** → invalidate inventory/stock caches
- **Admin action** → invalidate relevant cache tags

Use cache tags for invalidation:
```php
Cache::tags(['products'])->put('products.all', $data, 3600);
Cache::tags(['products'])->flush();  // Invalidate all tagged caches
```

#### 8. Hostinger-Specific Optimizations
1. **Disable query logging in production** (`APP_DEBUG=false`)
2. **Use file cache driver** (if Redis not available on plan)
3. **Enable OPCache** in PHP settings
4. **Set appropriate cache TTLs** (Hostinger may restart services)
5. **Monitor disk usage** — large cache files can fill disk

### Configuration (`.env`)

```env
# Cache driver (file or redis)
CACHE_DRIVER=file
CACHE_DEFAULT_TTL=3600

# API caching
API_CACHE_TTL=300

# CDN configuration
CDN_URL=https://your-cdn.example.com
CLOUDFLARE_ZONE=your-zone-id
CLOUDFLARE_TOKEN=your-api-token
```

### Cloudflare Setup (5 mins)

1. Sign up at cloudflare.com (free)
2. Add domain and change nameservers
3. Go to "Caching" tab → set cache rules:
   - Rule: `(cf_cache_status eq "HIT")` → Cache for 1 year
   - Rule: `/api/*` → Cache for 5 mins (if safe)
4. Set minimum cache TTL to 1 hour
5. Enable "Always Online" for uptime protection

### Testing

```bash
# Check cache headers
curl -I https://yoursite.com/css/app.css
# Should return: Cache-Control: public, max-age=31536000

# Check cache hits on CDN
curl -I https://yoursite.com/
# Should return: cf-cache-status: HIT (Cloudflare)
```

### Performance Gains
- **Static assets:** 100ms → < 10ms (CDN delivered)
- **Product listings:** 500ms → 50ms (cached)
- **Settings:** 200ms → < 5ms (cache hit)
- **Overall TTFB:** < 500ms with CDN + caching

### Monitoring
- Monitor cache hit rates via Cloudflare/CDN dashboard
- Use Laravel Telescope (`php artisan telescope:install`) to review cache performance
- Set cache TTLs conservatively; invalidate aggressively on updates
