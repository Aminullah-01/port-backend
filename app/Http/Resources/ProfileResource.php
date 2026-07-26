<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'avatar' => $this->resolveUrl($this->avatar),
            'resume_url' => $this->resolveUrl($this->resume_url),
            'website' => $this->website,
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url,
            'twitter_url' => $this->twitter_url,
            'facebook_url' => $this->facebook_url,
            'whatsapp_url' => $this->whatsapp_url,
            'location' => $this->location,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'certificates' => CertificateResource::collection($this->whenLoaded('certificates')),
            'blogs' => BlogResource::collection($this->whenLoaded('blogs')),
        ];
    }
}
