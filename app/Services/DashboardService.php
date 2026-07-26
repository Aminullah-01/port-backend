<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;

class DashboardService
{
    public function getMetrics(): array
    {
        return [
            'total_projects' => Project::count(),
            'total_skills' => Skill::count(),
            'total_services' => Service::count(),
            'total_certificates' => Certificate::count(),
            'total_messages' => ContactMessage::count(),
            'total_blogs' => BlogPost::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->where('archived', false)->count(),
            'featured_projects' => Project::where('featured', true)->count(),
        ];
    }
}
