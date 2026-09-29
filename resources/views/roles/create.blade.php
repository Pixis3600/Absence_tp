@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Créer un rôle</h2>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name">Nom du rôle</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            <small class="text-muted">Lettres, chiffres, underscore et tiret uniquement.</small>
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
                                    @checked(collect(old('abilities', []))->contains($ability->id))
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

        <button type="submit" class="btn btn-primary">Créer</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
