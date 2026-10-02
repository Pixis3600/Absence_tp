<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Silber\Bouncer\Database\Models;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user-view-all') ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $passwordRules = $this->isMethod('post')
            ? ['required', 'string', 'min:6']
            : ['nullable', 'string', 'min:6', 'confirmed'];

        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'lastname' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => $passwordRules,
            'role' => ['required', Rule::exists(Models::table('roles'), 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Le nom est obligatoire.'),
            'name.regex' => __('Le nom ne doit contenir que des lettres, espaces ou tirets.'),
            'lastname.required' => __('Le prénom est obligatoire.'),
            'lastname.regex' => __('Le prénom ne doit contenir que des lettres, espaces ou tirets.'),
            'email.required' => __('L\'email est obligatoire.'),
            'email.email' => __('L\'email doit être dans un format valide.'),
            'email.unique' => __('Cet email est déjà utilisé.'),
            'password.required' => __('Le mot de passe est obligatoire.'),
            'password.min' => __('Le mot de passe doit contenir au moins 6 caractères.'),
            'password.confirmed' => __('La confirmation du mot de passe ne correspond pas.'),
            'role.required' => __('Le rôle est obligatoire.'),
            'role.in' => __('Le rôle sélectionné est invalide.'),
        ];
    }
}
