@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-4">Détail de la localité</h1>

            <dl class="row mb-4">
                <dt class="col-sm-3">Code</dt>
                <dd class="col-sm-9">{{ $localite->code_localite }}</dd>

                <dt class="col-sm-3">Libellé</dt>
                <dd class="col-sm-9">{{ $localite->libelle_localite }}</dd>
            </dl>

            <a href="{{ route('localites.index') }}" class="btn btn-outline-secondary">Retour à la liste</a>
            <a href="{{ route('localites.edit', $localite->code_localite) }}" class="btn btn-primary">Modifier</a>
        </div>
    </div>
</div>
@endsection
