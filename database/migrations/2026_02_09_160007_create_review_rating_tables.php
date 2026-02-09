<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product Reviews
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained();
            $table->integer('rating')->unsigned(); // 1-5
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Review Helpful Votes
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_helpful'); // true for helpful, false for not helpful
            $table->timestamps();
            
            $table->unique(['product_review_id', 'user_id']);
        });

        // Vendor Reviews/Ratings
        Schema::create('vendor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained();
            $table->integer('rating')->unsigned(); // 1-5
            $table->integer('product_quality_rating')->unsigned()->nullable();
            $table->integer('shipping_rating')->unsigned()->nullable();
            $table->integer('communication_rating')->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Abuse Reports
        Schema::create('review_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reviewable_type'); // product_review, vendor_review
            $table->unsignedBigInteger('reviewable_id');
            $table->foreignId('reported_by')->constrained('users');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, resolved, dismissed
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['reviewable_type', 'reviewable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_reports');
        Schema::dropIfExists('vendor_reviews');
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('product_reviews');
    }
};
