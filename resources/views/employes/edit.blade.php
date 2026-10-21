@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom" style="border-color: #e8e5e1 !important;">
        <div>
            <a href="{{ route('employes.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="color: #4a3b32; border-color: #dcd7ce;">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <h1 class="h2 fw-bold text-brown">Modifier l'employé</h1>
            <p class="text-muted mb-0">Mettez à jour les informations du collaborateur sélectionné.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 8px;">
            <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i> Erreurs de validation :</div>
            <ul class="mb-0 small ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employes.update', $employe->matricule_employe) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-12 col-lg-8">

                <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 8px;">
                    <div class="card-header text-white py-3" style="background-color: #4a3b32;">
                        <h5 class="card-title mb-0 small text-uppercase fw-bold">
                            <i class="fa-solid fa-id-card me-2"></i> État Civil & Identité
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Matricule Employé <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="matricule_employe"
                                       class="form-control form-control-sm font-monospace fw-bold @error('matricule_employe') is-invalid @enderror"
                                       value="{{ old('matricule_employe', $employe->matricule_employe) }}"
                                       required>
                                @error('matricule_employe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Genre <span class="text-danger">*</span></label>
                                <select name="code_genre" class="form-select form-select-sm @error('code_genre') is-invalid @enderror" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->code_genre }}" {{ old('code_genre', $employe->code_genre) == $genre->code_genre ? 'selected' : '' }}>
                                            {{ $genre->libelle_genre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Adresse Email</label>
                                <input type="email"
                                       name="email"
                                       class="form-control form-control-sm @error('email') is-invalid @enderror"
                                       value="{{ old('email', $employe->email) }}"
                                       placeholder="exemple@domaine.ci">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nom <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="nom"
                                       class="form-control form-control-sm @error('nom') is-invalid @enderror"
                                       value="{{ old('nom', $employe->nom) }}"
                                       required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Prénoms <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="prenom"
                                       class="form-control form-control-sm @error('prenom') is-invalid @enderror"
                                       value="{{ old('prenom', $employe->prenom) }}"
                                       required>
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Date de naissance</label>
                                <input type="date"
                                       name="date_naissance"
                                       class="form-control form-control-sm @error('date_naissance') is-invalid @enderror"
                                       value="{{ old('date_naissance', $employe->date_naissance ? \Carbon\Carbon::parse($employe->date_naissance)->format('Y-m-d') : '') }}">
                                @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Localité de naissance</label>
                                <select name="code_localite_naissance" class="form-select form-select-sm @error('code_localite_naissance') is-invalid @enderror">
                                    <option value="">Choisir...</option>
                                    @foreach($localites as $l)
                                        <option value="{{ $l->code_localite }}" {{ old('code_localite_naissance', $employe->code_localite_naissance) == $l->code_localite ? 'selected' : '' }}>
                                            {{ $l->libelle_localite }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_localite_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            
                        
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Catégorie <span class="text-danger">*</span></label>
                                <select name="code_categorie" class="form-select form-select-sm @error('code_categorie') is-invalid @enderror" required>
                                    <option value="">Choisir...</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->code_categorie }}" {{ old('code_categorie', $employe->code_categorie) == $c->code_categorie ? 'selected' : '' }}>
                                            {{ $c->libelle_categorie }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_categorie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>


                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Solde Congé Actuel <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="total_conge_restant"
                                       class="form-control form-control-sm fw-bold text-success @error('total_conge_restant') is-invalid @enderror"
                                       min="0"
                                       value="{{ old('total_conge_restant', $employe->total_conge_restant) }}"
                                       required>
                                @error('total_conge_restant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                    </div>
                </div>




                </div>
            </div>

            <div class="col-12 col-lg-4">

                <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="card-title mb-0 small text-uppercase fw-bold"><i class="fa-solid fa-lock me-2"></i> Sécurité</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Statut Compte <span class="text-danger">*</span></label>
                            <select name="statut_compte" class="form-select form-select-sm @error('statut_compte') is-invalid @enderror" required>
                                <option value="actif" {{ old('statut_compte', $employe->statut_compte) == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ old('statut_compte', $employe->statut_compte) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                <option value="suspendu" {{ old('statut_compte', $employe->statut_compte) == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                            </select>
                            @error('statut_compte') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nouveau mot de passe</label>
                            <input type="password"
                                   name="mot_de_passe"
                                   class="form-control form-control-sm @error('mot_de_passe') is-invalid @enderror"
                                   placeholder="Laisser vide pour ne pas changer">
                            @error('mot_de_passe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn text-white w-100 py-2 shadow-sm mb-2" style="background-color: #2e7d32; font-weight: 600; border-radius: 6px;">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Mettre à jour l'employé
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('photoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if(preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    preview.outerHTML = `<img id="imagePreview" src="${e.target.result}" alt="Aperçu" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 2px solid #2e7d32;">`;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<style>
    .text-brown { color: #4a3b32 !important; }
</style>
@endsection
