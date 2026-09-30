@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ __('Liste des employés') }}</h2>

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
        {{ __('Ajouter un employé') }}
    </a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('Nom') }}</th>
                <th>{{ __('Prénom') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Rôle') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->lastname }}</td>
                    <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->first()?->name ?? __('Aucun') }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">{{ __('Modifier') }}</a>

                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Supprimer cet employé ?') }}')">
                                {{ __('Supprimer') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
