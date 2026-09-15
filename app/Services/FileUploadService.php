<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    protected ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        $cloudinaryUrl = config('services.cloudinary.url') ?: env('CLOUDINARY_URL');
        $cloudName = config('services.cloudinary.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');

        if (!empty($cloudinaryUrl)) {
            $this->cloudinary = new Cloudinary($cloudinaryUrl);
        } elseif (!empty($cloudName)) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => config('services.cloudinary.api_key') ?: env('CLOUDINARY_API_KEY'),
                    'api_secret' => config('services.cloudinary.api_secret') ?: env('CLOUDINARY_API_SECRET'),
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
            if (!$file->isValid()) {
                Log::warning('FileUploadService: Uploaded file is not valid', [
                    'error' => $file->getErrorMessage(),
                    'name' => $file->getClientOriginalName(),
                ]);
                return $existingPath;
            }

            $realPath = $file->getRealPath();
            if (!$realPath || !file_exists($realPath)) {
                Log::warning('FileUploadService: Uploaded file path not accessible');
                return $existingPath;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $isDocument = in_array($extension, ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'csv', 'xlsx', 'xls']);

            // 1. Try Cloudinary upload if configured
            if ($this->cloudinary) {
                try {
                    $uploadOptions = [
                        'folder' => "portfolio/{$folder}",
                        'resource_type' => 'auto',
                        'use_filename' => true,
                        'unique_filename' => true,
                    ];

                    $result = $this->cloudinary
                        ->uploadApi()
                        ->upload($realPath, $uploadOptions);

                    if (!empty($result['secure_url'])) {
                        if ($existingPath) {
                            $this->deleteFile($existingPath);
                        }
                        return $result['secure_url'];
                    }
                } catch (\Throwable $e) {
                    Log::warning('FileUploadService: Cloudinary auto upload failed, trying alternatives', [
                        'error' => $e->getMessage(),
                        'folder' => $folder,
                        'isDocument' => $isDocument,
                    ]);

                    // If it was a document and auto failed, retry with resource_type => raw
                    if ($isDocument) {
                        try {
                            $rawResult = $this->cloudinary
                                ->uploadApi()
                                ->upload($realPath, [
                                    'folder' => "portfolio/{$folder}",
                                    'resource_type' => 'raw',
                                    'use_filename' => true,
                                    'unique_filename' => true,
                                ]);

                            if (!empty($rawResult['secure_url'])) {
                                if ($existingPath) {
                                    $this->deleteFile($existingPath);
                                }
                                return $rawResult['secure_url'];
                            }
                        } catch (\Throwable $e2) {
                            Log::error('FileUploadService: Cloudinary raw upload also failed', [
                                'error' => $e2->getMessage(),
                            ]);
                        }
                    }
                }
            }

            // 2. Graceful fallback to local public disk storage
            try {
                $storedPath = $file->store("portfolio/{$folder}", 'public');
                if ($storedPath) {
                    if ($existingPath) {
                        $this->deleteFile($existingPath);
                    }
                    $url = Storage::disk('public')->url($storedPath);
                    if (request()->hasHeader('host') && str_starts_with($url, 'http://localhost')) {
                        $url = url('storage/' . $storedPath);
                    }
                    return $url;
                }
            } catch (\Throwable $e) {
                Log::error('FileUploadService: Local public disk storage failed', [
                    'error' => $e->getMessage(),
                ]);
            }

            return $existingPath;
        }

        return $existingPath;
    }

    public function deleteFile(?string $url): void
    {
        if (!$url) {
            return;
        }

        // Check if stored locally on public disk
        if (str_contains($url, '/storage/')) {
            try {
                $parts = explode('/storage/', $url);
                if (isset($parts[1])) {
                    Storage::disk('public')->delete(ltrim($parts[1], '/'));
                }
            } catch (\Throwable $e) {
                report($e);
            }
            return;
        }

        if (!$this->cloudinary) {
            return;
        }

        try {
            $publicId = $this->resolvePublicId($url);
            if ($publicId) {
                try {
                    $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                } catch (\Throwable) {
                    // ignore
                }
            }

            $rawPublicId = $this->resolveRawPublicId($url);
            if ($rawPublicId && $rawPublicId !== $publicId) {
                try {
                    $this->cloudinary->uploadApi()->destroy($rawPublicId, ['resource_type' => 'raw']);
                } catch (\Throwable) {
                    // ignore
                }
            }
        } catch (\Throwable $e) {
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

        $path = preg_replace('#^/[^/]+/(image|raw|video|files)/upload/#', '', $path);
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

    private function resolveRawPublicId(string $url): ?string
    {
        $parts = parse_url($url);
        if (!isset($parts['path'])) {
            return null;
        }

        $path = $parts['path'];
        $path = preg_replace('#^/[^/]+/(image|raw|video|files)/upload/#', '', $path);
        $path = preg_replace('#v\d+/#', '', $path);

        return ltrim($path, '/');
    }
}