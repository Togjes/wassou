<div>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            .card.border-danger {
                border-width: 2px !important;
                opacity: 0.85;
            }
            
            .card.border-success {
                border-width: 2px !important;
            }
            
            .card.border-success:hover {
                box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
                transform: translateY(-2px);
                transition: all 0.3s ease;
            }
            
            .card.border-danger:hover {
                box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            }
            
            select option:disabled {
                color: #dc3545;
                font-style: italic;
            }
            
            .chambre-card-disponible {
                cursor: pointer;
            }
            
            .chambre-card-occupee {
                cursor: not-allowed;
                background-color: #f8f9fa;
            }
        </style>
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
                                            <span class="badge badge-info ms-2">Propriétaire: <?php echo e($bien_selectionne->proprietaire->user->name); ?></span>
                                        </p>
                                    </div>
                                    <button type="button" 
                                            wire:click="resetBien" 
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-times me-2"></i>Changer</button>
                                </div>

                                <?php if(session()->has('success_bien')): ?>
                                    <div class="alert alert-success mt-3 mb-0">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        <?php echo e(session('success_bien')); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!-- Sélection de la chambre -->
                                
                                <!-- Sélection de la chambre - VERSION AVEC CARTES CLIQUABLES -->
                                <div class="mt-4">
                                    <label class="form-label">Sélectionnez une Chambre <span class="text-danger">*</span></label>
                                    
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['chambre_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="alert alert-danger"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    
                                    <!--[if BLOCK]><![endif]--><?php if(count($chambres) === 0): ?>
                                        <div class="alert alert-warning mt-2">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            Aucune chambre n'a été trouvée pour ce bien.
                                        </div>
                                    <?php else: ?>
                                        <div class="row g-3 mt-2">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $chambres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chambre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-6">
                                                    <div class="card h-100 <?php echo e($chambre->est_sous_contrat ? 'border-danger chambre-card-occupee' : 'border-success chambre-card-disponible'); ?> <?php echo e($chambre_id == $chambre->id ? 'border-primary shadow-lg' : ''); ?>"
                                                        wire:click="<?php echo e($chambre->est_sous_contrat ? '' : '$set(\'chambre_id\', \'' . $chambre->id . '\')'); ?>"
                                                        style="cursor: <?php echo e($chambre->est_sous_contrat ? 'not-allowed' : 'pointer'); ?>; border-width: <?php echo e($chambre_id == $chambre->id ? '3px' : '2px'); ?> !important;">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <h6 class="mb-0">
                                                                        <!--[if BLOCK]><![endif]--><?php if($chambre_id == $chambre->id): ?>
                                                                            <i class="fa-solid fa-check-circle text-primary me-2"></i>
                                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                        <?php echo e($chambre->nom_chambre); ?>

                                                                    </h6>
                                                                    <small class="text-muted"><?php echo e(ucfirst(str_replace('_', ' ', $chambre->type_chambre))); ?></small>
                                                                </div>
                                                                <!--[if BLOCK]><![endif]--><?php if($chambre->est_sous_contrat): ?>
                                                                    <span class="badge bg-danger">
                                                                        <i class="fa-solid fa-lock me-1"></i>Occupée
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-success">
                                                                        <i class="fa-solid fa-check me-1"></i>Disponible
                                                                    </span>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            </div>
                                                            
                                                            <div class="mb-2">
                                                                <p class="mb-1 small text-muted">
                                                                    <i class="fa-solid fa-tag me-1"></i>
                                                                    Réf: <?php echo e($chambre->reference); ?>

                                                                </p>
                                                                <!--[if BLOCK]><![endif]--><?php if($chambre->surface_m2): ?>
                                                                    <p class="mb-1 small text-muted">
                                                                        <i class="fa-solid fa-ruler-combined me-1"></i>
                                                                        <?php echo e(number_format($chambre->surface_m2, 0)); ?> m²
                                                                    </p>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                <p class="mb-1">
                                                                    <i class="fa-solid fa-money-bill me-1"></i>
                                                                    <strong class="text-primary"><?php echo e(number_format($chambre->loyer_mensuel, 0, ',', ' ')); ?> FCFA</strong>
                                                                    <small class="text-muted">/mois</small>
                                                                </p>
                                                            </div>
                                                            
                                                            <!--[if BLOCK]><![endif]--><?php if($chambre->est_sous_contrat && $chambre->contrat_actif): ?>
                                                                <div class="alert alert-danger p-2 mb-0">
                                                                    <small>
                                                                        <i class="fa-solid fa-info-circle me-1"></i>
                                                                        <strong>Cette chambre est louée</strong><br>
                                                                        <strong>Contrat:</strong> <?php echo e($chambre->contrat_actif->numero_contrat); ?><br>
                                                                        <strong>Locataire:</strong> <?php echo e($chambre->contrat_actif->locataire->user->full_name); ?><br>
                                                                        <strong>Statut:</strong> 
                                                                        <span class="badge 
                                                                            <?php if($chambre->contrat_actif->statut === 'actif'): ?> bg-success
                                                                            <?php else: ?> bg-warning
                                                                            <?php endif; ?>">
                                                                            <?php echo e($chambre->contrat_actif->statut_libelle); ?>

                                                                        </span>
                                                                    </small>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="text-center">
                                                                    <button type="button" 
                                                                            class="btn btn-sm <?php echo e($chambre_id == $chambre->id ? 'btn-primary' : 'btn-outline-primary'); ?> w-100"
                                                                            wire:click="$set('chambre_id', '<?php echo e($chambre->id); ?>')">
                                                                        <!--[if BLOCK]><![endif]--><?php if($chambre_id == $chambre->id): ?>
                                                                            <i class="fa-solid fa-check me-2"></i>Sélectionnée
                                                                        <?php else: ?>
                                                                            <i class="fa-solid fa-hand-pointer me-2"></i>Sélectionner
                                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                    </button>
                                                                </div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($chambre_id && $loyer_mensuel > 0): ?>
                                        <div class="alert alert-success mt-3">
                                            <i class="fa-solid fa-check-circle me-2"></i>
                                            <strong>Chambre sélectionnée avec succès !</strong><br>
                                            Loyer mensuel : <strong><?php echo e(number_format($loyer_mensuel, 0, ',', ' ')); ?> FCFA</strong>
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
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    <strong>Information :</strong> Les montants indiqués ci-dessous seront automatiquement enregistrés comme payés après validation.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Avance sur Loyer (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="avance_loyer" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Déduit progressivement du loyer mensuel</small>
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

                                <!-- Moyen de Paiement -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            <strong>Moyen de paiement utilisé par le locataire</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Moyen de Paiement <span class="text-danger">*</span></label>
                                        <select wire:model.live="methode_paiement_initial" 
                                                class="form-select <?php $__errorArgs = ['methode_paiement_initial'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">Sélectionnez un moyen</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $moyens_paiement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['methode_paiement_initial'];
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

                                    <!-- Détails selon le moyen de paiement -->
                                    <!--[if BLOCK]><![endif]--><?php if($methode_paiement_initial === 'mobile_money'): ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Numéro Mobile Money <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="mobile_money_number" 
                                                   class="form-control <?php $__errorArgs = ['mobile_money_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="+229 XX XX XX XX">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['mobile_money_number'];
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
                                            <label class="form-label">Référence de Transaction</label>
                                            <input type="text" 
                                                   wire:model="reference_transaction" 
                                                   class="form-control"
                                                   placeholder="Ex: MP240116.1234.A12345">
                                            <small class="text-muted">Optionnel</small>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($methode_paiement_initial === 'virement'): ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom de la Banque <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="bank_name" 
                                                   class="form-control <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="Ex: ORABANK">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['bank_name'];
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
                                            <label class="form-label">Référence de Transaction <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="reference_transaction" 
                                                   class="form-control <?php $__errorArgs = ['reference_transaction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="Ex: VIR20240116123456">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reference_transaction'];
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
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($methode_paiement_initial === 'cheque'): ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Numéro de Chèque <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="numero_cheque" 
                                                   class="form-control <?php $__errorArgs = ['numero_cheque'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="Ex: 1234567">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['numero_cheque'];
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
                                            <label class="form-label">Nom de la Banque <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="bank_name" 
                                                   class="form-control <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="Ex: BOA">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['bank_name'];
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
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <!-- Consentement -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="mb-3 text-danger">
                                                    <i class="fa-solid fa-hand-point-right me-2"></i>
                                                    Confirmation Importante
                                                </h6>
                                                <div class="form-check">
                                                    <input class="form-check-input <?php $__errorArgs = ['consentement_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                           type="checkbox" 
                                                           wire:model="consentement_paiement"
                                                           id="consentement">
                                                    <label class="form-check-label" for="consentement">
                                                        <strong>Je certifie avoir reçu les paiements initiaux</strong> d'un montant total de 
                                                        <strong class="text-primary"><?php echo e(number_format($total_a_payer, 0, ',', ' ')); ?> FCFA</strong> 
                                                        de la part de <strong><?php echo e($locataire_selectionne->user->full_name ?? 'ce locataire'); ?></strong> 
                                                        via <strong><?php echo e($moyens_paiement[$methode_paiement_initial] ?? 'le moyen sélectionné'); ?></strong>.
                                                        Ces montants seront enregistrés comme payés dans le système.
                                                    </label>
                                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['consentement_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Récapitulatif -->
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">Récapitulatif des Paiements</h6>
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php if($avance_loyer > 0): ?>
                                                    <tr>
                                                        <td>Avance sur Loyer</td>
                                                        <td class="text-end"><strong><?php echo e(number_format($avance_loyer, 0, ',', ' ')); ?> FCFA</strong></td>
                                                    </tr>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <!--[if BLOCK]><![endif]--><?php if($prepaye_loyer > 0): ?>
                                                    <tr>
                                                        <td>Prépayé Loyer</td>
                                                        <td class="text-end"><strong><?php echo e(number_format($prepaye_loyer, 0, ',', ' ')); ?> FCFA</strong></td>
                                                    </tr>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <!--[if BLOCK]><![endif]--><?php if($caution > 0): ?>
                                                    <tr>
                                                        <td>Caution</td>
                                                        <td class="text-end"><strong><?php echo e(number_format($caution, 0, ',', ' ')); ?> FCFA</strong></td>
                                                    </tr>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <!--[if BLOCK]><![endif]--><?php if($frais_dossier > 0): ?>
                                                    <tr>
                                                        <td>Frais de Dossier</td>
                                                        <td class="text-end"><strong><?php echo e(number_format($frais_dossier, 0, ',', ' ')); ?> FCFA</strong></td>
                                                    </tr>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <tr class="table-primary">
                                                    <td><strong>TOTAL À PAYER</strong></td>
                                                    <td class="text-end">
                                                        <h5 class="mb-0 text-primary"><?php echo e(number_format($total_a_payer, 0, ',', ' ')); ?> FCFA</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                            class="btn btn-success btn-lg" 
                                            wire:loading.attr="disabled"
                                            wire:target="saveContrat"
                                            <?php if(!$consentement_paiement): ?> disabled <?php endif; ?>>
                                        <span wire:loading.remove wire:target="saveContrat">
                                            <i class="fa-solid fa-check me-2"></i>Créer le Contrat et Enregistrer les Paiements
                                        </span>
                                        <span wire:loading wire:target="saveContrat">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>Création en cours...
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