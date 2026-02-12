<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginatedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function perPage(): int
    {
        return $this->validated('per_page', 15);
    }
}
