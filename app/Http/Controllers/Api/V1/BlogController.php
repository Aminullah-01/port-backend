<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\BlogPost;
use App\Services\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(private BlogService $blogService) {}

    public function index(Request $request): JsonResponse
    {
        $blogs = $this->blogService->getPosts($request->only(['category', 'status', 'per_page']));

        return response()->json([
            'success' => true,
            'message' => 'Blog posts retrieved successfully',
            'data' => BlogResource::collection($blogs),
        ]);
    }

    public function show($id): JsonResponse
    {
        $blog = $this->blogService->findBySlugOrId($id);

        return response()->json([
            'success' => true,
            'message' => 'Blog post retrieved successfully',
            'data' => new BlogResource($blog),
        ]);
    }

    public function store(StoreBlogRequest $request): JsonResponse
    {
        $blog = $this->blogService->createPost($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Blog post created successfully',
            'data' => new BlogResource($blog),
        ], 201);
    }

    public function update(UpdateBlogRequest $request, $id): JsonResponse
    {
        $blog = is_numeric($id) ? BlogPost::findOrFail($id) : BlogPost::where('slug', $id)->firstOrFail();
        $updated = $this->blogService->updatePost($blog, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Blog post updated successfully',
            'data' => new BlogResource($updated),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $blog = is_numeric($id) ? BlogPost::findOrFail($id) : BlogPost::where('slug', $id)->firstOrFail();
        $this->blogService->deletePost($blog);

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully',
            'data' => null,
        ]);
    }
}
