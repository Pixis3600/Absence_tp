@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ __('Ajouter un employé') }}</h2>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <x-input-text name="name" :label="__('Nom')" required />

        <x-input-text name="lastname" :label="__('Prénom')" required />

        <x-input-text name="email" type="email" :label="__('Email')" required />

        <x-input-text name="password" type="password" :label="__('Mot de passe')" required />

        <x-select-field name="role" :label="__('Rôle')" required>
            <option value="">{{ __('Choisir un rôle') }}</option>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(old('role', 'salarie') === $role)>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </x-select-field>

        <button type="submit" class="btn btn-primary">{{ __('Valider') }}</button>
    </form>
</div>
@endsection
