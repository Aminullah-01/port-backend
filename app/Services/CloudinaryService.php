<?php

namespace App\Services;

use Cloudinary\Cloudinary;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
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

    public function upload(string $filePath, string $folder = 'portfolio')
    {
        return $this->cloudinary
            ->uploadApi()
            ->upload($filePath, [
                'folder' => $folder,
            ]);
    }

    public function delete(string $publicId)
    {
        return $this->cloudinary
            ->uploadApi()
            ->destroy($publicId);
    }
}