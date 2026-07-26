<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectService
{
    public function __construct(private FileUploadService $fileUploadService) {}

    public function getProjects(array $filters = [])
    {
        $query = Project::query()->with('images')->orderBy('display_order', 'asc')->orderBy('created_at', 'desc');

        if (!empty($filters['category']) && $filters['category'] !== 'All') {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['featured'])) {
            $featured = filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN);
            $query->where('featured', $featured);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['per_page'])) {
            return $query->paginate((int) $filters['per_page']);
        }

        return $query->get();
    }

    public function findBySlugOrId(string|int $identifier): Project
    {
        return Project::with('images')
            ->where('slug', $identifier)
            ->orWhere('id', is_numeric($identifier) ? $identifier : 0)
            ->firstOrFail();
    }

    public function createProject(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            if (isset($data['thumbnail'])) {
                $data['thumbnail'] = $this->fileUploadService->uploadFile($data['thumbnail'], 'projects');
            }

            if (isset($data['featured'])) {
                $data['featured'] = filter_var($data['featured'], FILTER_VALIDATE_BOOLEAN);
            }

            return Project::create($data);
        });
    }

    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            if (isset($data['thumbnail'])) {
                $data['thumbnail'] = $this->fileUploadService->uploadFile($data['thumbnail'], 'projects', $project->thumbnail);
            }

            if (isset($data['featured'])) {
                $data['featured'] = filter_var($data['featured'], FILTER_VALIDATE_BOOLEAN);
            }

            $project->update($data);

            return $project->fresh(['images']);
        });
    }

    public function deleteProject(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            $this->fileUploadService->deleteFile($project->thumbnail);
            foreach ($project->images as $img) {
                $this->fileUploadService->deleteFile($img->image_path);
            }
            return (bool) $project->delete();
        });
    }
}
