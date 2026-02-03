<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(ProjectStatusEnum::class)],
            'deadline' => ['nullable', 'date', Rule::date()->afterOrEqual(today())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
