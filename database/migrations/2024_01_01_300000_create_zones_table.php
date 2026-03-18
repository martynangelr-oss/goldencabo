<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('number')->unique();
            $table->string('name', 100);
            $table->string('area', 200)->nullable();
            $table->decimal('round_trip_price', 8, 2)->default(0);
            $table->decimal('one_way_price', 8, 2)->default(0);
            $table->string('travel_time', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
