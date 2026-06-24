<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - SOGETHO-SA</title>
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
            background: rgba(255,255,255,0.95);
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
                        <p class="text-muted mb-0">Réinitialisation du mot de passe</p>
                    </div>

                    <p class="text-center text-muted mb-4 small">
                        Saisissez votre adresse email pour recevoir le lien de réinitialisation.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm small">
                            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold mini-text">Adresse email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control soft-input @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="ex: employe@sogetho-sa.com"
                                   required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-brand w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le lien
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color:#2c5e3b;">
                            Retour à la connexion
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