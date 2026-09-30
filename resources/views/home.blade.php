@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <h1 class="mb-3">{{ __('Bienvenue') }}</h1>
                    <p class="lead mb-4">{{ __('Vous êtes connecté au système.') }}</p>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('users.index') }}" class="btn btn-primary">{{ __('Voir les utilisateurs') }}</a>
                        <a href="{{ route('absences.index') }}" class="btn btn-success">{{ __('Voir les absences') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
