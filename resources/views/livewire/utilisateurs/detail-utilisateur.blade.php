<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Détails de l'Utilisateur</h3>
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
                            <a href="{{ route('utilisateurs.liste') }}">Utilisateurs</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $user->full_name }}</li>
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
            <!-- Colonne Gauche -->
            <div class="col-lg-4">
                <!-- Profil -->
                <div class="card">
                    <div class="card-body text-center">
                        @if($user->profile_image_url)
                            <img src="{{ Storage::url($user->profile_image_url) }}" 
                                 alt="{{ $user->full_name }}"
                                 class="rounded-circle mb-3"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 150px; height: 150px;">
                                <i class="fa-solid fa-user fa-4x text-muted"></i>
                            </div>
                        @endif

                        <h4 class="mb-1">{{ $user->full_name }}</h4>
                        
                        @if($user->user_type === 'admin')
                            <span class="badge badge-danger mb-3">Administrateur</span>
                        @elseif($user->user_type === 'proprietaire')
                            <span class="badge badge-primary mb-3">Propriétaire</span>
                        @elseif($user->user_type === 'locataire')
                            <span class="badge badge-info mb-3">Locataire</span>
                        @else
                            <span class="badge badge-warning mb-3">Démarcheur</span>
                        @endif

                        <div class="mb-3">
                            @if($user->is_active)
                                <span class="badge badge-success">Compte Actif</span>
                            @else
                                <span class="badge badge-secondary">Compte Inactif</span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('utilisateurs.modifier', $user->id) }}" class="btn btn-primary">
                                <i class="fa-solid fa-edit me-2"></i>Modifier
                            </a>
                            @if($user->id !== auth()->id())
                                <button wire:click="toggleStatus" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-power-off me-2"></i>
                                    {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-address-book me-2"></i>Contact
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0">
                                <i class="fa-solid fa-envelope text-primary me-2"></i>
                                {{ $user->email }}
                                @if($user->email_verified_at)
                                    <i class="fa-solid fa-check-circle text-success ms-1" title="Vérifié"></i>
                                @endif
                            </p>
                        </div>

                        @if($user->phone)
                            <div class="mb-3">
                                <label class="text-muted small">Téléphone</label>
                                <p class="mb-0">
                                    <i class="fa-solid fa-phone text-primary me-2"></i>
                                    {{ $user->phone }}
                                </p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="text-muted small">Localisation</label>
                            <p class="mb-0">
                                <i class="fa-solid fa-map-marker-alt text-primary me-2"></i>
                                {{ $user->ville }}, {{ $user->pays }}
                            </p>
                        </div>

                        @if($user->date_naissance)
                            <div>
                                <label class="text-muted small">Date de Naissance</label>
                                <p class="mb-0">
                                    <i class="fa-solid fa-calendar text-primary me-2"></i>
                                    {{ \Carbon\Carbon::parse($user->date_naissance)->format('d/m/Y') }}
                                    ({{ \Carbon\Carbon::parse($user->date_naissance)->age }} ans)
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne Droite -->
            <div class="col-lg-8">
                <!-- Informations spécifiques Propriétaire -->
                @if($user->isProprietaire() && $user->proprietaire)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-building me-2"></i>Informations Propriétaire
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($user->proprietaire->adresse)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Adresse</label>
                                        <p class="mb-0">{{ $user->proprietaire->adresse }}</p>
                                    </div>
                                @endif

                                @if($user->proprietaire->profession)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Profession</label>
                                        <p class="mb-0">{{ $user->proprietaire->profession }}</p>
                                    </div>
                                @endif

                                @if($user->proprietaire->mobile_money_number)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Mobile Money</label>
                                        <p class="mb-0">
                                            <i class="fa-solid fa-mobile-alt text-success me-2"></i>
                                            {{ $user->proprietaire->mobile_money_number }}
                                        </p>
                                    </div>
                                @endif

                                @if($user->proprietaire->bank_account_info)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Compte Bancaire</label>
                                        <p class="mb-0">
                                            <i class="fa-solid fa-university text-primary me-2"></i>
                                            {{ $user->proprietaire->bank_account_info['bank_name'] ?? '' }} - 
                                            {{ $user->proprietaire->bank_account_info['account_number'] ?? '' }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Biens du propriétaire -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-home me-2"></i>Biens Immobiliers
                                <span class="badge badge-primary ms-2">{{ $user->proprietaire->biens->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($user->proprietaire->biens->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Titre</th>
                                                <th>Type</th>
                                                <th>Chambres</th>
                                                <th>Localisation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->proprietaire->biens as $bien)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('biens.detail', $bien->id) }}">
                                                            {{ $bien->titre }}
                                                        </a>
                                                    </td>
                                                    <td>{{ ucfirst($bien->type_bien) }}</td>
                                                    <td>{{ $bien->chambres->count() }}</td>
                                                    <td>{{ $bien->ville }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-3">Aucun bien enregistré</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Informations spécifiques Locataire -->
                @if($user->isLocataire() && $user->locataire)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-user-check me-2"></i>Informations Locataire
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($user->locataire->adresse_actuelle)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Adresse Actuelle</label>
                                        <p class="mb-0">{{ $user->locataire->adresse_actuelle }}</p>
                                    </div>
                                @endif

                                @if($user->locataire->profession)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Profession</label>
                                        <p class="mb-0">{{ $user->locataire->profession }}</p>
                                    </div>
                                @endif

                                @if($user->locataire->salaire_mensuel)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Salaire Mensuel</label>
                                        <p class="mb-0">{{ number_format($user->locataire->salaire_mensuel, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                @endif

                                @if($user->locataire->mobile_money_number)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Mobile Money</label>
                                        <p class="mb-0">
                                            <i class="fa-solid fa-mobile-alt text-success me-2"></i>
                                            {{ $user->locataire->mobile_money_number }}
                                        </p>
                                    </div>
                                @endif

                                @if($user->locataire->contact_urgence)
                                    <div class="col-12">
                                        <hr>
                                        <h6 class="mb-3">Contact d'Urgence</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="text-muted small">Nom</label>
                                                <p class="mb-0">{{ $user->locataire->contact_urgence['nom'] ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="text-muted small">Téléphone</label>
                                                <p class="mb-0">{{ $user->locataire->contact_urgence['phone'] ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="text-muted small">Relation</label>
                                                <p class="mb-0">{{ $user->locataire->contact_urgence['relation'] ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contrats du locataire -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-file-contract me-2"></i>Contrats
                                <span class="badge badge-primary ms-2">{{ $user->locataire->contrats->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($user->locataire->contrats->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>N° Contrat</th>
                                                <th>Bien / Chambre</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->locataire->contrats as $contrat)
                                                <tr>
                                                    <td>{{ $contrat->numero_contrat }}</td>
                                                    <td>
                                                        {{ $contrat->chambre->bien->titre }}<br>
                                                        <small class="text-muted">{{ $contrat->chambre->nom_chambre }}</small>
                                                    </td>
                                                    <td>{{ $contrat->date_etablissement }}</td>
                                                    <td>
                                                        @if($contrat->statut === 'actif')
                                                            <span class="badge badge-success">Actif</span>
                                                        @elseif($contrat->statut === 'expire')
                                                            <span class="badge badge-secondary">Expiré</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ ucfirst($contrat->statut) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-3">Aucun contrat</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Informations spécifiques Démarcheur -->
                @if($user->isDemarcheur() && $user->demarcheur)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-handshake me-2"></i>Informations Démarcheur
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($user->demarcheur->default_mobile_money_number)
                                <div class="mb-3">
                                    <label class="text-muted small">Mobile Money (Commissions)</label>
                                    <p class="mb-0">
                                        <i class="fa-solid fa-mobile-alt text-success me-2"></i>
                                        {{ $user->demarcheur->default_mobile_money_number }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <label class="text-muted small">Statut</label>
                                <p class="mb-0">
                                    @if($user->demarcheur->is_active)
                                        <span class="badge badge-success">Actif</span>
                                    @else
                                        <span class="badge badge-secondary">Inactif</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Historique -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-clock me-2"></i>Historique
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Créé le :</strong> {{ $user->created_at->format('d/m/Y à H:i') }}
                        </p>
                        @if($user->updated_at)
                            <p class="mb-0">
                                <strong>Dernière modification :</strong> {{ $user->updated_at->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>