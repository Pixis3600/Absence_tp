@extends('layouts.app')

@section('content')
@php
    $dateDebut = old('date_debut') ?? ($absence->date_debut instanceof \Illuminate\Support\Carbon ? $absence->date_debut->format('Y-m-d') : ($absence->date_debut ?? ''));
    $dateFin = old('date_fin') ?? ($absence->date_fin instanceof \Illuminate\Support\Carbon ? $absence->date_fin->format('Y-m-d') : ($absence->date_fin ?? ''));
    $motifActuel = old('motif', $absence->motif ?? '');
@endphp
<div class="container mt-4">
    <h2>{{ __('Modifier une absence') }}</h2>

    <form action="{{ route('absences.update', $absence->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-select-field name="user_id" :label="__('Employé')" required>
            @foreach($users as $user)
                <option value="{{ $user->id }}"
                    {{ old('user_id', $absence->user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} {{ $user->lastname }}
                </option>
            @endforeach
        </x-select-field>

        <div class="mb-3">
            <label for="date_debut">{{ __('Date de début') }}</label>
            <input type="date" name="date_debut" id="date_debut" class="form-control"
                value="{{ $dateDebut }}" required>
            @error('date_debut')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date_fin">{{ __('Date de fin') }}</label>
            <input type="date" name="date_fin" id="date_fin" class="form-control"
                value="{{ $dateFin }}" required>
            @error('date_fin')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <x-select-field name="motif" :label="__('Motif')" required>
            <option value="">{{ __('Choisir un motif') }}</option>
            <option value="Accidents du travail" {{ $motifActuel == 'Accidents du travail' ? 'selected' : '' }}>Accidents du travail</option>
            <option value="Congé payé" {{ $motifActuel == 'Congé payé' ? 'selected' : '' }}>Congé payé</option>
            <option value="Congé paternité" {{ $motifActuel == 'Congé paternité' ? 'selected' : '' }}>Congé paternité</option>
            <option value="Congé maternité" {{ $motifActuel == 'Congé maternité' ? 'selected' : '' }}>Congé maternité</option>
            <option value="Congé maladie" {{ $motifActuel == 'Congé maladie' ? 'selected' : '' }}>Congé maladie</option>
            <option value="Congé sans solde" {{ $motifActuel == 'Congé sans solde' ? 'selected' : '' }}>Congé sans solde</option>
            <option value="Congé pour mariage" {{ $motifActuel == 'Congé pour mariage' ? 'selected' : '' }}>Congé pour mariage</option>
            <option value="Congé pour décès" {{ $motifActuel == 'Congé pour décès' ? 'selected' : '' }}>Congé pour décès</option>
            <option value="Formation" {{ $motifActuel == 'Formation' ? 'selected' : '' }}>Formation</option>
            <option value="Autres" {{ $motifActuel == 'Autres' ? 'selected' : '' }}>Autres</option>
        </x-select-field>

        <button type="submit" class="btn btn-primary">{{ __('Valider') }}</button>
    </form>
</div>
@endsection
