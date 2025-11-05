<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'created' => ['required', 'array'],
            'updated' => ['required', 'array'],
            'deleted' => ['required', 'array'],
            'last_synced_at' => ['required', 'date'],
        ];
    }
}
