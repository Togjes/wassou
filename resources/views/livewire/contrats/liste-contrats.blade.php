<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Gestion des Contrats</h3>
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
                        <li class="breadcrumb-item active">Contrats</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
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

                <!-- Filtres -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0 f-w-600">
                            <i class="fa-solid fa-filter me-2"></i>Filtres de recherche
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label f-w-600">Rechercher</label>
                                <input wire:model.live.debounce.300ms="search" 
                                       type="text" 
                                       class="form-control" 
                                       placeholder="N° contrat, locataire, bien...">
                            </div>

                            @if(auth()->user()->isProprietaire() && $biens->count() > 0)
                                <div class="col-md-3">
                                    <label class="form-label f-w-600">Bien</label>
                                    <select wire:model.live="bien_filter" class="form-select">
                                        <option value="">Tous les biens</option>
                                        @foreach($biens as $bien)
                                            <option value="{{ $bien->id }}">{{ $bien->titre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-3">
                                <label class="form-label f-w-600">Statut</label>
                                <select wire:model.live="statut_filter" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="brouillon">Brouillon</option>
                                    <option value="en_attente">En Attente</option>
                                    <option value="actif">Actif</option>
                                    <option value="expire">Expiré</option>
                                    <option value="resilie">Résilié</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button wire:click="resetFilters" class="btn btn-secondary w-100">
                                    <i class="fa-solid fa-refresh me-2"></i>Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-file-contract me-2"></i>Liste des Contrats
                                <span class="badge badge-light-primary ms-2">{{ $contrats->total() }}</span>
                            </h5>
                            @if(!auth()->user()->isLocataire())
                                <a href="{{ route('contrats.creer') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus me-2"></i>Nouveau Contrat
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>N° Contrat</th>
                                        <th>Locataire</th>
                                        <th>Bien / Chambre</th>
                                        <th>Loyer</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contrats as $contrat)
                                        <tr>
                                            <td>
                                                <strong>{{ $contrat->numero_contrat }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $contrat->locataire->user->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fa-solid fa-phone me-1"></i>
                                                        {{ $contrat->locataire->user->phone }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $contrat->chambre->bien->titre }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $contrat->chambre->nom_chambre }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-primary">
                                                    {{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA
                                                </strong>
                                                <br>
                                                <small class="text-muted">Paiement le {{ $contrat->date_paiement_loyer }}</small>
                                            </td>
                                            <td>
                                                {{ $contrat->date_etablissement->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                @if($contrat->statut === 'brouillon')
                                                    <span class="badge badge-secondary">Brouillon</span>
                                                @elseif($contrat->statut === 'en_attente')
                                                    <span class="badge badge-warning">En Attente</span>
                                                @elseif($contrat->statut === 'actif')
                                                    <span class="badge badge-success">Actif</span>
                                                @elseif($contrat->statut === 'expire')
                                                    <span class="badge badge-info">Expiré</span>
                                                @else
                                                    <span class="badge badge-danger">Résilié</span>
                                                @endif

                                                @if($contrat->hasUnpaidPayments())
                                                    <br>
                                                    <small class="badge badge-danger mt-1">
                                                        <i class="fa-solid fa-exclamation-triangle"></i> Impayés
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('contrats.detail', $contrat->id) }}" 
                                                       class="btn btn-outline-primary"
                                                       title="Voir détails">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    
                                                    @if(!auth()->user()->isLocataire() && $contrat->statut === 'brouillon')
                                                        <a href="{{ route('contrats.modifier', $contrat->id) }}" 
                                                           class="btn btn-outline-secondary"
                                                           title="Modifier">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    <button type="button"
                                                            class="btn btn-outline-info"
                                                            title="Télécharger PDF">
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fa-solid fa-file-contract fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun contrat trouvé</h5>
                                                <p class="text-muted">
                                                    @if($search || $statut_filter || $bien_filter)
                                                        Aucun contrat ne correspond à vos critères.
                                                    @else
                                                        Commencez par créer votre premier contrat.
                                                    @endif
                                                </p>
                                                @if(!auth()->user()->isLocataire())
                                                    <a href="{{ route('contrats.creer') }}" class="btn btn-primary mt-3">
                                                        <i class="fa-solid fa-plus me-2"></i>Créer un Contrat
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($contrats->hasPages())
                            <div class="mt-3">
                                {{ $contrats->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $contrats->where('statut', 'actif')->count() }}</h3>
                                <p class="mb-0">Contrats Actifs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $contrats->where('statut', 'en_attente')->count() }}</h3>
                                <p class="mb-0">En Attente de Signature</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $contrats->where('statut', 'expire')->count() }}</h3>
                                <p class="mb-0">Contrats Expirés</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $contrats->filter(fn($c) => $c->hasUnpaidPayments())->count() }}</h3>
                                <p class="mb-0">Avec Impayés</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>