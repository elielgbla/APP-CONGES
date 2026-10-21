@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">

    <!-- En-tête de la page -->
    <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom" style="border-color: #e8e5e1 !important;">
        <div>
            <h1 class="h2 fw-bold text-brown" style="color: #4a3b32;">Gestion des Demandes de Congé</h1>
            <p class="text-muted mb-0">Suivi des flux de validation et historique des absences.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="lancerImpressionRegistre()" class="btn btn-sm btn-outline-secondary px-3">
                <i class="fa-solid fa-print me-1"></i> Imprimer le registre
            </button>

            <script>
            function lancerImpressionRegistre() {
                const selectStatut = document.getElementById('statut_simple') || document.getElementById('statut') || document.getElementById('filter_statut');
                const valeurStatut = selectStatut ? selectStatut.value : 'tous';
                const url = "{{ route('demandes.imprimerRegistre') }}?statut=" + valeurStatut;
                ouvrirFenetreImpression(url);
            }
            </script>
            
            <a href="{{ route('demandes.create') }}" class="btn btn-sm text-white px-3" style="background-color: #2e7d32; font-weight: 500; border-radius: 6px;">
                <i class="fa-solid fa-plus me-1"></i> Nouvelle demande
            </a>
        </div>
    </div>

    <!-- Alertes de session -->
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 8px; background-color: #e8f5e9; color: #1b5e20;">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 8px; background-color: #ffebee; color: #c62828;">
        <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Bloc des filtres de recherche -->
    <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 8px;">
        <div class="card-body p-3">
            <form action="{{ route('demandes.index') }}" method="GET" id="formulaire_recherche">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 border" style="border-color: #ced4da;"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control form-control-sm border-start-0"
                                placeholder="Recherche rapide (matricule, nom...)" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select name="statut" id="statut_simple" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tous les statuts du circuit</option>
                            <option value="en_attente_n1" {{ request('statut') == 'en_attente_n1' ? 'selected' : '' }}>En attente N1 (Resp. Service)</option>
                            <option value="en_attente_n2" {{ request('statut') == 'en_attente_n2' ? 'selected' : '' }}>En attente N2 (RH)</option>
                            <option value="en_attente_n3" {{ request('statut') == 'en_attente_n3' ? 'selected' : '' }}>En attente N3 (Dir. Exploitation)</option>
                            <option value="en_attente_n4" {{ request('statut') == 'en_attente_n4' ? 'selected' : '' }}>En attente N4 (DG)</option>
                            <option value="approuvee" {{ request('statut') == 'approuvee' ? 'selected' : '' }}>Approuvées (Validées RH)</option>
                            <option value="rejetee" {{ request('statut') == 'rejetee' ? 'selected' : '' }}>Rejetées</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-dark flex-grow-1" style="border-radius: 6px;">Filtrer</button>
                        <button class="btn btn-sm btn-outline-dark" style="border-radius: 6px;" type="button" data-bs-toggle="collapse" data-bs-target="#recherche_avancee" aria-expanded="false">
                            <i class="fa-solid fa-sliders"></i> Avancé
                        </button>
                    </div>
                </div>

                <!-- Accordéon recherche avancée -->
                <div class="collapse {{ request()->filled('filter_matricule') || request()->filled('filter_type') || request()->filled('date_debut_du') ? 'show' : '' }}" id="recherche_avancee">
                    <div class="row g-3 pt-3 mt-2 border-top" style="border-color: #f2eee9 !important;">
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Par Matricule Employé</label>
                            <select name="filter_matricule" class="form-select form-select-sm">
                                <option value="">Choisir un employé...</option>
                                @foreach($employes as $emp)
                                    <option value="{{ $emp->matricule_employe }}" {{ request('filter_matricule') == $emp->matricule_employe ? 'selected' : '' }}>
                                        {{ $emp->matricule_employe }} - {{ $emp->nom }} {{ $emp->prenom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Par Type de Permission</label>
                            <select name="filter_type" class="form-select form-select-sm">
                                <option value="">Choisir un type...</option>
                                @foreach($types_permission as $tp)
                                    <option value="{{ $tp->code_type_permission }}" {{ request('filter_type') == $tp->code_type_permission ? 'selected' : '' }}>
                                        {{ $tp->libelle_type_permission }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Date Début Du</label>
                            <input type="date" name="date_debut_du" class="form-control form-control-sm" value="{{ request('date_debut_du') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Au</label>
                            <input type="date" name="date_debut_au" class="form-control form-control-sm" value="{{ request('date_debut_au') }}">
                        </div>
                        <div class="col-12 text-end">
                            <a href="{{ route('demandes.index') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">Réinitialiser</a>
                            <button type="submit" class="btn btn-sm text-white px-4" style="background-color: #4a3b32; border-radius: 6px;">Lancer l'analyse</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau de suivi des demandes -->
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 8px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="text-white font-monospace" style="background-color: #4a3b32;">
                    <tr>
                        <th class="ps-3 py-3">Code Demande</th>
                        <th>Agent</th>
                        <th>Type Congé / Permission</th>
                        <th>Période Prévue</th>
                        <th class="text-center">Durée</th>
                        <th class="text-center">Étape de Validation</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($demandes as $d)
                    <tr class="border-bottom" style="border-color: #f2eee9 !important;">
                        <td class="ps-3 font-monospace fw-bold text-secondary">{{ $d->code_demande }}</td>
                        <td>
                            @if($d->employe)
                                <div class="fw-bold text-dark">{{ $d->employe->nom }} {{ $d->employe->prenom }}</div>
                            @else
                                <div class="fw-bold text-danger">Employé inconnu</div>
                            @endif
                            <div class="small text-muted font-monospace">{{ $d->matricule_employe }}</div>
                        </td>
                        <td>
                            <span class="fw-semibold text-brown" style="color: #4a3b32;">
                                {{ $d->typePermission->libelle_type_permission ?? $d->code_type_permission }}
                            </span>
                        </td>
                        <td>
                            <div class="small">
                                <span class="text-muted">Du :</span> 
                                {{ \Carbon\Carbon::parse($d->date_debut_prevu)->format('d/m/Y') }}
                            </div>
                            <div class="small">
                                <span class="text-muted">Au :</span> 
                                {{ \Carbon\Carbon::parse($d->date_fin_prevu)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="text-center fw-bold text-success">
                            {{ $d->nombre_jours ?? (\Carbon\Carbon::parse($d->date_debut_prevu)->diffInDays(\Carbon\Carbon::parse($d->date_fin_prevu)) + 1) }} jours
                        </td>
                        <td class="text-center">
                            @if($d->statut_demande === 'approuvee')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 w-100" style="border-radius: 6px;">Approuvée (RH)</span>
                            @elseif($d->statut_demande === 'rejetee')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 w-100" style="border-radius: 6px;">Rejetée</span>
                            @elseif($d->statut_demande === 'en_attente_n1')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 w-100" style="border-radius: 6px; color: #b58105 !important;">N1 - Resp. Service</span>
                            @elseif($d->statut_demande === 'en_attente_n2')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 w-100" style="border-radius: 6px; color: #0288d1 !important;">N2 - RH</span>
                            @elseif($d->statut_demande === 'en_attente_n3')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 w-100" style="border-radius: 6px; color: #1565c0 !important;">N3 - Dir. Exploitation</span>
                            @elseif($d->statut_demande === 'en_attente_n4')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 w-100" style="border-radius: 6px; color: #555555 !important;">N4 - DG (Visa Final)</span>
                            @else
                                <span class="badge bg-light text-dark border px-3 py-2 w-100 font-monospace" style="border-radius: 6px;">
                                    {{ $d->statut_demande }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('demandes.show', $d->code_demande) }}" class="btn btn-outline-secondary" title="Ouvrir le dossier">
                                    <i class="fa-solid fa-folder-open"></i>
                                </a>

                                <button onclick="ouvrirFenetreImpression('{{ route('demandes.imprimerUnique', $d->code_demande) }}')" class="btn btn-outline-dark" title="Imprimer cette fiche">
                                    <i class="fa-solid fa-print"></i>
                                </button>
    
                                @if($d->statut_demande === 'en_attente_n1')
                                <a href="{{ route('demandes.edit', $d->code_demande) }}" class="btn btn-outline-warning" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('demandes.destroy', $d->code_demande) }}" method="POST" onsubmit="return confirm('Confirmer la suppression irréversible de cette demande ?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Aucune demande de congé enregistrée ou correspondante.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($demandes->hasPages())
        <div class="card-footer bg-white pt-3">
            {{ $demandes->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function ouvrirFenetreImpression(url) {
    const largeur = 900;
    const hauteur = 750;
    
    const gauche = (screen.width - largeur) / 2;
    const haut = (screen.height - hauteur) / 2;

    const popup = window.open(
        url, 
        'Impression_Sogetho', 
        `width=${largeur},height=${hauteur},top=${haut},left=${gauche},resizable=yes,scrollbars=yes,status=no,toolbar=no,menubar=no`
    );

    if (window.focus && popup) {
        popup.focus();
    }
}
</script>
@endsection