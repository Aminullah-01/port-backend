<?php

namespace App\Http\Resources\Concerns;

trait ResolvesCloudinaryUrl
{
    private function resolveUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return url($path);
    }
}
