<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesCloudinaryUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectImageResource extends JsonResource
{
    use ResolvesCloudinaryUrl;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_path' => $this->resolveUrl($this->image_path),
            'caption' => $this->caption,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
