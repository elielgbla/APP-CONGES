@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 p-5 text-center bg-white">
                <div class="text-warning mb-4">
                    <i class="fa-solid fa-user-lock fa-4x"></i>
                </div>

                <h3 class="fw-bold text-dark mb-2">Compte Inactif/Suspendu</h3>
                <p class="text-muted small px-3">
                    Votre compte est actuellement désactivé ou en attente d'approbation par la direction. Vous ne pouvez pas accéder à l'application.
                </p>

                <div class="alert alert-light border small py-2 my-3 text-start">
                    <i class="fa-solid fa-circle-info text-secondary me-2"></i>
                    <strong>Que faire ?</strong> Veuillez vous référer à l'administrateur système ou au service des Ressources Humaines pour réactiver vos accès.
                </div>

                <div class="mt-4">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-danger px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Retour à la connexion
                    </a>
                </div>

                {{-- Formulaire caché pour déconnecter proprement la session de l'inactif --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
