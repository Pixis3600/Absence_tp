<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Gestion') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header>
        <h1>{{ __('Gestion') }}</h1>
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">{{ __('Gestion') }}</a>
            <div class="navbar-nav d-flex flex-row gap-3 align-items-center">
                <a class="nav-link" href="{{ route('home') }}">{{ __('Accueil') }}</a>
                <a class="nav-link" href="{{ route('absences.index') }}">{{ __('Absences') }}</a>

                @auth
                    @can('user-view-all')
                        <a class="nav-link" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                        <a class="nav-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                    @endcan

                    <div class="btn-group btn-group-sm ms-2" role="group" aria-label="{{ __('Choisissez votre langue') }}">
                        <a class="btn btn-outline-light {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{ route('locale.switch', 'fr') }}">FR</a>
                        <a class="btn btn-outline-light {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}">EN</a>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="ms-3 mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">{{ __('Déconnexion') }}</button>
                    </form>
                @else
                    <div class="btn-group btn-group-sm ms-2" role="group" aria-label="{{ __('Choisissez votre langue') }}">
                        <a class="btn btn-outline-light {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{ route('locale.switch', 'fr') }}">FR</a>
                        <a class="btn btn-outline-light {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}">EN</a>
                    </div>

                    <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <footer>
        {{ __('Gestion') }}
    </footer>
</body>
</html>
