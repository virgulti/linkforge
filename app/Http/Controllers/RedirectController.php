<?php

namespace App\Http\Controllers;

use App\Jobs\RecordClick;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $link = Link::query()
            ->where('short_code', $code)
            ->active()
            ->firstOrFail();

        RecordClick::dispatch(
            linkId: $link->id,
            clickedAt: now()->toDateTimeString(),
            referrer: substr((string) $request->headers->get('referer'), 0, 2048) ?: null,
            userAgent: substr((string) $request->userAgent(), 0, 512) ?: null,
            ipHash: $request->ip() ? hash('sha256', $request->ip().config('app.key')) : null,
        );

        // 302: un 301 verrebbe cachato dal browser e i click successivi
        // salterebbero il server, perdendo il tracking.
        return redirect()->away($link->original_url, 302);
    }
}
