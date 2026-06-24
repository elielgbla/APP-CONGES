@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #fcfbfa;">

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: #e8e5e1 !important;">
        <div>
            <h1 class="h3 mb-1" style="color: #4a3b32; font-weight: 700;">
                <i class="fas fa-history me-2" style="color: #2c5e3b;"></i>Journalisation & Audit Système
            </h1>
            <p class="text-muted mb-0">Suivi en temps réel des actions de sécurité et des mouvements de données.</p>
        </div>
        <span class="badge px-3 py-2" style="background-color: #2c5e3b; color: #ffffff; font-size: 0.9rem;">
            Espace Sécurisé RH
        </span>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="background-color: #f4f1ea; border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('journalisation.index') }}" class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label small fw-bold" style="color: #5a4a42;">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0" placeholder="Matricule, détails...">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold" style="color: #5a4a42;">Action</label>
                    <select name="action_filter" class="form-select border-0">
                        <option value="">Tous</option>
                        @foreach($distinctActions as $action)
                            <option value="{{ $action }}" {{ request('action_filter') == $action ? 'selected' : '' }}>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold" style="color: #5a4a42;">Composant</label>
                    <select name="table_filter" class="form-select border-0">
                        <option value="">Tous</option>
                        @foreach($distinctTables as $table)
                            <option value="{{ $table }}" {{ request('table_filter') == $table ? 'selected' : '' }}>{{ $table }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold" style="color: #5a4a42;">Du</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-control border-0">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold" style="color: #5a4a42;">Au</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-control border-0">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn w-100 text-white" style="background-color: #2c5e3b;" title="Filtrer">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('journalisation.index') }}" class="btn btn-light border w-100" style="color: #6e584a;" title="Réinitialiser">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #4a3b32; color: #ffffff;">
                    <tr>
                        <th class="ps-4 py-3" style="font-weight: 500;">Horodatage</th>
                        <th style="font-weight: 500;">Collaborateur</th>
                        <th style="font-weight: 500;">Type d'Action</th>
                        <th style="font-weight: 500;">Table Ciblée</th>
                        <th style="font-weight: 500;">Détails de l'opération</th>
                        <th style="font-weight: 500;">Adresse IP</th>
                        <th class="pe-4 text-end" style="font-weight: 500;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr style="border-bottom: 1px solid #f2eee9;">
                            <td class="ps-4 py-3 fw-bold" style="color: #4a3b32; font-size: 0.9rem;">
                                {{ $log->date_action ? \Carbon\Carbon::parse($log->date_action)->format('d/m/Y H:i:s') : '—' }}
                            </td>
                            
                            <td>
                                <div>
                                    @if($log->employe)
                                        <div class="fw-bold text-dark small" style="line-height: 1.2;">
                                            {{ $log->employe->nom }} {{ $log->employe->prenom }}
                                        </div>
                                    @endif
                                    <span class="badge rounded-pill px-2 py-1 mt-1" style="background-color: #efece6; color: #5a4a42; font-weight: 600; font-size: 0.75rem;">
                                        <i class="fas fa-user-tag me-1"></i>{{ $log->matricule_employe ?? 'SYSTEM' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                @php $actionClean = strtolower($log->action); @endphp
                                @if(in_array($actionClean, ['connexion', 'inscription', 'login']))
                                    <span class="badge px-2 py-1" style="background-color: #e2f0d9; color: #2c5e3b;">
                                        <i class="fas fa-sign-in-alt me-1"></i>{{ $log->action }}
                                    </span>
                                @elseif(in_array($actionClean, ['déconnexion', 'logout']))
                                    <span class="badge px-2 py-1" style="background-color: #fce4d6; color: #c65911;">
                                        <i class="fas fa-sign-out-alt me-1"></i>{{ $log->action }}
                                    </span>
                                @elseif(in_array($actionClean, ['suppression', 'delete', 'destruction']))
                                    <span class="badge px-2 py-1" style="background-color: #f8d7da; color: #842029;">
                                        <i class="fas fa-trash-alt me-1"></i>{{ $log->action }}
                                    </span>
                                @else
                                    <span class="badge px-2 py-1" style="background-color: #e1f5fe; color: #0288d1;">
                                        <i class="fas fa-cog me-1"></i>{{ $log->action }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-uppercase font-monospace small fw-bold" style="color: #6e584a;">
                                @if(!empty($log->table_concernee))
                                    <i class="fas fa-table me-1 opacity-50"></i>{{ $log->table_concernee }}
                                @elseif(!empty($log->table_cible)) {{-- Alternative au cas où le nom différerait --}}
                                    <i class="fas fa-table me-1 opacity-50"></i>{{ $log->table_cible }}
                                @else
                                    <span class="text-muted font-sans-serif fw-normal" style="font-style: italic;">Application</span>
                                @endif
                            </td>

                            <td style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #555555; font-size: 0.9rem;" title="{{ $log->details }}">
                                @php $jsonData = json_decode($log->details, true); @endphp

                                @if(is_array($jsonData))
                                    <span class="text-dark">
                                        Navigation sur l'adresse <code class="small bg-light px-1 py-0.5 text-success" style="font-size: 0.85rem;">{{ parse_url($jsonData['url'] ?? '', PHP_URL_PATH) }}</code> 
                                        via la méthode d'accès <strong class="text-uppercase">{{ $jsonData['method'] ?? 'GET' }}</strong>.
                                        @if(!empty($jsonData['input']))
                                            <span class="text-muted italic">(Données transmises enregistrées)</span>
                                        @endif
                                    </span>
                                @else
                                    {{ $log->details }}
                                @endif
                            </td>

                            <td class="text-muted small">
                                <i class="fas fa-desktop me-1 opacity-50"></i>{{ $log->ip_address ?? '127.0.0.1' }}
                            </td>

                            <td class="pe-4 text-end">
                                <a href="{{ route('journalisation.show', $log->code_log) }}" class="btn btn-sm btn-light" style="border-color: #dcd7ce; color: #4a3b32;" title="Voir la trace complète">
                                    <i class="fas fa-eye text-success"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open d-block fs-2 mb-2" style="color: #dcd7ce;"></i>
                                Aucun enregistrement trouvé dans les journaux d'audit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer p-3 d-flex justify-content-center" style="background-color: #ffffff; border-top: 1px solid #f2eee9;">
                {!! $logs->links() !!}
            </div>
        @endif
    </div>
</div>

<style>
    .page-item.active .page-link {
        background-color: #2c5e3b !important;
        border-color: #2c5e3b !important;
        color: #ffffff !important;
    }
    .page-link {
        color: #4a3b32 !important;
    }
</style>
@endsection