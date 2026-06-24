<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SOGETHO-SA</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <style>
        body {
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(44,94,59,0.95), rgba(74,59,50,0.92)),
                url('{{ asset('images/palm_club_hotel.png') }}') center/cover no-repeat fixed;
        }
        .auth-card {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.94);
            border: 0;
            border-radius: 22px;
            box-shadow: 0 18px 50px rgba(0,0,0,0.18);
        }
        .brand-badge {
            width: 92px;
            height: 92px;
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
            background: linear-gradient(135deg, #2c5e3b, #4a3b32);
            border: none;
            color: #fff;
        }
        .btn-brand:hover {
            color: #fff;
            opacity: .95;
        }
        .soft-input:focus {
            border-color: #2c5e3b;
            box-shadow: 0 0 0 .2rem rgba(44,94,59,.15);
        }
        .mini-text {
            color: #6e584a;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card auth-card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/palm_club_logo.png') }}" alt="SOGETHO-SA" class="brand-badge mb-3">
                        <h2 class="fw-bold title-main mb-1">SOGETHO-SA</h2>
                        <p class="text-muted mb-0">Portail RH sécurisé</p>
                    </div>

                    <div class="mb-4 text-center">
                        <span class="badge rounded-pill px-3 py-2" style="background-color:#2c5e3b; color:#fff;">Connexion employé</span>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="matricule_employe" class="form-label fw-semibold mini-text">Matricule ou Adresse Email</label>
                            <input type="text"
                                   name="matricule_employe"
                                   id="matricule_employe"
                                   class="form-control soft-input @error('matricule_employe') is-invalid @enderror"
                                   value="{{ old('matricule_employe') }}"
                                   placeholder="Ex: EMP0001 ou employe@sogetho.ci"
                                   required autofocus>
                            @error('matricule_employe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="mot_de_passe" class="form-label fw-semibold mini-text">Mot de passe</label>
                            <input type="password"
                                   name="mot_de_passe"
                                   id="mot_de_passe"
                                   class="form-control soft-input @error('mot_de_passe') is-invalid @enderror"
                                   placeholder="••••••••"
                                   required>
                            @error('mot_de_passe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-brand w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-right-to-bracket me-2"></i>Se connecter
                        </button>
                        
                        <div class="text-center mt-3">
                             <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold" style="color:#2c5e3b;">
                                 Mot de passe oublié ?
                             </a>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted">
                            Nouveau sur la plateforme ?
                            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none" style="color:#2c5e3b;">Créer un compte</a>
                        </p>
                    </div>
                </div>
            </div>

            <p class="text-center mt-3 mb-0 small text-white-50">
                © {{ date('Y') }} SOGETHO-SA · Tous droits réservés
            </p>
        </div>
    </div>
</div>

</body>
</html>