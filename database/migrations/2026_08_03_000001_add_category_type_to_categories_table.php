<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add category_type column to categories table.
     *
     * Values:
     *   frame    → Eyeglasses / Frames (default)
     *   lens     → Contact Lenses
     *   sunglass → Sunglasses
     *
     * This single column drives all dynamic field toggling in the
     * B2C product creation form and the customer-facing detail page.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('category_type', ['frame', 'lens', 'sunglass'])
                  ->default('frame')
                  ->after('allowed_filters')
                  ->comment('Controls which product fields are shown: frame|lens|sunglass');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('category_type');
        });
    }
};
