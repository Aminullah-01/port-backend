<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? $this->resource : (array) $this->resource;

        return [
            'total_projects' => $data['total_projects'] ?? 0,
            'total_skills' => $data['total_skills'] ?? 0,
            'total_services' => $data['total_services'] ?? 0,
            'total_certificates' => $data['total_certificates'] ?? 0,
            'total_messages' => $data['total_messages'] ?? 0,
            'total_blogs' => $data['total_blogs'] ?? 0,
            'unread_messages' => $data['unread_messages'] ?? 0,
            'featured_projects' => $data['featured_projects'] ?? 0,
        ];
    }
}
