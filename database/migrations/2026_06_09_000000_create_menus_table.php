<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('parent_id')
                ->nullable()
                ->constrained('menus')
                ->nullOnDelete();
            $table->string('name');
            $table->string('section')->default('Main Menu');
            $table->string('icon')->default('circle');
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('active_pattern')->nullable();
            $table->string('target', 10)->default('_self');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'section', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
