<?php

namespace App\Services;

use App\Models\Service;

class ServiceManagementService
{
    public function getServices(array $filters = [])
    {
        $query = Service::query()->orderBy('display_order', 'asc')->orderBy('title', 'asc');

        if (!empty($filters['per_page'])) {
            return $query->paginate((int) $filters['per_page']);
        }

        return $query->get();
    }

    public function createService(array $data): Service
    {
        return Service::create($data);
    }

    public function updateService(Service $service, array $data): Service
    {
        $service->update($data);
        return $service->fresh();
    }

    public function deleteService(Service $service): bool
    {
        return (bool) $service->delete();
    }
}
