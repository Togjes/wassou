<div>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/vendors/sweetalert2.css')); ?>">
        <style>
            .info-card {
                border-left: 4px solid var(--theme-deafult);
                transition: all 0.3s ease;
            }
            .info-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            .photo-gallery {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }
            .photo-item {
                position: relative;
                overflow: hidden;
                border-radius: 8px;
                height: 180px;
                cursor: pointer;
            }
            .photo-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }
            .photo-item:hover img {
                transform: scale(1.1);
            }
        </style>
    <?php $__env->stopPush(); ?>

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3><?php echo e($chambre->nom_chambre); ?></h3>
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
                            <a href="<?php echo e(route('biens.liste')); ?>">Biens</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('biens.detail', $bien->id)); ?>"><?php echo e($bien->titre); ?></a>
                        </li>
                        <li class="breadcrumb-item active"><?php echo e($chambre->nom_chambre); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-check-circle"></i>
                <?php echo e(session('success')); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-exclamation-circle"></i>
                <?php echo e(session('error')); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="row g-4">
            <!-- Colonne Principale -->
            <div class="col-xl-8">
                <!-- En-tête de la chambre -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-2"><?php echo e($chambre->nom_chambre); ?></h4>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge badge-light-secondary">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $chambre->type_chambre))); ?>

                                    </span>
                                    <!--[if BLOCK]><![endif]--><?php if($chambre->statut === 'disponible'): ?>
                                        <span class="badge badge-success">Disponible</span>
                                    <?php elseif($chambre->statut === 'loue'): ?>
                                        <span class="badge badge-primary">Louée</span>
                                    <?php elseif($chambre->statut === 'renovation'): ?>
                                        <span class="badge badge-warning">Rénovation</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Réservée</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($chambre->surface_m2): ?>
                                        <span class="badge badge-light-info"><?php echo e(number_format($chambre->surface_m2, 0)); ?> m²</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <span class="badge badge-light-dark"><?php echo e($chambre->nombre_pieces); ?> pièce(s)</span>
                                </div>
                            </div>
                            
                            <!--[if BLOCK]><![endif]--><?php if($canEdit || $canDelete): ?>
                                <div class="btn-group">
                                    <!--[if BLOCK]><![endif]--><?php if($canEdit): ?>
                                        <a href="<?php echo e(route('biens.chambres.modifier', [$bien->id, $chambre->id])); ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-edit"></i> Modifier
                                        </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    
                                    <!--[if BLOCK]><![endif]--><?php if($canDelete): ?>
                                        <button wire:click="confirmDelete" 
                                                class="btn btn-danger btn-sm"
                                                <?php if($chambre->statut === 'loue'): ?> 
                                                    disabled 
                                                    title="Impossible de supprimer une chambre louée" 
                                                <?php endif; ?>>
                                            <i class="fa-solid fa-trash"></i> Supprimer
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($chambre->description): ?>
                            <p class="text-muted mb-0"><?php echo e($chambre->description); ?></p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- Photos -->
                <!--[if BLOCK]><![endif]--><?php if(!empty($chambre->photos) && count($chambre->photos) > 0): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">Photos de la Chambre</h5>
                            <div class="photo-gallery">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $chambre->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="photo-item">
                                        <img src="<?php echo e(Storage::url($photo)); ?>" alt="Photo chambre">
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Équipements -->
                <!--[if BLOCK]><![endif]--><?php if(!empty($chambre->equipements)): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Équipements</h5>
                            <div class="row">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $chambre->equipements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                                            <span><?php echo e($equipement); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Colonne Latérale -->
            <div class="col-xl-4">
                <!-- Tarification -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h6 class="mb-3 text-primary">
                            <i class="fa-solid fa-money-bill-wave me-2"></i>Tarification
                        </h6>
                        <div class="mb-3">
                            <small class="text-muted d-block">Loyer mensuel</small>
                            <h3 class="text-primary mb-0"><?php echo e(number_format($chambre->loyer_mensuel, 0, ',', ' ')); ?> FCFA</h3>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($chambre->avance): ?>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted">Avance</span>
                                <strong><?php echo e(number_format($chambre->avance, 0, ',', ' ')); ?> FCFA</strong>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if($chambre->prepaye): ?>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted">Prépayé</span>
                                <strong><?php echo e(number_format($chambre->prepaye, 0, ',', ' ')); ?> FCFA</strong>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if($chambre->caution): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Caution</span>
                                <strong><?php echo e(number_format($chambre->caution, 0, ',', ' ')); ?> FCFA</strong>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- Informations -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h6 class="mb-3 text-primary">
                            <i class="fa-solid fa-info-circle me-2"></i>Informations
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Type</span>
                                    <strong><?php echo e(ucfirst(str_replace('_', ' ', $chambre->type_chambre))); ?></strong>
                                </div>
                            </li>
                            <!--[if BLOCK]><![endif]--><?php if($chambre->surface_m2): ?>
                                <li class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Surface</span>
                                        <strong><?php echo e(number_format($chambre->surface_m2, 0)); ?> m²</strong>
                                    </div>
                                </li>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Pièces</span>
                                    <strong><?php echo e($chambre->nombre_pieces); ?></strong>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Statut</span>
                                    <strong><?php echo e(ucfirst($chambre->statut)); ?></strong>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Disponible</span>
                                    <strong><?php echo e($chambre->disponible ? 'Oui' : 'Non'); ?></strong>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bien associé -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3 text-primary">
                            <i class="fa-solid fa-home me-2"></i>Bien Associé
                        </h6>
                        <h6><?php echo e($bien->titre); ?></h6>
                        <p class="text-muted mb-2">
                            <i class="fa-solid fa-map-marker-alt me-1"></i>
                            <?php echo e($bien->ville); ?>, <?php echo e($bien->quartier); ?>

                        </p>
                        <a href="<?php echo e(route('biens.detail', $bien->id)); ?>" class="btn btn-outline-primary btn-sm w-100">
                            Voir le bien
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmation de Suppression -->
    <!--[if BLOCK]><![endif]--><?php if($showDeleteModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>Attention !</strong> Cette action est irréversible.
                        </div>

                        <h6 class="mb-3">Conditions vérifiées :</h6>
                        <ul class="list-unstyled mb-3">
                            <li class="mb-2">
                                <i class="fa-solid fa-check-circle text-success me-2"></i>
                                Aucun contrat actif ou en attente
                            </li>
                            <li class="mb-2">
                                <i class="fa-solid fa-check-circle text-success me-2"></i>
                                Aucun paiement en attente
                            </li>
                            <li class="mb-2">
                                <i class="fa-solid fa-check-circle text-success me-2"></i>
                                Chambre non louée actuellement
                            </li>
                        </ul>

                        <p class="mb-0">Êtes-vous sûr de vouloir supprimer la chambre <strong><?php echo e($chambre->nom_chambre); ?></strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">
                            Annuler
                        </button>
                        <button type="button" 
                                class="btn btn-danger" 
                                wire:click="deleteChambre"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteChambre">
                                <i class="fa-solid fa-trash me-2"></i>Supprimer définitivement
                            </span>
                            <span wire:loading wire:target="deleteChambre">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i>Suppression...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/chambres/detail-chambre.blade.php ENDPATH**/ ?>