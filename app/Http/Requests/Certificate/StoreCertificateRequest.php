<?php

namespace App\Http\Requests\Certificate;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['nullable', 'string', 'max:255'],
            'credential_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable'],
            'display_order' => ['nullable', 'integer'],
        ];
    }
}
