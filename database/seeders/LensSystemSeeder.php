<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PDO;

class LensSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $refPdo = new PDO("mysql:host=127.0.0.1;dbname=speckarts", "root", "");
            $refPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Seed power_type_cat
            $stmt = $refPdo->query("SELECT * FROM power_type_cat");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('power_type_cat')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_package_tags
            $stmt = $refPdo->query("SELECT * FROM lens_package_tags");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_package_tags')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_benefits
            $stmt = $refPdo->query("SELECT * FROM lens_benefits");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_benefits')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed coupons
            $stmt = $refPdo->query("SELECT * FROM coupons");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('coupons')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_packages
            $stmt = $refPdo->query("SELECT * FROM lens_packages");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_packages')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_package_tag_map
            $stmt = $refPdo->query("SELECT * FROM lens_package_tag_map");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_package_tag_map')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_package_benefits
            $stmt = $refPdo->query("SELECT * FROM lens_package_benefits");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_package_benefits')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_package_badges
            $stmt = $refPdo->query("SELECT * FROM lens_package_badges");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_package_badges')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_package_coupons
            $stmt = $refPdo->query("SELECT * FROM lens_package_coupons");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_package_coupons')->updateOrInsert(['id' => $row['id']], $row);
            }

            // Seed lens_package_power_types
            $stmt = $refPdo->query("SELECT * FROM lens_package_power_types");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                DB::table('lens_package_power_types')->updateOrInsert(['id' => $row['id']], $row);
            }

            echo "Lens System data seeded successfully from reference database!\n";

        } catch (\Exception $e) {
            echo "Error seeding data: " . $e->getMessage() . "\n";
        }
    }
}
