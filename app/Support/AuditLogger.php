<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    public static function record(string $action, ?Model $subject = null): void
    {
        Log::channel(config('logging.default'))->info('admin.mutation', [
            'action' => $action,
            'actor' => Auth::id(),
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
        ]);
    }
}
