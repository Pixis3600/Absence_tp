<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absences du salarié</title>
</head>
<body>
    <h1>Absences du salarié</h1>

    <p><strong>ID :</strong> {{ $user->id }}</p>
    <p><strong>Prénom :</strong> {{ $user->name }}</p>
    <p><strong>Nom :</strong> {{ $user->lastname }}</p>
    <p><strong>Email :</strong> {{ $user->email }}</p>

    <h2>Liste des absences</h2>

    @if ($user->absences->isEmpty())
        <p>Aucune absence enregistrée pour ce salarié.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Motif</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->absences as $absence)
                    <tr>
                        <td>{{ $absence->id }}</td>
                        <td>{{ $absence->date_debut?->format('d/m/Y') }}</td>
                        <td>{{ $absence->date_fin?->format('d/m/Y') }}</td>
                        <td>{{ $absence->motif }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p><a href="{{ url()->previous() }}">Retour</a></p>
</body>
</html>
