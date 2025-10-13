<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Détails du Contrat</h3>
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
                        <li class="breadcrumb-item active">{{ $contrat->numero_contrat }}</li>
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
                <!-- Informations Générales -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-file-contract me-2"></i>
                            {{ $contrat->numero_contrat }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Statut</label>
                            <div>
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
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Date d'Établissement</label>
                            <p class="mb-0">
                                <i class="fa-solid fa-calendar me-2"></i>
                                {{ $contrat->date_etablissement->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Jour de Paiement</label>
                            <p class="mb-0">
                                <i class="fa-solid fa-calendar-day me-2"></i>
                                Le {{ $contrat->date_paiement_loyer }} de chaque mois
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Loyer Mensuel</label>
                            <h4 class="mb-0 text-primary">
                                {{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>

                        @if($contrat->hasUnpaidPayments())
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                <strong>Impayés :</strong> {{ number_format($contrat->total_unpaid, 0, ',', ' ') }} FCFA
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button wire:click="downloadPDF" class="btn btn-primary">
                                <i class="fa-solid fa-file-pdf me-2"></i>Télécharger PDF
                            </button>

                            @if(!auth()->user()->isLocataire())
                                @if($contrat->statut === 'en_attente')
                                    <button wire:click="activerContrat" class="btn btn-success">
                                        <i class="fa-solid fa-check me-2"></i>Activer le Contrat
                                    </button>
                                @endif

                                @if($contrat->statut === 'actif')
                                    <button wire:click="openTerminateModal" class="btn btn-danger">
                                        <i class="fa-solid fa-times me-2"></i>Résilier le Contrat
                                    </button>
                                @endif
                            @endif

                            @if(!$contrat->etatLieuxEntree)
                                <!-- Lien vers état des lieux d'entrée -->
                                <a href="{{ route('contrats.etat-lieux', [$contrat->id, 'entree']) }}" class="btn btn-info">
                                    <i class="fa-solid fa-clipboard-check me-2"></i>État des Lieux Entrée
                                </a>
                            @endif

                            @if($contrat->statut === 'actif' && !$contrat->etatLieuxSortie)
                                <!-- Lien vers état des lieux de sortie -->
                                <a href="{{ route('contrats.etat-lieux', [$contrat->id, 'sortie']) }}" class="btn btn-warning">
                                    <i class="fa-solid fa-clipboard-list me-2"></i>État des Lieux Sortie
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite -->
            <div class="col-lg-8">
                <!-- Parties du Contrat -->
                <div class="row">
                    <!-- Propriétaire -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fa-solid fa-user-tie me-2"></i>Propriétaire
                                </h6>
                            </div>
                            <div class="card-body">
                                <h5>{{ $contrat->proprietaire->user->full_name }}</h5>
                                <p class="mb-1">
                                    <i class="fa-solid fa-envelope me-2"></i>
                                    {{ $contrat->proprietaire->user->email }}
                                </p>
                                <p class="mb-1">
                                    <i class="fa-solid fa-phone me-2"></i>
                                    {{ $contrat->proprietaire->user->phone }}
                                </p>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Signature</span>
                                    @if($contrat->date_signature_proprietaire)
                                        <span class="badge badge-success">
                                            <i class="fa-solid fa-check me-1"></i>
                                            Signé le {{ $contrat->date_signature_proprietaire->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning">En attente</span>
                                    @endif
                                </div>
                                @if(!$contrat->date_signature_proprietaire && (auth()->user()->isAdmin() || auth()->id() === $contrat->proprietaire->user_id))
                                    <button wire:click="openSignatureModal('proprietaire')" class="btn btn-sm btn-primary w-100 mt-2">
                                        <i class="fa-solid fa-signature me-2"></i>Signer
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Locataire -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fa-solid fa-user me-2"></i>Locataire
                                </h6>
                            </div>
                            <div class="card-body">
                                <h5>{{ $contrat->locataire->user->full_name }}</h5>
                                <p class="mb-1">
                                    <i class="fa-solid fa-envelope me-2"></i>
                                    {{ $contrat->locataire->user->email }}
                                </p>
                                <p class="mb-1">
                                    <i class="fa-solid fa-phone me-2"></i>
                                    {{ $contrat->locataire->user->phone }}
                                </p>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Signature</span>
                                    @if($contrat->date_signature_locataire)
                                        <span class="badge badge-success">
                                            <i class="fa-solid fa-check me-1"></i>
                                            Signé le {{ $contrat->date_signature_locataire->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning">En attente</span>
                                    @endif
                                </div>
                                @if(!$contrat->date_signature_locataire && (auth()->user()->isAdmin() || auth()->id() === $contrat->locataire->user_id))
                                    <button wire:click="openSignatureModal('locataire')" class="btn btn-sm btn-primary w-100 mt-2">
                                        <i class="fa-solid fa-signature me-2"></i>Signer
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bien et Chambre -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-home me-2"></i>Bien et Chambre
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>{{ $contrat->chambre->bien->titre }}</h5>
                                <p class="text-muted mb-2">
                                    <i class="fa-solid fa-map-marker-alt me-2"></i>
                                    {{ $contrat->chambre->bien->quartier }}, {{ $contrat->chambre->bien->ville }}
                                </p>
                                <h6 class="text-primary">{{ $contrat->chambre->nom_chambre }}</h6>
                                <p class="mb-0">
                                    Type: {{ ucfirst(str_replace('_', ' ', $contrat->chambre->type_chambre)) }}
                                    @if($contrat->chambre->surface_m2)
                                        | {{ $contrat->chambre->surface_m2 }} m²
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('biens.chambres.detail', [$contrat->chambre->bien_id, $contrat->chambre_id]) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fa-solid fa-eye me-2"></i>Voir la chambre
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Démarcheur (si présent) -->
                @if($contrat->demarcheur)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-handshake me-2"></i>Démarcheur
                            </h6>
                        </div>
                        <div class="card-body">
                            <h5>{{ $contrat->demarcheur->user->full_name }}</h5>
                            <p class="mb-0">
                                <i class="fa-solid fa-phone me-2"></i>
                                {{ $contrat->demarcheur->user->phone }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Avances et Prépayés -->
                @if($contrat->avances->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-piggy-bank me-2"></i>Avances et Prépayés
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Initial</th>
                                            <th>Consommé</th>
                                            <th>Restant</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contrat->avances as $avance)
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', $avance->type_avance)) }}</td>
                                                <td>{{ number_format($avance->montant_initial, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ number_format($avance->montant_consomme, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ number_format($avance->montant_restant, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    @if($avance->statut === 'actif')
                                                        <span class="badge badge-success">Actif</span>
                                                    @elseif($avance->statut === 'consomme')
                                                        <span class="badge badge-secondary">Consommé</span>
                                                    @else
                                                        <span class="badge badge-warning">{{ ucfirst($avance->statut) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Historique des Paiements -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-money-bill me-2"></i>Historique des Paiements
                            <span class="badge badge-light-primary ms-2">{{ $contrat->paiements->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($contrat->paiements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>N° Facture</th>
                                            <th>Type</th>
                                            <th>Montant</th>
                                            <th>Échéance</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contrat->paiements->take(10) as $paiement)
                                            <tr>
                                                <td>{{ $paiement->numero_facture }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $paiement->type_paiement)) }}</td>
                                                <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ $paiement->date_echeance->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($paiement->statut === 'paye')
                                                        <span class="badge badge-success">Payé</span>
                                                    @elseif($paiement->statut === 'en_attente')
                                                        <span class="badge badge-warning">En attente</span>
                                                    @elseif($paiement->statut === 'en_retard')
                                                        <span class="badge badge-danger">En retard</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($paiement->statut) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Aucun paiement enregistré</p>
                        @endif
                    </div>
                </div>

                <!-- États des Lieux -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clipboard-check me-2"></i>États des Lieux
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="border p-3 rounded">
                                    <h6>État des Lieux d'Entrée</h6>
                                    @if($contrat->etatLieuxEntree)
                                        <p class="mb-1">
                                            <i class="fa-solid fa-calendar me-2"></i>
                                            {{ $contrat->etatLieuxEntree->date_etat->format('d/m/Y') }}
                                        </p>
                                        @if($contrat->etatLieuxEntree->isSigne())
                                            <span class="badge badge-success">Signé</span>
                                        @else
                                            <span class="badge badge-warning">En attente de signature</span>
                                        @endif
                                        <a href="" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fa-solid fa-eye me-2"></i>Voir
                                        </a>
                                    @else
                                        <p class="text-muted mb-2">Non effectué</p>
                                        <a href="" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-plus me-2"></i>Créer
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border p-3 rounded">
                                    <h6>État des Lieux de Sortie</h6>
                                    @if($contrat->etatLieuxSortie)
                                        <p class="mb-1">
                                            <i class="fa-solid fa-calendar me-2"></i>
                                            {{ $contrat->etatLieuxSortie->date_etat->format('d/m/Y') }}
                                        </p>
                                        @if($contrat->etatLieuxSortie->cout_reparations > 0)
                                            <p class="text-danger mb-1">
                                                Réparations: {{ number_format($contrat->etatLieuxSortie->cout_reparations, 0, ',', ' ') }} FCFA
                                            </p>
                                        @endif
                                        @if($contrat->etatLieuxSortie->isSigne())
                                            <span class="badge badge-success">Signé</span>
                                        @else
                                            <span class="badge badge-warning">En attente de signature</span>
                                        @endif
                                        <a href="{{ route('contrats.etat-lieux', [$contrat->id, 'sortie']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fa-solid fa-eye me-2"></i>Voir
                                        </a>
                                    @else
                                        <p class="text-muted mb-2">Non effectué</p>
                                        @if($contrat->statut === 'actif')
                                            <a href="{{ route('contrats.etat-lieux', [$contrat->id, 'sortie']) }}" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-plus me-2"></i>Créer
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <p>En signant ce contrat, vous acceptez tous les termes et conditions énoncés.</p>
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

    <!-- Modal Résiliation -->
    @if($showTerminateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Résilier le Contrat
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showTerminateModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Cette action est irréversible. La chambre redeviendra disponible.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motif de Résiliation *</label>
                            <textarea wire:model="motif_resiliation" 
                                      class="form-control @error('motif_resiliation') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Expliquez le motif de la résiliation..."></textarea>
                            @error('motif_resiliation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showTerminateModal', false)">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="resilierContrat">
                            <i class="fa-solid fa-times me-2"></i>Résilier le Contrat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>