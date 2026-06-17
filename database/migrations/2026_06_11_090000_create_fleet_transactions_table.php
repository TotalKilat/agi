<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('fleet_id')->constrained()->restrictOnDelete();
            $table->date('transaction_date');
            $table->string('vehicle_name_snapshot', 200);
            $table->decimal('odometer_km', 12, 2)->default(0);
            $table->decimal('initial_volume_l', 12, 2)->nullable();
            $table->decimal('final_volume_l', 12, 2)->nullable();
            $table->decimal('usage_l', 12, 2)->default(0);
            $table->decimal('cost_rp', 16, 2)->default(0);
            $table->decimal('idle_usage_l', 12, 2)->nullable();
            $table->decimal('km_per_l', 12, 4)->nullable();
            $table->decimal('l_per_km', 12, 4)->nullable();
            $table->decimal('cost_per_km', 16, 4)->nullable();
            $table->decimal('refuel_l', 12, 3)->nullable();
            $table->unsignedSmallInteger('refuel_times')->nullable();
            $table->unsignedInteger('running_duration_seconds')->nullable();
            $table->unsignedInteger('idle_duration_seconds')->nullable();
            $table->unsignedInteger('stop_duration_seconds')->nullable();
            $table->string('source_filename', 255)->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['fleet_id', 'transaction_date'], 'fleet_transactions_fleet_date_unique');
            $table->index('transaction_date');
            $table->index('vehicle_name_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_transactions');
    }
};
