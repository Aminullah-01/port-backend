<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Services\ServiceManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(private ServiceManagementService $serviceManagementService) {}

    public function index(Request $request): JsonResponse
    {
        $services = $this->serviceManagementService->getServices($request->only(['per_page']));

        return response()->json([
            'success' => true,
            'message' => 'Services retrieved successfully',
            'data' => ServiceResource::collection($services),
        ]);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Service details retrieved successfully',
            'data' => new ServiceResource($service),
        ]);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->serviceManagementService->createService($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => new ServiceResource($service),
        ], 201);
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $updated = $this->serviceManagementService->updateService($service, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => new ServiceResource($updated),
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->serviceManagementService->deleteService($service);

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
            'data' => null,
        ]);
    }
}
