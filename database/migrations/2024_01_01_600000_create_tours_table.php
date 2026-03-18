<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('duration', 50)->nullable();
            $table->text('route_description')->nullable();
            $table->json('destinations')->nullable();
            $table->decimal('price_usd', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('image_path', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
