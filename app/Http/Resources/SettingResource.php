<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SettingResource extends JsonResource
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
            'site_name' => $this->site_name,
            'logo' => $this->resolveUrl($this->logo),
            'favicon' => $this->resolveUrl($this->favicon),
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'theme_colors' => $this->theme_colors,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'social_links' => $this->social_links,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
