<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesCloudinaryUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    use ResolvesCloudinaryUrl;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'organization' => $this->organization,
            'issue_date' => $this->issue_date,
            'credential_url' => $this->credential_url,
            'image' => $this->resolveUrl($this->image),
            'display_order' => $this->display_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
