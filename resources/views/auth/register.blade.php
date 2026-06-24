<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - SOGETHO-SA</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <style>
        body {
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(74,59,50,0.92), rgba(44,94,59,0.92)),
                url('{{ asset('images/palm_club_hotel.png') }}') center/cover no-repeat fixed;
        }
        .auth-card {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.96);
            border: 0;
            border-radius: 22px;
            box-shadow: 0 18px 50px rgba(0,0,0,0.18);
        }
        .brand-badge {
            width: 88px;
            height: 88px;
            object-fit: contain;
            background: #fff;
            border-radius: 18px;
            padding: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .title-main {
            color: #4a3b32;
            letter-spacing: .5px;
        }
        .btn-brand {
            background: linear-gradient(135deg, #4a3b32, #2c5e3b);
            border: none;
            color: #fff;
        }
        .btn-brand:hover {
            color: #fff;
            opacity: .95;
        }
        .soft-input:focus, .soft-select:focus {
            border-color: #2c5e3b;
            box-shadow: 0 0 0 .2rem rgba(44,94,59,.15);
        }
        .mini-text {
            color: #6e584a;
        }
        .section-chip {
            background: #efece6;
            color: #4a3b32;
        }
    </style>
</head>
<body class="py-4">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card auth-card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/palm_club_logo.png') }}" alt="SOGETHO-SA" class="brand-badge mb-3">
                        <h2 class="fw-bold title-main mb-1">SOGETHO-SA</h2>
                        <p class="text-muted mb-0">Création de compte employé</p>
                    </div>

                    <div class="mb-4 text-center">
                        <span class="badge rounded-pill px-3 py-2 section-chip">Formulaire d'inscription sécurisé</span>
                    </div>

                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold mini-text">Matricule Employé <span class="text-danger">*</span></label>
                                <input type="text" name="matricule_employe" class="form-control soft-input @error('matricule_employe') is-invalid @enderror" value="{{ old('matricule_employe') }}" placeholder="Ex: EMP202601" required>
                                @error('matricule_employe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold mini-text">Adresse Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control soft-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="employe@sogetho-sa.com" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold mini-text">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="nom" class="form-control soft-input @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold mini-text">Prénoms <span class="text-danger">*</span></label>
                                <input type="text" name="prenom" class="form-control soft-input @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" required>
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold mini-text">Genre <span class="text-danger">*</span></label>
                                <select name="code_genre" class="form-select soft-select @error('code_genre') is-invalid @enderror" required>
                                    <option value="">Sélectionnez...</option>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->code_genre }}" {{ old('code_genre') == $genre->code_genre ? 'selected' : '' }}>
                                            {{ $genre->libelle_genre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold mini-text">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-control soft-input @error('date_naissance') is-invalid @enderror" value="{{ old('date_naissance') }}">
                                @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold mini-text">Lieu de naissance <span class="text-danger">*</span></label>
                                <select name="code_localite_naissance" class="form-select soft-select @error('code_localite_naissance') is-invalid @enderror" required>
                                    <option value="">Sélectionnez...</option>
                                    @foreach($localites as $localite)
                                        <option value="{{ $localite->code_localite }}" {{ old('code_localite_naissance') == $localite->code_localite ? 'selected' : '' }}>
                                            {{ $localite->libelle_localite }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_localite_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold mini-text">Service Affecté <span class="text-danger">*</span></label>
                                <select name="code_service" class="form-select soft-select @error('code_service') is-invalid @enderror" required>
                                    <option value="">Sélectionnez...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->code_service }}" {{ old('code_service') == $service->code_service ? 'selected' : '' }}>
                                            {{ $service->libelle_service }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_service') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold mini-text">Poste / Rôle <span class="text-danger">*</span></label>
                                <select name="code_poste" class="form-select soft-select @error('code_poste') is-invalid @enderror" required>
                                    <option value="">Sélectionnez...</option>
                                    @foreach($postes as $poste)
                                        <option value="{{ $poste->code_poste }}" {{ old('code_poste') == $poste->code_poste ? 'selected' : '' }}>
                                            {{ $poste->libelle_poste }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_poste') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold mini-text">Catégorie <span class="text-danger">*</span></label>
                                <select name="code_categorie" class="form-select soft-select @error('code_categorie') is-invalid @enderror" required>
                                    <option value="">Sélectionnez...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->code_categorie }}" {{ old('code_categorie') == $cat->code_categorie ? 'selected' : '' }}>
                                            {{ $cat->libelle_categorie }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_categorie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mini-text">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password" name="mot_de_passe" class="form-control soft-input @error('mot_de_passe') is-invalid @enderror" placeholder="Minimum 6 caractères" required>
                                @error('mot_de_passe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mini-text">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                <input type="password" name="mot_de_passe_confirmation" class="form-control soft-input" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-user-plus me-2"></i>Finaliser l'Inscription
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color:#2c5e3b;">
                            Retour à la page de connexion
                        </a>
                    </div>
                </div>
            </div>

            <p class="text-center mt-3 mb-0 small text-white-50">
                © {{ date('Y') }} SOGETHO-SA · Portail RH
            </p>
        </div>
    </div>
</div>

</body>
</html>