<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SOGETHO-SA</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/all.min.css')); ?>">
    <style>
        body {
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(44,94,59,0.95), rgba(74,59,50,0.92)),
                url('<?php echo e(asset('images/palm_club_hotel.png')); ?>') center/cover no-repeat fixed;
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
                        <img src="<?php echo e(asset('images/palm_club_logo.png')); ?>" alt="SOGETHO-SA" class="brand-badge mb-3">
                        <h2 class="fw-bold title-main mb-1">SOGETHO-SA</h2>
                        <p class="text-muted mb-0">Portail RH sécurisé</p>
                    </div>

                    <div class="mb-4 text-center">
                        <span class="badge rounded-pill px-3 py-2" style="background-color:#2c5e3b; color:#fff;">Connexion employé</span>
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger border-0 shadow-sm">
                            <ul class="mb-0 ps-3">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('login.post')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="matricule_employe" class="form-label fw-semibold mini-text">Matricule ou Adresse Email</label>
                            <input type="text"
                                   name="matricule_employe"
                                   id="matricule_employe"
                                   class="form-control soft-input <?php $__errorArgs = ['matricule_employe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('matricule_employe')); ?>"
                                   placeholder="Ex: EMP0001 ou employe@sogetho.ci"
                                   required autofocus>
                            <?php $__errorArgs = ['matricule_employe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-4">
                            <label for="mot_de_passe" class="form-label fw-semibold mini-text">Mot de passe</label>
                            <input type="password"
                                   name="mot_de_passe"
                                   id="mot_de_passe"
                                   class="form-control soft-input <?php $__errorArgs = ['mot_de_passe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="••••••••"
                                   required>
                            <?php $__errorArgs = ['mot_de_passe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-right-to-bracket me-2"></i>Se connecter
                        </button>
                        
                        <div class="text-center mt-3">
                             <a href="<?php echo e(route('password.request')); ?>" class="text-decoration-none fw-semibold" style="color:#2c5e3b;">
                                 Mot de passe oublié ?
                             </a>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted">
                            Nouveau sur la plateforme ?
                            <a href="<?php echo e(route('register')); ?>" class="fw-semibold text-decoration-none" style="color:#2c5e3b;">Créer un compte</a>
                        </p>
                    </div>
                </div>
            </div>

            <p class="text-center mt-3 mb-0 small text-white-50">
                © <?php echo e(date('Y')); ?> SOGETHO-SA · Tous droits réservés
            </p>
        </div>
    </div>
</div>

</body>
</html><?php /**PATH C:\wamp64\www\APP_CONGE\resources\views/auth/login.blade.php ENDPATH**/ ?>