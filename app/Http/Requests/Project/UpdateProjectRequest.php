<?php

namespace App\Http\Requests\Project;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectParam = $this->route('project');
        $projectId = null;

        if ($projectParam instanceof Project) {
            $projectId = $projectParam->id;
        } elseif (is_numeric($projectParam)) {
            $projectId = (int) $projectParam;
        } elseif (is_string($projectParam)) {
            $found = Project::where('slug', $projectParam)->first();
            $projectId = $found?->id;
        }

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($projectId)],
            'description' => ['sometimes', 'required', 'string'],
            'problem' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'technologies' => ['nullable'],
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
