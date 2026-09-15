<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use Carbon\Carbon;

class DashboardService
{
    public function getMetrics(): array
    {
        // 7-day visitor data
        $visitorChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $count = AnalyticsEvent::where('event_type', 'visit')
                ->whereDate('created_at', $day->toDateString())
                ->count();
            $visitorChart[] = [
                'd' => $day->format('D'),
                'date' => $day->toDateString(),
                'v' => $count,
            ];
        }

        // Projects by category
        $categoryDistribution = Project::whereNotNull('category')
            ->where('category', '!=', '')
            ->selectRaw('category as name, count(*) as value')
            ->groupBy('category')
            ->orderByDesc('value')
            ->get()
            ->map(fn($item) => [
                'name' => (string) $item->name,
                'value' => (int) $item->value,
            ])
            ->toArray();

        // Recent real activities
        $activities = collect();

        ContactMessage::latest()->take(3)->get()->each(function ($msg) use (&$activities) {
            $activities->push([
                'title' => "New message from {$msg->name}",
                'time' => $msg->created_at?->diffForHumans() ?? 'recently',
                'timestamp' => $msg->created_at ?? now(),
            ]);
        });

        AnalyticsEvent::where('event_type', 'cv_download')->latest()->take(3)->get()->each(function ($ev) use (&$activities) {
            $activities->push([
                'title' => 'CV downloaded by visitor',
                'time' => $ev->created_at?->diffForHumans() ?? 'recently',
                'timestamp' => $ev->created_at ?? now(),
            ]);
        });

        Project::latest()->take(2)->get()->each(function ($proj) use (&$activities) {
            $activities->push([
                'title' => "Project '{$proj->title}' updated",
                'time' => $proj->updated_at?->diffForHumans() ?? 'recently',
                'timestamp' => $proj->updated_at ?? now(),
            ]);
        });

        BlogPost::latest()->take(2)->get()->each(function ($post) use (&$activities) {
            $activities->push([
                'title' => "Blog post '{$post->title}' published",
                'time' => $post->created_at?->diffForHumans() ?? 'recently',
                'timestamp' => $post->created_at ?? now(),
            ]);
        });

        $recentActivities = $activities
            ->sortByDesc('timestamp')
            ->take(5)
            ->values()
            ->map(fn($item) => [
                'title' => $item['title'],
                'time' => $item['time'],
            ])
            ->toArray();

        if (empty($recentActivities)) {
            $recentActivities = [
                ['title' => 'Portfolio dashboard ready', 'time' => 'just now'],
            ];
        }

        return [
            'total_projects' => Project::count(),
            'total_skills' => Skill::count(),
            'total_services' => Service::count(),
            'total_certificates' => Certificate::count(),
            'total_messages' => ContactMessage::count(),
            'total_blogs' => BlogPost::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->where('archived', false)->count(),
            'featured_projects' => Project::where('featured', true)->count(),
            'visitors_7d' => AnalyticsEvent::where('event_type', 'visit')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'total_visitors' => AnalyticsEvent::where('event_type', 'visit')->count(),
            'cv_downloads' => AnalyticsEvent::where('event_type', 'cv_download')->count(),
            'visitor_chart' => $visitorChart,
            'category_distribution' => $categoryDistribution,
            'recent_activities' => $recentActivities,
        ];
    }
}
