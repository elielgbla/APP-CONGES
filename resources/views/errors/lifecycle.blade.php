@extends('layouts.app')

@section('content')
<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 p-5 bg-white">
                <div class="text-danger mb-4">
                    <i class="fa-solid fa-hourglass-end fa-4x animate__animated animate__pulse animate__infinite"></i>
                </div>
                
                <h3 class="fw-bold text-dark mb-3">Mise à niveau système obligatoire</h3>
                <p class="text-muted small px-3">
                    Cette application a atteint sa limite maximale d'utilisation continue sans vérification technique.
                </p>

                <div class="alert alert-danger border-0 small py-3 my-4 text-start">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Sécurité & Stabilité :</strong> Pour des raisons de performance, de sécurité des données et de mise à jour des dépendances, l'accès est momentanément restreint.
                </div>

                <p class="fw-bold text-secondary">
                    Veuillez contacter votre développeur / administrateur pour appliquer le patch de maintenance.
                </p>

                <div class="mt-4 border-top pt-3">
                    <span class="text-muted small">Code d'erreur : <code class="text-danger">LIFECYCLE_EXPIRED_5Y</code></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection