@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ __('Ajouter une absence') }}</h2>

    <form action="{{ route('absences.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="user_id">{{ __('Employé') }}</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">{{ __('Choisir un employé') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} {{ $user->lastname }}
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date_debut">{{ __('Date de début') }}</label>
            <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ old('date_debut') }}" required>
            @error('date_debut')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date_fin">{{ __('Date de fin') }}</label>
            <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ old('date_fin') }}" required>
            @error('date_fin')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="motif">{{ __('Motif') }}</label>
            <select name="motif" id="motif" class="form-control" required>
                <option value="">{{ __('Choisir un motif') }}</option>
                <option value="Accidents du travail" {{ old('motif') == 'Accidents du travail' ? 'selected' : '' }}>Accidents du travail</option>
                <option value="Congé payé" {{ old('motif') == 'Congé payé' ? 'selected' : '' }}>Congé payé</option>
                <option value="Congé paternité" {{ old('motif') == 'Congé paternité' ? 'selected' : '' }}>Congé paternité</option>
                <option value="Congé maternité" {{ old('motif') == 'Congé maternité' ? 'selected' : '' }}>Congé maternité</option>
                <option value="Congé maladie" {{ old('motif') == 'Congé maladie' ? 'selected' : '' }}>Congé maladie</option>
                <option value="Congé sans solde" {{ old('motif') == 'Congé sans solde' ? 'selected' : '' }}>Congé sans solde</option>
                <option value="Congé pour mariage" {{ old('motif') == 'Congé pour mariage' ? 'selected' : '' }}>Congé pour mariage</option>
                <option value="Congé pour décès" {{ old('motif') == 'Congé pour décès' ? 'selected' : '' }}>Congé pour décès</option>
                <option value="Formation" {{ old('motif') == 'Formation' ? 'selected' : '' }}>Formation</option>
                <option value="Autres" {{ old('motif') == 'Autres' ? 'selected' : '' }}>Autres</option>
            </select>
            @error('motif')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Valider') }}</button>
    </form>
</div>
@endsection
