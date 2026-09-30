@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ __('Gestion des rôles') }}</h2>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">{{ __('Ajouter un rôle') }}</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('Rôle') }}</th>
                <th>{{ __('Nombre d\'utilisateurs') }}</th>
                <th>{{ __('Autorisations') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td>
                        @if($role->abilities->isEmpty())
                            <span class="text-muted">{{ __('Aucune') }}</span>
                        @else
                            {{ $role->abilities->pluck('name')->sort()->implode(', ') }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">{{ __('Modifier') }}</a>

                        <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('{{ __('Supprimer ce rôle ? Les utilisateurs perdront ce rôle.') }}')"
                            >
                                {{ __('Supprimer') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">{{ __('Aucun rôle enregistré.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
