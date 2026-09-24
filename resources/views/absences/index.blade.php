@extends('layouts.app')

@section('content')
@php
    $isAdmin = Auth::check() && Auth::user()->is_admin;
@endphp
<div class="container mt-4">
    <h2>Liste des absences</h2>

    <a href="{{ route('absences.create') }}" class="btn btn-primary mb-3">
        Ajouter une absence
    </a>

    @if($absences->isEmpty())
        <p>Aucune absence enregistrée.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Actions</th>
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
                            'accepte' => 'Accepté',
                            'refuse' => 'Refusé',
                            default => 'En attente',
                        };

                        $isOwner = Auth::check() && $absence->user_id === Auth::id();
                    @endphp
                    <tr>
                        <td>{{ $absence->user->name ?? 'Inconnu' }}</td>
                        <td>{{ $absence->date_debut }}</td>
                        <td>{{ $absence->date_fin }}</td>
                        <td>{{ $absence->motif }}</td>
                        <td>
                            <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            @if($isAdmin && (($absence->status ?? 'en_attente') === 'en_attente'))
                                <form action="{{ route('absences.accept', $absence->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Accepter</button>
                                </form>

                                <form action="{{ route('absences.reject', $absence->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
                                </form>
                            @elseif(! $isAdmin && $absence->status !== 'accepte' && $absence->status !== 'refuse')
                                <span class="text-muted">En attente</span>
                            @else
                                <span class="text-muted">Traitée</span>
                            @endif

                            @if($isAdmin || $isOwner)
                                <a href="{{ route('absences.edit', $absence->id) }}" class="btn btn-warning btn-sm ms-1">
                                    Modifier
                                </a>

                                <form action="{{ route('absences.destroy', $absence->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette absence ?')">
                                        Supprimer
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
