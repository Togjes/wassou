<div class="step-content">
    <h5 class="mb-4">Photos & Moyens de Paiement</h5>

    <!-- Photos existantes (en mode édition) -->
    @if($isEdit && !empty($existing_photos))
        <div class="mb-4">
            <label class="form-label f-w-600">Photos actuelles</label>
            <div class="row g-3">
                @foreach($existing_photos as $index => $photo)
                    <div class="col-md-3">
                        <div class="position-relative">
                            <img src="{{ Storage::url($photo) }}" 
                                 alt="Photo {{ $index + 1 }}" 
                                 class="img-fluid rounded"
                                 style="width: 100%; height: 150px; object-fit: cover;">
                            <button type="button" 
                                    wire:click="deleteExistingPhoto({{ $index }})"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Upload nouvelles photos -->
    <div class="mb-4">
        <label class="form-label f-w-600">
            {{ $isEdit ? 'Ajouter de nouvelles photos' : 'Photos du Bien' }}
        </label>
        <input type="file" 
               wire:model="photos_generales" 
               class="form-control @error('photos_generales.*') is-invalid @enderror"
               multiple
               accept="image/*">
        @error('photos_generales.*')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Formats acceptés: JPG, PNG. Taille max: 5MB par photo</small>

        @if (!empty($photos_generales))
            <div class="mt-3">
                <div wire:loading wire:target="photos_generales" class="text-info">
                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement des photos...
                </div>
                <p class="text-success" wire:loading.remove wire:target="photos_generales">
                    {{ count($photos_generales) }} nouvelle(s) photo(s) sélectionnée(s)
                </p>
            </div>
        @endif
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
        
        @if (!empty($documents))
            <div class="mt-2">
                <div wire:loading wire:target="documents" class="text-info">
                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement des documents...
                </div>
                <p class="text-success" wire:loading.remove wire:target="documents">
                    {{ count($documents) }} document(s) sélectionné(s)
                </p>
            </div>
        @endif
    </div>

    <hr class="my-4">

    <!-- Moyens de paiement -->
    <div class="mb-4">
        <label class="form-label f-w-600">Moyens de Paiement Acceptés *</label>
        <div class="row">
            @foreach($moyens_paiement_disponibles as $moyen)
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input @error('moyens_paiement_acceptes') is-invalid @enderror" 
                               type="checkbox" 
                               wire:model.live="moyens_paiement_acceptes"
                               value="{{ $moyen }}"
                               id="paiement_{{ $moyen }}">
                        <label class="form-check-label" for="paiement_{{ $moyen }}">
                            @switch($moyen)
                                @case('mobile_money')
                                    <i class="fa-solid fa-mobile-alt me-2"></i>Mobile Money
                                    @break
                                @case('virement')
                                    <i class="fa-solid fa-university me-2"></i>Virement Bancaire
                                    @break
                                @case('especes')
                                    <i class="fa-solid fa-money-bill-wave me-2"></i>Espèces
                                    @break
                                @case('cheque')
                                    <i class="fa-solid fa-money-check me-2"></i>Chèque
                                    @break
                                @case('carte_bancaire')
                                    <i class="fa-solid fa-credit-card me-2"></i>Carte Bancaire
                                    @break
                            @endswitch
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        @error('moyens_paiement_acceptes')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>

    <!-- Champs conditionnels selon les moyens de paiement sélectionnés -->
    
    <!-- Mobile Money -->
    @if(in_array('mobile_money', $moyens_paiement_acceptes))
        <div class="card border-primary mb-3">
            <div class="card-header bg-light-primary">
                <h6 class="mb-0"><i class="fa-solid fa-mobile-alt me-2"></i>Informations Mobile Money</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Numéro Mobile Money</label>
                    <input type="text" 
                           wire:model="mobile_money_number" 
                           class="form-control @error('mobile_money_number') is-invalid @enderror"
                           placeholder="Ex: +229 XX XX XX XX">
                    @error('mobile_money_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Numéro pour recevoir les paiements</small>
                </div>
            </div>
        </div>
    @endif

    <!-- Virement Bancaire -->
    @if(in_array('virement', $moyens_paiement_acceptes))
        <div class="card border-success mb-3">
            <div class="card-header bg-light-success">
                <h6 class="mb-0"><i class="fa-solid fa-university me-2"></i>Informations Bancaires</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom de la Banque</label>
                        <input type="text" 
                               wire:model="bank_name" 
                               class="form-control @error('bank_name') is-invalid @enderror"
                               placeholder="Ex: BOA, Ecobank, SGBB...">
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Numéro de Compte</label>
                        <input type="text" 
                               wire:model="bank_account_number" 
                               class="form-control @error('bank_account_number') is-invalid @enderror"
                               placeholder="Ex: BJ00000000000000000000">
                        @error('bank_account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informations pour espèces, chèque, carte bancaire -->
    @if(in_array('especes', $moyens_paiement_acceptes) || in_array('cheque', $moyens_paiement_acceptes) || in_array('carte_bancaire', $moyens_paiement_acceptes))
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i>
            <strong>Information :</strong> Les moyens de paiement sélectionnés (espèces, chèque, carte bancaire) seront gérés directement lors des transactions.
        </div>
    @endif

    <!-- Statut du bien -->
    <div class="mb-4">
        <label class="form-label f-w-600">Statut du Bien</label>
        <select wire:model="statut" class="form-select">
            <option value="Location">En Location</option>
            <option value="Construction">En Construction</option>
            <option value="Renovation">En Rénovation</option>
        </select>
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
                <i class="fa-solid fa-check me-2"></i>{{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
            </span>
            <span wire:loading wire:target="saveBien">
                <i class="fa-solid fa-spinner fa-spin me-2"></i>Enregistrement en cours...
            </span>
        </button>
    </div>
</div>