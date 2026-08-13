<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the persistent shopping carts table for B2C users & guest visitors.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // ── User / Guest Identification ────────────────────────────────
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('FK to users.id; null for guest users');
            $table->string('session_id', 100)->nullable()->index()->comment('Session or cookie identifier for guest cart tracking');

            // ── Applied Discount / Offer Info ──────────────────────────────
            $table->unsignedBigInteger('offer_id')->nullable()->index()->comment('FK to offers.id if coupon/offer applied');
            $table->string('coupon_code', 100)->nullable()->comment('Applied coupon code');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Calculated discount amount');

            $table->timestamps();

            // ── Foreign Key Constraints ────────────────────────────────────
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
