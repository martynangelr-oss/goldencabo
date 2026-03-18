<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            // Zone
            $table->unsignedTinyInteger('zone');
            $table->string('zone_name', 100);
            // Service type
            $table->enum('trip_type', ['one_way', 'round_trip']);
            $table->enum('direction', ['airport_to_hotel', 'hotel_to_airport']);
            $table->decimal('price_usd', 8, 2);
            // Passenger
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 191);
            $table->string('phone', 30);
            $table->string('hotel', 191);
            $table->unsignedTinyInteger('pax')->default(1);
            // Flight info
            $table->string('arrival_flight', 20)->nullable();
            $table->string('departure_flight', 20)->nullable();
            $table->date('arrival_date');
            $table->time('arrival_time')->nullable();
            // Status
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('confirmed');
            $table->text('notes')->nullable();
            $table->boolean('voucher_sent')->default(false);
            $table->timestamps();

            $table->index('email');
            $table->index('status');
            $table->index('arrival_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
