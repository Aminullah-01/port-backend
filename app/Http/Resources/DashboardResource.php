<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_projects' => $this['total_projects'] ?? 0,
            'total_skills' => $this['total_skills'] ?? 0,
            'total_services' => $this['total_services'] ?? 0,
            'total_certificates' => $this['total_certificates'] ?? 0,
            'total_messages' => $this['total_messages'] ?? 0,
            'total_blogs' => $this['total_blogs'] ?? 0,
            'unread_messages' => $this['unread_messages'] ?? 0,
            'featured_projects' => $this['featured_projects'] ?? 0,
        ];
    }
}
