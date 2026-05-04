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
        Schema::create('geo_device', function (Blueprint $table) {
            $table->id();
            $table->string('name', 4000);
        });

        Schema::create('geo_zone', function (Blueprint $table) {
            $table->id();
            $table->string('object_type', 30);
            $table->unsignedInteger('object_id');
            $table->string('name', 4000);
            $table->string('geometry', 16);
            $table->decimal('lat_center', 12, 10);
            $table->decimal('lon_center', 12, 10);
            $table->decimal('radius', 10)->default(1);
        });

        Schema::create('geo_zone_point', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('geo_zone')->onDelete('cascade');
            $table->smallInteger('serial_index');
            $table->decimal('lat', 12, 10);
            $table->decimal('lon', 12, 10);
            $table->decimal('radius', 10);
        });

        Schema::create('geo_point', function (Blueprint $table) {
            $table->string('id', 16)->primary();
            $table->unsignedInteger('device_id');
            $table->string('track_id', 16)->nullable()->index();
            $table->unsignedInteger('zone_id')->nullable()->index();
            $table->bigInteger('ts_device');
            $table->timestamp('dt_device');
            $table->date('dt_gps');
            $table->decimal('lon', 12, 10);
            $table->decimal('lat', 12, 10);
            $table->decimal('distance', 10)->nullable();
            $table->decimal('speed')->nullable();
            $table->decimal('alt')->nullable();
            $table->decimal('bearing')->nullable();
            $table->index(['device_id', 'id']);
        });

        Schema::create('geo_segment', function (Blueprint $table) {
            $table->string('id', 16)->primary();
            $table->string('track_id', 16)->index();
            $table->string('prev_id', 16)->nullable();
            $table->string('next_id', 16)->nullable();
            $table->foreignId('device_id')->constrained('geo_device')->onDelete('cascade');
            $table->foreignId('geo_zone_id')->constrained('geo_zone')->onDelete('cascade');
            $table->date('dt_begin');
            $table->date('dt_end');
            $table->decimal('distance', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_segment');
        Schema::dropIfExists('geo_point');
        Schema::dropIfExists('geo_zone_point');
        Schema::dropIfExists('geo_zone');
        Schema::dropIfExists('geo_device');
    }
};
