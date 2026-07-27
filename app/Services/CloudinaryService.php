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
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
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