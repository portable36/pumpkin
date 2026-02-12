<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->fullText(['name', 'description', 'short_description']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText(['name', 'description', 'short_description']);
            $table->dropForeign(['vendor_id', 'category_id', 'brand_id', 'created_by', 'updated_by']);
            $table->dropIndex(['vendor_id', 'category_id', 'is_active']);
            $table->dropColumn(['vendor_id', 'category_id', 'brand_id', 'created_by', 'updated_by']);
        });
    }
};
