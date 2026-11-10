<?php $__env->startSection('content'); ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Emploi du Temps du Personnel</h1>
            <p class="text-muted small mb-0">Liste des agents disposant d'un planning actif à la SOGETHO-SA.</p>
        </div>
        <a href="<?php echo e(route('plannings.create')); ?>" class="btn btn-sm btn-success px-3">
            <i class="fa-solid fa-calendar-plus me-1"></i> Fixer un planning
        </a>
    </div>

    <!-- Barre de filtrage et recherche -->
    <div class="row mb-4">
        <div class="col-md-5">
            <form action="<?php echo e(route('plannings.index')); ?>" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un agent (Nom, matricule...)..." value="<?php echo e($search); ?>">
                </div>
                <button type="submit" class="btn btn-sm btn-dark px-3">Filtrer</button>
            </form>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success border-0 shadow-sm small py-2 mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <!-- TABLEAU SIMPLIFIÉ DES PERSONNES PROGRAMMÉES -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 small">
                <thead class="table-light text-muted" style="font-size: 0.75rem;">
                    <tr>
                        <th style="width: 20%;">Matricule</th>
                        <th class="text-start" style="width: 45%;">Nom & Prénoms</th>
                        <th style="width: 15%;">Programmes Actifs</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $employes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="badge bg-danger px-2 py-1" style="font-size: 0.75rem;"><?php echo e($emp->matricule_employe); ?></span>
                            </td>
                            <td class="text-start">
                                <strong class="text-dark text-uppercase"><?php echo e($emp->nom); ?></strong> <?php echo e($emp->prenom); ?>

                            </td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-semibold">
                                    <?php echo e($emp->planning_count); ?> créneau(x)
                                </span>
                            </td>
                            <td>
                                <!-- Action unique : Redirection vers le Show pro de l'agent -->
                                <a href="<?php echo e(route('plannings.show', $emp->matricule_employe)); ?>" class="btn btn-sm btn-primary px-3 py-1 rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                                    <i class="fa-solid fa-eye me-1"></i> Voir le planning
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-muted py-4">
                                <i class="fa-solid fa-calendar-xmark d-block fs-4 mb-2 text-secondary"></i>
                                Aucun agent ne possède de programme actuellement.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\APP-CONGES\resources\views/plannings/index.blade.php ENDPATH**/ ?>