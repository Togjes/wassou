<div>
    @push('styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
        <!-- Ajout de Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --theme-primary: #667eea;
                --theme-secondary: #764ba2;
                --theme-success: #28a745;
                --theme-info: #17a2b8;
                --theme-warning: #ffc107;
                --theme-danger: #dc3545;
            }
            
            .property-header {
                background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
                color: white;
                padding: 2.5rem 2rem;
                border-radius: 15px;
                margin-bottom: 2rem;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                position: relative;
                overflow: hidden;
            }
            
            .property-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
                background-size: cover;
            }
            
            .property-header .row {
                position: relative;
                z-index: 1;
            }
            
            .stat-card {
                border-left: 4px solid var(--theme-primary);
                transition: all 0.3s ease;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                height: 100%;
                border: 1px solid rgba(0,0,0,0.05);
            }
            
            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            }
            
            .photo-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1rem;
            }
            
            .photo-item {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                height: 200px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                cursor: pointer;
            }
            
            .photo-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.4s ease;
            }
            
            .photo-item:hover img {
                transform: scale(1.08);
            }
            
            .chambre-card {
                border: 2px solid #f1f1f1;
                border-radius: 12px;
                transition: all 0.3s ease;
                height: 100%;
                box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                overflow: hidden;
                background: white;
            }
            
            .chambre-card:hover {
                border-color: var(--theme-primary);
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                transform: translateY(-3px);
            }
            
            .info-badge {
                background: #f8f9fa;
                padding: 0.75rem 1rem;
                border-radius: 10px;
                margin-bottom: 0.75rem;
                border-left: 3px solid var(--theme-primary);
                transition: all 0.3s ease;
            }
            
            .info-badge:hover {
                background: #e9ecef;
                transform: translateX(5px);
            }
            
            .section-title {
                position: relative;
                padding-bottom: 1rem;
                margin-bottom: 1.5rem;
                font-weight: 600;
                color: #2c3e50;
            }
            
            .section-title::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 60px;
                height: 4px;
                background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
                border-radius: 2px;
            }
            
            .badge {
                font-weight: 500;
                padding: 0.5rem 0.75rem;
                border-radius: 8px;
                font-size: 0.8rem;
            }
            
            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.08);
                margin-bottom: 1.5rem;
                transition: all 0.3s ease;
            }
            
            .card:hover {
                box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            }
            
            .card-header {
                background-color: white;
                border-bottom: 1px solid rgba(0,0,0,0.08);
                padding: 1.25rem 1.5rem;
                border-radius: 15px 15px 0 0 !important;
                font-weight: 600;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .btn {
                border-radius: 10px;
                font-weight: 500;
                padding: 0.6rem 1.5rem;
                transition: all 0.3s ease;
                border: none;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            
            .btn-light {
                background-color: rgba(255,255,255,0.9);
                border: 1px solid rgba(255,255,255,0.5);
                color: var(--theme-primary);
                font-weight: 600;
            }
            
            .btn-light:hover {
                background-color: white;
                color: var(--theme-primary);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(255,255,255,0.3);
            }
            
            .bg-light-primary {
                background-color: rgba(102, 126, 234, 0.1) !important;
            }
            
            .bg-light-success {
                background-color: rgba(40, 167, 69, 0.1) !important;
            }
            
            .bg-light-info {
                background-color: rgba(23, 162, 184, 0.1) !important;
            }
            
            .bg-light-warning {
                background-color: rgba(255, 193, 7, 0.1) !important;
            }
            
            .page-title h3 {
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 0.5rem;
            }
            
            .breadcrumb {
                background-color: transparent;
                padding: 0;
                margin-bottom: 0;
            }
            
            .breadcrumb-item a {
                color: var(--theme-primary);
                text-decoration: none;
                font-weight: 500;
            }
            
            .breadcrumb-item.active {
                color: #6c757d;
                font-weight: 500;
            }
            
            .modal-content {
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                border: none;
            }
            
            .modal-header {
                border-bottom: 1px solid rgba(0,0,0,0.1);
                padding: 1.5rem;
            }
            
            .modal-footer {
                border-top: 1px solid rgba(0,0,0,0.1);
                padding: 1.5rem;
            }
            
            /* Styles pour les badges de statut */
            .badge-success {
                background-color: var(--theme-success) !important;
            }
            
            .badge-primary {
                background-color: var(--theme-primary) !important;
            }
            
            .badge-warning {
                background-color: var(--theme-warning) !important;
            }
            
            .badge-info {
                background-color: var(--theme-info) !important;
            }
            
            .badge-light-secondary {
                background-color: #f8f9fa !important;
                color: #495057 !important;
                border: 1px solid #dee2e6;
            }
            
            .badge-light-info {
                background-color: rgba(23, 162, 184, 0.1) !important;
                color: var(--theme-info) !important;
                border: 1px solid rgba(23, 162, 184, 0.2);
            }
            
            @media (max-width: 768px) {
                .property-header {
                    padding: 1.5rem 1rem;
                    text-align: center;
                }
                
                .photo-grid {
                    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                }
                
                .stat-card {
                    margin-bottom: 1rem;
                }
            }
            
            /* Animation pour les cartes de chambres */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .chambre-card {
                animation: fadeInUp 0.5s ease forwards;
            }
            
            .chambre-card:nth-child(1) { animation-delay: 0.1s; }
            .chambre-card:nth-child(2) { animation-delay: 0.2s; }
            .chambre-card:nth-child(3) { animation-delay: 0.3s; }
            .chambre-card:nth-child(4) { animation-delay: 0.4s; }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Détails du Bien</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('biens.liste') }}">Biens</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $bien->titre }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- En-tête du bien -->
        <div class="property-header">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="text-white mb-3">{{ $bien->titre }}</h2>
                    <p class="mb-3 fs-5">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $bien->adresse ?? $bien->quartier }}, {{ $bien->ville }}
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-white text-dark">{{ ucfirst($bien->type_bien) }}</span>
                        @if($bien->statut === 'Location')
                            <span class="badge bg-success">En Location</span>
                        @elseif($bien->statut === 'Construction')
                            <span class="badge bg-warning">En Construction</span>
                        @else
                            <span class="badge bg-info">En Rénovation</span>
                        @endif
                        @if($bien->surface_totale_m2)
                            <span class="badge bg-white text-dark">{{ number_format($bien->surface_totale_m2, 0) }} m²</span>
                        @endif
                        <span class="badge bg-white text-dark">{{ $bien->chambres->count() }} Chambres</span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('biens.modifier', $bien->id) }}" class="btn btn-light btn-lg">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques Clés -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Revenu Potentiel</h6>
                                <h3 class="mb-0 text-primary">
                                    {{ number_format($bien->chambres->sum('loyer_mensuel'), 0, ',', ' ') }}
                                </h3>
                                <small class="text-muted">FCFA / mois</small>
                            </div>
                            <div class="bg-light-primary rounded p-3">
                                <i class="fas fa-wallet fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Revenu Actuel</h6>
                                <h3 class="mb-0 text-success">
                                    {{ number_format($bien->chambres->where('statut', 'loue')->sum('loyer_mensuel'), 0, ',', ' ') }}
                                </h3>
                                <small class="text-muted">FCFA / mois</small>
                            </div>
                            <div class="bg-light-success rounded p-3">
                                <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Taux d'Occupation</h6>
                                <h3 class="mb-0 text-info">{{ $bien->taux_occupation }}%</h3>
                                <small class="text-muted">{{ $bien->chambres_louees }}/{{ $bien->chambres->count() }} louées</small>
                            </div>
                            <div class="bg-light-info rounded p-3">
                                <i class="fas fa-chart-pie fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Disponibles</h6>
                                <h3 class="mb-0 text-warning">{{ $bien->chambres->where('statut', 'disponible')->count() }}</h3>
                                <small class="text-muted">Chambres libres</small>
                            </div>
                            <div class="bg-light-warning rounded p-3">
                                <i class="fas fa-bed fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Colonne Principale -->
            <div class="col-xl-8">
                <!-- Description -->
                @if($bien->description)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="section-title">Description</h5>
                            <p class="text-muted fs-6 lh-base">{{ $bien->description }}</p>
                        </div>
                    </div>
                @endif

                <!-- Galerie Photos -->
                @if(!empty($bien->photos_generales) && count($bien->photos_generales) > 0)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="section-title">Photos du Bien</h5>
                            <div class="photo-grid">
                                @foreach($bien->photos_generales as $photo)
                                    <div class="photo-item">
                                        <img src="{{ Storage::url($photo) }}" alt="Photo du bien">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Chambres -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-bed me-2"></i>Chambres ({{ $bien->chambres->count() }})
                            </h5>
                            <a href="{{ route('biens.chambres.creer', $bien->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Ajouter
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($bien->chambres->count() > 0)
                            <div class="row g-3">
                                @foreach($bien->chambres as $chambre)
                                    <div class="col-md-6">
                                        <div class="chambre-card p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="mb-1">{{ $chambre->nom_chambre }}</h6>
                                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $chambre->type_chambre)) }}</small>
                                                </div>
                                                @if($chambre->statut === 'disponible')
                                                    <span class="badge badge-success">Disponible</span>
                                                @elseif($chambre->statut === 'loue')
                                                    <span class="badge badge-primary">Louée</span>
                                                @elseif($chambre->statut === 'renovation')
                                                    <span class="badge badge-warning">Rénovation</span>
                                                @else
                                                    <span class="badge badge-info">Réservée</span>
                                                @endif
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <div class="info-badge">
                                                        <small class="text-muted d-block">Surface</small>
                                                        <strong>{{ $chambre->surface_m2 ? number_format($chambre->surface_m2, 0) . ' m²' : 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="info-badge">
                                                        <small class="text-muted d-block">Pièces</small>
                                                        <strong>{{ $chambre->nombre_pieces }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <div>
                                                    <small class="text-muted d-block">Loyer mensuel</small>
                                                    <h5 class="mb-0 text-primary">{{ number_format($chambre->loyer_mensuel, 0, ',', ' ') }} FCFA</h5>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('biens.chambres.detail', [$bien->id, $chambre->getKey()]) }}" 
                                                    class="btn btn-outline-primary"
                                                    title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('biens.chambres.modifier', [$bien->id, $chambre->getKey()]) }}" 
                                                    class="btn btn-outline-secondary"
                                                    title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                            wire:click="confirmDeleteChambre('{{ $chambre->getKey() }}')"
                                                            class="btn btn-outline-danger" 
                                                            title="Supprimer"
                                                            @if($chambre->statut === 'loue') disabled @endif>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-bed fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucune chambre</h5>
                                <p class="text-muted">Ajoutez votre première chambre à ce bien</p>
                                <a href="{{ route('biens.chambres.creer', $bien->id) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajouter une Chambre
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne Latérale -->
            <div class="col-xl-4">
                <!-- Caractéristiques -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="section-title">Caractéristiques</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Type de bien</span>
                                    <strong>{{ ucfirst($bien->type_bien) }}</strong>
                                </div>
                            </li>
                            @if($bien->surface_totale_m2)
                                <li class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Surface totale</span>
                                        <strong>{{ number_format($bien->surface_totale_m2, 0) }} m²</strong>
                                    </div>
                                </li>
                            @endif
                            @if($bien->annee_construction)
                                <li class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Année</span>
                                        <strong>{{ \Carbon\Carbon::parse($bien->annee_construction)->format('Y') }}</strong>
                                    </div>
                                </li>
                            @endif
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Chambres</span>
                                    <strong>{{ $bien->chambres->count() }}</strong>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Statut</span>
                                    <strong>{{ $bien->statut }}</strong>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Équipements -->
                @if(!empty($bien->equipements_communs))
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="section-title">Équipements</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($bien->equipements_communs as $equipement)
                                    <span class="badge badge-light-secondary">
                                        <i class="fas fa-check me-1"></i>{{ $equipement }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Moyens de Paiement -->
                @if(!empty($bien->moyens_paiement_acceptes))
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="section-title">Paiements Acceptés</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($bien->moyens_paiement_acceptes as $moyen)
                                    <span class="badge badge-light-info">
                                        <i class="fas fa-credit-card me-1"></i>{{ ucfirst($moyen) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Propriétaire -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="section-title">Propriétaire</h5>
                        <div class="d-flex align-items-center mb-3">
                            @if($bien->proprietaire->user->profile_image_url)
                                <img src="{{ Storage::url($bien->proprietaire->user->profile_image_url) }}" 
                                     alt="{{ $bien->proprietaire->user->full_name }}"
                                     class="rounded-circle me-3"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle me-3 d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user fa-lg text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $bien->proprietaire->user->full_name }}</h6>
                                <small class="text-muted">{{ $bien->proprietaire->profession ?? 'Propriétaire' }}</small>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <small>{{ $bien->proprietaire->user->email }}</small>
                            </li>
                            @if($bien->proprietaire->user->phone)
                                <li>
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <small>{{ $bien->proprietaire->user->phone }}</small>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de suppression -->
    @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cancelDeleteChambre"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cette chambre ?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Cette action est irréversible !
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDeleteChambre">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteChambre">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    @endpush
</div>