<div>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/vendors/sweetalert2.css')); ?>">
    <?php $__env->stopPush(); ?>

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3><?php echo e($isEdit ? 'Modifier' : 'Ajouter'); ?> une Chambre</h3>
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
                            <a href="<?php echo e(route('biens.detail', $bienId)); ?>"><?php echo e($bien->titre); ?></a>
                        </li>
                        <li class="breadcrumb-item active"><?php echo e($isEdit ? 'Modifier' : 'Ajouter'); ?> Chambre</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="fa-solid fa-bed me-2"></i>
                            <?php echo e($isEdit ? 'Modification de la chambre' : 'Nouvelle chambre'); ?>

                        </h5>
                        <p class="text-muted mb-0">Bien : <strong><?php echo e($bien->titre); ?></strong></p>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveChambre">
                            <div class="row">
                                <!-- Colonne Gauche -->
                                <div class="col-lg-6">
                                    <!-- Informations de base -->
                                    <h6 class="mb-3 text-primary">Informations de Base</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Nom de la Chambre *</label>
                                        <input type="text" 
                                               wire:model="nom_chambre" 
                                               class="form-control <?php $__errorArgs = ['nom_chambre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               placeholder="Ex: Chambre 1, Studio A, Bureau 01...">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nom_chambre'];
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

                                    <div class="mb-3">
                                        <label class="form-label">Type de Chambre *</label>
                                        <select wire:model="type_chambre" 
                                                class="form-select <?php $__errorArgs = ['type_chambre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $types_chambre; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['type_chambre'];
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

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Surface (m²)</label>
                                            <input type="number" 
                                                   wire:model="surface_m2" 
                                                   class="form-control <?php $__errorArgs = ['surface_m2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   step="0.01"
                                                   placeholder="25.5">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['surface_m2'];
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
                                            <label class="form-label">Nombre de Pièces *</label>
                                            <input type="number" 
                                                   wire:model="nombre_pieces" 
                                                   class="form-control <?php $__errorArgs = ['nombre_pieces'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   min="1"
                                                   placeholder="1">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nombre_pieces'];
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

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea wire:model="description" 
                                                  class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                  rows="3"
                                                  placeholder="Description détaillée de la chambre..."></textarea>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
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

                                    <!-- Équipements -->
                                    <h6 class="mb-3 text-primary mt-4">Équipements</h6>
                                    <div class="mb-3">
                                        <div class="row">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $equipements_disponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               wire:model="equipements"
                                                               value="<?php echo e($equipement); ?>"
                                                               id="equip_<?php echo e(str_replace(' ', '_', $equipement)); ?>">
                                                        <label class="form-check-label" for="equip_<?php echo e(str_replace(' ', '_', $equipement)); ?>">
                                                            <?php echo e($equipement); ?>

                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>

                                    <!-- Photos -->
                                    <h6 class="mb-3 text-primary mt-4">Photos de la Chambre</h6>

                                    <!--[if BLOCK]><![endif]--><?php if($isEdit && !empty($existing_photos)): ?>
                                        <div class="mb-3">
                                            <label class="form-label">Photos actuelles</label>
                                            <div class="row g-2">
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $existing_photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="col-md-4">
                                                        <div class="position-relative">
                                                            <img src="<?php echo e(Storage::url($photo)); ?>" 
                                                                 alt="Photo <?php echo e($index + 1); ?>" 
                                                                 class="img-fluid rounded"
                                                                 style="width: 100%; height: 120px; object-fit: cover;">
                                                            <button type="button" 
                                                                    wire:click="deleteExistingPhoto(<?php echo e($index); ?>)"
                                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1">
                                                                <i class="fa-solid fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <div class="mb-3">
                                        <label class="form-label">
                                            <?php echo e($isEdit ? 'Ajouter de nouvelles photos' : 'Photos'); ?>

                                        </label>
                                        <input type="file" 
                                               wire:model="photos" 
                                               class="form-control <?php $__errorArgs = ['photos.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               multiple
                                               accept="image/*">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['photos.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <small class="text-muted">Max: 5MB par photo</small>

                                        <!--[if BLOCK]><![endif]--><?php if(!empty($photos)): ?>
                                            <div class="mt-2">
                                                <div wire:loading wire:target="photos" class="text-info">
                                                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement...
                                                </div>
                                                <p class="text-success" wire:loading.remove wire:target="photos">
                                                    <?php echo e(count($photos)); ?> photo(s) sélectionnée(s)
                                                </p>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Colonne Droite -->
                                <div class="col-lg-6">
                                    <!-- Tarification -->
                                    <h6 class="mb-3 text-primary">Tarification</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Loyer Mensuel (FCFA) *</label>
                                        <input type="number" 
                                               wire:model="loyer_mensuel" 
                                               class="form-control <?php $__errorArgs = ['loyer_mensuel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               placeholder="50000"
                                               min="0"
                                               step="1000">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['loyer_mensuel'];
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

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Avance (FCFA)</label>
                                            <input type="number" 
                                                   wire:model="avance" 
                                                   class="form-control <?php $__errorArgs = ['avance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="100000"
                                                   min="0"
                                                   step="1000">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['avance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            <small class="text-muted">Optionnel</small>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Prépayé (FCFA)</label>
                                            <input type="number" 
                                                   wire:model="prepaye" 
                                                   class="form-control <?php $__errorArgs = ['prepaye'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="50000"
                                                   min="0"
                                                   step="1000">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['prepaye'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            <small class="text-muted">Optionnel</small>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Caution (FCFA)</label>
                                            <input type="number" 
                                                   wire:model="caution" 
                                                   class="form-control <?php $__errorArgs = ['caution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   placeholder="50000"
                                                   min="0"
                                                   step="1000">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['caution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            <small class="text-muted">Optionnel</small>
                                        </div>
                                    </div>

                                    <!-- Disponibilité -->
                                    <h6 class="mb-3 text-primary mt-4">Disponibilité</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Statut *</label>
                                        <select wire:model="statut" 
                                                class="form-select <?php $__errorArgs = ['statut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['statut'];
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

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   wire:model="disponible"
                                                   id="disponible">
                                            <label class="form-check-label" for="disponible">
                                                Chambre disponible à la location
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Récapitulatif -->
                                    <div class="card bg-light mt-4">
                                        <div class="card-body">
                                            <h6 class="mb-3">Récapitulatif</h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Loyer mensuel :</span>
                                                    <strong><?php echo e(number_format($loyer_mensuel ?: 0, 0, ',', ' ')); ?> FCFA</strong>
                                                </li>
                                                <!--[if BLOCK]><![endif]--><?php if($avance): ?>
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Avance :</span>
                                                        <strong><?php echo e(number_format($avance, 0, ',', ' ')); ?> FCFA</strong>
                                                    </li>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <!--[if BLOCK]><![endif]--><?php if($caution): ?>
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Caution :</span>
                                                        <strong><?php echo e(number_format($caution, 0, ',', ' ')); ?> FCFA</strong>
                                                    </li>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <li class="d-flex justify-content-between pt-2 border-top">
                                                    <span class="text-muted">Équipements :</span>
                                                    <strong><?php echo e(count($equipements)); ?></strong>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="<?php echo e(route('biens.detail', $bienId)); ?>" class="btn btn-secondary">
                                            <i class="fa-solid fa-arrow-left me-2"></i>Retour
                                        </a>
                                        <button type="submit" 
                                                class="btn btn-success" 
                                                wire:loading.attr="disabled"
                                                wire:target="saveChambre, photos">
                                            <span wire:loading.remove wire:target="saveChambre">
                                                <i class="fa-solid fa-check me-2"></i><?php echo e($isEdit ? 'Mettre à jour' : 'Enregistrer'); ?>

                                            </span>
                                            <span wire:loading wire:target="saveChambre">
                                                <i class="fa-solid fa-spinner fa-spin me-2"></i>Enregistrement...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/chambres/creer-chambre.blade.php ENDPATH**/ ?>