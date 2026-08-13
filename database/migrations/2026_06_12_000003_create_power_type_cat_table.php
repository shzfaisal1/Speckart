<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('power_type_cat')) {
            Schema::create('power_type_cat', function (Blueprint $table) {
                $table->integer('id', true); // Custom integer primary key
                $table->string('images', 255)->nullable();
                $table->string('description', 255)->nullable();
                $table->string('tag', 255)->nullable();
                $table->string('is_active', 255)->default('1');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_type_cat');
    }
};
