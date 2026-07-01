<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use App\Services\ShortCodeGenerator;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function __construct(
        private readonly ShortCodeGenerator $shortCodeGenerator
    ) {
    }

    public function index(Request $request)
    {
        $recentLinkIds = $request->session()->get('recent_links', []);
        
        $recentLinks = Link::withCount('clicks')
            ->whereIn('id', $recentLinkIds)
            ->orderByDesc('created_at')
            ->get();
            
        return view('welcome', compact('recentLinks'));
    }

    public function clickCounts(Request $request)
    {
        $recentLinkIds = $request->session()->get('recent_links', []);

        $counts = Link::withCount('clicks')
            ->whereIn('id', $recentLinkIds)
            ->get(['id'])
            ->pluck('clicks_count', 'id');

        return response()->json($counts);
    }

    public function store(StoreLinkRequest $request)
    {
        try {
            $link = $this->shortCodeGenerator->createLink(
                $request->url,
                auth()->id(),
                $request->custom_code,
                $request->expires_at
            );
            
            session()->push('recent_links', $link->id);
            
            return redirect()->back()
                ->with('success', [
                    'original_url' => $request->url,
                    'short_code' => $link->short_code
                ]);
        } catch (\App\Exceptions\ShortCodeException $e) {
            return redirect()->back()
                ->withErrors(['custom_code' => $e->getMessage()])
                ->withInput();
        }
    }
}
