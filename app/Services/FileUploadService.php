<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class FileUploadService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
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

            // Delete old file from Cloudinary
            if ($existingPath) {
                $this->deleteFile($existingPath);
            }

            $result = $this->cloudinary
                ->uploadApi()
                ->upload(
                    $file->getRealPath(),
                    [
                        'folder' => "portfolio/{$folder}"
                    ]
                );

            return $result['secure_url'];
        }

        return $existingPath;
    }

    public function deleteFile(?string $url): void
    {
        if (!$url) {
            return;
        }

        try {

            // Example URL:
            // https://res.cloudinary.com/demo/image/upload/v123456/portfolio/projects/image.jpg

            $parts = parse_url($url);

            if (!isset($parts['path'])) {
                return;
            }

            $path = $parts['path'];

            $path = preg_replace('#^/[^/]+/image/upload/#', '', $path);

            $path = preg_replace('#v\d+/#', '', $path);

            $publicId = pathinfo($path, PATHINFO_DIRNAME)
                . '/'
                . pathinfo($path, PATHINFO_FILENAME);

            $this->cloudinary
                ->uploadApi()
                ->destroy($publicId);

        } catch (\Exception $e) {

            report($e);

        }
    }
}