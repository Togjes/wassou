<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            .card.border-danger {
                border-width: 2px !important;
                opacity: 0.85;
            }
            
            .card.border-success {
                border-width: 2px !important;
            }
            
            .card.border-success:hover {
                box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
                transform: translateY(-2px);
                transition: all 0.3s ease;
            }
            
            .card.border-danger:hover {
                box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            }
            
            select option:disabled {
                color: #dc3545;
                font-style: italic;
            }
            
            .chambre-card-disponible {
                cursor: pointer;
            }
            
            .chambre-card-occupee {
                cursor: not-allowed;
                background-color: #f8f9fa;
            }
        </style>
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
                                            <span class="badge badge-info ms-2">Propriétaire: {{ $bien_selectionne->proprietaire->user->name }}</span>
                                        </p>
                                    </div>
                                    <button type="button" 
                                            wire:click="resetBien" 
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-times me-2"></i>Changer</button>
                                </div>

                                @if(session()->has('success_bien'))
                                    <div class="alert alert-success mt-3 mb-0">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        {{ session('success_bien') }}
                                    </div>
                                @endif

                                <!-- Sélection de la chambre -->
                                {{-- <div class="mt-4">
                                    <label class="form-label">Chambre <span class="text-danger">*</span></label>
                                    <select wire:model.live="chambre_id" 
                                            class="form-select @error('chambre_id') is-invalid @enderror">
                                        <option value="">Sélectionnez une chambre</option>
                                        @foreach($chambres as $chambre)
                                            <option value="{{ $chambre->id }}" @if($chambre->est_sous_contrat) disabled @endif>
                                                {{ $chambre->nom_chambre }} 
                                                (Réf: {{ $chambre->reference }}) - 
                                                {{ number_format($chambre->loyer_mensuel, 0, ',', ' ') }} FCFA/mois
                                                @if($chambre->est_sous_contrat)
                                                    - ⚠️ SOUS CONTRAT
                                                @else
                                                    - ✓ Disponible
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('chambre_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(count($chambres) === 0)
                                        <div class="alert alert-warning mt-2">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            Aucune chambre n'a été trouvée pour ce bien.
                                        </div>
                                    @else
                                        <!-- Affichage visuel des chambres -->
                                        <div class="row g-2 mt-3">
                                            @foreach($chambres as $chambre)
                                                <div class="col-md-6">
                                                    <div class="card {{ $chambre->est_sous_contrat ? 'border-danger' : 'border-success' }} h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="mb-0">{{ $chambre->nom_chambre }}</h6>
                                                                @if($chambre->est_sous_contrat)
                                                                    <span class="badge bg-danger">
                                                                        <i class="fa-solid fa-lock me-1"></i>Sous Contrat
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-success">
                                                                        <i class="fa-solid fa-check me-1"></i>Disponible
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <p class="mb-1 small text-muted">
                                                                <i class="fa-solid fa-tag me-1"></i>
                                                                Réf: {{ $chambre->reference }}
                                                            </p>
                                                            <p class="mb-1 small">
                                                                <i class="fa-solid fa-money-bill me-1"></i>
                                                                <strong>{{ number_format($chambre->loyer_mensuel, 0, ',', ' ') }} FCFA/mois</strong>
                                                            </p>
                                                            @if($chambre->est_sous_contrat && $chambre->contrat_actif)
                                                                <div class="alert alert-danger p-2 mb-0 mt-2">
                                                                    <small>
                                                                        <i class="fa-solid fa-info-circle me-1"></i>
                                                                        <strong>Contrat N°:</strong> {{ $chambre->contrat_actif->numero_contrat }}<br>
                                                                        <strong>Locataire:</strong> {{ $chambre->contrat_actif->locataire->user->full_name }}<br>
                                                                        <strong>Statut:</strong> {{ $chambre->contrat_actif->statut_libelle }}</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($loyer_mensuel > 0)
                                        <div class="alert alert-info mt-3">
                                            <i class="fa-solid fa-info-circle me-2"></i>
                                            <strong>Loyer mensuel de la chambre sélectionnée :</strong> {{ number_format($loyer_mensuel, 0, ',', ' ') }} FCFA
                                        </div>
                                    @endif
                                </div> --}}
                                <!-- Sélection de la chambre - VERSION AVEC CARTES CLIQUABLES -->
                                <div class="mt-4">
                                    <label class="form-label">Sélectionnez une Chambre <span class="text-danger">*</span></label>
                                    
                                    @error('chambre_id')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(count($chambres) === 0)
                                        <div class="alert alert-warning mt-2">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            Aucune chambre n'a été trouvée pour ce bien.
                                        </div>
                                    @else
                                        <div class="row g-3 mt-2">
                                            @foreach($chambres as $chambre)
                                                <div class="col-md-6">
                                                    <div class="card h-100 {{ $chambre->est_sous_contrat ? 'border-danger chambre-card-occupee' : 'border-success chambre-card-disponible' }} {{ $chambre_id == $chambre->id ? 'border-primary shadow-lg' : '' }}"
                                                        wire:click="{{ $chambre->est_sous_contrat ? '' : '$set(\'chambre_id\', \'' . $chambre->id . '\')' }}"
                                                        style="cursor: {{ $chambre->est_sous_contrat ? 'not-allowed' : 'pointer' }}; border-width: {{ $chambre_id == $chambre->id ? '3px' : '2px' }} !important;">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <h6 class="mb-0">
                                                                        @if($chambre_id == $chambre->id)
                                                                            <i class="fa-solid fa-check-circle text-primary me-2"></i>
                                                                        @endif
                                                                        {{ $chambre->nom_chambre }}
                                                                    </h6>
                                                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $chambre->type_chambre)) }}</small>
                                                                </div>
                                                                @if($chambre->est_sous_contrat)
                                                                    <span class="badge bg-danger">
                                                                        <i class="fa-solid fa-lock me-1"></i>Occupée
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-success">
                                                                        <i class="fa-solid fa-check me-1"></i>Disponible
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="mb-2">
                                                                <p class="mb-1 small text-muted">
                                                                    <i class="fa-solid fa-tag me-1"></i>
                                                                    Réf: {{ $chambre->reference }}
                                                                </p>
                                                                @if($chambre->surface_m2)
                                                                    <p class="mb-1 small text-muted">
                                                                        <i class="fa-solid fa-ruler-combined me-1"></i>
                                                                        {{ number_format($chambre->surface_m2, 0) }} m²
                                                                    </p>
                                                                @endif
                                                                <p class="mb-1">
                                                                    <i class="fa-solid fa-money-bill me-1"></i>
                                                                    <strong class="text-primary">{{ number_format($chambre->loyer_mensuel, 0, ',', ' ') }} FCFA</strong>
                                                                    <small class="text-muted">/mois</small>
                                                                </p>
                                                            </div>
                                                            
                                                            @if($chambre->est_sous_contrat && $chambre->contrat_actif)
                                                                <div class="alert alert-danger p-2 mb-0">
                                                                    <small>
                                                                        <i class="fa-solid fa-info-circle me-1"></i>
                                                                        <strong>Cette chambre est louée</strong><br>
                                                                        <strong>Contrat:</strong> {{ $chambre->contrat_actif->numero_contrat }}<br>
                                                                        <strong>Locataire:</strong> {{ $chambre->contrat_actif->locataire->user->full_name }}<br>
                                                                        <strong>Statut:</strong> 
                                                                        <span class="badge 
                                                                            @if($chambre->contrat_actif->statut === 'actif') bg-success
                                                                            @else bg-warning
                                                                            @endif">
                                                                            {{ $chambre->contrat_actif->statut_libelle }}
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                            @else
                                                                <div class="text-center">
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ $chambre_id == $chambre->id ? 'btn-primary' : 'btn-outline-primary' }} w-100"
                                                                            wire:click="$set('chambre_id', '{{ $chambre->id }}')">
                                                                        @if($chambre_id == $chambre->id)
                                                                            <i class="fa-solid fa-check me-2"></i>Sélectionnée
                                                                        @else
                                                                            <i class="fa-solid fa-hand-pointer me-2"></i>Sélectionner
                                                                        @endif
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($chambre_id && $loyer_mensuel > 0)
                                        <div class="alert alert-success mt-3">
                                            <i class="fa-solid fa-check-circle me-2"></i>
                                            <strong>Chambre sélectionnée avec succès !</strong><br>
                                            Loyer mensuel : <strong>{{ number_format($loyer_mensuel, 0, ',', ' ') }} FCFA</strong>
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
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    <strong>Information :</strong> Les montants indiqués ci-dessous seront automatiquement enregistrés comme payés après validation.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Avance sur Loyer (FCFA)</label>
                                        <input type="number" 
                                               wire:model.live="avance_loyer" 
                                               class="form-control"
                                               min="0"
                                               step="1000">
                                        <small class="text-muted">Déduit progressivement du loyer mensuel</small>
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

                                <!-- Moyen de Paiement -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            <strong>Moyen de paiement utilisé par le locataire</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Moyen de Paiement <span class="text-danger">*</span></label>
                                        <select wire:model.live="methode_paiement_initial" 
                                                class="form-select @error('methode_paiement_initial') is-invalid @enderror">
                                            <option value="">Sélectionnez un moyen</option>
                                            @foreach($moyens_paiement as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('methode_paiement_initial')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Détails selon le moyen de paiement -->
                                    @if($methode_paiement_initial === 'mobile_money')
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Numéro Mobile Money <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="mobile_money_number" 
                                                   class="form-control @error('mobile_money_number') is-invalid @enderror"
                                                   placeholder="+229 XX XX XX XX">
                                            @error('mobile_money_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Référence de Transaction</label>
                                            <input type="text" 
                                                   wire:model="reference_transaction" 
                                                   class="form-control"
                                                   placeholder="Ex: MP240116.1234.A12345">
                                            <small class="text-muted">Optionnel</small>
                                        </div>
                                    @endif

                                    @if($methode_paiement_initial === 'virement')
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom de la Banque <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="bank_name" 
                                                   class="form-control @error('bank_name') is-invalid @enderror"
                                                   placeholder="Ex: ORABANK">
                                            @error('bank_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Référence de Transaction <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="reference_transaction" 
                                                   class="form-control @error('reference_transaction') is-invalid @enderror"
                                                   placeholder="Ex: VIR20240116123456">
                                            @error('reference_transaction')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    @if($methode_paiement_initial === 'cheque')
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Numéro de Chèque <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="numero_cheque" 
                                                   class="form-control @error('numero_cheque') is-invalid @enderror"
                                                   placeholder="Ex: 1234567">
                                            @error('numero_cheque')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom de la Banque <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   wire:model="bank_name" 
                                                   class="form-control @error('bank_name') is-invalid @enderror"
                                                   placeholder="Ex: BOA">
                                            @error('bank_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                                <!-- Consentement -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="mb-3 text-danger">
                                                    <i class="fa-solid fa-hand-point-right me-2"></i>
                                                    Confirmation Importante
                                                </h6>
                                                <div class="form-check">
                                                    <input class="form-check-input @error('consentement_paiement') is-invalid @enderror" 
                                                           type="checkbox" 
                                                           wire:model="consentement_paiement"
                                                           id="consentement">
                                                    <label class="form-check-label" for="consentement">
                                                        <strong>Je certifie avoir reçu les paiements initiaux</strong> d'un montant total de 
                                                        <strong class="text-primary">{{ number_format($total_a_payer, 0, ',', ' ') }} FCFA</strong> 
                                                        de la part de <strong>{{ $locataire_selectionne->user->full_name ?? 'ce locataire' }}</strong> 
                                                        via <strong>{{ $moyens_paiement[$methode_paiement_initial] ?? 'le moyen sélectionné' }}</strong>.
                                                        Ces montants seront enregistrés comme payés dans le système.
                                                    </label>
                                                    @error('consentement_paiement')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Récapitulatif -->
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">Récapitulatif des Paiements</h6>
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                @if($avance_loyer > 0)
                                                    <tr>
                                                        <td>Avance sur Loyer</td>
                                                        <td class="text-end"><strong>{{ number_format($avance_loyer, 0, ',', ' ') }} FCFA</strong></td>
                                                    </tr>
                                                @endif
                                                @if($prepaye_loyer > 0)
                                                    <tr>
                                                        <td>Prépayé Loyer</td>
                                                        <td class="text-end"><strong>{{ number_format($prepaye_loyer, 0, ',', ' ') }} FCFA</strong></td>
                                                    </tr>
                                                @endif
                                                @if($caution > 0)
                                                    <tr>
                                                        <td>Caution</td>
                                                        <td class="text-end"><strong>{{ number_format($caution, 0, ',', ' ') }} FCFA</strong></td>
                                                    </tr>
                                                @endif
                                                @if($frais_dossier > 0)
                                                    <tr>
                                                        <td>Frais de Dossier</td>
                                                        <td class="text-end"><strong>{{ number_format($frais_dossier, 0, ',', ' ') }} FCFA</strong></td>
                                                    </tr>
                                                @endif
                                                <tr class="table-primary">
                                                    <td><strong>TOTAL À PAYER</strong></td>
                                                    <td class="text-end">
                                                        <h5 class="mb-0 text-primary">{{ number_format($total_a_payer, 0, ',', ' ') }} FCFA</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                            class="btn btn-success btn-lg" 
                                            wire:loading.attr="disabled"
                                            wire:target="saveContrat"
                                            @if(!$consentement_paiement) disabled @endif>
                                        <span wire:loading.remove wire:target="saveContrat">
                                            <i class="fa-solid fa-check me-2"></i>Créer le Contrat et Enregistrer les Paiements
                                        </span>
                                        <span wire:loading wire:target="saveContrat">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>Création en cours...
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