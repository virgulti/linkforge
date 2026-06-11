<?php

namespace App\Http\Requests;

use App\Rules\SafeUrl;
use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'string', new SafeUrl],
            'custom_code' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9-_]{3,16}$/'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
