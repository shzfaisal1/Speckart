<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('prescription_name')->default('My Prescription');
            $table->string('power_type')->default('Single Vision'); // Single Vision, Bifocal, Progressive, Contact Lens
            $table->string('rx_file')->nullable(); // Image or PDF upload path
            
            // Right Eye (OD)
            $table->string('r_sph')->nullable();
            $table->string('r_cyl')->nullable();
            $table->string('r_axis')->nullable();
            $table->string('r_add')->nullable();
            
            // Left Eye (OS)
            $table->string('l_sph')->nullable();
            $table->string('l_cyl')->nullable();
            $table->string('l_axis')->nullable();
            $table->string('l_add')->nullable();
            
            // Pupillary Distance & Remarks
            $table->string('pd')->nullable();
            $table->text('remarks')->nullable();
            
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_prescriptions');
    }
};
