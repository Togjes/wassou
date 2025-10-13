<div class="container-fluid">
    <div class="row">
        <div class="col-xl-5 p-0">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/2.jpg') }}" alt="registerpage">
        </div>
        <div class="col-xl-7 p-0">
            <div class="login-card login-dark">
                <div>
                    <div>
                        <a class="logo text-start" href="{{ route('login') }}">
                            <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="Wassou">
                            <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="Wassou">
                        </a>
                    </div>
                    <div class="login-main">
                        <form wire:submit.prevent="register" class="theme-form">
                            <h4>Créer un compte</h4>
                            <p>Inscrivez-vous pour commencer à utiliser Wassou</p>

                            <!-- Info Code Unique -->
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i data-feather="info"></i>
                                <strong>Code Unique :</strong> Un code unique vous sera automatiquement attribué lors de votre inscription pour faciliter votre identification.
                                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                            </div>

                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i data-feather="alert-circle"></i>
                                    <p>{{ session('error') }}</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <!-- Type d'utilisateur -->
                            <div class="form-group">
                                <label class="col-form-label">Je suis :</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <input type="radio" wire:model.live="user_type" value="locataire" id="type_locataire" class="radio_animated" checked>
                                        <label class="form-check-label" for="type_locataire">
                                            <div class="text-center p-3 border rounded {{ $user_type === 'locataire' ? 'border-primary bg-light-primary' : '' }}">
                                                <i data-feather="user" class="mb-2"></i>
                                                <div>Locataire</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" wire:model.live="user_type" value="proprietaire" id="type_proprietaire" class="radio_animated">
                                        <label class="form-check-label" for="type_proprietaire">
                                            <div class="text-center p-3 border rounded {{ $user_type === 'proprietaire' ? 'border-primary bg-light-primary' : '' }}">
                                                <i data-feather="home" class="mb-2"></i>
                                                <div>Propriétaire</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" wire:model.live="user_type" value="demarcheur" id="type_demarcheur" class="radio_animated">
                                        <label class="form-check-label" for="type_demarcheur">
                                            <div class="text-center p-3 border rounded {{ $user_type === 'demarcheur' ? 'border-primary bg-light-primary' : '' }}">
                                                <i data-feather="briefcase" class="mb-2"></i>
                                                <div>Démarcheur</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @error('user_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Prénom -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Prénom</label>
                                        <input wire:model="first_name" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               type="text" 
                                               placeholder="Jean">
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nom -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Nom</label>
                                        <input wire:model="last_name" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               type="text" 
                                               placeholder="Dupont">
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Email</label>
                                        <input wire:model="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               type="email" 
                                               placeholder="jean.dupont@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Téléphone -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Téléphone (optionnel)</label>
                                        <input wire:model="phone" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               type="tel" 
                                               placeholder="+229 XX XX XX XX">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Ville -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Ville</label>
                                        <input wire:model="ville" 
                                               class="form-control @error('ville') is-invalid @enderror" 
                                               type="text" 
                                               placeholder="Cotonou">
                                        @error('ville')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Pays -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Pays</label>
                                        <select wire:model="pays" 
                                                class="form-control @error('pays') is-invalid @enderror">
                                            <option value="Bénin">Bénin</option>
                                            <option value="Togo">Togo</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Burkina Faso">Burkina Faso</option>
                                            <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                        </select>
                                        @error('pays')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @if($user_type === 'proprietaire' || $user_type === 'locataire')
                            <div class="row">
                                <!-- Profession -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Profession (optionnel)</label>
                                        <input wire:model="profession" 
                                               class="form-control" 
                                               type="text" 
                                               placeholder="Votre profession">
                                    </div>
                                </div>

                                <!-- Mobile Money -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">N° Mobile Money (optionnel)</label>
                                        <input wire:model="mobile_money_number" 
                                               class="form-control" 
                                               type="tel" 
                                               placeholder="+229 XX XX XX XX">
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <!-- Mot de passe -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Mot de passe</label>
                                        <input wire:model="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               type="password" 
                                               placeholder="••••••••">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Min. 8 caractères avec lettres, chiffres et symboles</small>
                                    </div>
                                </div>

                                <!-- Confirmation -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Confirmer le mot de passe</label>
                                        <input wire:model="password_confirmation" 
                                               class="form-control" 
                                               type="password" 
                                               placeholder="••••••••">
                                    </div>
                                </div>
                            </div>

                            <!-- Accepter les conditions -->
                            <div class="form-group">
                                <div class="checkbox p-0">
                                    <input wire:model="accept_terms" 
                                           id="accept_terms" 
                                           type="checkbox" 
                                           class="@error('accept_terms') is-invalid @enderror">
                                    <label class="text-muted" for="accept_terms">
                                        J'accepte les 
                                        <a href="#" class="ms-1">conditions d'utilisation</a> et la 
                                        <a href="#">politique de confidentialité</a>
                                    </label>
                                </div>
                                @error('accept_terms')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block w-100" 
                                        type="submit" 
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove>Créer mon compte</span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Création en cours...
                                    </span>
                                </button>
                            </div>

                            <p class="mt-4 mb-0 text-center">
                                Vous avez déjà un compte ? 
                                <a class="ms-2" href="{{ route('login') }}">Se connecter</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>