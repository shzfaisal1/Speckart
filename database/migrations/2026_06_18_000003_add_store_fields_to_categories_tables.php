<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('is_active');
            $table->string('store_id')->nullable()->after('added_by');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('is_active');
            $table->string('store_id')->nullable()->after('added_by');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['added_by', 'store_id']);
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn(['added_by', 'store_id']);
        });
    }
};
