<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>{{ $isEdit ? 'Modifier' : 'Ajouter' }} une Chambre</h3>
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
                            <a href="{{ route('biens.liste') }}">Biens</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('biens.detail', $bienId) }}">{{ $bien->titre }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $isEdit ? 'Modifier' : 'Ajouter' }} Chambre</li>
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
                            {{ $isEdit ? 'Modification de la chambre' : 'Nouvelle chambre' }}
                        </h5>
                        <p class="text-muted mb-0">Bien : <strong>{{ $bien->titre }}</strong></p>
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
                                               class="form-control @error('nom_chambre') is-invalid @enderror"
                                               placeholder="Ex: Chambre 1, Studio A, Bureau 01...">
                                        @error('nom_chambre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Type de Chambre *</label>
                                        <select wire:model="type_chambre" 
                                                class="form-select @error('type_chambre') is-invalid @enderror">
                                            @foreach($types_chambre as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('type_chambre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Surface (m²)</label>
                                            <input type="number" 
                                                   wire:model="surface_m2" 
                                                   class="form-control @error('surface_m2') is-invalid @enderror"
                                                   step="0.01"
                                                   placeholder="25.5">
                                            @error('surface_m2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nombre de Pièces *</label>
                                            <input type="number" 
                                                   wire:model="nombre_pieces" 
                                                   class="form-control @error('nombre_pieces') is-invalid @enderror"
                                                   min="1"
                                                   placeholder="1">
                                            @error('nombre_pieces')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea wire:model="description" 
                                                  class="form-control @error('description') is-invalid @enderror"
                                                  rows="3"
                                                  placeholder="Description détaillée de la chambre..."></textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Équipements -->
                                    <h6 class="mb-3 text-primary mt-4">Équipements</h6>
                                    <div class="mb-3">
                                        <div class="row">
                                            @foreach($equipements_disponibles as $equipement)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               wire:model="equipements"
                                                               value="{{ $equipement }}"
                                                               id="equip_{{ str_replace(' ', '_', $equipement) }}">
                                                        <label class="form-check-label" for="equip_{{ str_replace(' ', '_', $equipement) }}">
                                                            {{ $equipement }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Photos -->
                                    <h6 class="mb-3 text-primary mt-4">Photos de la Chambre</h6>

                                    @if($isEdit && !empty($existing_photos))
                                        <div class="mb-3">
                                            <label class="form-label">Photos actuelles</label>
                                            <div class="row g-2">
                                                @foreach($existing_photos as $index => $photo)
                                                    <div class="col-md-4">
                                                        <div class="position-relative">
                                                            <img src="{{ Storage::url($photo) }}" 
                                                                 alt="Photo {{ $index + 1 }}" 
                                                                 class="img-fluid rounded"
                                                                 style="width: 100%; height: 120px; object-fit: cover;">
                                                            <button type="button" 
                                                                    wire:click="deleteExistingPhoto({{ $index }})"
                                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1">
                                                                <i class="fa-solid fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label">
                                            {{ $isEdit ? 'Ajouter de nouvelles photos' : 'Photos' }}
                                        </label>
                                        <input type="file" 
                                               wire:model="photos" 
                                               class="form-control @error('photos.*') is-invalid @enderror"
                                               multiple
                                               accept="image/*">
                                        @error('photos.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Max: 5MB par photo</small>

                                        @if (!empty($photos))
                                            <div class="mt-2">
                                                <div wire:loading wire:target="photos" class="text-info">
                                                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement...
                                                </div>
                                                <p class="text-success" wire:loading.remove wire:target="photos">
                                                    {{ count($photos) }} photo(s) sélectionnée(s)
                                                </p>
                                            </div>
                                        @endif
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
                                               class="form-control @error('loyer_mensuel') is-invalid @enderror"
                                               placeholder="50000"
                                               min="0"
                                               step="1000">
                                        @error('loyer_mensuel')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Avance (FCFA)</label>
                                            <input type="number" 
                                                   wire:model="avance" 
                                                   class="form-control @error('avance') is-invalid @enderror"
                                                   placeholder="100000"
                                                   min="0"
                                                   step="1000">
                                            @error('avance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Optionnel</small>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Prépayé (FCFA)</label>
                                            <input type="number" 
                                                   wire:model="prepaye" 
                                                   class="form-control @error('prepaye') is-invalid @enderror"
                                                   placeholder="50000"
                                                   min="0"
                                                   step="1000">
                                            @error('prepaye')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Optionnel</small>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Caution (FCFA)</label>
                                            <input type="number" 
                                                   wire:model="caution" 
                                                   class="form-control @error('caution') is-invalid @enderror"
                                                   placeholder="50000"
                                                   min="0"
                                                   step="1000">
                                            @error('caution')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Optionnel</small>
                                        </div>
                                    </div>

                                    <!-- Disponibilité -->
                                    <h6 class="mb-3 text-primary mt-4">Disponibilité</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Statut *</label>
                                        <select wire:model="statut" 
                                                class="form-select @error('statut') is-invalid @enderror">
                                            @foreach($statuts as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('statut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                                    <strong>{{ number_format($loyer_mensuel ?: 0, 0, ',', ' ') }} FCFA</strong>
                                                </li>
                                                @if($avance)
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Avance :</span>
                                                        <strong>{{ number_format($avance, 0, ',', ' ') }} FCFA</strong>
                                                    </li>
                                                @endif
                                                @if($caution)
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Caution :</span>
                                                        <strong>{{ number_format($caution, 0, ',', ' ') }} FCFA</strong>
                                                    </li>
                                                @endif
                                                <li class="d-flex justify-content-between pt-2 border-top">
                                                    <span class="text-muted">Équipements :</span>
                                                    <strong>{{ count($equipements) }}</strong>
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
                                        <a href="{{ route('biens.detail', $bienId) }}" class="btn btn-secondary">
                                            <i class="fa-solid fa-arrow-left me-2"></i>Retour
                                        </a>
                                        <button type="submit" 
                                                class="btn btn-success" 
                                                wire:loading.attr="disabled"
                                                wire:target="saveChambre, photos">
                                            <span wire:loading.remove wire:target="saveChambre">
                                                <i class="fa-solid fa-check me-2"></i>{{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
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

    @push('scripts')
        <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    @endpush
</div>