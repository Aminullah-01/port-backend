<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogService
{
    public function __construct(private FileUploadService $fileUploadService) {}

    public function getPosts(array $filters = [])
    {
        $query = BlogPost::query()->orderBy('created_at', 'desc');

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['per_page'])) {
            return $query->paginate((int) $filters['per_page']);
        }

        return $query->get();
    }

    public function findBySlugOrId(string|int $identifier): BlogPost
    {
        return BlogPost::where('slug', $identifier)
            ->orWhere('id', is_numeric($identifier) ? $identifier : 0)
            ->firstOrFail();
    }

    public function createPost(array $data): BlogPost
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            if (isset($data['cover_image'])) {
                $data['cover_image'] = $this->fileUploadService->uploadFile($data['cover_image'], 'blogs');
            }

            return BlogPost::create($data);
        });
    }

    public function updatePost(BlogPost $post, array $data): BlogPost
    {
        return DB::transaction(function () use ($post, $data) {
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            if (isset($data['cover_image'])) {
                $data['cover_image'] = $this->fileUploadService->uploadFile($data['cover_image'], 'blogs', $post->cover_image);
            }

            $post->update($data);

            return $post->fresh();
        });
    }

    public function deletePost(BlogPost $post): bool
    {
        $this->fileUploadService->deleteFile($post->cover_image);
        return (bool) $post->delete();
    }
}
