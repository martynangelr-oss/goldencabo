<?php

namespace App\Http\Middleware;

use App\Services\SiteSettingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function __construct(private SiteSettingService $settings) {}

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $minutes = max(5, min(480, $this->settings->sessionTimeoutMinutes()));
            $seconds = $minutes * 60;

            $lastActivity = $request->session()->get('_last_activity');

            if ($lastActivity !== null && (time() - $lastActivity) > $seconds) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Tu sesión expiró por inactividad. Inicia sesión nuevamente.']);
            }

            $request->session()->put('_last_activity', time());
        }

        return $next($request);
    }
}
