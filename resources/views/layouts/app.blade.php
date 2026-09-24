<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon web</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header>
        <h1>Mon site test</h1>
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Gestion</a>
            <div class="navbar-nav d-flex flex-row gap-3 align-items-center">
                <a class="nav-link" href="{{ route('home') }}">Accueil</a>
                <a class="nav-link" href="{{ route('absences.index') }}">Absences</a>

                @auth
                    @if(Auth::user()->is_admin)
                        <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="ms-3 mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Déconnexion</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                    <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <footer>
        footerTest
    </footer>
</body>
</html>
