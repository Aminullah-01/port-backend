<?php

namespace App\Http\Requests\Blog;

use App\Models\BlogPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $blogParam = $this->route('blog');
        $blogId = null;

        if ($blogParam instanceof BlogPost) {
            $blogId = $blogParam->id;
        } elseif (is_numeric($blogParam)) {
            $blogId = (int) $blogParam;
        } elseif (is_string($blogParam)) {
            $found = BlogPost::where('slug', $blogParam)->first();
            $blogId = $found?->id;
        }

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($blogId)],
            'cover_image' => ['nullable'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['sometimes', 'required', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:draft,published'],
            'tags' => ['nullable'],
        ];
    }
}
