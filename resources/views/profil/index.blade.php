@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mon Profil Personnel</h1>
        <p class="text-muted small">Consultez vos informations de carrière et gérez vos accès de sécurité.</p>
    </div>

    {{-- Alertes de notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><strong>Erreur !</strong> Veuillez vérifier les champs du formulaire.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- BLOC GAUCHE : FICHE D'IDENTITÉ VISUELLE --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow border-left-primary">
                <div class="card-body text-center pt-4">
                    <div class="mb-3 position-relative d-inline-block">
                        @if(!empty($user->photo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->photo))
                            <img src="{{ asset('storage/' . $user->photo) }}" class="rounded-circle img-thumbnail shadow-sm" style="width: 140px; height: 140px; object-fit: cover;" alt="Photo de profil">
                        @else
                            {{-- Génération automatique d'un avatar avec initiales si aucune photo n'est présente --}}
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nom . ' ' . $user->prenom) }}&background=4e73df&color=fff&size=140" class="rounded-circle img-thumbnail shadow-sm" alt="Avatar par défaut">
                        @endif
                    </div>
                    <h5 class="font-weight-bold text-gray-800 mb-1">{{ strtoupper($user->nom) }} {{ ucwords($user->prenom) }}</h5>
                    <p class="text-muted small mb-3">{{ $user->poste->libelle_poste ?? 'Poste non assigné' }}</p>
                    <span class="badge bg-dark px-3 py-2 font-monospace">{{ $user->matricule_employe }}</span>
                </div>
                
                <div class="card-body bg-light border-top small">
                    <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-briefcase me-2"></i>Détails Carrière & Contrat</h6>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Service :</div>
                        <div class="col-7 text-dark fw-semibold">{{ $user->service->libelle_service ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Catégorie :</div>
                        <div class="col-7 text-dark fw-semibold">{{ $user->categorie->libelle_categorie ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Genre :</div>
                        <div class="col-7 text-dark fw-semibold">{{ $user->genre->libelle_genre ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Lieu Naiss. :</div>
                        <div class="col-7 text-dark fw-semibold">{{ $user->localiteNaissance->libelle_localite ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-5 text-muted fw-bold">Solde Congés :</div>
                        <div class="col-7 text-success fw-bold">{{ $user->total_conge_restant }} Jours</div>
                    </div>
                </div>

                {{-- ZONE DANGER : DÉSACTIVATION COMPTE --}}
                <div class="card-footer bg-white text-center py-3">
                    <form action="{{ route('profil.destroy') }}" method="POST" onsubmit="return confirm('ATTENTION : Cette action va désactiver votre compte et vous déconnecter de l\'application. Continuer ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none small">
                            <i class="fas fa-power-off me-1"></i> Désactiver mon compte utilisateur
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- BLOC DROITE : FORMULAIRE DE MISE À JOUR --}}
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-edit me-2"></i>Modifier mes informations personnelles</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Champ Nom --}}
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label fw-bold text-secondary text-xs text-uppercase">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Champ Prénom --}}
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label fw-bold text-secondary text-xs text-uppercase">Prénom(s) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Champ Email --}}
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold text-secondary text-xs text-uppercase">Adresse Email Professionnelle <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>



                        {{-- BLOC SÉCURITÉ : MOT DE PASSE --}}
                        <div class="card bg-light border p-3 mb-4">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-lock me-2"></i>Sécurité & Mot de passe</h6>
                            <p class="text-muted small">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe actuel.</p>
                            
                            {{-- Ancien mot de passe --}}
                            <div class="mb-3">
                                <label for="ancien_mdp" class="form-label fw-bold text-secondary text-xs text-uppercase">Mot de passe actuel</label>
                                <input type="password" class="form-control @error('ancien_mdp') is-invalid @enderror" id="ancien_mdp" name="ancien_mdp" placeholder="Obligatoire pour valider le nouveau mot de passe">
                                @error('ancien_mdp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                {{-- Nouveau mot de passe --}}
                                <div class="col-md-6 mb-3">
                                    <label for="nouveau_mdp" class="form-label fw-bold text-secondary text-xs text-uppercase">Nouveau mot de passe</label>
                                    <input type="password" class="form-control @error('nouveau_mdp') is-invalid @enderror" id="nouveau_mdp" name="nouveau_mdp" placeholder="Minimum 8 caractères">
                                    @error('nouveau_mdp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Confirmation du nouveau mot de passe --}}
                                <div class="col-md-6 mb-3">
                                    <label for="nouveau_mdp_confirmation" class="form-label fw-bold text-secondary text-xs text-uppercase">Confirmer le nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="nouveau_mdp_confirmation" name="nouveau_mdp_confirmation" placeholder="Répéter le mot de passe">
                                </div>
                            </div>
                        </div>

                        {{-- BOUTON SOUMISSION --}}
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i>Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection