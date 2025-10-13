<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Gestion des Utilisateurs</h3>
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
                        <li class="breadcrumb-item active">Utilisateurs</li>
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
                                       placeholder="Nom, email, téléphone...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label f-w-600">Type d'utilisateur</label>
                                <select wire:model.live="type_filter" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="proprietaire">Propriétaire</option>
                                    <option value="locataire">Locataire</option>
                                    <option value="demarcheur">Démarcheur</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label f-w-600">Statut</label>
                                <select wire:model.live="status_filter" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
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
                                <i class="fa-solid fa-users me-2"></i>Liste des Utilisateurs
                                <span class="badge badge-light-primary ms-2">{{ $users->total() }}</span>
                            </h5>
                            <a href="{{ route('utilisateurs.creer') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-2"></i>Nouvel Utilisateur
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Nom Complet</th>
                                        <th>Email / Téléphone</th>
                                        <th>Type</th>
                                        <th>Ville</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>
                                                @if($user->profile_image_url)
                                                    <img src="{{ Storage::url($user->profile_image_url) }}" 
                                                         alt="{{ $user->full_name }}"
                                                         class="rounded-circle"
                                                         style="width: 45px; height: 45px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                                         style="width: 45px; height: 45px;">
                                                        <i class="fa-solid fa-user text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $user->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Inscrit le {{ $user->created_at->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fa-solid fa-envelope text-primary me-1"></i>
                                                    <small>{{ $user->email }}</small>
                                                    @if($user->email_verified_at)
                                                        <i class="fa-solid fa-check-circle text-success ms-1" title="Email vérifié"></i>
                                                    @endif
                                                    <br>
                                                    @if($user->phone)
                                                        <i class="fa-solid fa-phone text-primary me-1"></i>
                                                        <small>{{ $user->phone }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($user->user_type === 'admin')
                                                    <span class="badge badge-danger">Administrateur</span>
                                                @elseif($user->user_type === 'proprietaire')
                                                    <span class="badge badge-primary">Propriétaire</span>
                                                @elseif($user->user_type === 'locataire')
                                                    <span class="badge badge-info">Locataire</span>
                                                @else
                                                    <span class="badge badge-warning">Démarcheur</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->ville }}</td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge badge-success">Actif</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('utilisateurs.detail', $user->id) }}" 
                                                       class="btn btn-outline-primary"
                                                       title="Voir détails">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('utilisateurs.modifier', $user->id) }}" 
                                                       class="btn btn-outline-secondary"
                                                       title="Modifier">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </a>
                                                    @if($user->id !== auth()->id())
                                                        <button type="button"
                                                                wire:click="confirmDeleteUser('{{ $user->id }}')"
                                                                class="btn btn-outline-danger"
                                                                title="Supprimer">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fa-solid fa-users fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun utilisateur trouvé</h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($users->hasPages())
                            <div class="mt-3">
                                {{ $users->links() }}
                            </div>
                        @endif
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
                        <h5 class="modal-title">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cancelDeleteUser"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>Attention !</strong> Cette action est irréversible.
                        </div>
                        <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                        <p class="text-muted">Toutes les données associées seront également supprimées.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDeleteUser">
                            Annuler
                        </button>
                        <button type="button" 
                                class="btn btn-danger" 
                                wire:click="deleteUser"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteUser">
                                <i class="fa-solid fa-trash me-2"></i>Supprimer
                            </span>
                            <span wire:loading wire:target="deleteUser">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i>Suppression...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>