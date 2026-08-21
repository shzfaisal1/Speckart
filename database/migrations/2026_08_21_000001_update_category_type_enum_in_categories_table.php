<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('categories')) {
            if (!Schema::hasColumn('categories', 'category_type')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->enum('category_type', ['frame', 'sunglass', 'lens', 'solution', 'accessory', 'glass'])
                          ->default('frame')
                          ->after('allowed_filters')
                          ->comment('Controls product fields: frame|sunglass|lens|solution|accessory|glass');
                });
            } else {
                DB::statement("ALTER TABLE categories MODIFY COLUMN category_type ENUM('frame', 'sunglass', 'lens', 'solution', 'accessory', 'glass') DEFAULT 'frame' COMMENT 'Controls product fields: frame|sunglass|lens|solution|accessory|glass'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'category_type')) {
            DB::statement("ALTER TABLE categories MODIFY COLUMN category_type ENUM('frame', 'lens', 'sunglass') DEFAULT 'frame'");
        }
    }
};
