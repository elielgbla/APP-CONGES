<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portail RH') - SOGETHO-SA</title>

    <link class="sub-asset" rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link class="sub-asset" rel="stylesheet" href="{{ asset('css/all.min.css') }}">

    <script src="{{ asset('js/chart.umd.js') }}"></script>

    <style>
        /* Force l'affichage du menu déroulant au survol */
        .navbar-nav .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        :root {
            --primary-green: #2e7d32;
            --corporate-brown: #5d4037;
            --bg-cream: #fcfbfa;
        }
        body { 
            background-color: var(--bg-cream); 
            font-family: sans-serif; 
        }
        .bg-marron { 
            background-color: #4a3b32 !important; 
        }
        .text-brown { 
            color: var(--corporate-brown); 
        }
        .card-stat { 
            border: none; 
            border-left: 5px solid var(--primary-green); 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-marron shadow-sm mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="fas fa-leaf text-success me-2"></i>SOGETHO-SA
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">

                    {{-- ========================================================================= --}}
                    {{-- ESPACE AGENT (ACCESSIBLE À TOUS)                                          --}}
                    {{-- ========================================================================= --}}
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-chart-pie me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('demandes.*') ? 'active' : '' }}" href="{{ route('demandes.index') }}">
                            <i class="fa-solid fa-file-signature me-1"></i> Mes Demandes
                        </a>
                    </li>

                    {{-- ========================================================================= --}}
                    {{-- ESPACE CIRCUITS DE VISA (RESP_SERVI, RH, DIR_EXPLOI, DIR_GENERA)          --}}
                    {{-- ========================================================================= --}}
                    @if(Auth::check() && in_array(Auth::user()->code_poste, ['RESP_SERVI', 'RH', 'DIR_EXPLOI', 'DIR_GENERA']))
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('validations.*') ? 'active' : '' }}" href="{{ route('validations.index') }}">
                                <i class="fas fa-stamp me-1 text-warning"></i> Validations en attente
                            </a>
                        </li>
                    @endif

                    {{-- ========================================================================= --}}
                    {{-- ESPACE GESTION ADMINISTRATIVE (RH ET ADMIN)                              --}}
                    {{-- ========================================================================= --}}
                    @if(Auth::check() && in_array(Auth::user()->code_poste, ['RH', 'ADMIN']))
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('employes.*') ? 'active' : '' }}" href="{{ route('employes.index') }}">
                                <i class="fa-solid fa-users me-1"></i> Personnel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('plannings.*') ? 'active' : '' }}" href="{{ route('plannings.index') }}">
                                <i class="fa-solid fa-calendar-days me-1"></i> Plannings Horaires
                            </a>
                        </li>
                    @endif

                    {{-- ========================================================================= --}}
                    {{-- ESPACE ADMINISTRATION TECHNIQUE (ADMIN UNIQUEMENT)                         --}}
                    {{-- ========================================================================= --}}
                    @if(Auth::check() && Auth::user()->code_poste === 'ADMIN')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ Request::routeIs(['type-permissions.*', 'services.*', 'postes.*', 'historique_postes.*', 'contacts.*', 'residences.*']) ? 'active' : '' }}" href="#" id="navbarDropdownDonneesRef" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-database me-1 text-success"></i> Tables de Référence
                            </a>
                            <ul class="dropdown-menu shadow" aria-labelledby="navbarDropdownDonneesRef">
                                <li><h6 class="dropdown-header text-uppercase small fw-bold">Configurations Métier</h6></li>
                                <li>
                                    <a class="dropdown-item small {{ Request::routeIs('type-permissions.*') ? 'active bg-light text-dark' : '' }}" href="{{ route('type-permissions.index') }}">
                                        <i class="fa-solid fa-list-check me-2 text-muted"></i> Types de Permissions
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small {{ Request::routeIs('services.*') ? 'active bg-light text-dark' : '' }}" href="{{ route('services.index') }}">
                                        <i class="fa-solid fa-layer-group me-2 text-muted"></i> Services / Directions
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small {{ Request::routeIs('postes.*') ? 'active bg-light text-dark' : '' }}" href="{{ route('postes.index') }}">
                                        <i class="fa-solid fa-briefcase me-2 text-muted"></i> Postes & Fonctions
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header text-uppercase small fw-bold">Traçabilité & Historiques</h6></li>
                                <li>
                                    <a class="dropdown-item small {{ Request::routeIs('historique_postes.*') ? 'active bg-light text-dark' : '' }}" href="{{ route('historique_postes.index') }}">
                                        <i class="fa-solid fa-timeline me-2 text-muted"></i> Historique & Mutations
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small {{ Request::routeIs('contacts.*') ? 'active bg-light text-dark' : '' }}" href="{{ route('contacts.index') }}">
                                        <i class="fa-solid fa-address-book me-2 text-muted"></i> Répertoire Contacts
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('localites.index') }}" class="flex items-center p-2 {{ request()->routeIs('localites.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-900' }} rounded-lg hover:bg-gray-100">
                                       <i class="fa-solid fa-location-dot"></i>
                                           <span class="ms-3">Gestion Localités</span>
                                     </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small {{ Request::routeIs('residences.*') ? 'active bg-light text-dark' : '' }}" href="{{ route('residences.index') }}">
                                        <i class="fa-solid fa-map-location-dot me-2 text-muted"></i> Historique Résidences
                                    </a>
                                </li>
                               <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header text-uppercase small fw-bold">Bilan</h6></li>

                                <li class="nav-item">
                                  <a class="nav-link {{ request()->routeIs('statistiques.index') ? 'active fw-bold text-primary' : 'text-dark' }}" href="{{ route('statistiques.index') }}">
                                      <i class="fa-solid fa-chart-line me-2 text-secondary"></i>
                                          <span>Statistiques & Décisions</span>
                                      </a>
                                </li>
                            </ul>
                        </li>
                      
        

                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('journalisation.*') ? 'active' : '' }}" href="{{ route('journalisation.index') }}">
                                <i class="fa-solid fa-history me-1"></i> Journaux (Logs)
                            </a>
                        </li>
                    @endif

                </ul>

                {{-- BLOCK PROFIL ET DECONNEXION --}}
                @if(Auth::check())
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle bg-white bg-opacity-10 rounded px-3 text-white" href="#" id="profDrop" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->nom }} {{ Auth::user()->prenom }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow mt-2" aria-labelledby="profDrop">
                            <div class="px-3 py-1 small text-muted border-bottom">
                                Matricule : <strong>{{ Auth::user()->matricule_employe }}</strong>
                            </div>
                            <li>
                                <a class="dropdown-item py-2 {{ Request::routeIs('profil.*') ? 'active' : '' }}" href="{{ route('profil.index') }}">
                                    <i class="fas fa-id-card me-2 text-muted"></i>Mon Profil / Sécurité
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                               <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                 <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
                               </a>

                               <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                   @csrf
                               </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                @endif
            </div>
        </div>
    </nav>

    {{-- ZONE DES MESSAGES FLASH ET CONTENU GLOBAL --}}
    <main class="container-fluid px-4">
        
        {{-- Message de Succès --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Message d'Erreur Applicative --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Injection du contenu de la vue fille --}}
        @yield('content')
        
    </main>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function ouvrirFenetreImpression(url) {
            const largeur = 900;
            const hauteur = 700;
            const gauche = (screen.width - largeur) / 2;
            const haut = (screen.height - hauteur) / 2;

            const options = `width=${largeur},height=${hauteur},left=${gauche},top=${haut},scrollbars=yes,toolbar=no,status=no,location=no,menubar=no`;
            const fenetre = window.open(url, 'Impression_SOGETHO', options);

            if (fenetre) {
                fenetre.focus();
            } else {
                alert('Veuillez autoriser les fenêtres pop-up pour pouvoir imprimer les documents.');
            }
        }
    </script>
</body>
</html>