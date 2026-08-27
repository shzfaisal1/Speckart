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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('full_name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->text('address_line_1')->nullable()->after('pincode');
            $table->text('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('road_area');
            $table->string('state')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'address_line_1', 'address_line_2', 'city', 'state']);
        });
    }
};
