<div class="container-fluid">
    <div class="row">
        <div class="col-xl-5 p-0">
            <img class="bg-img-cover bg-center" src="<?php echo e(asset('assets/images/login/1.jpg')); ?>" alt="looginpage">
        </div>
        <div class="col-xl-7 p-0">
            <div class="login-card login-dark">
                <div>
                    <div>
                        <a class="logo text-start" href="<?php echo e(route('login')); ?>">
                            <img class="img-fluid for-light" src="<?php echo e(asset('assets/images/logo/logo.png')); ?>" alt="Wassou">
                            <img class="img-fluid for-dark" src="<?php echo e(asset('assets/images/logo/logo_dark.png')); ?>" alt="Wassou">
                        </a>
                    </div>
                    <div class="login-main">
                        <form wire:submit.prevent="login" class="theme-form">
                            <h4>Connexion à votre compte</h4>
                            <p>Entrez votre email et mot de passe pour vous connecter</p>
                            
                            <!--[if BLOCK]><![endif]--><?php if(session()->has('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i data-feather="alert-circle"></i>
                                    <p><?php echo e(session('error')); ?></p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <?php if(session()->has('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i data-feather="check-circle"></i>
                                    <p><?php echo e(session('success')); ?></p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            
                            <div class="form-group">
                                <label class="col-form-label">Adresse Email</label>
                                <input wire:model="email" 
                                       class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       type="email" 
                                       placeholder="exemple@email.com"
                                       autofocus>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            
                            <div class="form-group">
                                <label class="col-form-label">Mot de passe</label>
                                <div class="form-input position-relative">
                                    <input wire:model="password" 
                                           class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="password" 
                                           placeholder="*********">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            
                            <div class="form-group mb-0">
                                <div class="checkbox p-0">
                                    <input wire:model="remember" id="checkbox1" type="checkbox">
                                    <label class="text-muted" for="checkbox1">Se souvenir de moi</label>
                                </div>
                                <a class="link" href="<?php echo e(route('password.request')); ?>">Mot de passe oublié ?</a>
                                
                                <div class="text-end mt-3">
                                    <button class="btn btn-primary btn-block w-100" 
                                            type="submit" 
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove>Se connecter</span>
                                        <span wire:loading>
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Connexion en cours...
                                        </span>
                                    </button>
                                </div>
                            </div>
                            
                            <p class="mt-4 mb-0 text-center">
                                Vous n'avez pas de compte ? 
                                <a class="ms-2" href="<?php echo e(route('register')); ?>">Créer un compte</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/auth/login.blade.php ENDPATH**/ ?>