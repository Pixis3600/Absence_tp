@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ __('Modifier un employé') }}</h2>

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-input-text name="name" :label="__('Nom')" :value="$user->name" required />

        <x-input-text name="lastname" :label="__('Prénom')" :value="$user->lastname" required />

        <x-input-text name="email" type="email" :label="__('Email')" :value="$user->email" required />

        <x-input-text name="password" type="password" :label="__('Nouveau mot de passe')" />

        <x-select-field name="role" :label="__('Rôle')" required>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(old('role', $user->roles->first()?->name ?? 'salarie') === $role)>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </x-select-field>

        <button type="submit" class="btn btn-primary">{{ __('Modifier') }}</button>
    </form>
</div>
@endsection
