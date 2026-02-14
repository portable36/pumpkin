<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enhanced settings table for dynamic platform configuration
     * Allows all platform features to be controlled from admin dashboard
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique()->index();
                $table->longText('value')->nullable();
                $table->string('type')->default('string'); // string, boolean, integer, float, array, json
                $table->string('category')->index(); // shipping, payment, features, etc
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } else {
            // Add missing columns to existing settings table
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'type')) {
                    $table->string('type')->default('string')->after('value');
                }
                if (!Schema::hasColumn('settings', 'category')) {
                    $table->string('category')->nullable()->after('type')->index();
                }
                if (!Schema::hasColumn('settings', 'description')) {
                    $table->text('description')->nullable()->after('category');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
