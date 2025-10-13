<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Créer un Contrat</h3>
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
                            <a href="{{ route('contrats.liste') }}">Contrats</a>
                        </li>
                        <li class="breadcrumb-item active">Créer</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fa-solid fa-times-circle"></i>
                        {{ session('error') }}
                        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form wire:submit.prevent="saveContrat">
                    
                    <!-- ÉTAPE 1 : Rechercher le Bien -->
                    <div class="card {{ $bien_trouve ? 'border-success' : 'border-warning' }}">
                        <div class="card-header {{ $bien_trouve ? 'bg-light-success' : 'bg-light-warning' }}">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-building me-2"></i>
                                Étape 1 : Rechercher le Bien
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(!$bien_trouve)
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label">Référence du Bien <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               wire:model="reference_bien" 
                                               class="form-control @error('reference_bien') is-invalid @enderror"
                                               placeholder="Ex: BIEN-ABC123-001">
                                        @error('reference_bien')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Entrez la référence du bien immobilier</small>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" 
                                                wire:click="chercherBien" 
                                                class="btn btn-primary w-100"
                                                wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="chercherBien">
                                                <i class="fa-solid fa-search me-2"></i>Chercher
                                            </span>
                                            <span wire:loading wire:target="chercherBien">
                                                <i class="fa-solid fa-spinner fa-spin me-2"></i>Recherche...
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                @if(session()->has('error_bien'))
                                    <div class="alert alert-danger mt-3 mb-0">
                                        <i class="fa-solid fa-exclamation-circle me-2"></i>
                                        {{ session('error_bien') }}
                                    </div>
                                @endif
                            @else
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1">{{ $bien_selectionne->titre ?? $bien_selectionne->type_bien }}</h6>
                                        <p class="mb-0 text-muted">
                                            <i class="fa-solid fa-map-marker-alt me-2"></i>
                                            {{ $bien_selectionne->ville }}, {{ $bien_selectionne->quartier }}
                                        </p>
                                        <p class="mb-0">
                                            <span class="badge badge-success">Réf: {{ $bien_selectionne->reference }}</span>
                                        </p>
                                    </div>
                                    <button type="button" 
                                            wire:click="resetBien" 
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-times me-2"></i>Changer
                                    </button>
                                </div>

                                @if(session()->has('success_bien'))
                                    <div class="alert alert-success mt-3 mb-0">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        {{ session('success_bien') }}
                                    </div>
                                @endif

                                <!-- Sélection de la chambre -->
                                <div class="mt-4">
                                    <label class="form-label">Chambre <span class="text-danger">*</span></label>
                                    <select wire:model.live="chambre_id" 
                                            class="form-select @error('chambre_id') is-invalid @enderror">
                                        <option value="">Sélectionnez une chambre</option>
                                        @foreach($chambres as $chambre)
                                            <option value="{{ $chambre->id }}">
                                                {{ $chambre->nom_chambre }} (Réf: {{ $chambre->reference }}) - 
                                                {{ number_format($chambre->loyer_mensuel, 0, ',', ' ') }} FCFA/mois
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('chambre_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(count($chambres) === 0)
                                        <div class="alert alert-warning mt-2">
                                            Aucune chambre disponible pour ce bien
                                        </div>
                                    @endif

                                    @if($loyer_mensuel > 0)
                                        <div class="alert alert-info mt-2">
                                            <strong>Loyer mensuel :</strong> {{ number_format($loyer_mensuel, 0, ',', ' ') }} FCFA
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- ÉTAPE 2 : Rechercher le Locataire -->
                    @if($bien_trouve && $chambre_id)
                        <div class="card mt-3 {{ $locataire_trouve ? 'border-success' : 'border-warning' }}">
                            <div class="card-header {{ $locataire_trouve ? 'bg-light-success' : 'bg-light-warning' }}">
                                <h5 class="mb-0">
                                    <i class="fa-solid fa-user me-2"></i>
                                    Étape 2 : Rechercher le Locataire
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(!$locataire_trouve)
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Code Unique du Locataire <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="code_locataire" 
                                                   class="form-control @error('code_locataire') is-invalid @enderror"
                                                   placeholder="Ex: LOC-XXXXXXXX">
                                            @error('code_locataire')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Entrez le code unique du locataire</small>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" 
                                                    wire:click="chercherLocataire" 
                                                    class="btn btn-primary w-100"
                                                    wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="chercherLocataire">
                                                    <i class="fa-solid fa-search me-2"></i>Chercher
                                                </span>
                                                <span wire:loading wire:target="chercherLocataire">
                                                    <i class="fa-solid fa-spinner fa-spin me-2"></i>Recherche...
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    @if(session()->has('error_locataire'))
                                        <div class="alert alert-danger mt-3 mb-0">
                                            <i class="fa-solid fa-exclamation-circle me-2"></i>
                                            {{ session('error_locataire') }}
                                        </div>
                                    @endif
                                @else
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-1">{{ $locataire_selectionne->user->full_name }}</h6>
                                            <p class="mb-0 text-muted">
                                                <i class="fa-solid fa-phone me-2"></i>{{ $locataire_selectionne->user->phone }}
                                            </p>
                                            <p class="mb-0 text-muted">
                                                <i class="fa-solid fa-envelope me-2"></i>{{ $locataire_selectionne->user->email }}
                                            </p>
                                            <p class="mb-0">
                                                <span class="badge badge-info">Code: {{ $locataire_selectionne->user->code_unique }}</span>
                                            </p>
                                        </div>
                                        <button type="button" 
                                                wire:click="resetLocataire" 
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="fa-solid fa-times me-2"></i>Changer
                                        </button>
                                    </div>

                                    @if(session()->has('success_locataire'))
                                        <div class="alert alert-success mt-3 mb-0">
                                            <i class="fa-solid fa-check-circle me-2"></i>
                                            {{ session('success_locataire') }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- ÉTAPE 3 : Informations du Contrat -->
                    @if($bien_trouve && $chambre_id && $locataire_trouve)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>
                                    <i class="fa-solid fa-file-contract me-2"></i>
                                    Étape 3 : Informations du Contrat
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date d'Établissement *</label>
                                        <input type="date" 
                                               wire:model="date_etablissement" 
                                               class="form-control @error('date_etablissement') is-invalid @enderror">
                                        @error('date_etablissement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jour de Paiement du Loyer *</label>
                                        <select wire:model="date_paiement_loyer" 
                                                class="form-select @error('date_paiement_loyer') is-invalid @enderror">
                                            @for($i = 1; $i <= 28; $i++)
                                                <option value="{{ $i }}">Le {{ $i }} de chaque mois</option>
                                            @endfor
                                        </select>
                                        @error('date_paiement_loyer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if(count($demarcheurs) > 0)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Démarcheur (optionnel)</label>
                                            <select wire:model="demarcheur_id" class="form-select">
                                                <option value="">Aucun</option>
                                                @foreach($demarcheurs as $demarcheur)
                                                    <option value="{{ $demarcheur->id }}">
                                                        {{ $demarcheur->user->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ÉTAPE 4 : Paiements Initiaux -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>
                                    <i class="fa-solid fa-money-bill me-2"></i>
                                    Étape 4 : Paiements Initiaux
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Avance sur Loyer (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="avance_loyer" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Déduit progressivement du loyer</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prépayé Loyer (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="prepaye_loyer" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Mois payés d'avance</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Caution (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="caution" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Remboursable en fin de contrat</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Frais de Dossier (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="frais_dossier" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Non remboursables</small>
                                    </div>
                                </div>

                                <!-- Récapitulatif -->
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">Récapitulatif</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Total à Payer</h5>
                                            <h4 class="mb-0 text-primary">
                                                {{ number_format($total_a_payer, 0, ',', ' ') }} FCFA
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('contrats.liste') }}" class="btn btn-secondary">
                                        <i class="fa-solid fa-arrow-left me-2"></i>Retour
                                    </a>
                                    <button type="submit" 
                                            class="btn btn-success" 
                                            wire:loading.attr="disabled"
                                            wire:target="saveContrat">
                                        <span wire:loading.remove wire:target="saveContrat">
                                            <i class="fa-solid fa-check me-2"></i>Créer le Contrat
                                        </span>
                                        <span wire:loading wire:target="saveContrat">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>Création...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>