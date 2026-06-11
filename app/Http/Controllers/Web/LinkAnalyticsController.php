<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkAnalyticsController extends Controller
{
    /**
     * Display analytics for a specific shortened link.
     */
    public function show(string $code): View
    {
        $link = Link::where('short_code', $code)
            ->withCount('clicks')
            ->firstOrFail();

        // 1. Total clicks
        $totalClicks = $link->clicks_count;

        // 2. Click history for the last 30 days (filling in missing days with 0)
        $startDate = now()->subDays(29)->startOfDay();
        $rawClicks = $link->clicks()
            ->where('clicked_at', '>=', $startDate)
            ->selectRaw("strftime('%Y-%m-%d', clicked_at) as date, count(*) as count")
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $clicksPerDay = [];
        for ($i = 29; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $clicksPerDay[$dateStr] = $rawClicks[$dateStr] ?? 0;
        }

        // 3. Top Referrers
        $topReferrers = $link->clicks()
            ->selectRaw("COALESCE(NULLIF(referrer, ''), 'Direct / Other') as referrer, count(*) as count")
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // 4. Device Breakdown
        $clicks = $link->clicks()->select('user_agent')->get();
        $devices = ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0, 'Unknown' => 0];

        foreach ($clicks as $click) {
            $ua = $click->user_agent;
            if (empty($ua)) {
                $devices['Unknown']++;
                continue;
            }
            if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
                $devices['Tablet']++;
            } elseif (preg_match('/(mobi|ipod|phone|blackberry|opera mini|fennec|minimo|symbian|psp|android)/i', $ua)) {
                $devices['Mobile']++;
            } else {
                $devices['Desktop']++;
            }
        }

        return view('analytics', compact(
            'link',
            'totalClicks',
            'clicksPerDay',
            'topReferrers',
            'devices'
        ));
    }
}
