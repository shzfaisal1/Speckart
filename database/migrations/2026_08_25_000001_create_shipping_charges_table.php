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
        if (!Schema::hasTable('tbl_shipping_charges')) {
            Schema::create('tbl_shipping_charges', function (Blueprint $table) {
                $table->id();
                $table->string('pincode', 10)->unique();
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->tinyInteger('is_cod_available')->default(1)->comment('1 = COD Available, 0 = Prepaid Only');
                $table->tinyInteger('status')->default(1)->comment('1 = Enabled / Serviceable, 0 = Disabled / Unserviceable');
                $table->timestamps();
                
                $table->index(['pincode', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_shipping_charges');
    }
};
