## Search Indexing Strategy

### Recommendation: Meilisearch (Best for Hostinger)

**Why Meilisearch?**
- Lightweight standalone binary (no Node.js required)
- Fast full-text search (< 100ms)
- Can run on shared hosting via Docker or standalone binary
- RESTful API — simple integration with Laravel
- Free tier on cloud (meili.com) or self-hosted

### Option 1: Meilisearch Cloud (Recommended for Hostinger)

**Setup:**
1. Sign up at `meili.com`
2. Create project and get API key
3. Install Laravel Scout + Meilisearch driver:

```bash
composer require laravel/scout meilisearch/meilisearch-php
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

**Configuration** (`.env`):
```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=https://your-project.meilisearch.io
MEILISEARCH_KEY=your-api-key
```

**Model Setup**:
```php
use Laravel\Scout\Searchable;

class Product extends Model {
    use Searchable;

    public function toSearchableArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category->name,
            'vendor' => $this->vendor->name,
        ];
    }
}
```

**Indexing:**
```bash
php artisan scout:import "App\Models\Product"
```

**Search Endpoint** (API):
```php
Route::get('/api/search/products', function (Request $request) {
    $query = $request->query('q', '');
    return Product::search($query)->get();
});
```

### Option 2: Algolia (High-performance, SaaS)

**Pros:**
- Managed, enterprise-grade search
- Fast, scalable
- Great for large catalogs

**Cons:**
- Paid (starts ~$45/month)
- Requires Scout integration

### Implementation Steps (Meilisearch)

1. **Install packages:**
```bash
composer require laravel/scout meilisearch/meilisearch-php
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

2. **Update models** (Product, Vendor, Category):
```php
use Laravel\Scout\Searchable;

class Product extends Model {
    use Searchable;
}
```

3. **Create search API route:**
```php
Route::get('/api/search', function (Request $request) {
    $query = $request->input('q');
    $type = $request->input('type', 'products');

    if ($type === 'products') {
        return Product::search($query)->limit(20)->get();
    } elseif ($type === 'vendors') {
        return Vendor::search($query)->limit(10)->get();
    }

    return [];
});
```

4. **Add search to frontend** (React/Vue):
```javascript
async function search(q) {
    const res = await fetch(`/api/search?q=${encodeURIComponent(q)}&type=products`);
    return res.json();
}
```

5. **Sync indexes on model changes:**
```php
// Automatically sync to Meilisearch on save/delete
Product::observe(ProductObserver::class);

class ProductObserver {
    public function saved(Product $product) {
        $product->searchable();  // Index to Meilisearch
    }

    public function deleted(Product $product) {
        $product->unsearchable();  // Remove from index
    }
}
```

### Search Features

**Full-text search:**
- Multi-field search (name, description, vendor)
- Typo tolerance
- Synonym support
- Ranking/boosting

**Filters:**
```php
Product::search($query)
    ->where('price', '>=', 100)
    ->where('category_id', '=', 5)
    ->get();
```

**Sorting:**
```php
Product::search($query)
    ->orderBy('price', 'desc')
    ->orderBy('rating', 'desc')
    ->get();
```

### Hostinger Compatibility

**On Shared Hosting:**
- Use Meilisearch Cloud (no local resource burden)
- Cron job to sync indexes daily (if offline sync needed):
  ```bash
  * * * * * php /path/to/artisan scout:import "App\Models\Product"
  ```
- Cache search results with Laravel cache (file or Redis if available)

### Fallback: Database Search (If no external service)

```php
// Simple LIKE search as fallback
Route::get('/api/search', function (Request $request) {
    $query = $request->input('q');
    return Product::where('name', 'like', "%{$query}%")
        ->orWhere('description', 'like', "%{$query}%")
        ->limit(20)
        ->get();
});
```

**Performance:** OK for < 5K products; slow beyond that.

### Estimated Costs

- **Meilisearch Cloud:** Free ($0) to $29/month
- **Algolia:** $45/month and up
- **Self-hosted Meilisearch:** $0 (hosting cost only)
- **Database search:** $0 (no external cost, slow)

**Recommendation:** Start with Meilisearch Cloud free tier; upgrade if needed.
