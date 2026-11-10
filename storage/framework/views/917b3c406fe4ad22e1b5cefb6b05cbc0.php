<?php $__env->startSection('title', 'Tableau de bord'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        
        
        <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
            <div>
                <h1 class="h2 fw-bold text-brown mb-1">Tableau de bord</h1>
                <p class="text-muted mb-0">Ravi de vous revoir, <strong><?php echo e($user->prenom); ?> <?php echo e($user->nom); ?></strong></p>
            </div>
            <span class="badge bg-dark p-2 shadow-sm rounded-pill px-3">
                <i class="fa-solid fa-calendar-day me-2 text-success"></i><?php echo e(now()->format('d/m/Y')); ?>

            </span>
        </div>

        
        <div class="row g-3 mb-4">
            
            
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">Solde de congés</h6>
                            <h3 class="fw-bold mb-0 text-brown"><?php echo e($stats['conge_restant']); ?> Jours</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                            <i class="fa-solid fa-umbrella-beach fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100" style="border-left-color: #0d6efd;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">Mes demandes</h6>
                            <h3 class="fw-bold mb-0 text-primary"><?php echo e($stats['mes_demandes_compte']); ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                            <i class="fa-solid fa-folder-open fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if(in_array($user->code_poste, ['RESP_SERVI', 'RH', 'DIR_EXPLOI', 'DIR_GENERA']) || $user->code_poste === 'ADMIN'): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100" style="border-left-color: #ff9800;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">À valider</h6>
                            <h3 class="fw-bold mb-0 text-warning">
                                <?php echo e($stats['en_attente_validation']); ?>

                            </h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                            <i class="fa-solid fa-stamp fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if(in_array($user->code_poste, ['RH', 'ADMIN'])): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100" style="border-left-color: #5d4037;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">Effectif Total</h6>
                            <h3 class="fw-bold mb-0 text-brown"><?php echo e($stats['total_employes']); ?></h3>
                        </div>
                        <div class="bg-dark bg-opacity-10 p-3 rounded-3 text-dark">
                            <i class="fa-solid fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

     

        
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold text-brown mb-3"><i class="fa-solid fa-bolt me-2 text-warning"></i> Actions Rapides</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo e(route('demandes.index')); ?>" class="btn btn-outline-success fw-semibold">
                            <i class="fa-solid fa-plus me-2"></i>Nouvelle demande de congé
                        </a>
                        
                        <?php if(in_array($user->code_poste, ['RESP_SERVI', 'RH', 'DIR_EXPLOI', 'DIR_GENERA'])): ?>
                            <a href="<?php echo e(route('validations.index')); ?>" class="btn btn-outline-warning fw-semibold">
                                <i class="fa-solid fa-check-double me-2"></i>Traiter les validations
                            </a>
                        <?php endif; ?>

                        <?php if(in_array($user->code_poste, ['RH', 'ADMIN'])): ?>
                            <a href="<?php echo e(route('employes.index')); ?>" class="btn btn-outline-primary fw-semibold">
                                <i class="fa-solid fa-users me-2"></i>Gérer le personnel
                            </a>
                            <a href="<?php echo e(route('plannings.index')); ?>" class="btn btn-outline-secondary fw-semibold">
                                <i class="fa-solid fa-calendar-days me-2"></i>Plannings Horaires
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\APP-CONGES\resources\views/dashboard.blade.php ENDPATH**/ ?>