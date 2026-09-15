<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function trackVisit(Request $request): JsonResponse
    {
        $pageUrl = (string) $request->input('page_url', '/');
        $ip = $request->ip();

        // Prevent duplicate hits from the same IP on the same path within 10 minutes
        $recentVisit = AnalyticsEvent::where('event_type', 'visit')
            ->where('ip_address', $ip)
            ->where('page_url', $pageUrl)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if (!$recentVisit) {
            AnalyticsEvent::create([
                'event_type' => 'visit',
                'page_url' => substr($pageUrl, 0, 255),
                'ip_address' => $ip,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Visit tracked successfully',
            'data' => null,
        ]);
    }

    public function trackCvDownload(Request $request): JsonResponse
    {
        AnalyticsEvent::create([
            'event_type' => 'cv_download',
            'page_url' => substr((string) $request->input('page_url', '/resume'), 0, 255),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CV download tracked successfully',
            'data' => null,
        ]);
    }
}
