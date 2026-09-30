@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ __('Modifier un employé') }}</h2>

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name">{{ __('Nom') }}</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>

        <div class="mb-3">
            <label for="lastname">{{ __('Prénom') }}</label>
            <input type="text" name="lastname" class="form-control" value="{{ $user->lastname }}" required>
        </div>

        <div class="mb-3">
            <label for="email">{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>

        <div class="mb-3">
            <label for="password">{{ __('Nouveau mot de passe') }}</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label for="role">{{ __('Rôle') }}</label>
            <select name="role" class="form-control" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $user->roles->first()?->name ?? 'salarie') === $role)>
                        {{ ucfirst($role) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Modifier') }}</button>
    </form>
</div>
@endsection
