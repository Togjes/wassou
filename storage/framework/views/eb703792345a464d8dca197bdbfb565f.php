<div>
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Mes Démarcheurs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">
                            <svg class="stroke-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-home')); ?>"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Gestion</li>
                        <li class="breadcrumb-item active">Démarcheurs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Messages de succès/erreur -->
                <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        <?php echo e(session('success')); ?>

                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php if(session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fa-solid fa-exclamation-circle me-2"></i>
                        <?php echo e(session('error')); ?>

                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Card principale -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Liste de mes démarcheurs autorisés</h5>
                        <button type="button" 
                                class="btn btn-primary" 
                                wire:click="$set('showAddModal', true)">
                            <i class="fa-solid fa-plus me-2"></i>Ajouter un démarcheur
                        </button>
                    </div>
                    <div class="card-body">
                        <!--[if BLOCK]><![endif]--><?php if($demarcheurs->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Démarcheur</th>
                                            <th>Code</th>
                                            <th>Contact</th>
                                            <th>Statut</th>
                                            <th>Date d'ajout</th>
                                            <th>Permissions</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $demarcheurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demarcheur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <!--[if BLOCK]><![endif]--><?php if($demarcheur->user->profile_image_url): ?>
                                                                <img src="<?php echo e(Storage::url($demarcheur->user->profile_image_url)); ?>" 
                                                                     alt="<?php echo e($demarcheur->user->name); ?>"
                                                                     class="rounded-circle"
                                                                     width="40"
                                                                     height="40"
                                                                     style="object-fit: cover;">
                                                            <?php else: ?>
                                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" 
                                                                     style="width: 40px; height: 40px; font-weight: bold; font-size: 14px;">
                                                                    <?php echo e(substr($demarcheur->user->first_name, 0, 1)); ?><?php echo e(substr($demarcheur->user->last_name, 0, 1)); ?>

                                                                </div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                        <div>
                                                            <strong><?php echo e($demarcheur->user->name); ?></strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo e($demarcheur->user->code_unique); ?></span>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <i class="fa-solid fa-phone me-1"></i><?php echo e($demarcheur->user->phone); ?><br>
                                                        <i class="fa-solid fa-envelope me-1"></i><?php echo e($demarcheur->user->email); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <!--[if BLOCK]><![endif]--><?php switch($demarcheur->pivot->statut):
                                                        case ('actif'): ?>
                                                            <span class="badge badge-success">
                                                                <i class="fa-solid fa-check-circle me-1"></i>Actif
                                                            </span>
                                                            <?php break; ?>
                                                        <?php case ('suspendu'): ?>
                                                            <span class="badge badge-warning">
                                                                <i class="fa-solid fa-pause-circle me-1"></i>Suspendu
                                                            </span>
                                                            <?php break; ?>
                                                        <?php case ('refuse'): ?>
                                                            <span class="badge badge-danger">
                                                                <i class="fa-solid fa-times-circle me-1"></i>Refusé
                                                            </span>
                                                            <?php break; ?>
                                                    <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                                                </td>
                                                <td><?php echo e($demarcheur->pivot->created_at->format('d/m/Y')); ?></td>
                                                <td>
                                                    <!--[if BLOCK]><![endif]--><?php if(empty($demarcheur->pivot->permissions)): ?>
                                                        <span class="badge badge-primary">Toutes</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary"><?php echo e(count($demarcheur->pivot->permissions)); ?> permission(s)</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary"
                                                                wire:click="ouvrirModalPermissions('<?php echo e($demarcheur->id); ?>')"
                                                                title="Gérer les permissions">
                                                            <i class="fa-solid fa-key"></i>
                                                        </button>
                                                        
                                                        <!--[if BLOCK]><![endif]--><?php if($demarcheur->pivot->statut === 'actif'): ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    wire:click="suspendre('<?php echo e($demarcheur->id); ?>')"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir suspendre ce démarcheur ?')"
                                                                    title="Suspendre">
                                                                <i class="fa-solid fa-pause"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-success"
                                                                    wire:click="reactiver('<?php echo e($demarcheur->id); ?>')"
                                                                    title="Réactiver">
                                                                <i class="fa-solid fa-play"></i>
                                                            </button>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                wire:click="retirer('<?php echo e($demarcheur->id); ?>')"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir retirer définitivement ce démarcheur ? Cette action est irréversible.')"
                                                                title="Retirer">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <?php echo e($demarcheurs->links()); ?>

                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Vous n'avez pas encore ajouté de démarcheur. Cliquez sur le bouton "Ajouter un démarcheur" pour commencer.
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter un démarcheur -->
    <!--[if BLOCK]><![endif]--><?php if($showAddModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-user-plus me-2"></i>Ajouter un démarcheur
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showAddModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <?php if(session()->has('error_modal')): ?>
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-exclamation-circle me-2"></i>
                                <?php echo e(session('error_modal')); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div class="mb-3">
                            <label class="form-label">Code Unique du Démarcheur <span class="txt-danger">*</span></label>
                            <input type="text" 
                                   wire:model="code_demarcheur" 
                                   class="form-control <?php $__errorArgs = ['code_demarcheur'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Ex: DEM-XXXXX">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['code_demarcheur'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block">
                                    <i class="fa-solid fa-exclamation-circle me-1"></i><?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <small class="text-muted">Demandez au démarcheur de vous communiquer son code unique.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (optionnel)</label>
                            <textarea wire:model="notes" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="Ex: Gestionnaire principal, responsable de la zone Nord..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>Info :</strong> Par défaut, le démarcheur aura toutes les permissions. Vous pourrez les ajuster après l'ajout.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary" 
                                wire:click="$set('showAddModal', false)">
                            Annuler
                        </button>
                        <button type="button" 
                                class="btn btn-primary" 
                                wire:click="ajouterDemarcheur"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="ajouterDemarcheur">
                                <i class="fa-solid fa-plus me-2"></i>Ajouter
                            </span>
                            <span wire:loading wire:target="ajouterDemarcheur">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i>Ajout en cours...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Modal Gérer les permissions -->
    <!--[if BLOCK]><![endif]--><?php if($showPermissionsModal && $demarcheur_en_cours): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-key me-2"></i>Gérer les permissions - <?php echo e($demarcheur_en_cours->user->name); ?>

                        </h5>
                        <button type="button" class="btn-close" wire:click="fermerModalPermissions"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Si aucune permission n'est sélectionnée, le démarcheur aura accès à toutes les fonctionnalités.
                        </div>

                        <p class="mb-3">Sélectionnez les permissions spécifiques que vous souhaitez accorder :</p>

                        <div class="row">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $permissions_disponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       wire:model.live="permissions_selectionnees"
                                                       value="<?php echo e($key); ?>"
                                                       id="perm_<?php echo e($key); ?>">
                                                <label class="form-check-label fw-bold" for="perm_<?php echo e($key); ?>">
                                                    <?php echo e($label); ?>

                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!-- Debug info -->
                        <div class="alert alert-info mt-3">
                            <small>
                                <strong>Sélectionnées :</strong> <?php echo e(count($permissions_selectionnees)); ?> permission(s)
                                <!--[if BLOCK]><![endif]--><?php if(!empty($permissions_selectionnees)): ?>
                                    <br><?php echo e(implode(', ', $permissions_selectionnees)); ?>

                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary" 
                                wire:click="fermerModalPermissions">
                            Annuler
                        </button>
                        <button type="button" 
                                class="btn btn-success" 
                                wire:click="sauvegarderPermissions"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sauvegarderPermissions">
                                <i class="fa-solid fa-save me-2"></i>Enregistrer
                            </span>
                            <span wire:loading wire:target="sauvegarderPermissions">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i>Enregistrement...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <style>
        .form-check-input:checked {
            background-color: #7366ff;
            border-color: #7366ff;
        }
        
        .card:has(.form-check-input:checked) {
            background-color: #f0f4ff;
            border-color: #7366ff;
        }
    </style>
</div>
<?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/gerer-demarcheurs.blade.php ENDPATH**/ ?>