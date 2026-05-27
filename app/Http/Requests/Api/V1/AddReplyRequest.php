<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class AddReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:10000'],
            'is_internal' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if ($user && $user->role === UserRole::CUSTOMER) {
            $this->merge(['is_internal' => false]);
        }
    }
}
