<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'max:254', 'unique:clients,email'],
            'phone' => ['required'],
            'company' => ['required'],
            'address' => ['required'],
            'status' => ['required', Rule::enum(StatusEnum::class)],
            'deleted_at' => ['nullable', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return Gate::allows('manage clients');
    }
}
