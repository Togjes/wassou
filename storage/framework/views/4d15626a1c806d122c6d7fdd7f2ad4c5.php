<div class="step-content">
    <h5 class="mb-4">Photos & Moyens de Paiement</h5>

    <!-- Photos existantes (en mode édition) -->
    <!--[if BLOCK]><![endif]--><?php if($isEdit && !empty($existing_photos)): ?>
        <div class="mb-4">
            <label class="form-label f-w-600">Photos actuelles</label>
            <div class="row g-3">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $existing_photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3">
                        <div class="position-relative">
                            <img src="<?php echo e(Storage::url($photo)); ?>" 
                                 alt="Photo <?php echo e($index + 1); ?>" 
                                 class="img-fluid rounded"
                                 style="width: 100%; height: 150px; object-fit: cover;">
                            <button type="button" 
                                    wire:click="deleteExistingPhoto(<?php echo e($index); ?>)"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Upload nouvelles photos -->
    <div class="mb-4">
        <label class="form-label f-w-600">
            <?php echo e($isEdit ? 'Ajouter de nouvelles photos' : 'Photos du Bien'); ?>

        </label>
        <input type="file" 
               wire:model="photos_generales" 
               class="form-control <?php $__errorArgs = ['photos_generales.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               multiple
               accept="image/*">
        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['photos_generales.*'];
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
        <small class="text-muted">Formats acceptés: JPG, PNG. Taille max: 5MB par photo</small>

        <!--[if BLOCK]><![endif]--><?php if(!empty($photos_generales)): ?>
            <div class="mt-3">
                <div wire:loading wire:target="photos_generales" class="text-info">
                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement des photos...
                </div>
                <p class="text-success" wire:loading.remove wire:target="photos_generales">
                    <?php echo e(count($photos_generales)); ?> nouvelle(s) photo(s) sélectionnée(s)
                </p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Documents (optionnel) -->
    <div class="mb-4">
        <label class="form-label f-w-600">Documents (optionnel)</label>
        <input type="file" 
               wire:model="documents" 
               class="form-control" 
               multiple 
               accept=".pdf,.doc,.docx">
        <small class="text-muted">Titre de propriété, plans, etc.</small>
        
        <!--[if BLOCK]><![endif]--><?php if(!empty($documents)): ?>
            <div class="mt-2">
                <div wire:loading wire:target="documents" class="text-info">
                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement des documents...
                </div>
                <p class="text-success" wire:loading.remove wire:target="documents">
                    <?php echo e(count($documents)); ?> document(s) sélectionné(s)
                </p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <hr class="my-4">

    <!-- Moyens de paiement -->
    <div class="mb-4">
        <label class="form-label f-w-600">Moyens de Paiement Acceptés <span class="txt-danger">*</span></label>
        <div class="row">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $moyens_paiement_disponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moyen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input <?php $__errorArgs = ['moyens_paiement_acceptes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               type="checkbox" 
                               wire:model.live="moyens_paiement_acceptes"
                               value="<?php echo e($moyen); ?>"
                               id="paiement_<?php echo e($moyen); ?>">
                        <label class="form-check-label" for="paiement_<?php echo e($moyen); ?>">
                            <!--[if BLOCK]><![endif]--><?php switch($moyen):
                                case ('mobile_money'): ?>
                                    <i class="fa-solid fa-mobile-alt me-2"></i>Mobile Money
                                    <?php break; ?>
                                <?php case ('virement'): ?>
                                    <i class="fa-solid fa-university me-2"></i>Virement Bancaire
                                    <?php break; ?>
                                <?php case ('especes'): ?>
                                    <i class="fa-solid fa-money-bill-wave me-2"></i>Espèces
                                    <?php break; ?>
                                <?php case ('cheque'): ?>
                                    <i class="fa-solid fa-money-check me-2"></i>Chèque
                                    <?php break; ?>
                                <?php case ('carte_bancaire'): ?>
                                    <i class="fa-solid fa-credit-card me-2"></i>Carte Bancaire
                                    <?php break; ?>
                            <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                        </label>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['moyens_paiement_acceptes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="text-danger small mt-2">
                <i class="fa-solid fa-exclamation-circle me-1"></i><?php echo e($message); ?>

            </div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Champs conditionnels selon les moyens de paiement -->
    
    <!-- Mobile Money -->
    <!--[if BLOCK]><![endif]--><?php if(in_array('mobile_money', $moyens_paiement_acceptes)): ?>
        <div class="card border-primary mb-3">
            <div class="card-header bg-light-primary">
                <h6 class="mb-0">
                    <i class="fa-solid fa-mobile-alt me-2"></i>Informations Mobile Money
                    <span class="txt-danger">*</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Numéro Mobile Money <span class="txt-danger">*</span></label>
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
                           placeholder="Ex: +229 XX XX XX XX">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['mobile_money_number'];
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
                    <small class="text-muted">Numéro pour recevoir les paiements</small>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Virement Bancaire -->
    <!--[if BLOCK]><![endif]--><?php if(in_array('virement', $moyens_paiement_acceptes)): ?>
        <div class="card border-success mb-3">
            <div class="card-header bg-light-success">
                <h6 class="mb-0">
                    <i class="fa-solid fa-university me-2"></i>Informations Bancaires
                    <span class="txt-danger">*</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom de la Banque <span class="txt-danger">*</span></label>
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
                               placeholder="Ex: BOA, Ecobank, SGBB...">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['bank_name'];
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Numéro de Compte <span class="txt-danger">*</span></label>
                        <input type="text" 
                               wire:model="bank_account_number" 
                               class="form-control <?php $__errorArgs = ['bank_account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Ex: BJ00000000000000000000">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['bank_account_number'];
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
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Informations pour espèces, chèque, carte bancaire -->
    <!--[if BLOCK]><![endif]--><?php if(in_array('especes', $moyens_paiement_acceptes) || in_array('cheque', $moyens_paiement_acceptes) || in_array('carte_bancaire', $moyens_paiement_acceptes)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i>
            <strong>Information :</strong> Les moyens de paiement sélectionnés (espèces, chèque, carte bancaire) seront gérés directement lors des transactions.
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Statut du bien -->
    <div class="mb-4">
        <label class="form-label f-w-600">Statut du Bien <span class="txt-danger">*</span></label>
        <select wire:model="statut" class="form-select <?php $__errorArgs = ['statut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Sélectionner un statut</option>
            <option value="Location">En Location</option>
            <option value="Construction">En Construction</option>
            <option value="Renovation">En Rénovation</option>
        </select>
        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['statut'];
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

    <!-- Navigation -->
    <div class="d-flex justify-content-between mt-4">
        <button type="button" 
                wire:click="previousStep" 
                class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Précédent
        </button>
        <button type="button" 
                wire:click="saveBien" 
                class="btn btn-success" 
                wire:loading.attr="disabled"
                wire:target="saveBien, photos_generales, documents">
            <span wire:loading.remove wire:target="saveBien">
                <i class="fa-solid fa-check me-2"></i><?php echo e($isEdit ? 'Mettre à jour' : 'Enregistrer'); ?>

            </span>
            <span wire:loading wire:target="saveBien">
                <i class="fa-solid fa-spinner fa-spin me-2"></i>Enregistrement en cours...
            </span>
        </button>
    </div>
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/biens/steps/step2.blade.php ENDPATH**/ ?>