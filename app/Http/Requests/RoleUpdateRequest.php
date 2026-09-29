<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Silber\Bouncer\Database\Models;

class RoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user-view-all') ?? false;
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique(Models::table('roles'), 'name')->ignore($roleId),
            ],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => [Rule::exists(Models::table('abilities'), 'id')],
            'new_abilities' => ['nullable', 'string', 'max:255'],
        ];
    }
}
