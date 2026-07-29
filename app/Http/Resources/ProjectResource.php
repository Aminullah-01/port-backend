<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesCloudinaryUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    use ResolvesCloudinaryUrl;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'problem' => $this->problem,
            'solution' => $this->solution,
            'technologies' => $this->technologies ?? [],
            'category' => $this->category,
            'thumbnail' => $this->resolveUrl($this->thumbnail),
            'featured' => (bool) $this->featured,
            'status' => $this->status,
            'github_url' => $this->github_url,
            'live_url' => $this->live_url,
            'display_order' => $this->display_order,
            'images' => ProjectImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
