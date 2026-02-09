<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tags
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Attributes (for product variants)
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('select'); // select, color, radio
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_filterable')->default(true);
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->string('color_code')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Update products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('vendor_id')->constrained()->nullOnDelete();
            $table->string('sku')->unique()->after('name');
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            $table->decimal('selling_price', 10, 2)->after('cost_price');
            $table->string('thumbnail')->nullable()->after('description');
            $table->json('images')->nullable()->after('thumbnail');
            $table->json('videos')->nullable()->after('images');
            $table->string('meta_title')->nullable()->after('slug');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            $table->integer('min_order_quantity')->default(1)->after('stock');
            $table->integer('max_order_quantity')->nullable()->after('min_order_quantity');
            $table->integer('views_count')->default(0)->after('is_active');
            $table->decimal('weight', 8, 2)->nullable()->after('views_count');
            $table->string('weight_unit')->default('kg')->after('weight');
            $table->softDeletes();
            
            $table->dropColumn('price');
        });

        // Product Variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Product Tags
        Schema::create('product_tag', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->primary(['product_id', 'tag_id']);
        });

        // Product Attributes (for products without variants)
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('product_variant_attributes');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
        
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('description');
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id', 'sku', 'barcode', 'cost_price', 'selling_price',
                'thumbnail', 'images', 'videos', 'meta_title', 'meta_description',
                'meta_keywords', 'min_order_quantity', 'max_order_quantity',
                'views_count', 'weight', 'weight_unit', 'deleted_at'
            ]);
        });
    }
};
