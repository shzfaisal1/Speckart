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
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->string('age')->nullable()->after('product_name');
            $table->string('occasion')->nullable()->after('age');
            $table->string('face_shape')->nullable()->after('occasion');
            $table->string('sunglass_colour')->nullable()->after('face_shape');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->dropColumn(['age', 'occasion', 'face_shape', 'sunglass_colour']);
        });
    }
};
