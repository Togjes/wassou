<div class="sidebar-body">
    <form class="row g-3 common-form">
        <div class="col-12">
            <h5 class="mb-3">Informations de Base</h5>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="type_bien">Type de Bien <span class="txt-danger">*</span></label>
            <select wire:model="type_bien" 
                    class="form-select <?php $__errorArgs = ['type_bien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                    id="type_bien">
                <option value="">Sélectionner un type</option>
                <option value="appartement">Appartement</option>
                <option value="maison">Maison</option>
                <option value="bureau">Bureau</option>
                <option value="magasin">Magasin</option>
                <option value="terrain">Terrain</option>
            </select>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['type_bien'];
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
        </div>

        <!-- AFFICHER LA RÉFÉRENCE EN MODE ÉDITION -->
        <!--[if BLOCK]><![endif]--><?php if($isEdit && $bienId): ?>
            <div class="col-md-6">
                <label class="form-label">Référence du Bien</label>
                <input type="text" 
                    value="<?php echo e(\App\Models\BienImmobilier::find($bienId)->reference); ?>" 
                    class="form-control-plaintext fw-bold text-primary" 
                    readonly>
                <small class="text-muted">Référence unique générée automatiquement</small>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="col-md-6">
            <label class="form-label" for="ville">Ville <span class="txt-danger">*</span></label>
            <input wire:model="ville" 
                   class="form-control <?php $__errorArgs = ['ville'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="ville" 
                   type="text" 
                   placeholder="Cotonou">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ville'];
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
        </div>

        <div class="col-md-6">
            <label class="form-label" for="quartier">Quartier <span class="txt-danger">*</span></label>
            <input wire:model="quartier" 
                   class="form-control <?php $__errorArgs = ['quartier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="quartier" 
                   type="text" 
                   placeholder="Ex: Akpakpa, Fidjrossè...">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['quartier'];
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
        </div>

        <div class="col-md-6">
            <label class="form-label" for="adresse">Adresse complète <span class="txt-danger">*</span></label>
            <input wire:model="adresse" 
                   class="form-control <?php $__errorArgs = ['adresse'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="adresse" 
                   type="text" 
                   placeholder="Ex: CALAVI/IITA">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['adresse'];
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
        </div>

        <div class="col-md-12">
            <label class="form-label">Description du Bien</label>
            <textarea wire:model="description" 
                      class="form-control" 
                      rows="4" 
                      placeholder="Décrivez votre bien immobilier..."></textarea>
            <small class="text-muted">Décrivez les atouts principaux de votre bien</small>
        </div>

        <div class="col-12">
            <hr class="my-3">
            <h5 class="mb-3">Caractéristiques</h5>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="annee_construction">Année de Construction <span class="txt-danger">*</span></label>
            <input wire:model="annee_construction" 
                   class="form-control <?php $__errorArgs = ['annee_construction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="annee_construction" 
                   type="date"
                   max="<?php echo e(date('Y-m-d')); ?>">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['annee_construction'];
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
        </div>

        <div class="col-12">
            <label class="form-label d-block">Équipements Communs</label>
            <div class="row g-2">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $equipements_disponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 col-6">
                        <div class="form-check">
                            <input wire:model="equipements_communs" 
                                   class="form-check-input" 
                                   type="checkbox" 
                                   value="<?php echo e($equipement); ?>" 
                                   id="equip_<?php echo e($loop->index); ?>">
                            <label class="form-check-label" for="equip_<?php echo e($loop->index); ?>">
                                <?php echo e($equipement); ?>

                            </label>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <div></div>
            <button type="button" 
                    wire:click="nextStep" 
                    class="btn btn-success" 
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Suivant
                    <i class="bi bi-arrow-right-circle-fill ms-1"></i>
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin me-2"></i>Chargement...
                </span>
            </button>
        </div>
    </form>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/biens/steps/step1.blade.php ENDPATH**/ ?>