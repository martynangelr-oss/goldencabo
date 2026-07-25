<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthTokenController extends Controller
{
    public const ALL_ABILITIES = [
        'bookings', 'contacts',
        'cms', 'vehicles', 'tours', 'zones', 'carousel', 'gallery', 'sections',
        'settings',
    ];

    public function issue(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password) || ! $user->is_active) {
            Log::warning('API token issuance failed', ['email' => $credentials['email'], 'ip' => $request->ip()]);

            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        $abilities = $user->isAdmin()
            ? self::ALL_ABILITIES
            : array_keys(array_filter($user->permissions ?? []));

        $expiresAt = config('sanctum.expiration') ? now()->addMinutes(config('sanctum.expiration')) : null;
        $token = $user->createToken('api-'.$user->id, $abilities, $expiresAt);

        return response()->json([
            'token' => $token->plainTextToken,
            'abilities' => $abilities,
            'expires_at' => $token->accessToken->expires_at,
        ]);
    }

    public function revoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token revocado.']);
    }
}
