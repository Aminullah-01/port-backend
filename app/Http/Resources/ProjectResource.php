<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    private function resolveUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return url(Storage::url($path));
    }

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
