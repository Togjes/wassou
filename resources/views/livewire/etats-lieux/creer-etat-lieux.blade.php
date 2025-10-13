<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>État des Lieux {{ $type === 'entree' ? 'd\'Entrée' : 'de Sortie' }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            {{-- <a href="{{ route('contrats.liste') }}">Contrats</a> --}}
                        </li>
                        <li class="breadcrumb-item">
                            {{-- <a href="{{ route('contrats.detail', $contratId) }}">{{ $contrat->numero_contrat }}</a> --}}
                        </li>
                        <li class="breadcrumb-item active">État des Lieux {{ ucfirst($type) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-check-circle"></i>
                {{ session('success') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-times-circle"></i>
                {{ session('error') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Informations du Contrat -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-file-contract me-2"></i>
                            Contrat {{ $contrat->numero_contrat }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Propriétaire :</strong> {{ $contrat->proprietaire->user->full_name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Locataire :</strong> {{ $contrat->locataire->user->full_name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Bien :</strong> {{ $contrat->chambre->bien->titre }} - {{ $contrat->chambre->nom_chambre }}
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
                                       class="form-control @error('date_etat') is-invalid @enderror">
                                @error('date_etat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                        @foreach($equipements as $nom => $details)
                                            <tr>
                                                <td>
                                                    <strong>{{ $nom }}</strong>
                                                </td>
                                                <td>
                                                    <select wire:model="equipements.{{ $nom }}.etat" 
                                                            class="form-select form-select-sm">
                                                        <option value="bon">✓ Bon état</option>
                                                        <option value="moyen">~ État moyen</option>
                                                        <option value="mauvais">✗ Mauvais état</option>
                                                        <option value="absent">- Absent</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                           wire:model="equipements.{{ $nom }}.observations"
                                                           class="form-control form-control-sm"
                                                           placeholder="Précisions...">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Dégâts Constatés (uniquement pour sortie) -->
                    @if($type === 'sortie')
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
                                @if(count($degats) > 0)
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
                                                @foreach($degats as $index => $degat)
                                                    <tr>
                                                        <td>
                                                            <input type="text" 
                                                                   wire:model="degats.{{ $index }}.description"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="Décrivez le dégât...">
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   wire:model.blur="degats.{{ $index }}.cout"
                                                                   wire:change="calculateCoutReparations"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="0"
                                                                   min="0"
                                                                   step="100">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" 
                                                                    wire:click="removeDegat({{ $index }})"
                                                                    class="btn btn-sm btn-danger">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-info">
                                                    <td class="text-end"><strong>Total des Réparations :</strong></td>
                                                    <td colspan="2">
                                                        <strong class="text-danger">
                                                            {{ number_format($cout_reparations, 0, ',', ' ') }} FCFA
                                                        </strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted text-center py-3">
                                        <i class="fa-solid fa-check-circle text-success fa-2x mb-2"></i><br>
                                        Aucun dégât constaté
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

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
                            @if(count($existing_photos) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Photos Existantes</label>
                                    <div class="row">
                                        @foreach($existing_photos as $index => $photo)
                                            <div class="col-md-3 mb-3">
                                                <div class="position-relative">
                                                    <img src="{{ Storage::url($photo) }}" 
                                                         alt="Photo {{ $index + 1 }}"
                                                         class="img-fluid rounded"
                                                         style="height: 150px; width: 100%; object-fit: cover;">
                                                    <button type="button"
                                                            wire:click="removeExistingPhoto({{ $index }})"
                                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Ajouter de nouvelles photos -->
                            <div>
                                <label class="form-label">Ajouter des Photos</label>
                                <input type="file" 
                                       wire:model="photos" 
                                       class="form-control @error('photos.*') is-invalid @enderror"
                                       multiple
                                       accept="image/*">
                                @error('photos.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Vous pouvez sélectionner plusieurs images (max 2 Mo chacune)</small>
                                
                                @if($photos)
                                    <div wire:loading wire:target="photos" class="text-info mt-2">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Chargement des photos...
                                    </div>
                                    <div wire:loading.remove wire:target="photos" class="mt-2">
                                        <p class="text-success">
                                            <i class="fa-solid fa-check-circle"></i>
                                            {{ count($photos) }} photo(s) sélectionnée(s)
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('contrats.detail', $contratId) }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>Retour au Contrat
                                </a>
                                <button type="submit" 
                                        class="btn btn-success" 
                                        wire:loading.attr="disabled"
                                        wire:target="saveEtatLieux, photos">
                                    <span wire:loading.remove wire:target="saveEtatLieux">
                                        <i class="fa-solid fa-save me-2"></i>
                                        {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }} l'État des Lieux
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
                @if($etatLieux)
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
                                <p class="mb-1"><strong>{{ $contrat->proprietaire->user->full_name }}</strong></p>
                                @if($etatLieux->date_signature_proprietaire)
                                    <div class="alert alert-success">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        Signé le {{ $etatLieux->date_signature_proprietaire->format('d/m/Y à H:i') }}
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        En attente de signature
                                    </div>
                                    @if(auth()->user()->isAdmin() || auth()->id() === $contrat->proprietaire->user_id)
                                        <button wire:click="openSignatureModal('proprietaire')" 
                                                class="btn btn-primary btn-sm w-100">
                                            <i class="fa-solid fa-signature me-2"></i>Signer
                                        </button>
                                    @endif
                                @endif
                            </div>

                            <!-- Signature Locataire -->
                            <div>
                                <h6 class="mb-2">Locataire</h6>
                                <p class="mb-1"><strong>{{ $contrat->locataire->user->full_name }}</strong></p>
                                @if($etatLieux->date_signature_locataire)
                                    <div class="alert alert-success">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        Signé le {{ $etatLieux->date_signature_locataire->format('d/m/Y à H:i') }}
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        En attente de signature
                                    </div>
                                    @if(auth()->user()->isAdmin() || auth()->id() === $contrat->locataire->user_id)
                                        <button wire:click="openSignatureModal('locataire')" 
                                                class="btn btn-primary btn-sm w-100">
                                            <i class="fa-solid fa-signature me-2"></i>Signer
                                        </button>
                                    @endif
                                @endif
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
                    @if($type === 'sortie' && $cout_reparations > 0)
                        <div class="card mt-3 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">
                                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                    Résumé des Dégâts
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">Nombre de dégâts : <strong>{{ count($degats) }}</strong></p>
                                <h5 class="mb-0 text-danger">
                                    Total à déduire : {{ number_format($cout_reparations, 0, ',', ' ') }} FCFA
                                </h5>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Enregistrez d'abord l'état des lieux pour pouvoir le signer.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Signature -->
    @if($showSignatureModal)
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
                            Vous signez en tant que <strong>{{ $signature_type === 'proprietaire' ? 'Propriétaire' : 'Locataire' }}</strong>
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
    @endif
</div>