@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Modifier le rôle {{ $role->name }}</h2>

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name">Nom du rôle</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Autorisations existantes</label>
            @if($abilities->isEmpty())
                <p class="text-muted">Aucune autorisation existante.</p>
            @else
                <div class="row">
                    @foreach($abilities as $ability)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="abilities[]"
                                    value="{{ $ability->id }}"
                                    id="ability_{{ $ability->id }}"
                                    @checked(collect(old('abilities', $role->abilities->pluck('id')->all()))->contains($ability->id))
                                >
                                <label class="form-check-label" for="ability_{{ $ability->id }}">
                                    {{ $ability->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @error('abilities.*')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="new_abilities">Ajouter de nouvelles autorisations</label>
            <input
                type="text"
                name="new_abilities"
                class="form-control"
                value="{{ old('new_abilities') }}"
                placeholder="ex: role-manage, report-export"
            >
            <small class="text-muted">Sépare les noms par des virgules.</small>
            @error('new_abilities')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
