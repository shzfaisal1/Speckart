<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create wishlists table for website front-end.
     * Linked to the users table (web_customers who sign in via OTP).
     */
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');          // FK → users.id
            $table->unsignedBigInteger('product_id');       // FK → tbl_product_code.product_id

            $table->timestamps();

            // Prevent duplicate entries for same user+product
            $table->unique(['user_id', 'product_id']);

            // Foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
