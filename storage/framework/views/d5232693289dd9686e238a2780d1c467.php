<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <a href="<?php echo e(route('plannings.index')); ?>" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">Planifier un Créneau Horaire</h4>
            </div>

            <?php if(session('error')): ?>
                <div class="alert alert-danger border-0 small py-2 mb-3 shadow-sm"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="<?php echo e(route('plannings.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- SÉLECTION AGENT (AVEC PRÉ-SÉLECTION CONTEXTUELLE) -->
                    <div class="mb-3">
                        <label for="matricule_employe" class="form-label small fw-bold text-muted">Affecter à l'agent</label>
                        <select name="matricule_employe" id="matricule_employe" class="form-select form-select-sm" required>
                            <option value="" disabled <?php echo e(!old('matricule_employe') && !$matriculePreselectionne ? 'selected' : ''); ?>>
                                -- Choisir l'employé --
                            </option>
                            <?php $__currentLoopData = $employes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->matricule_employe); ?>"
                                    <?php echo e((old('matricule_employe') ?? $matriculePreselectionne) == $emp->matricule_employe ? 'selected' : ''); ?>>
                                    [<?php echo e($emp->matricule_employe); ?>] <?php echo e(strtoupper($emp->nom)); ?> <?php echo e($emp->prenom); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- DATES -->
                    <div class="row mb-3">
                        <div class="col-12">
                        <label for="date_debut" class="form-label small fw-bold text-muted">Date de Prise d'Effet (Début)</label>
                           <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm" value="<?php echo e(old('date_debut', date('Y-m-d'))); ?>" required>
                        </div>
                    </div>

                    <!-- HEURES -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="heure_debut" class="form-label small fw-bold text-muted">Heure Début Prise en Charge</label>
                            <input type="time" name="heure_debut" id="heure_debut" class="form-control form-control-sm" value="<?php echo e(old('heure_debut', '08:00')); ?>" required>
                        </div>
                        <div class="col-6">
                            <label for="heure_fin" class="form-label small fw-bold text-muted">Heure Fin de Service</label>
                            <input type="time" name="heure_fin" id="heure_fin" class="form-control form-control-sm" value="<?php echo e(old('heure_fin', '17:00')); ?>" required>
                        </div>
                    </div>

                    <!-- CONFIGURATION DES JOURS DE REPOS (Nouveaux champs alignés avec le calendrier) -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="jour_repos_1" class="form-label small fw-bold text-muted">Premier Jour de Repos</label>
                            <select name="jour_repos_1" id="jour_repos_1" class="form-select form-select-sm" required>
                                <option value="" disabled <?php echo e(!old('jour_repos_1') ? 'selected' : ''); ?>>-- Choisir --</option>
                                <option value="1" <?php echo e(old('jour_repos_1') == '1' ? 'selected' : ''); ?>>Lundi</option>
                                <option value="2" <?php echo e(old('jour_repos_1') == '2' ? 'selected' : ''); ?>>Mardi</option>
                                <option value="3" <?php echo e(old('jour_repos_1') == '3' ? 'selected' : ''); ?>>Mercredi</option>
                                <option value="4" <?php echo e(old('jour_repos_1') == '4' ? 'selected' : ''); ?>>Jeudi</option>
                                <option value="5" <?php echo e(old('jour_repos_1') == '5' ? 'selected' : ''); ?>>Vendredi</option>
                                <option value="6" <?php echo e(old('jour_repos_1') == '6' ? 'selected' : ''); ?>>Samedi</option>
                                <option value="7" <?php echo e(old('jour_repos_1') == '7' ? 'selected' : ''); ?>>Dimanche</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="jour_repos_2" class="form-label small fw-bold text-muted">Second Jour de Repos (Optionnel)</label>
                            <select name="jour_repos_2" id="jour_repos_2" class="form-select form-select-sm">
                                <option value="" <?php echo e(!old('jour_repos_2') ? 'selected' : ''); ?>>Aucun</option>
                                <option value="1" <?php echo e(old('jour_repos_2') == '1' ? 'selected' : ''); ?>>Lundi</option>
                                <option value="2" <?php echo e(old('jour_repos_2') == '2' ? 'selected' : ''); ?>>Mardi</option>
                                <option value="3" <?php echo e(old('jour_repos_2') == '3' ? 'selected' : ''); ?>>Mercredi</option>
                                <option value="4" <?php echo e(old('jour_repos_2') == '4' ? 'selected' : ''); ?>>Jeudi</option>
                                <option value="5" <?php echo e(old('jour_repos_2') == '5' ? 'selected' : ''); ?>>Vendredi</option>
                                <option value="6" <?php echo e(old('jour_repos_2') == '6' ? 'selected' : ''); ?>>Samedi</option>
                                <option value="7" <?php echo e(old('jour_repos_2') == '7' ? 'selected' : ''); ?>>Dimanche</option>
                            </select>
                        </div>
                    </div>

                    <!-- BOUTONS D'ACTION -->
                    <div class="text-end border-top pt-3">
                        <a href="<?php echo e(route('plannings.index')); ?>" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">Valider le créneau</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\APP-CONGES\resources\views/plannings/create.blade.php ENDPATH**/ ?>