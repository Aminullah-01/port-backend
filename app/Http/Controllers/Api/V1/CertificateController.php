<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Certificate\StoreCertificateRequest;
use App\Http\Requests\Certificate\UpdateCertificateRequest;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $certificateService) {}

    public function index(Request $request): JsonResponse
    {
        $certificates = $this->certificateService->getCertificates($request->only(['per_page']));

        return response()->json([
            'success' => true,
            'message' => 'Certificates retrieved successfully',
            'data' => CertificateResource::collection($certificates),
        ]);
    }

    public function show(Certificate $certificate): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Certificate details retrieved successfully',
            'data' => new CertificateResource($certificate),
        ]);
    }

    public function store(StoreCertificateRequest $request): JsonResponse
    {
        $certificate = $this->certificateService->createCertificate($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Certificate created successfully',
            'data' => new CertificateResource($certificate),
        ], 201);
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate): JsonResponse
    {
        $updated = $this->certificateService->updateCertificate($certificate, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Certificate updated successfully',
            'data' => new CertificateResource($updated),
        ]);
    }

    public function destroy(Certificate $certificate): JsonResponse
    {
        $this->certificateService->deleteCertificate($certificate);

        return response()->json([
            'success' => true,
            'message' => 'Certificate deleted successfully',
            'data' => null,
        ]);
    }
}
