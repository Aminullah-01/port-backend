<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'description' => ['required', 'string'],
            'problem' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['string'],
            'category' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable'],
            'featured' => ['nullable'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
            'github_url' => ['nullable', 'string', 'max:255'],
            'live_url' => ['nullable', 'string', 'max:255'],
            'display_order' => ['nullable', 'integer'],
        ];
    }
}
