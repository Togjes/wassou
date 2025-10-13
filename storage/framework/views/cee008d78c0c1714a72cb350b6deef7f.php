<div>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php $__env->stopPush(); ?>

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Gestion des Contrats</h3>
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
                        <li class="breadcrumb-item active">Contrats</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fa-solid fa-check-circle"></i>
                        <?php echo e(session('success')); ?>

                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php if(session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fa-solid fa-times-circle"></i>
                        <?php echo e(session('error')); ?>

                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Filtres -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0 f-w-600">
                            <i class="fa-solid fa-filter me-2"></i>Filtres de recherche
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label f-w-600">Rechercher</label>
                                <input wire:model.live.debounce.300ms="search" 
                                       type="text" 
                                       class="form-control" 
                                       placeholder="N° contrat, locataire, bien...">
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if(auth()->user()->isProprietaire() && $biens->count() > 0): ?>
                                <div class="col-md-3">
                                    <label class="form-label f-w-600">Bien</label>
                                    <select wire:model.live="bien_filter" class="form-select">
                                        <option value="">Tous les biens</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bien->id); ?>"><?php echo e($bien->titre); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <div class="col-md-3">
                                <label class="form-label f-w-600">Statut</label>
                                <select wire:model.live="statut_filter" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="brouillon">Brouillon</option>
                                    <option value="en_attente">En Attente</option>
                                    <option value="actif">Actif</option>
                                    <option value="expire">Expiré</option>
                                    <option value="resilie">Résilié</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button wire:click="resetFilters" class="btn btn-secondary w-100">
                                    <i class="fa-solid fa-refresh me-2"></i>Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-file-contract me-2"></i>Liste des Contrats
                                <span class="badge badge-light-primary ms-2"><?php echo e($contrats->total()); ?></span>
                            </h5>
                            <!--[if BLOCK]><![endif]--><?php if(!auth()->user()->isLocataire()): ?>
                                <a href="<?php echo e(route('contrats.creer')); ?>" class="btn btn-primary">
                                    <i class="fa-solid fa-plus me-2"></i>Nouveau Contrat
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>N° Contrat</th>
                                        <th>Locataire</th>
                                        <th>Bien / Chambre</th>
                                        <th>Loyer</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($contrat->numero_contrat); ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo e($contrat->locataire->user->full_name); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fa-solid fa-phone me-1"></i>
                                                        <?php echo e($contrat->locataire->user->phone); ?>

                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo e($contrat->chambre->bien->titre); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo e($contrat->chambre->nom_chambre); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-primary">
                                                    <?php echo e(number_format($contrat->loyer_mensuel, 0, ',', ' ')); ?> FCFA
                                                </strong>
                                                <br>
                                                <small class="text-muted">Paiement le <?php echo e($contrat->date_paiement_loyer); ?></small>
                                            </td>
                                            <td>
                                                <?php echo e($contrat->date_etablissement->format('d/m/Y')); ?>

                                            </td>
                                            <td>
                                                <!--[if BLOCK]><![endif]--><?php if($contrat->statut === 'brouillon'): ?>
                                                    <span class="badge badge-secondary">Brouillon</span>
                                                <?php elseif($contrat->statut === 'en_attente'): ?>
                                                    <span class="badge badge-warning">En Attente</span>
                                                <?php elseif($contrat->statut === 'actif'): ?>
                                                    <span class="badge badge-success">Actif</span>
                                                <?php elseif($contrat->statut === 'expire'): ?>
                                                    <span class="badge badge-info">Expiré</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Résilié</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <!--[if BLOCK]><![endif]--><?php if($contrat->hasUnpaidPayments()): ?>
                                                    <br>
                                                    <small class="badge badge-danger mt-1">
                                                        <i class="fa-solid fa-exclamation-triangle"></i> Impayés
                                                    </small>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo e(route('contrats.detail', $contrat->id)); ?>" 
                                                       class="btn btn-outline-primary"
                                                       title="Voir détails">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if(!auth()->user()->isLocataire() && $contrat->statut === 'brouillon'): ?>
                                                        <a href="<?php echo e(route('contrats.modifier', $contrat->id)); ?>" 
                                                           class="btn btn-outline-secondary"
                                                           title="Modifier">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                    <button type="button"
                                                            class="btn btn-outline-info"
                                                            title="Télécharger PDF">
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fa-solid fa-file-contract fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun contrat trouvé</h5>
                                                <p class="text-muted">
                                                    <!--[if BLOCK]><![endif]--><?php if($search || $statut_filter || $bien_filter): ?>
                                                        Aucun contrat ne correspond à vos critères.
                                                    <?php else: ?>
                                                        Commencez par créer votre premier contrat.
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </p>
                                                <!--[if BLOCK]><![endif]--><?php if(!auth()->user()->isLocataire()): ?>
                                                    <a href="<?php echo e(route('contrats.creer')); ?>" class="btn btn-primary mt-3">
                                                        <i class="fa-solid fa-plus me-2"></i>Créer un Contrat
                                                    </a>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                        </tr>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($contrats->hasPages()): ?>
                            <div class="mt-3">
                                <?php echo e($contrats->links()); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h3 class="mb-0"><?php echo e($contrats->where('statut', 'actif')->count()); ?></h3>
                                <p class="mb-0">Contrats Actifs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h3 class="mb-0"><?php echo e($contrats->where('statut', 'en_attente')->count()); ?></h3>
                                <p class="mb-0">En Attente de Signature</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h3 class="mb-0"><?php echo e($contrats->where('statut', 'expire')->count()); ?></h3>
                                <p class="mb-0">Contrats Expirés</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h3 class="mb-0"><?php echo e($contrats->filter(fn($c) => $c->hasUnpaidPayments())->count()); ?></h3>
                                <p class="mb-0">Avec Impayés</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/contrats/liste-contrats.blade.php ENDPATH**/ ?>