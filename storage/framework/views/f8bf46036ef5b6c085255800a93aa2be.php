

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
            <div>
                <h1 class="h2 fw-bold text-brown">Tableau de bord</h1>
                <p class="text-muted mb-0">Ravi de vous revoir, <strong><?php echo e(Auth::user()->nom); ?> <?php echo e(Auth::user()->prenom); ?></strong></p>
            </div>
            <span class="badge bg-secondary p-2 shadow-sm"><i class="fa fa-calendar me-2"></i> <?php echo e(now()->format('d/m/Y')); ?></span>
        </div>

        <div class="row g-3 mb-4">
            
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Solde de congés</h6>
                            <h3 class="fw-bold mb-0 text-brown"><?php echo e($stats['conge_restant'] ?? 0); ?> Jours</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded text-success">
                            <i class="fa-solid fa-umbrella-beach fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white" style="border-left-color: #0d6efd;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Mes demandes</h6>
                            <h3 class="fw-bold mb-0 text-primary"><?php echo e($stats['mes_demandes_compte'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded text-primary">
                            <i class="fa-solid fa-folder-open fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white" style="border-left-color: #ef6c00;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">À valider</h6>
                            <h3 class="fw-bold mb-0 text-warning"><?php echo e($stats['en_attente_validation'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded text-warning">
                            <i class="fa-solid fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(in_array(Auth::user()->code_poste ?? '', ['RH', 'ADMIN'])): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white" style="border-left-color: #5d4037;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Effectif Total</h6>
                            <h3 class="fw-bold mb-0 text-brown"><?php echo e($stats['total_employes'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-dark bg-opacity-10 p-3 rounded text-dark">
                            <i class="fa-solid fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold text-brown mb-3"><i class="fa-solid fa-clock-history me-2"></i> Activités Récentes</h5>
            <p class="text-muted mb-0">Le suivi de vos dernières actions s'affichera ici au fur et à mesure.</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\APP_CONGE\resources\views/dashboard.blade.php ENDPATH**/ ?>