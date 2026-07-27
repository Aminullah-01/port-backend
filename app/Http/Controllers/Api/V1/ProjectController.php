<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService) {}

    public function index(Request $request): JsonResponse
    {
        $projects = $this->projectService->getProjects($request->only(['category', 'featured', 'status', 'per_page']));

        return response()->json([
            'success' => true,
            'message' => 'Projects retrieved successfully',
            'data' => ProjectResource::collection($projects),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $project = $this->projectService->findBySlugOrId($slug);

        return response()->json([
            'success' => true,
            'message' => 'Project details retrieved successfully',
            'data' => new ProjectResource($project),
        ]);
    }

    $cloudinary = new CloudinaryService();

$result = $cloudinary->upload(
    $request->file('image')->getRealPath(),
    'portfolio/projects'
);

    public function update(UpdateProjectRequest $request, $id): JsonResponse
    {
        $project = is_numeric($id) ? Project::findOrFail($id) : Project::where('slug', $id)->firstOrFail();
        $updated = $this->projectService->updateProject($project, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => new ProjectResource($updated),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $project = is_numeric($id) ? Project::findOrFail($id) : Project::where('slug', $id)->firstOrFail();
        $this->projectService->deleteProject($project);

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully',
            'data' => null,
        ]);
    }
}
