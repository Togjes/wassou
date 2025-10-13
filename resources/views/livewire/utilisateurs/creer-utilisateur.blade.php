<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>{{ $isEdit ? 'Modifier' : 'Créer' }} un Utilisateur</h3>
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
                        <li class="breadcrumb-item active">{{ $isEdit ? 'Modifier' : 'Créer' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form wire:submit.prevent="saveUser">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                <i class="fa-solid fa-user me-2"></i>
                                Informations de Base
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Prénom -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" 
                                           wire:model="first_name" 
                                           class="form-control @error('first_name') is-invalid @enderror"
                                           placeholder="Prénom">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Nom -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" 
                                           wire:model="last_name" 
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           placeholder="Nom">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" 
                                           wire:model="email" 
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="exemple@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Téléphone -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" 
                                           wire:model="phone" 
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="+229 XX XX XX XX">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date de naissance -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date de Naissance</label>
                                    <input type="date" 
                                           wire:model="date_naissance" 
                                           class="form-control @error('date_naissance') is-invalid @enderror">
                                    @error('date_naissance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ville -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ville *</label>
                                    <input type="text" 
                                           wire:model="ville" 
                                           class="form-control @error('ville') is-invalid @enderror"
                                           placeholder="Cotonou">
                                    @error('ville')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Pays -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Pays *</label>
                                    <input type="text" 
                                           wire:model="pays" 
                                           class="form-control @error('pays') is-invalid @enderror"
                                           placeholder="Bénin">
                                    @error('pays')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Type d'utilisateur -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type d'Utilisateur *</label>
                                    <select wire:model.live="user_type" 
                                            class="form-select @error('user_type') is-invalid @enderror"
                                            @if($isEdit) disabled @endif>
                                        <option value="admin">Administrateur</option>
                                        <option value="proprietaire">Propriétaire</option>
                                        <option value="locataire">Locataire</option>
                                        <option value="demarcheur">Démarcheur</option>
                                    </select>
                                    @error('user_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($isEdit)
                                        <small class="text-muted">Le type d'utilisateur ne peut pas être modifié</small>
                                    @endif
                                </div>

                                <!-- Statut -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Statut</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               wire:model="is_active"
                                               id="is_active">
                                        <label class="form-check-label" for="is_active">
                                            Compte actif
                                        </label>
                                    </div>
                                </div>

                                <!-- Photo de profil -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Photo de Profil</label>
                                    
                                    @if($isEdit && $existing_profile_image)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($existing_profile_image) }}" 
                                                 alt="Photo actuelle"
                                                 class="rounded"
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                    @endif
                                    
                                    <input type="file" 
                                           wire:model="profile_image" 
                                           class="form-control @error('profile_image') is-invalid @enderror"
                                           accept="image/*">
                                    @error('profile_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($profile_image)
                                        <div class="mt-2">
                                            <div wire:loading wire:target="profile_image" class="text-info">
                                                <i class="fa-solid fa-spinner fa-spin"></i> Chargement...
                                            </div>
                                            <p class="text-success" wire:loading.remove wire:target="profile_image">
                                                Photo sélectionnée avec succès
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>
                                <i class="fa-solid fa-lock me-2"></i>
                                {{ $isEdit ? 'Changer le Mot de Passe (optionnel)' : 'Mot de Passe *' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mot de Passe {{ $isEdit ? '' : '*' }}</label>
                                    <input type="password" 
                                           wire:model="password" 
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Minimum 8 caractères">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmer le Mot de Passe {{ $isEdit ? '' : '*' }}</label>
                                    <input type="password" 
                                           wire:model="password_confirmation" 
                                           class="form-control"
                                           placeholder="Confirmer le mot de passe">
                                </div>

                                @if(!$isEdit)
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   wire:model="send_email"
                                                   id="send_email">
                                            <label class="form-check-label" for="send_email">
                                                Envoyer les identifiants par email à l'utilisateur
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informations spécifiques selon le type -->
                    @if($user_type === 'proprietaire')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>
                                    <i class="fa-solid fa-building me-2"></i>
                                    Informations Propriétaire
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Adresse</label>
                                        <textarea wire:model="adresse_proprietaire" 
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Adresse complète"></textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Profession</label>
                                        <input type="text" 
                                               wire:model="profession_proprietaire" 
                                               class="form-control"
                                               placeholder="Profession">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Mobile Money</label>
                                        <input type="text" 
                                               wire:model="mobile_money_proprietaire" 
                                               class="form-control"
                                               placeholder="+229 XX XX XX XX">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Numéro de Compte Bancaire</label>
                                        <input type="text" 
                                               wire:model="bank_account_number" 
                                               class="form-control"
                                               placeholder="Numéro de compte">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nom de la Banque</label>
                                        <input type="text" 
                                               wire:model="bank_name" 
                                               class="form-control"
                                               placeholder="Ex: BOA, ECOBANK">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($user_type === 'locataire')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>
                                    <i class="fa-solid fa-user-check me-2"></i>
                                    Informations Locataire
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Adresse Actuelle</label>
                                        <textarea wire:model="adresse_locataire" 
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Adresse actuelle"></textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Profession</label>
                                        <input type="text" 
                                               wire:model="profession_locataire" 
                                               class="form-control"
                                               placeholder="Profession">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Salaire Mensuel (FCFA)</label>
                                        <input type="number" 
                                               wire:model="salaire_mensuel" 
                                               class="form-control"
                                               placeholder="100000"
                                               min="0"
                                               step="1000">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile Money</label>
                                        <input type="text" 
                                               wire:model="mobile_money_locataire" 
                                               class="form-control"
                                               placeholder="+229 XX XX XX XX">
                                    </div>

                                    <div class="col-12">
                                        <h6 class="mt-3 mb-3">Contact d'Urgence</h6>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nom Complet</label>
                                        <input type="text" 
                                               wire:model="contact_urgence_nom" 
                                               class="form-control"
                                               placeholder="Nom du contact">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Téléphone</label>
                                        <input type="text" 
                                               wire:model="contact_urgence_phone" 
                                               class="form-control"
                                               placeholder="+229 XX XX XX XX">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Relation</label>
                                        <input type="text" 
                                               wire:model="contact_urgence_relation" 
                                               class="form-control"
                                               placeholder="Ex: Père, Frère, Ami">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($user_type === 'demarcheur')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>
                                    <i class="fa-solid fa-handshake me-2"></i>
                                    Informations Démarcheur
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile Money</label>
                                        <input type="text" 
                                               wire:model="mobile_money_demarcheur" 
                                               class="form-control"
                                               placeholder="+229 XX XX XX XX">
                                        <small class="text-muted">Numéro pour recevoir les commissions</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('utilisateurs.liste') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>Retour
                                </a>
                                <button type="submit" 
                                        class="btn btn-success" 
                                        wire:loading.attr="disabled"
                                        wire:target="saveUser, profile_image">
                                    <span wire:loading.remove wire:target="saveUser">
                                        <i class="fa-solid fa-check me-2"></i>{{ $isEdit ? 'Mettre à jour' : 'Créer l\'utilisateur' }}
                                    </span>
                                    <span wire:loading wire:target="saveUser">
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