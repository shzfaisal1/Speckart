<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add default_lens_package_id to product_type_masters.
     *
     * This column links a ProductType (e.g. "Zero Power") to a specific
     * LensPackage so the frontend can auto-bundle the correct Blue-Cut /
     * Zero-Power lens package when the customer clicks BUY NOW without
     * going through the 3-step lens selection drawer.
     *
     * Without this column the $ptype->default_lens_package_id is always
     * null, cart receives lens_package_id = null, and the item is stored
     * as "Basic / Frame Only" with no lens — breaking the Zero Power flow.
     */
    public function up(): void
    {
        Schema::table('product_type_masters', function (Blueprint $table) {
            // Nullable FK to lens_packages.id (new admin lens system)
            // Constrained as nullable so existing rows are unaffected.
            $table->unsignedBigInteger('default_lens_package_id')
                  ->nullable()
                  ->after('default_powers')
                  ->comment('Auto-bundled lens package for zero-power / non-powered flows');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('product_type_masters', function (Blueprint $table) {
            $table->dropColumn('default_lens_package_id');
        });
    }
};
