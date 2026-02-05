<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'disk' => ['required'],
            'path' => ['required'],
            'original_name' => ['required'],
            'mime_type' => ['required'],
            'size' => ['required', 'integer'],
            'project_id' => ['required', 'exists:projects'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
