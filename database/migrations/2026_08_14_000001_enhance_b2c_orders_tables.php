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
        // 1. Enhance b2c_orders table if not already enhanced
        if (Schema::hasTable('b2c_orders')) {
            Schema::table('b2c_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('b2c_orders', 'rx_verification_status')) {
                    $table->enum('rx_verification_status', [
                        'not_required',
                        'pending_upload',
                        'pending_review',
                        'approved',
                        'clarification_needed',
                        'rejected'
                    ])->default('not_required')->after('order_status');
                }
                if (!Schema::hasColumn('b2c_orders', 'is_rx_required')) {
                    $table->boolean('is_rx_required')->default(false)->after('rx_verification_status');
                }
                if (!Schema::hasColumn('b2c_orders', 'delivery_method')) {
                    $table->enum('delivery_method', ['standard', 'express', 'store_pickup'])->default('standard')->after('payment_status');
                }
                if (!Schema::hasColumn('b2c_orders', 'frame_total')) {
                    $table->decimal('frame_total', 10, 2)->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('b2c_orders', 'lens_total')) {
                    $table->decimal('lens_total', 10, 2)->default(0)->after('frame_total');
                }
                if (!Schema::hasColumn('b2c_orders', 'assigned_lab_id')) {
                    $table->unsignedBigInteger('assigned_lab_id')->nullable()->after('tracking_url');
                }
                if (!Schema::hasColumn('b2c_orders', 'lab_status')) {
                    $table->enum('lab_status', [
                        'pending',
                        'assigned',
                        'cutting',
                        'fitting',
                        'qc_passed',
                        'qc_failed',
                        'completed'
                    ])->default('pending')->after('assigned_lab_id');
                }
                if (!Schema::hasColumn('b2c_orders', 'lab_job_number')) {
                    $table->string('lab_job_number', 50)->nullable()->after('lab_status');
                }
                if (!Schema::hasColumn('b2c_orders', 'lab_notes')) {
                    $table->text('lab_notes')->nullable()->after('lab_job_number');
                }
                if (!Schema::hasColumn('b2c_orders', 'lab_assigned_at')) {
                    $table->timestamp('lab_assigned_at')->nullable()->after('lab_notes');
                }
                if (!Schema::hasColumn('b2c_orders', 'lab_completed_at')) {
                    $table->timestamp('lab_completed_at')->nullable()->after('lab_assigned_at');
                }
                if (!Schema::hasColumn('b2c_orders', 'verified_by')) {
                    $table->unsignedBigInteger('verified_by')->nullable()->after('lab_completed_at');
                }
                if (!Schema::hasColumn('b2c_orders', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }
                if (!Schema::hasColumn('b2c_orders', 'optometrist_notes')) {
                    $table->text('optometrist_notes')->nullable()->after('verified_at');
                }
                if (!Schema::hasColumn('b2c_orders', 'return_reason')) {
                    $table->string('return_reason')->nullable()->after('admin_note');
                }
                if (!Schema::hasColumn('b2c_orders', 'exchange_type')) {
                    $table->string('exchange_type')->nullable()->after('return_reason');
                }
                if (!Schema::hasColumn('b2c_orders', 'warranty_status')) {
                    $table->string('warranty_status')->nullable()->after('exchange_type');
                }
            });
        }

        // 2. Enhance b2c_order_items table
        if (Schema::hasTable('b2c_order_items')) {
            Schema::table('b2c_order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('b2c_order_items', 'prescription_source')) {
                    $table->string('prescription_source', 50)->nullable()->default('manual_entry')->after('product_type');
                }
                if (!Schema::hasColumn('b2c_order_items', 'prescription_file_url')) {
                    $table->string('prescription_file_url', 255)->nullable()->after('prescription_source');
                }
                if (!Schema::hasColumn('b2c_order_items', 'prescription_type')) {
                    $table->string('prescription_type', 50)->nullable()->after('prescription_file_url');
                }
                if (!Schema::hasColumn('b2c_order_items', 'lens_type')) {
                    $table->string('lens_type', 100)->nullable()->after('lens_package_id');
                }
                if (!Schema::hasColumn('b2c_order_items', 'lens_coating')) {
                    $table->string('lens_coating', 150)->nullable()->after('lens_type');
                }
                if (!Schema::hasColumn('b2c_order_items', 'lens_index')) {
                    $table->string('lens_index', 50)->nullable()->after('lens_coating');
                }
                if (!Schema::hasColumn('b2c_order_items', 'frame_color')) {
                    $table->string('frame_color', 50)->nullable()->after('product_name');
                }
                if (!Schema::hasColumn('b2c_order_items', 'frame_size')) {
                    $table->string('frame_size', 50)->nullable()->after('frame_color');
                }
                if (!Schema::hasColumn('b2c_order_items', 'frame_sku')) {
                    $table->string('frame_sku', 100)->nullable()->after('frame_size');
                }
                if (!Schema::hasColumn('b2c_order_items', 'GL_EYE_RPD')) {
                    $table->decimal('GL_EYE_RPD', 6, 2)->nullable()->comment('Right Monocular PD')->after('GL_EYE_totalPD');
                }
                if (!Schema::hasColumn('b2c_order_items', 'GL_EYE_LPD')) {
                    $table->decimal('GL_EYE_LPD', 6, 2)->nullable()->comment('Left Monocular PD')->after('GL_EYE_RPD');
                }
                if (!Schema::hasColumn('b2c_order_items', 'fitting_height')) {
                    $table->decimal('fitting_height', 6, 2)->nullable()->comment('Fitting Height for Progressives')->after('GL_EYE_LPD');
                }
            });
        }

        // 3. Create b2c_order_logs table
        if (!Schema::hasTable('b2c_order_logs')) {
            Schema::create('b2c_order_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->comment('Admin or Optometrist who made change');
                $table->string('action', 100);
                $table->string('from_status', 100)->nullable();
                $table->string('to_status', 100)->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('order_id')->references('id')->on('b2c_orders')->onDelete('cascade');
            });
        }

        // 4. Create b2c_order_notes table
        if (!Schema::hasTable('b2c_order_notes')) {
            Schema::create('b2c_order_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->comment('Admin author');
                $table->text('note');
                $table->boolean('is_customer_visible')->default(false);
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('b2c_orders')->onDelete('cascade');
            });
        }

        // 5. Create b2c_order_returns table
        if (!Schema::hasTable('b2c_order_returns')) {
            Schema::create('b2c_order_returns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('order_item_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->enum('return_type', ['refund', 'replacement', 'lens_remake'])->default('refund');
                $table->enum('reason', ['power_mismatch', 'frame_damage', 'fit_issue', 'changed_mind', 'other'])->default('other');
                $table->enum('exchange_type', ['same_product', 'different_power', 'different_frame', 'none'])->default('none');
                $table->enum('status', [
                    'requested',
                    'approved',
                    'rejected',
                    'item_received',
                    'remake_in_progress',
                    'completed'
                ])->default('requested');
                $table->text('admin_notes')->nullable();
                $table->boolean('warranty_claim')->default(false);
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('b2c_orders')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2c_order_returns');
        Schema::dropIfExists('b2c_order_notes');
        Schema::dropIfExists('b2c_order_logs');
    }
};
