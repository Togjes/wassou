<div>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php $__env->stopPush(); ?>

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>État des Lieux <?php echo e($type === 'entree' ? 'd\'Entrée' : 'de Sortie'); ?></h3>
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
                            
                        </li>
                        <li class="breadcrumb-item">
                            
                        </li>
                        <li class="breadcrumb-item active">État des Lieux <?php echo e(ucfirst($type)); ?></li>
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
                <i class="fa-solid fa-times-circle"></i>
                <?php echo e(session('error')); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="row">
            <!-- Informations du Contrat -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-file-contract me-2"></i>
                            Contrat <?php echo e($contrat->numero_contrat); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Propriétaire :</strong> <?php echo e($contrat->proprietaire->user->full_name); ?>

                            </div>
                            <div class="col-md-4">
                                <strong>Locataire :</strong> <?php echo e($contrat->locataire->user->full_name); ?>

                            </div>
                            <div class="col-md-4">
                                <strong>Bien :</strong> <?php echo e($contrat->chambre->bien->titre); ?> - <?php echo e($contrat->chambre->nom_chambre); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire État des Lieux -->
            <div class="col-lg-8">
                <form wire:submit.prevent="saveEtatLieux">
                    <!-- Informations Générales -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Informations Générales
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Date de l'État des Lieux *</label>
                                <input type="date" 
                                       wire:model="date_etat" 
                                       class="form-control <?php $__errorArgs = ['date_etat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_etat'];
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
                                <label class="form-label">Observations Générales</label>
                                <textarea wire:model="observations" 
                                          class="form-control"
                                          rows="4"
                                          placeholder="Observations générales sur l'état du bien..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- État des Équipements -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-tools me-2"></i>
                                État des Équipements
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30%;">Équipement</th>
                                            <th style="width: 25%;">État</th>
                                            <th style="width: 45%;">Observations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $equipements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nom => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo e($nom); ?></strong>
                                                </td>
                                                <td>
                                                    <select wire:model="equipements.<?php echo e($nom); ?>.etat" 
                                                            class="form-select form-select-sm">
                                                        <option value="bon">✓ Bon état</option>
                                                        <option value="moyen">~ État moyen</option>
                                                        <option value="mauvais">✗ Mauvais état</option>
                                                        <option value="absent">- Absent</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                           wire:model="equipements.<?php echo e($nom); ?>.observations"
                                                           class="form-control form-control-sm"
                                                           placeholder="Précisions...">
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Dégâts Constatés (uniquement pour sortie) -->
                    <!--[if BLOCK]><![endif]--><?php if($type === 'sortie'): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fa-solid fa-exclamation-triangle me-2 text-danger"></i>
                                        Dégâts Constatés
                                    </h6>
                                    <button type="button" 
                                            wire:click="addDegat" 
                                            class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus me-2"></i>Ajouter un dégât
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!--[if BLOCK]><![endif]--><?php if(count($degats) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 60%;">Description du Dégât</th>
                                                    <th style="width: 30%;">Coût Réparation (FCFA)</th>
                                                    <th style="width: 10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $degats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $degat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td>
                                                            <input type="text" 
                                                                   wire:model="degats.<?php echo e($index); ?>.description"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="Décrivez le dégât...">
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   wire:model.blur="degats.<?php echo e($index); ?>.cout"
                                                                   wire:change="calculateCoutReparations"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="0"
                                                                   min="0"
                                                                   step="100">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" 
                                                                    wire:click="removeDegat(<?php echo e($index); ?>)"
                                                                    class="btn btn-sm btn-danger">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-info">
                                                    <td class="text-end"><strong>Total des Réparations :</strong></td>
                                                    <td colspan="2">
                                                        <strong class="text-danger">
                                                            <?php echo e(number_format($cout_reparations, 0, ',', ' ')); ?> FCFA
                                                        </strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3">
                                        <i class="fa-solid fa-check-circle text-success fa-2x mb-2"></i><br>
                                        Aucun dégât constaté
                                    </p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!-- Photos -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-camera me-2"></i>
                                Photos
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Photos existantes -->
                            <!--[if BLOCK]><![endif]--><?php if(count($existing_photos) > 0): ?>
                                <div class="mb-3">
                                    <label class="form-label">Photos Existantes</label>
                                    <div class="row">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $existing_photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-3 mb-3">
                                                <div class="position-relative">
                                                    <img src="<?php echo e(Storage::url($photo)); ?>" 
                                                         alt="Photo <?php echo e($index + 1); ?>"
                                                         class="img-fluid rounded"
                                                         style="height: 150px; width: 100%; object-fit: cover;">
                                                    <button type="button"
                                                            wire:click="removeExistingPhoto(<?php echo e($index); ?>)"
                                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <!-- Ajouter de nouvelles photos -->
                            <div>
                                <label class="form-label">Ajouter des Photos</label>
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
                                <small class="text-muted">Vous pouvez sélectionner plusieurs images (max 2 Mo chacune)</small>
                                
                                <!--[if BLOCK]><![endif]--><?php if($photos): ?>
                                    <div wire:loading wire:target="photos" class="text-info mt-2">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Chargement des photos...
                                    </div>
                                    <div wire:loading.remove wire:target="photos" class="mt-2">
                                        <p class="text-success">
                                            <i class="fa-solid fa-check-circle"></i>
                                            <?php echo e(count($photos)); ?> photo(s) sélectionnée(s)
                                        </p>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo e(route('contrats.detail', $contratId)); ?>" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>Retour au Contrat
                                </a>
                                <button type="submit" 
                                        class="btn btn-success" 
                                        wire:loading.attr="disabled"
                                        wire:target="saveEtatLieux, photos">
                                    <span wire:loading.remove wire:target="saveEtatLieux">
                                        <i class="fa-solid fa-save me-2"></i>
                                        <?php echo e($isEdit ? 'Mettre à jour' : 'Enregistrer'); ?> l'État des Lieux
                                    </span>
                                    <span wire:loading wire:target="saveEtatLieux">
                                        <i class="fa-solid fa-spinner fa-spin me-2"></i>Enregistrement...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Panneau Signatures -->
            <div class="col-lg-4">
                <!--[if BLOCK]><![endif]--><?php if($etatLieux): ?>
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-signature me-2"></i>
                                Signatures
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Signature Propriétaire -->
                            <div class="mb-4">
                                <h6 class="mb-2">Propriétaire</h6>
                                <p class="mb-1"><strong><?php echo e($contrat->proprietaire->user->full_name); ?></strong></p>
                                <!--[if BLOCK]><![endif]--><?php if($etatLieux->date_signature_proprietaire): ?>
                                    <div class="alert alert-success">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        Signé le <?php echo e($etatLieux->date_signature_proprietaire->format('d/m/Y à H:i')); ?>

                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        En attente de signature
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php if(auth()->user()->isAdmin() || auth()->id() === $contrat->proprietaire->user_id): ?>
                                        <button wire:click="openSignatureModal('proprietaire')" 
                                                class="btn btn-primary btn-sm w-100">
                                            <i class="fa-solid fa-signature me-2"></i>Signer
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!-- Signature Locataire -->
                            <div>
                                <h6 class="mb-2">Locataire</h6>
                                <p class="mb-1"><strong><?php echo e($contrat->locataire->user->full_name); ?></strong></p>
                                <!--[if BLOCK]><![endif]--><?php if($etatLieux->date_signature_locataire): ?>
                                    <div class="alert alert-success">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        Signé le <?php echo e($etatLieux->date_signature_locataire->format('d/m/Y à H:i')); ?>

                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        En attente de signature
                                    </div>
                                    <?php if(auth()->user()->isAdmin() || auth()->id() === $contrat->locataire->user_id): ?>
                                        <button wire:click="openSignatureModal('locataire')" 
                                                class="btn btn-primary btn-sm w-100">
                                            <i class="fa-solid fa-signature me-2"></i>Signer
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button wire:click="downloadPDF" class="btn btn-danger">
                                    <i class="fa-solid fa-file-pdf me-2"></i>Télécharger PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé -->
                    <!--[if BLOCK]><![endif]--><?php if($type === 'sortie' && $cout_reparations > 0): ?>
                        <div class="card mt-3 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">
                                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                    Résumé des Dégâts
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">Nombre de dégâts : <strong><?php echo e(count($degats)); ?></strong></p>
                                <h5 class="mb-0 text-danger">
                                    Total à déduire : <?php echo e(number_format($cout_reparations, 0, ',', ' ')); ?> FCFA
                                </h5>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Enregistrez d'abord l'état des lieux pour pouvoir le signer.
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    <!-- Modal Signature -->
    <!--[if BLOCK]><![endif]--><?php if($showSignatureModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-signature me-2"></i>
                            Confirmer la Signature
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showSignatureModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p>En signant cet état des lieux, vous confirmez l'exactitude des informations reportées.</p>
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Vous signez en tant que <strong><?php echo e($signature_type === 'proprietaire' ? 'Propriétaire' : 'Locataire'); ?></strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showSignatureModal', false)">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="signer">
                            <i class="fa-solid fa-check me-2"></i>Confirmer la Signature
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/livewire/etats-lieux/creer-etat-lieux.blade.php ENDPATH**/ ?>