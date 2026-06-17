<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->boolean('has_fuel_sensor')->default(false)->after('device_name');
            $table->date('fuel_sensor_installed_at')->nullable()->after('has_fuel_sensor');
        });
    }

    public function down(): void
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->dropColumn([
                'has_fuel_sensor',
                'fuel_sensor_installed_at',
            ]);
        });
    }
};
