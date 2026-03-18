<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('travel_time');
            $table->string('image_path_secondary')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_path_secondary']);
        });
    }
};
