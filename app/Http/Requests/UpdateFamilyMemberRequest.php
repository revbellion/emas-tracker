<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFamilyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
