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
        Schema::create('fleet_type', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name', 200)->unique();
            $table->timestamps();
        });

        Schema::table('fleets', function (Blueprint $table) {
            $table->foreignUlid('fleet_type_id')
                ->nullable()
                ->after('customer_id')
                ->constrained('fleet_type')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fleet_type_id');
        });

        Schema::dropIfExists('fleet_type');
    }
};
