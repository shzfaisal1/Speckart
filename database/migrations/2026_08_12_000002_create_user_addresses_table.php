<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('address_type', 50)->default('Home');
            $table->string('full_name');
            $table->string('phone', 20);
            $table->string('pincode', 10);
            $table->text('house_no');
            $table->text('road_area');
            $table->string('landmark')->nullable();
            $table->string('email')->nullable();
            $table->text('full_address');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
