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
        Schema::create('home_eye_test_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id', 50)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 100);
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->string('pincode', 10);
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->text('address');
            $table->string('landmark', 255)->nullable();
            $table->date('appointment_date');
            $table->string('time_slot', 50);
            $table->integer('people_count')->default(1);
            $table->decimal('fee', 10, 2)->default(99.00);
            $table->string('payment_method', 50)->default('pay_on_visit');
            $table->string('payment_status', 50)->default('pending');
            $table->string('status', 50)->default('confirmed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_eye_test_appointments');
    }
};
