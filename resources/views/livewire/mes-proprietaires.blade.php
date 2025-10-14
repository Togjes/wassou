<div>
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Mes Propriétaires</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Gestion</li>
                        <li class="breadcrumb-item active">Propriétaires</li>
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
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5>Liste des propriétaires qui m'ont autorisé</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           wire:model.live.debounce.300ms="search" 
                                           class="form-control" 
                                           placeholder="Rechercher un propriétaire...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($proprietaires->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Propriétaire</th>
                                            <th>Code</th>
                                            <th>Contact</th>
                                            <th>Statut</th>
                                            <th>Biens</th>
                                            <th>Permissions</th>
                                            <th>Date d'autorisation</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($proprietaires as $proprietaire)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            @if($proprietaire->user->profile_image_url)
                                                                <img src="{{ Storage::url($proprietaire->user->profile_image_url) }}" 
                                                                     alt="{{ $proprietaire->user->name }}"
                                                                     class="rounded-circle"
                                                                     width="40"
                                                                     height="40"
                                                                     style="object-fit: cover;">
                                                            @else
                                                                <div class="avatar-initial rounded-circle bg-success d-flex align-items-center justify-content-center text-white" 
                                                                     style="width: 40px; height: 40px; font-weight: bold;">
                                                                    {{ substr($proprietaire->user->first_name, 0, 1) }}{{ substr($proprietaire->user->last_name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <strong>{{ $proprietaire->user->name }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $proprietaire->user->code_unique }}</span>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <i class="fa-solid fa-phone me-1"></i>{{ $proprietaire->user->phone }}<br>
                                                        <i class="fa-solid fa-envelope me-1"></i>{{ $proprietaire->user->email }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @switch($proprietaire->pivot->statut)
                                                        @case('actif')
                                                            <span class="badge badge-success">
                                                                <i class="fa-solid fa-check-circle me-1"></i>Actif
                                                            </span>
                                                            @break
                                                        @case('suspendu')
                                                            <span class="badge badge-warning">
                                                                <i class="fa-solid fa-pause-circle me-1"></i>Suspendu
                                                            </span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        {{ $proprietaire->biensImmobiliers->count() }} bien(s)
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(empty($proprietaire->pivot->permissions))
                                                        <span class="badge badge-primary">
                                                            <i class="fa-solid fa-unlock me-1"></i>Toutes
                                                        </span>
                                                    @else
                                                        <div>
                                                            @foreach($proprietaire->pivot->permissions as $permission)
                                                                <span class="badge badge-secondary badge-sm mb-1">
                                                                    {{ str_replace('_', ' ', ucfirst($permission)) }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $proprietaire->pivot->date_validation ? $proprietaire->pivot->date_validation->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    @if($proprietaire->pivot->statut === 'actif')
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('biens.liste') }}?proprietaire={{ $proprietaire->id }}" 
                                                               class="btn btn-sm btn-outline-primary"
                                                               title="Voir les biens">
                                                                <i class="fa-solid fa-building"></i>
                                                            </a>
                                                            
                                                            @if(empty($proprietaire->pivot->permissions) || in_array('creer_bien', $proprietaire->pivot->permissions))
                                                                <a href="{{ route('biens.creer') }}?code={{ $proprietaire->user->code_unique }}" 
                                                                   class="btn btn-sm btn-outline-success"
                                                                   title="Créer un bien">
                                                                    <i class="fa-solid fa-plus"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Accès suspendu</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $proprietaires->links() }}
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                @if($search)
                                    Aucun propriétaire trouvé correspondant à votre recherche.
                                @else
                                    <div>
                                        <h5>Aucun propriétaire ne vous a encore autorisé</h5>
                                        <p class="mb-3">Pour commencer à gérer des biens, vous devez être ajouté par un propriétaire.</p>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="mb-3">Comment être autorisé ?</h6>
                                                <ol class="text-start">
                                                    <li>Communiquez votre <strong>code unique</strong> au propriétaire : 
                                                        <span class="badge badge-success">{{ auth()->user()->code_unique }}</span>
                                                    </li>
                                                    <li>Demandez-lui de vous ajouter depuis son espace <strong>"Mes Démarcheurs"</strong></li>
                                                    <li>Une fois ajouté, vous recevrez une notification</li>
                                                    <li>Vous pourrez alors créer et gérer ses biens selon les permissions accordées</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>