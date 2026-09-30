<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Absence mise a jour') }}</title>
</head>
<body>
    <h2>{{ __('Votre absence a ete mise a jour') }}</h2>

    <p>{{ __('Bonjour :name,', ['name' => $absence->user?->name ?? '']) }}</p>

    <p>{{ __('Les informations de votre absence ont ete modifiees.') }}</p>

    <ul>
        <li>{{ __('Date de debut') }}: {{ optional($absence->date_debut)->format('d/m/Y') }}</li>
        <li>{{ __('Date de fin') }}: {{ optional($absence->date_fin)->format('d/m/Y') }}</li>
        <li>{{ __('Motif') }}: {{ $absence->motif }}</li>
        <li>{{ __('Statut') }}: {{ $absence->status ?? 'en_attente' }}</li>
    </ul>

    <p>{{ __('Merci.') }}</p>
</body>
</html>
