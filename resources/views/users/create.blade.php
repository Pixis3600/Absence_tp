@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ __('Ajouter un employé') }}</h2>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name">{{ __('Nom') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="lastname">{{ __('Prénom') }}</label>
            <input type="text" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
            @error('lastname')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email">{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password">{{ __('Mot de passe') }}</label>
            <input type="password" name="password" class="form-control" required>
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="role">{{ __('Rôle') }}</label>
            <select name="role" class="form-control" required>
                <option value="">{{ __('Choisir un rôle') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', 'salarie') === $role)>
                        {{ ucfirst($role) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Valider') }}</button>
    </form>
</div>
@endsection
