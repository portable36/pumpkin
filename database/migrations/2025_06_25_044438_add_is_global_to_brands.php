<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $indexName = null;

        // Detect index name automatically
        $indexes = \DB::select('SHOW INDEX FROM brands');
        foreach ($indexes as $index) {
            if ($index->Column_name === 'name' && $index->Non_unique == 0) {
                $indexName = $index->Key_name;
            }
        }

        Schema::table('brands', function (Blueprint $table) use ($indexName) {
            if ($indexName) {
                $table->dropUnique($indexName);
            }

            if (! Schema::hasColumn('brands', 'is_global')) {
                $table->boolean('is_global')->default(true)->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                // Re-add unique constraint (will fail silently if exists)
                try {
                    $table->unique('name');
                } catch (\Exception $e) {
                    // Unique already exists, ignore
                }
            });

            // Drop column if exists
            if (Schema::hasColumn('brands', 'is_global')) {
                Schema::table('brands', function (Blueprint $table) {
                    $table->dropColumn('is_global');
                });
            }
        }
    }
};
