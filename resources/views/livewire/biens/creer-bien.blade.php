<div>
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>{{ $isEdit ? 'Modifier' : 'Ajouter' }} un Bien Immobilier</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Biens</li>
                        <li class="breadcrumb-item active">{{ $isEdit ? 'Modifier' : 'Ajouter' }}</li>
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
                        <h5>Formulaire de Bien Immobilier</h5>
                    </div>
                    <div class="card-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i data-feather="check-circle"></i>
                                {{ session('success') }}
                                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i data-feather="alert-circle"></i>
                                {{ session('error') }}
                                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Section recherche propriétaire (Admin et Démarcheur uniquement) -->
                        @if((Auth::user()->isAdmin() || Auth::user()->isDemarcheur()) && !$isEdit)
                            <div class="card mb-4 {{ $proprietaire_trouve ? 'border-success' : 'border-warning' }}">
                                <div class="card-header {{ $proprietaire_trouve ? 'bg-light-success' : 'bg-light-warning' }}">
                                    <h6 class="mb-0">
                                        <i class="fa-solid fa-user-tie me-2"></i>
                                        Sélection du Propriétaire
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if(!$proprietaire_trouve)
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label">Code Unique du Propriétaire <span class="txt-danger">*</span></label>
                                                <input type="text" 
                                                       wire:model="code_proprietaire" 
                                                       class="form-control @error('code_proprietaire') is-invalid @enderror"
                                                       placeholder="Ex: PROP-XXXXX">
                                                @error('code_proprietaire')
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="button" 
                                                        wire:click="chercherProprietaire" 
                                                        class="btn btn-primary w-100"
                                                        wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="chercherProprietaire">
                                                        <i class="fa-solid fa-search me-2"></i>Chercher
                                                    </span>
                                                    <span wire:loading wire:target="chercherProprietaire">
                                                        <i class="fa-solid fa-spinner fa-spin me-2"></i>Recherche...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>

                                        @if(session()->has('error_search'))
                                            <div class="alert alert-danger mt-3 mb-0">
                                                <i class="fa-solid fa-exclamation-circle me-2"></i>
                                                {{ session('error_search') }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h6 class="mb-1">{{ $proprietaire_selectionne->user->name }}</h6>
                                                <p class="mb-0 text-muted">
                                                    <i class="fa-solid fa-phone me-2"></i>{{ $proprietaire_selectionne->telephone }}
                                                </p>
                                                <p class="mb-0 text-muted">
                                                    <i class="fa-solid fa-envelope me-2"></i>{{ $proprietaire_selectionne->user->email }}
                                                </p>
                                                <p class="mb-0">
                                                    <span class="badge badge-success">Code: {{ $proprietaire_selectionne->user->code_unique }}</span>
                                                </p>
                                            </div>
                                            <button type="button" 
                                                    wire:click="resetProprietaire" 
                                                    class="btn btn-outline-danger btn-sm">
                                                <i class="fa-solid fa-times me-2"></i>Changer
                                            </button>
                                        </div>

                                        @if(session()->has('success_search'))
                                            <div class="alert alert-success mt-3 mb-0">
                                                <i class="fa-solid fa-check-circle me-2"></i>
                                                {{ session('success_search') }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Afficher le propriétaire connecté (pour les propriétaires) -->
                        @if(Auth::user()->isProprietaire() && !$isEdit)
                            <div class="alert alert-info mb-4">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                <strong>Création pour :</strong> {{ Auth::user()->name }}
                            </div>
                        @endif

                        <div class="row g-xl-5 g-3">
                            <!-- Sidebar des étapes -->
                            <div class="col-xxl-3 col-xl-4 box-col-4e sidebar-left-wrapper">
                                <ul class="sidebar-left-icons nav nav-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $currentStep === 1 ? 'active' : '' }}" href="#">
                                            <div class="nav-rounded">
                                                <div class="product-icons">
                                                    <svg class="stroke-icon">
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#product-detail') }}"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="product-tab-content">
                                                <h6>1. Informations du Bien</h6>
                                                <p>Type, localisation et équipements</p>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ $currentStep === 2 ? 'active' : '' }}" href="#">
                                            <div class="nav-rounded">
                                                <div class="product-icons">
                                                    <svg class="stroke-icon">
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#product-gallery') }}"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="product-tab-content">
                                                <h6>2. Photos & Paiement</h6>
                                                <p>Images et modes de paiement</p>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Contenu des étapes -->
                            <div class="col-xxl-9 col-xl-8 box-col-8 position-relative">
                                @if($currentStep === 1)
                                    @include('livewire.biens.steps.step1')
                                @endif

                                @if($currentStep === 2)
                                    @include('livewire.biens.steps.step2')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>