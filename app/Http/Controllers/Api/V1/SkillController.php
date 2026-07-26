<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Skill\StoreSkillRequest;
use App\Http\Requests\Skill\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use App\Services\SkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function __construct(private SkillService $skillService) {}

    public function index(Request $request): JsonResponse
    {
        $skills = $this->skillService->getSkills($request->only(['per_page']));

        return response()->json([
            'success' => true,
            'message' => 'Skills retrieved successfully',
            'data' => SkillResource::collection($skills),
        ]);
    }

    public function show(Skill $skill): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Skill details retrieved successfully',
            'data' => new SkillResource($skill),
        ]);
    }

    public function store(StoreSkillRequest $request): JsonResponse
    {
        $skill = $this->skillService->createSkill($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Skill created successfully',
            'data' => new SkillResource($skill),
        ], 201);
    }

    public function update(UpdateSkillRequest $request, Skill $skill): JsonResponse
    {
        $updated = $this->skillService->updateSkill($skill, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Skill updated successfully',
            'data' => new SkillResource($updated),
        ]);
    }

    public function destroy(Skill $skill): JsonResponse
    {
        $this->skillService->deleteSkill($skill);

        return response()->json([
            'success' => true,
            'message' => 'Skill deleted successfully',
            'data' => null,
        ]);
    }
}
