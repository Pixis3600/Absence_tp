<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Absences du salarié') }}</title>
</head>
<body>
    <h1>{{ __('Absences du salarié') }}</h1>

    <p><strong>ID :</strong> {{ $user->id }}</p>
    <p><strong>{{ __('Prénom') }} :</strong> {{ $user->name }}</p>
    <p><strong>{{ __('Nom') }} :</strong> {{ $user->lastname }}</p>
    <p><strong>{{ __('Email') }} :</strong> {{ $user->email }}</p>
    <p><strong>{{ __('Rôle') }} :</strong> {{ $user->roles->first()?->name ?? __('Aucun') }}</p>

    <h2>{{ __('Liste des absences') }}</h2>

    @if ($user->absences->isEmpty())
        <p>{{ __('Aucune absence enregistrée pour ce salarié.') }}</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('Date de début') }}</th>
                    <th>{{ __('Date de fin') }}</th>
                    <th>{{ __('Motif') }}</th>
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

    <p><a href="{{ url()->previous() }}">{{ __('Retour') }}</a></p>
</body>
</html>
