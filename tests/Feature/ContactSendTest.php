<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactSendTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@gmail.com',
            'phone' => '+521234567890',
            'service' => 'Traslado Aeropuerto',
            'message' => 'Quisiera información sobre traslados.',
            'hp_field' => '',
            'form_ts' => now()->subSeconds(10)->timestamp,
        ], $overrides);
    }

    public function test_valid_contact_is_stored(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->payload());

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseCount('contacts', 1);
        $this->assertDatabaseHas('contacts', ['email' => 'juan@gmail.com']);
    }

    public function test_honeypot_filled_is_silently_rejected(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->payload(['hp_field' => 'http://spam.example']));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseCount('contacts', 0);
        Mail::assertNothingSent();
    }

    public function test_too_fast_submission_is_silently_rejected(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->payload(['form_ts' => now()->timestamp]));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseCount('contacts', 0);
        Mail::assertNothingSent();
    }

    public function test_invalid_payload_returns_422(): void
    {
        $response = $this->postJson('/api/contact', $this->payload(['email' => 'not-an-email']));

        $response->assertStatus(422);
        $this->assertDatabaseCount('contacts', 0);
    }
}
