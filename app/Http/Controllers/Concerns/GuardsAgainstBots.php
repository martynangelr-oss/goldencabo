<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait GuardsAgainstBots
{
    /**
     * Honeypot + timing check. Real visitors never fill the decoy field and
     * can't submit faster than MIN_FILL_SECONDS because the timestamp is
     * stamped server-side when the page is rendered.
     */
    protected function looksLikeBot(Request $request, string $context): bool
    {
        if (filled($request->input('hp_field'))) {
            Log::warning("Bot guard [{$context}]: honeypot filled", [
                'ip' => $request->ip(),
            ]);

            return true;
        }

        $formTs = (int) $request->input('form_ts', 0);
        $elapsed = time() - $formTs;

        if ($formTs <= 0 || $elapsed < 2 || $elapsed > 21600) {
            Log::warning("Bot guard [{$context}]: timing check failed", [
                'ip' => $request->ip(),
                'elapsed' => $elapsed,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Bots get a fake "success" so scripts don't adapt/retry with new tactics.
     */
    protected function fakeSuccess()
    {
        return response()->json(['success' => true]);
    }
}
