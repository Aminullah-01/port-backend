<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadFile($file, string $folder = 'uploads', ?string $existingPath = null): ?string
    {
        if (!$file) {
            return $existingPath;
        }

        if (is_string($file)) {
            return $file;
        }

        if ($file instanceof UploadedFile) {
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }

            return $file->store($folder, 'public');
        }

        return $existingPath;
    }

    public function deleteFile(?string $path): void
    {
        if ($path && !filter_var($path, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
