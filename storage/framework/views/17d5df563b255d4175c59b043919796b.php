<div>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/vendors/sweetalert2.css')); ?>">
    <?php $__env->stopPush(); ?>

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>
                        <!--[if BLOCK]><![endif]--><?php if(auth()->user()->isAdmin()): ?>
                            Tous les Biens Immobiliers
                        <?php elseif(auth()->user()->isProprietaire()): ?>
                            Mes Biens Immobiliers
                        <?php else: ?>
                            Biens Gérés
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">
                            <svg class="stroke-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-home')); ?>"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item active">Liste</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid list-product-view product-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i data-feather="check-circle"></i>
                        <?php echo e(session('success')); ?>

                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php if(session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i data-feather="alert-circle"></i>
                        <?php echo e(session('error')); ?>

                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Section Filtres en haut -->
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-filter"></i> Filtres de recherche
                            </h6>
                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                                <i class="fa-solid fa-filter me-2"></i>
                                <?php echo e(($search || $type_bien_filter || $ville_filter || $statut_filter || $proprietaire_filter) ? 'Filtres actifs' : 'Filtres'); ?>

                            </button>
                        </div>
                    </div>
                    <div class="collapse show" id="filtersCollapse">
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Recherche -->
                                <div class="col-md-3">
                                    <label class="form-label">Rechercher</label>
                                    <input wire:model.live.debounce.300ms="search" 
                                        type="text" 
                                        class="form-control" 
                                        placeholder="Titre, ville, référence...">
                                </div>

                                <!-- Type de bien -->
                                <div class="col-md-3">
                                    <label class="form-label">Type de Bien</label>
                                    <select wire:model.live="type_bien_filter" class="form-select">
                                        <option value="">Tous les types</option>
                                        <option value="maison">Maison</option>
                                        <option value="appartement">Appartement</option>
                                        <option value="bureau">Bureau</option>
                                        <option value="terrain">Terrain</option>
                                        <option value="commerce">Commerce</option>
                                        <option value="magasin">Magasin</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>

                                <!-- Ville -->
                                <div class="col-md-3">
                                    <label class="form-label">Ville</label>
                                    <select wire:model.live="ville_filter" class="form-select">
                                        <option value="">Toutes les villes</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $villes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ville): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($ville); ?>"><?php echo e($ville); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>

                                <!-- Statut -->
                                <div class="col-md-3">
                                    <label class="form-label">Statut</label>
                                    <select wire:model.live="statut_filter" class="form-select">
                                        <option value="">Tous les statuts</option>
                                        <option value="Location">En Location</option>
                                        <option value="Construction">En Construction</option>
                                        <option value="Renovation">En Rénovation</option>
                                    </select>
                                </div>

                                <!-- Filtre Propriétaire (Admin uniquement) -->
                                <!--[if BLOCK]><![endif]--><?php if(auth()->user()->isAdmin()): ?>
                                    <div class="col-md-3">
                                        <label class="form-label">Propriétaire</label>
                                        <select wire:model.live="proprietaire_filter" class="form-select">
                                            <option value="">Tous les propriétaires</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $proprietaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($prop['id']); ?>">
                                                    <?php echo e($prop['name']); ?> (<?php echo e($prop['code']); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <!-- Badge filtres actifs à gauche -->
                                    <div>
                                        <!--[if BLOCK]><![endif]--><?php if($search || $type_bien_filter || $ville_filter || $statut_filter || $proprietaire_filter): ?>
                                            <span class="badge badge-light-info">
                                                <i class="fa-solid fa-filter me-1"></i>Filtres actifs
                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    
                                    <!-- Bouton Réinitialiser à droite -->
                                    <div>
                                        <button wire:click="resetFilters" 
                                                class="btn btn-secondary" 
                                                type="button">
                                            <i class="fa fa-refresh me-2"></i>Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Tableau en bas -->
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa fa-home me-2"></i>
                                <!--[if BLOCK]><![endif]--><?php if(auth()->user()->isAdmin()): ?>
                                    Tous les Biens
                                <?php elseif(auth()->user()->isProprietaire()): ?>
                                    Mes Biens
                                <?php else: ?>
                                    Biens Gérés
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <span class="badge badge-light-primary ms-2"><?php echo e($biens->total()); ?> bien(s)</span>
                            </h5>
                            <!--[if BLOCK]><![endif]--><?php if(!auth()->user()->isDemarcheur() || auth()->user()->demarcheur->proprietairesActifs()->count() > 0): ?>
                                <a class="btn btn-primary f-w-500" href="<?php echo e(route('biens.creer')); ?>">
                                    <i class="fa fa-plus pe-2"></i>Ajouter un Bien
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="list-product">
                            <div class="recent-table table-responsive custom-scrollbar product-list-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><span class="c-o-light">Titre du Bien</span></th>
                                            <th><span class="c-o-light">Type</span></th>
                                            <th><span class="c-o-light">Localisation</span></th>
                                            
                                            <?php if(!auth()->user()->isProprietaire()): ?>
                                                <th><span class="c-o-light">Propriétaire</span></th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            
                                            <?php if(auth()->user()->isProprietaire() || auth()->user()->isAdmin()): ?>
                                                <th><span class="c-o-light">Créé par</span></th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            
                                            <th><span class="c-o-light">Chambres</span></th>
                                            <th><span class="c-o-light">Occupation</span></th>
                                            <th><span class="c-o-light">Statut</span></th>
                                            <th><span class="c-o-light">Actions</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr class="product-removes">
                                                <td></td>
                                                <td>
                                                    <div class="product-names">
                                                        <div class="light-product-box">
                                                            <!--[if BLOCK]><![endif]--><?php if(!empty($bien->photos_generales) && count($bien->photos_generales) > 0): ?>
                                                                <img class="img-fluid" 
                                                                     src="<?php echo e(Storage::url($bien->photos_generales[0])); ?>" 
                                                                     alt="<?php echo e($bien->titre); ?>">
                                                            <?php else: ?>
                                                                <img class="img-fluid" 
                                                                     src="<?php echo e(asset('assets/images/dashboard-8/product-categories/laptop.png')); ?>" 
                                                                     alt="<?php echo e($bien->titre); ?>">
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                        <div>
                                                            <a href="<?php echo e(route('biens.detail', $bien->id)); ?>">
                                                                <?php echo e($bien->titre); ?>

                                                            </a>
                                                            <br>
                                                            <small class="text-muted">Réf: <?php echo e($bien->reference); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="c-o-light"><?php echo e(ucfirst($bien->type_bien)); ?></p>
                                                </td>
                                                <td>
                                                    <p class="c-o-light mb-0"><?php echo e($bien->ville); ?></p>
                                                    <small class="text-muted"><?php echo e($bien->quartier); ?></small>
                                                </td>
                                                
                                                <?php if(!auth()->user()->isProprietaire()): ?>
                                                    <td>
                                                        <div>
                                                            <strong><?php echo e($bien->proprietaire->user->name); ?></strong>
                                                            <br>
                                                            <small class="text-muted"><?php echo e($bien->proprietaire->user->code_unique); ?></small>
                                                        </div>
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <?php if(auth()->user()->isProprietaire() || auth()->user()->isAdmin()): ?>
                                                    <td>
                                                        <!--[if BLOCK]><![endif]--><?php if($bien->created_by_type && $bien->createdBy): ?>
                                                            <span class="badge 
                                                                <?php if($bien->created_by_type === 'admin'): ?> badge-light-danger
                                                                <?php elseif($bien->created_by_type === 'demarcheur'): ?> badge-light-warning
                                                                <?php else: ?> badge-light-success
                                                                <?php endif; ?>">
                                                                <!--[if BLOCK]><![endif]--><?php if($bien->created_by_type === 'admin'): ?>
                                                                    <i class="fa-solid fa-user-shield me-1"></i>
                                                                <?php elseif($bien->created_by_type === 'demarcheur'): ?>
                                                                    <i class="fa-solid fa-user-tie me-1"></i>
                                                                <?php else: ?>
                                                                    <i class="fa-solid fa-user me-1"></i>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                <?php echo e($bien->created_by_name); ?>

                                                            </span>
                                                            <br>
                                                            <small class="text-muted"><?php echo e($bien->created_by_role); ?></small>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <td>
                                                    <p class="c-o-light"><?php echo e($bien->chambres->count()); ?></p>
                                                </td>
                                                <td>
                                                    <p class="c-o-light mb-0"><?php echo e($bien->taux_occupation); ?>%</p>
                                                    <small class="text-muted">
                                                        <?php echo e($bien->chambres_louees); ?>/<?php echo e($bien->chambres->count()); ?>

                                                    </small>
                                                </td>
                                                <td>
                                                    <!--[if BLOCK]><![endif]--><?php if($bien->statut === 'Location'): ?>
                                                        <span class="badge badge-light-primary">En Location</span>
                                                    <?php elseif($bien->statut === 'Construction'): ?>
                                                        <span class="badge badge-light-secondary">Construction</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-light-secondary">Rénovation</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </td>
                                                <td>
                                                    <div class="product-action">
                                                        <a class="square-white" 
                                                           href="<?php echo e(route('biens.detail', $bien->id)); ?>"
                                                           title="Voir les détails">
                                                            <svg>
                                                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#eye')); ?>"></use>
                                                            </svg>
                                                        </a>
                                                        
                                                        <?php
                                                            $canEdit = false;
                                                            $canDelete = false;
                                                            
                                                            if(auth()->user()->isAdmin()) {
                                                                $canEdit = true;
                                                                $canDelete = true;
                                                            } elseif(auth()->user()->isProprietaire() && $bien->proprietaire->user_id === auth()->id()) {
                                                                $canEdit = true;
                                                                $canDelete = true;
                                                            } elseif(auth()->user()->isDemarcheur()) {
                                                                $demarcheur = auth()->user()->demarcheur;
                                                                $canEdit = $demarcheur->hasPermissionFor($bien->proprietaire_id, 'modifier_bien');
                                                                $canDelete = $demarcheur->hasPermissionFor($bien->proprietaire_id, 'supprimer_bien');
                                                            }
                                                        ?>
                                                        
                                                        <!--[if BLOCK]><![endif]--><?php if($canEdit): ?>
                                                            <a class="square-white" 
                                                               href="<?php echo e(route('biens.modifier', $bien->id)); ?>"
                                                               title="Modifier">
                                                                <svg>
                                                                    <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#edit-content')); ?>"></use>
                                                                </svg>
                                                            </a>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        
                                                        <!--[if BLOCK]><![endif]--><?php if($canDelete): ?>
                                                            <a class="" 
                                                                href="#!"
                                                                wire:click.prevent="confirmDeleteBien('<?php echo e($bien->id); ?>')"
                                                                title="Supprimer">
                                                                    <svg>
                                                                        <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#trash1')); ?>"></use>
                                                                    </svg>
                                                            </a>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="10" class="text-center py-5">
                                                    <div class="py-4">
                                                        <svg width="100" height="100" class="text-muted mb-3">
                                                            <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-home')); ?>"></use>
                                                        </svg>
                                                        <h5 class="text-muted">Aucun bien trouvé</h5>
                                                        <p class="text-muted">
                                                            <!--[if BLOCK]><![endif]--><?php if($search || $type_bien_filter || $ville_filter || $statut_filter || $proprietaire_filter): ?>
                                                                Aucun bien ne correspond à vos critères de recherche.
                                                            <?php elseif(auth()->user()->isDemarcheur()): ?>
                                                                Vous n'avez pas encore été autorisé par un propriétaire.
                                                            <?php else: ?>
                                                                Vous n'avez pas encore ajouté de bien immobilier.
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </p>
                                                        <!--[if BLOCK]><![endif]--><?php if(!auth()->user()->isDemarcheur() || auth()->user()->demarcheur->proprietairesActifs()->count() > 0): ?>
                                                            <a href="<?php echo e(route('biens.creer')); ?>" class="btn btn-primary mt-3">
                                                                <i class="fa fa-plus me-2"></i>Ajouter mon premier bien
                                                            </a>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination Livewire -->
                            <!--[if BLOCK]><![endif]--><?php if($biens->hasPages()): ?>
                                <div class="px-3 py-3">
                                    <?php echo e($biens->links()); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de suppression -->
    <!--[if BLOCK]><![endif]--><?php if($showDeleteModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cancelDeleteBien"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa fa-info-circle me-2"></i>
                            <strong>Attention !</strong> Cette action est irréversible.
                        </div>
                        <p>Êtes-vous sûr de vouloir supprimer ce bien immobilier ?</p>
                        <p class="text-muted">Toutes les photos et documents associés seront également supprimés.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDeleteBien">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteBien" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteBien">
                                <i class="fa fa-trash me-2"></i>Supprimer définitivement
                            </span>
                            <span wire:loading wire:target="deleteBien">
                                <i class="fa fa-spinner fa-spin me-2"></i>Suppression...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/trash_popup.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/biens/liste-biens.blade.php ENDPATH**/ ?>