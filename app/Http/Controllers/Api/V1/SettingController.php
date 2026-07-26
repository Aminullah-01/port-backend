<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function show(): JsonResponse
    {
        $settings = $this->settingService->getSettings();

        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully',
            'data' => new SettingResource($settings),
        ]);
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $settings = $this->settingService->updateSettings($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => new SettingResource($settings),
        ]);
    }
}
