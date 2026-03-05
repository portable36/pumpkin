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
        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table) {
                // Add column only if not exists
                if (! Schema::hasColumn('attributes', 'is_global')) {
                    $table->boolean('is_global')->default(true)->after('is_filterable');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table) {
                // Drop column if exists
                if (Schema::hasColumn('attributes', 'is_global')) {
                    $table->dropColumn('is_global');
                }
            });
        }
    }
};
