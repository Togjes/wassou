<div>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php $__env->stopPush(); ?>

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Créer un Contrat</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>">
                                <svg class="stroke-icon">
                                    <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-home')); ?>"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('contrats.liste')); ?>">Contrats</a>
                        </li>
                        <li class="breadcrumb-item active">Créer</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!--[if BLOCK]><![endif]--><?php if(session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fa-solid fa-times-circle"></i>
                        <?php echo e(session('error')); ?>

                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <form wire:submit.prevent="saveContrat">
                    
                    <!-- ÉTAPE 1 : Rechercher le Bien -->
                    <div class="card <?php echo e($bien_trouve ? 'border-success' : 'border-warning'); ?>">
                        <div class="card-header <?php echo e($bien_trouve ? 'bg-light-success' : 'bg-light-warning'); ?>">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-building me-2"></i>
                                Étape 1 : Rechercher le Bien
                            </h5>
                        </div>
                        <div class="card-body">
                            <!--[if BLOCK]><![endif]--><?php if(!$bien_trouve): ?>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label">Référence du Bien <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               wire:model="reference_bien" 
                                               class="form-control <?php $__errorArgs = ['reference_bien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               placeholder="Ex: BIEN-ABC123-001">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reference_bien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <small class="text-muted">Entrez la référence du bien immobilier</small>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" 
                                                wire:click="chercherBien" 
                                                class="btn btn-primary w-100"
                                                wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="chercherBien">
                                                <i class="fa-solid fa-search me-2"></i>Chercher
                                            </span>
                                            <span wire:loading wire:target="chercherBien">
                                                <i class="fa-solid fa-spinner fa-spin me-2"></i>Recherche...
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <!--[if BLOCK]><![endif]--><?php if(session()->has('error_bien')): ?>
                                    <div class="alert alert-danger mt-3 mb-0">
                                        <i class="fa-solid fa-exclamation-circle me-2"></i>
                                        <?php echo e(session('error_bien')); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1"><?php echo e($bien_selectionne->titre ?? $bien_selectionne->type_bien); ?></h6>
                                        <p class="mb-0 text-muted">
                                            <i class="fa-solid fa-map-marker-alt me-2"></i>
                                            <?php echo e($bien_selectionne->ville); ?>, <?php echo e($bien_selectionne->quartier); ?>

                                        </p>
                                        <p class="mb-0">
                                            <span class="badge badge-success">Réf: <?php echo e($bien_selectionne->reference); ?></span>
                                        </p>
                                    </div>
                                    <button type="button" 
                                            wire:click="resetBien" 
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-times me-2"></i>Changer
                                    </button>
                                </div>

                                <?php if(session()->has('success_bien')): ?>
                                    <div class="alert alert-success mt-3 mb-0">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        <?php echo e(session('success_bien')); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!-- Sélection de la chambre -->
                                <div class="mt-4">
                                    <label class="form-label">Chambre <span class="text-danger">*</span></label>
                                    <select wire:model.live="chambre_id" 
                                            class="form-select <?php $__errorArgs = ['chambre_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Sélectionnez une chambre</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $chambres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chambre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($chambre->id); ?>">
                                                <?php echo e($chambre->nom_chambre); ?> (Réf: <?php echo e($chambre->reference); ?>) - 
                                                <?php echo e(number_format($chambre->loyer_mensuel, 0, ',', ' ')); ?> FCFA/mois
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['chambre_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    
                                    <!--[if BLOCK]><![endif]--><?php if(count($chambres) === 0): ?>
                                        <div class="alert alert-warning mt-2">
                                            Aucune chambre disponible pour ce bien
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($loyer_mensuel > 0): ?>
                                        <div class="alert alert-info mt-2">
                                            <strong>Loyer mensuel :</strong> <?php echo e(number_format($loyer_mensuel, 0, ',', ' ')); ?> FCFA
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <!-- ÉTAPE 2 : Rechercher le Locataire -->
                    <!--[if BLOCK]><![endif]--><?php if($bien_trouve && $chambre_id): ?>
                        <div class="card mt-3 <?php echo e($locataire_trouve ? 'border-success' : 'border-warning'); ?>">
                            <div class="card-header <?php echo e($locataire_trouve ? 'bg-light-success' : 'bg-light-warning'); ?>">
                                <h5 class="mb-0">
                                    <i class="fa-solid fa-user me-2"></i>
                                    Étape 2 : Rechercher le Locataire
                                </h5>
                            </div>
                            <div class="card-body">
                                <!--[if BLOCK]><![endif]--><?php if(!$locataire_trouve): ?>
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Code Unique du Locataire <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="code_locataire" 
                                                   class="form-control <?php $__errorArgs = ['code_locataire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="Ex: LOC-XXXXXXXX">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['code_locataire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            <small class="text-muted">Entrez le code unique du locataire</small>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" 
                                                    wire:click="chercherLocataire" 
                                                    class="btn btn-primary w-100"
                                                    wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="chercherLocataire">
                                                    <i class="fa-solid fa-search me-2"></i>Chercher
                                                </span>
                                                <span wire:loading wire:target="chercherLocataire">
                                                    <i class="fa-solid fa-spinner fa-spin me-2"></i>Recherche...
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <?php if(session()->has('error_locataire')): ?>
                                        <div class="alert alert-danger mt-3 mb-0">
                                            <i class="fa-solid fa-exclamation-circle me-2"></i>
                                            <?php echo e(session('error_locataire')); ?>

                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-1"><?php echo e($locataire_selectionne->user->full_name); ?></h6>
                                            <p class="mb-0 text-muted">
                                                <i class="fa-solid fa-phone me-2"></i><?php echo e($locataire_selectionne->user->phone); ?>

                                            </p>
                                            <p class="mb-0 text-muted">
                                                <i class="fa-solid fa-envelope me-2"></i><?php echo e($locataire_selectionne->user->email); ?>

                                            </p>
                                            <p class="mb-0">
                                                <span class="badge badge-info">Code: <?php echo e($locataire_selectionne->user->code_unique); ?></span>
                                            </p>
                                        </div>
                                        <button type="button" 
                                                wire:click="resetLocataire" 
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="fa-solid fa-times me-2"></i>Changer
                                        </button>
                                    </div>

                                    <?php if(session()->has('success_locataire')): ?>
                                        <div class="alert alert-success mt-3 mb-0">
                                            <i class="fa-solid fa-check-circle me-2"></i>
                                            <?php echo e(session('success_locataire')); ?>

                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!-- ÉTAPE 3 : Informations du Contrat -->
                    <!--[if BLOCK]><![endif]--><?php if($bien_trouve && $chambre_id && $locataire_trouve): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>
                                    <i class="fa-solid fa-file-contract me-2"></i>
                                    Étape 3 : Informations du Contrat
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date d'Établissement *</label>
                                        <input type="date" 
                                               wire:model="date_etablissement" 
                                               class="form-control <?php $__errorArgs = ['date_etablissement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_etablissement'];
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

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jour de Paiement du Loyer *</label>
                                        <select wire:model="date_paiement_loyer" 
                                                class="form-select <?php $__errorArgs = ['date_paiement_loyer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <!--[if BLOCK]><![endif]--><?php for($i = 1; $i <= 28; $i++): ?>
                                                <option value="<?php echo e($i); ?>">Le <?php echo e($i); ?> de chaque mois</option>
                                            <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_paiement_loyer'];
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

                                    <!--[if BLOCK]><![endif]--><?php if(count($demarcheurs) > 0): ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Démarcheur (optionnel)</label>
                                            <select wire:model="demarcheur_id" class="form-select">
                                                <option value="">Aucun</option>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $demarcheurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demarcheur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($demarcheur->id); ?>">
                                                        <?php echo e($demarcheur->user->full_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </select>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>

                        <!-- ÉTAPE 4 : Paiements Initiaux -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>
                                    <i class="fa-solid fa-money-bill me-2"></i>
                                    Étape 4 : Paiements Initiaux
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Avance sur Loyer (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="avance_loyer" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Déduit progressivement du loyer</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prépayé Loyer (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="prepaye_loyer" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Mois payés d'avance</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Caution (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="caution" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Remboursable en fin de contrat</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Frais de Dossier (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="frais_dossier" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Non remboursables</small>
                                    </div>
                                </div>

                                <!-- Récapitulatif -->
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">Récapitulatif</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Total à Payer</h5>
                                            <h4 class="mb-0 text-primary">
                                                <?php echo e(number_format($total_a_payer, 0, ',', ' ')); ?> FCFA
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo e(route('contrats.liste')); ?>" class="btn btn-secondary">
                                        <i class="fa-solid fa-arrow-left me-2"></i>Retour
                                    </a>
                                    <button type="submit" 
                                            class="btn btn-success" 
                                            wire:loading.attr="disabled"
                                            wire:target="saveContrat">
                                        <span wire:loading.remove wire:target="saveContrat">
                                            <i class="fa-solid fa-check me-2"></i>Créer le Contrat
                                        </span>
                                        <span wire:loading wire:target="saveContrat">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>Création...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </form>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/contrats/creer-contrat.blade.php ENDPATH**/ ?>