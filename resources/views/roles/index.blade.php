@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Gestion des rôles</h2>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">Ajouter un rôle</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rôle</th>
                <th>Nombre d'utilisateurs</th>
                <th>Autorisations</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td>
                        @if($role->abilities->isEmpty())
                            <span class="text-muted">Aucune</span>
                        @else
                            {{ $role->abilities->pluck('name')->sort()->implode(', ') }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">Modifier</a>

                        <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Supprimer ce rôle ? Les utilisateurs perdront ce rôle.')"
                            >
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Aucun rôle enregistré.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
