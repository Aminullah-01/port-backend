<?php

namespace App\Services;

use App\Models\Certificate;

class CertificateService
{
    public function __construct(private FileUploadService $fileUploadService) {}

    public function getCertificates(array $filters = [])
    {
        $query = Certificate::query()->orderBy('display_order', 'asc')->orderBy('created_at', 'desc');

        if (!empty($filters['per_page'])) {
            return $query->paginate((int) $filters['per_page']);
        }

        return $query->get();
    }

    public function createCertificate(array $data): Certificate
    {
        if (isset($data['image'])) {
            $data['image'] = $this->fileUploadService->uploadFile($data['image'], 'certificates');
        }

        return Certificate::create($data);
    }

    public function updateCertificate(Certificate $certificate, array $data): Certificate
    {
        if (isset($data['image'])) {
            $data['image'] = $this->fileUploadService->uploadFile($data['image'], 'certificates', $certificate->image);
        }

        $certificate->update($data);
        return $certificate->fresh();
    }

    public function deleteCertificate(Certificate $certificate): bool
    {
        $this->fileUploadService->deleteFile($certificate->image);
        return (bool) $certificate->delete();
    }
}
