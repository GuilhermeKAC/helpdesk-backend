<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'technician_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if ($user && ! in_array($user->role, [UserRole::TECHNICIAN, UserRole::ADMIN])) {
                        $fail('O usuário selecionado não é um técnico.');
                    }
                },
            ],
        ];
    }
}
