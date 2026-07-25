<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmation;
use App\Mail\BookingNotification;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'zone' => 1,
            'trip_type' => 'one_way',
            'direction' => 'airport_to_hotel',
            'first_name' => 'Maria',
            'last_name' => 'Lopez',
            'email' => 'maria@example.com',
            'phone' => '+521234567890',
            'hotel' => 'Hyatt Ziva Los Cabos',
            'pax' => 2,
            'arrival_date' => now()->addDay()->toDateString(),
            'hp_field' => '',
            'form_ts' => now()->subSeconds(10)->timestamp,
        ], $overrides);
    }

    public function test_valid_booking_is_created_and_emails_sent(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/bookings', $this->payload());

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseCount('bookings', 1);
        $this->assertStringStartsWith('GC-', Booking::first()->order_number);

        Mail::assertSent(BookingConfirmation::class);
        Mail::assertSent(BookingNotification::class);
    }

    public function test_honeypot_filled_is_silently_rejected(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/bookings', $this->payload(['hp_field' => 'http://spam.example']));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseCount('bookings', 0);
        Mail::assertNothingSent();
    }

    public function test_too_fast_submission_is_silently_rejected(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/bookings', $this->payload(['form_ts' => now()->timestamp]));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseCount('bookings', 0);
        Mail::assertNothingSent();
    }

    public function test_invalid_payload_returns_422(): void
    {
        $response = $this->postJson('/api/bookings', $this->payload(['email' => 'not-an-email']));

        $response->assertStatus(422);
        $this->assertDatabaseCount('bookings', 0);
    }
}
