<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class FileUploadService
{
    protected ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        if (config('services.cloudinary.cloud_name')) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
                'url' => [
                    'secure' => config('services.cloudinary.secure', true),
                ],
            ]);
        }
    }

    public function uploadFile($file, string $folder = 'uploads', ?string $existingPath = null): ?string
    {
        if (!$file) {
            return $existingPath;
        }

        if (is_string($file)) {
            return $file;
        }

        if ($file instanceof UploadedFile) {
            if (!$this->cloudinary) {
                return $existingPath;
            }

            if ($existingPath) {
                $this->deleteFile($existingPath);
            }

            $result = $this->cloudinary
                ->uploadApi()
                ->upload(
                    $file->getRealPath(),
                    [
                        'folder' => "portfolio/{$folder}",
                    ]
                );

            return $result['secure_url'];
        }

        return $existingPath;
    }

    public function deleteFile(?string $url): void
    {
        if (!$url || !$this->cloudinary) {
            return;
        }

        try {
            $publicId = $this->resolvePublicId($url);

            if ($publicId) {
                $this->cloudinary->uploadApi()->destroy($publicId);
            }
        } catch (\Exception $e) {
            report($e);
        }
    }

    private function resolvePublicId(string $url): ?string
    {
        $parts = parse_url($url);
        if (!isset($parts['path'])) {
            return null;
        }

        $path = $parts['path'];

        $path = preg_replace('#^/[^/]+/image/upload/#', '', $path);
        $path = preg_replace('#v\d+/#', '', $path);

        $path = ltrim($path, '/');

        $publicId = pathinfo($path, PATHINFO_DIRNAME);
        if ($publicId === '.') {
            $publicId = pathinfo($path, PATHINFO_FILENAME);
        } else {
            $publicId .= '/' . pathinfo($path, PATHINFO_FILENAME);
        }

        return $publicId;
    }
}