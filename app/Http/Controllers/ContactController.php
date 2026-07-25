<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsAgainstBots;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    use GuardsAgainstBots;

    public function __construct(private ContactService $contacts) {}

    public function send(Request $request)
    {
        if ($this->looksLikeBot($request, 'contact')) {
            return $this->fakeSuccess();
        }

        // Server-side rate limit: 15 contact attempts per IP per hour (backstop for bots rotating phone numbers)
        $ipKey = 'contact-ip:'.$request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 15)) {
            return $this->fakeSuccess();
        }
        RateLimiter::hit($ipKey, 3600);

        $validated = $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'nullable|string|max:80',
            'email' => ['required', 'string', 'max:255', 'email:rfc,dns', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
            'phone' => ['required', 'string', 'regex:/^\+\d{1,3}\d{10}$/'],
            'service' => 'nullable|string|max:80',
            'message' => 'required|string|max:1000',
        ], [
            'email.regex' => 'Introduce un correo electrónico válido (usuario@dominio.com).',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono debe tener 10 dígitos con código de país (ej: +521234567890).',
        ]);

        // Server-side rate limit: 5 contact attempts per phone per hour
        $phoneKey = 'contact:'.preg_replace('/\D/', '', $validated['phone']);
        if (RateLimiter::tooManyAttempts($phoneKey, 5)) {
            $seconds = RateLimiter::availableIn($phoneKey);

            return response()->json([
                'success' => false,
                'message' => 'Ha superado el límite de intentos. Intente en '.ceil($seconds / 60).' minuto(s).',
            ], 429);
        }
        RateLimiter::hit($phoneKey, 3600);

        $contact = $this->contacts->create($validated);

        // Simple notification to admin
        try {
            Mail::raw(
                "Nuevo mensaje de contacto de {$contact->first_name} {$contact->last_name}\n".
                "Email: {$contact->email}\nServicio: {$contact->service}\n\n{$contact->message}",
                function ($m) use ($contact) {
                    $m->to(config('app.admin_email', 'informes@goldencabotransportation.com'))
                        ->subject("Contacto Web — {$contact->first_name} {$contact->last_name}");
                }
            );
        } catch (\Exception $e) {
            \Log::warning('Contact email failed: '.$e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
