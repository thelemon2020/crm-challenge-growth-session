<?php

namespace App\Http\Requests;

use App\Enums\ClientStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function rules(): array
    {
        $client = $this->route('client');

        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'max:254', Rule::unique('clients')->ignore($client)],
            'phone' => ['required'],
            'company' => ['required'],
            'address' => ['required'],
            'status' => ['required', Rule::enum(ClientStatusEnum::class)],
            'deleted_at' => ['nullable', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return Gate::allows('manage clients');
    }
}
