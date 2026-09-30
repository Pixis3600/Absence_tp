@extends('layouts.app')

@section('content')
@php
    $canManageAllAbsences = Auth::check() && Auth::user()->can('absence-view-all');
@endphp
<div class="container mt-4">
    <h2>{{ __('Liste des absences') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('absences.create') }}" class="btn btn-primary mb-3">
        {{ __('Ajouter une absence') }}
    </a>

    @if($canManageAllAbsences && $absences->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ __('Test envoi mail') }}</h5>
                <p class="text-muted mb-3">{{ __('Envoyer un mail de test avec les informations d\'une absence.') }}</p>

                <form action="{{ route('absences.test-mail') }}" method="POST" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <x-select-field name="absence_id" :label="__('Absence')" required>
                            @foreach($absences as $absence)
                                <option value="{{ $absence->id }}">
                                    #{{ $absence->id }} - {{ $absence->user->name ?? __('Aucun') }} - {{ $absence->motif }}
                                </option>
                            @endforeach
                        </x-select-field>
                    </div>

                    <div class="col-md-5">
                        <label for="email" class="form-label">{{ __('Email destinataire') }}</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            value="{{ old('email', Auth::user()->email ?? '') }}"
                            required
                        >
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-primary">{{ __('Envoyer test') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($absences->isEmpty())
        <p>{{ __('Aucune absence enregistrée.') }}</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ __('Employé') }}</th>
                    <th>{{ __('Date début') }}</th>
                    <th>{{ __('Date fin') }}</th>
                    <th>{{ __('Motif') }}</th>
                    <th>{{ __('Statut') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absences as $absence)
                    @php
                        $statusClass = match($absence->status ?? 'en_attente') {
                            'accepte' => 'success',
                            'refuse' => 'danger',
                            default => 'warning',
                        };

                        $statusLabel = match($absence->status ?? 'en_attente') {
                            'accepte' => __('Accepté'),
                            'refuse' => __('Refusé'),
                            default => __('En attente'),
                        };

                        $isOwner = Auth::check() && $absence->user_id === Auth::id();
                    @endphp
                    <tr>
                        <td>{{ $absence->user->name ?? __('Aucun') }}</td>
                        <td>{{ $absence->date_debut }}</td>
                        <td>{{ $absence->date_fin }}</td>
                        <td>{{ $absence->motif }}</td>
                        <td>
                            <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            @if($canManageAllAbsences && (($absence->status ?? 'en_attente') === 'en_attente'))
                                <form action="{{ route('absences.accept', $absence->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">{{ __('Accepter') }}</button>
                                </form>

                                <form action="{{ route('absences.reject', $absence->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">{{ __('Refuser') }}</button>
                                </form>
                            @elseif(! $canManageAllAbsences && $absence->status !== 'accepte' && $absence->status !== 'refuse')
                                <span class="text-muted">{{ __('En attente') }}</span>
                            @else
                                <span class="text-muted">{{ __('Traitée') }}</span>
                            @endif

                            @if($canManageAllAbsences || $isOwner)
                                <a href="{{ route('absences.edit', $absence->id) }}" class="btn btn-warning btn-sm ms-1">
                                    {{ __('Modifier') }}
                                </a>

                                <form action="{{ route('absences.destroy', $absence->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Supprimer cette absence ?') }}')">
                                        {{ __('Supprimer') }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
