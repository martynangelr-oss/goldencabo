<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->string('name_en', 191)->nullable()->after('name');
            $table->text('route_description_en')->nullable()->after('route_description');
            $table->json('destinations_en')->nullable()->after('destinations');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'route_description_en', 'destinations_en']);
        });
    }
};
