<!-- Page Sidebar Start-->
<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
    <div>
        <div class="logo-wrapper">
            <a href="{{ route('dashboard') }}">
                <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="Wassou">
                <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="Wassou">
            </a>
            <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"></i></div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{ route('dashboard') }}">
                <img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="Wassou">
            </a>
        </div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <div class="mobile-back text-end">
                            <span>Retour</span>
                            <i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i>
                        </div>
                    </li>
                    
                    <!-- Menu Principal -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Menu Principal</h6></div>
                    </li>

                    <!-- Dashboard -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('dashboard*') ? 'active' : '' }}" 
                           href="{{ route('dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>Tableau de bord</span>
                        </a>
                    </li>

                    <!-- ========== SECTION ADMIN ========== -->
                    @if(auth()->user()->isAdmin())
                    
                    <!-- Gestion -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Administration</h6></div>
                    </li>

                    <!-- Utilisateurs -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('utilisateurs*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                            </svg>
                            <span>Utilisateurs</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('utilisateurs.liste') }}" class="{{ request()->routeIs('utilisateurs.liste') ? 'active' : '' }}">
                                <i class="fa-solid fa-list me-2"></i>Liste des utilisateurs
                            </a></li>
                            <li><a href="{{ route('utilisateurs.creer') }}" class="{{ request()->routeIs('utilisateurs.creer') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-plus me-2"></i>Créer un utilisateur
                            </a></li>
                        </ul>
                    </li>

                    <!-- Biens Immobiliers -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('biens*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>Biens Immobiliers</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('biens.liste') }}" class="{{ request()->routeIs('biens.liste') ? 'active' : '' }}">
                                <i class="fa-solid fa-building me-2"></i>Tous les biens
                            </a></li>
                            <li><a href="{{ route('biens.creer') }}" class="{{ request()->routeIs('biens.creer') ? 'active' : '' }}">
                                <i class="fa-solid fa-plus-circle me-2"></i>Ajouter un bien
                            </a></li>
                        </ul>
                    </li>

                    <!-- Contrats -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('contrats*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file-text') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-file-text') }}"></use>
                            </svg>
                            <span>Contrats</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('contrats.liste') }}" class="{{ request()->routeIs('contrats.liste') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-contract me-2"></i>Tous les contrats
                            </a></li>
                            <li><a href="{{ route('contrats.creer') }}" class="{{ request()->routeIs('contrats.creer') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-signature me-2"></i>Créer un contrat
                            </a></li>
                        </ul>
                    </li>

                    @endif

                    <!-- ========== SECTION PROPRIÉTAIRE ========== -->
                    @if(auth()->user()->isProprietaire())
                    
                    <!-- Gestion -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Gestion</h6></div>
                    </li>

                    <!-- Mes Biens -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('biens*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>Mes Biens</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('biens.liste') }}" class="{{ request()->routeIs('biens.liste') ? 'active' : '' }}">
                                <i class="fa-solid fa-building me-2"></i>Mes biens immobiliers
                            </a></li>
                            <li><a href="{{ route('biens.creer') }}" class="{{ request()->routeIs('biens.creer') ? 'active' : '' }}">
                                <i class="fa-solid fa-plus-circle me-2"></i>Ajouter un bien
                            </a></li>
                        </ul>
                    </li>

                    <!-- Mes Contrats -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('contrats*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file-text') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-file-text') }}"></use>
                            </svg>
                            <span>Mes Contrats</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('contrats.liste') }}" class="{{ request()->routeIs('contrats.liste') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-contract me-2"></i>Mes contrats
                            </a></li>
                            <li><a href="{{ route('contrats.creer') }}" class="{{ request()->routeIs('contrats.creer') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-signature me-2"></i>Créer un contrat
                            </a></li>
                        </ul>
                    </li>

                    <!-- Mes Démarcheurs -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('proprietaire.demarcheurs') ? 'active' : '' }}" 
                           href="{{ route('proprietaire.demarcheurs') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                            </svg>
                            <span>Mes Démarcheurs</span>
                        </a>
                    </li>

                    @endif

                    <!-- ========== SECTION LOCATAIRE ========== -->
                    @if(auth()->user()->isLocataire())
                    
                    <!-- Mon Logement -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Mon Logement</h6></div>
                    </li>

                    @if(auth()->user()->locataire && auth()->user()->locataire->hasActiveContract())
                    
                    <!-- Mon Contrat -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('contrats*') ? 'active' : '' }}" 
                           href="{{ route('contrats.liste') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file-text') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-file-text') }}"></use>
                            </svg>
                            <span>Mon Contrat</span>
                        </a>
                    </li>

                    <!-- Mes Paiements -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('paiements*') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-credit-card') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-credit-card') }}"></use>
                            </svg>
                            <span>Mes Paiements</span>
                        </a>
                    </li>

                    <!-- Mes Réclamations -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('reclamations*') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-alert-circle') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-alert-circle') }}"></use>
                            </svg>
                            <span>Mes Réclamations</span>
                        </a>
                    </li>

                    @else
                    
                    <!-- Rechercher un logement -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('chambres.recherche') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-search') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-search') }}"></use>
                            </svg>
                            <span>Rechercher un logement</span>
                        </a>
                    </li>

                    @endif

                    @endif

                    <!-- ========== SECTION DÉMARCHEUR ========== -->
                    @if(auth()->user()->isDemarcheur())
                    
                    <!-- Gestion -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Gestion</h6></div>
                    </li>

                    <!-- Mes Propriétaires -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('demarcheur.proprietaires') ? 'active' : '' }}" 
                           href="{{ route('demarcheur.proprietaires') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                            </svg>
                            <span>Mes Propriétaires</span>
                            @php
                                $totalProprietaires = auth()->user()->demarcheur->proprietairesActifs()->count();
                            @endphp
                            @if($totalProprietaires > 0)
                                <span class="badge badge-primary rounded-pill">{{ $totalProprietaires }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Biens Gérés -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('biens*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>Biens Gérés</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('biens.liste') }}" class="{{ request()->routeIs('biens.liste') ? 'active' : '' }}">
                                <i class="fa-solid fa-building me-2"></i>Liste des biens
                            </a></li>
                            <li><a href="{{ route('biens.creer') }}" class="{{ request()->routeIs('biens.creer') ? 'active' : '' }}">
                                <i class="fa-solid fa-plus-circle me-2"></i>Ajouter un bien
                            </a></li>
                        </ul>
                    </li>

                    <!-- Contrats Gérés -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('contrats*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file-text') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-file-text') }}"></use>
                            </svg>
                            <span>Contrats Gérés</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('contrats.liste') }}" class="{{ request()->routeIs('contrats.liste') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-contract me-2"></i>Mes contrats
                            </a></li>
                            <li><a href="{{ route('contrats.creer') }}" class="{{ request()->routeIs('contrats.creer') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-signature me-2"></i>Créer un contrat
                            </a></li>
                        </ul>
                    </li>

                    <!-- Mes Commissions -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('demarcheur.commissions') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-dollar-sign') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-dollar-sign') }}"></use>
                            </svg>
                            <span>Mes Commissions</span>
                        </a>
                    </li>

                    @endif

                    <!-- ========== SECTION COMMUNE ========== -->
                    
                    <!-- Paiements (Sauf pour locataire sans contrat) -->
                    @if(!auth()->user()->isLocataire() || (auth()->user()->locataire && auth()->user()->locataire->hasActiveContract()))
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Finances</h6></div>
                    </li>

                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('paiements*') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-credit-card') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-credit-card') }}"></use>
                            </svg>
                            <span>
                                @if(auth()->user()->isProprietaire())
                                    Paiements reçus
                                @elseif(auth()->user()->isLocataire())
                                    Mes paiements
                                @else
                                    Paiements
                                @endif
                            </span>
                        </a>
                    </li>
                    @endif

                    <!-- Notifications -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Communication</h6></div>
                    </li>

                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('notifications*') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-bell') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-bell') }}"></use>
                            </svg>
                            <span>Notifications</span>
                            @php
                                $notificationsNonLues = auth()->user()->notifications_non_lues;
                            @endphp
                            @if($notificationsNonLues > 0)
                                <span class="badge badge-danger rounded-pill">{{ $notificationsNonLues }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Mon Compte -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Mon Compte</h6></div>
                    </li>

                    <!-- Mon Profil -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('profil*') ? 'active' : '' }}" 
                           href="{{ route('profil.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                            </svg>
                            <span>Mon Profil</span>
                        </a>
                    </li>

                    <!-- Paramètres -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('parametres*') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-settings') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-settings') }}"></use>
                            </svg>
                            <span>Paramètres</span>
                        </a>
                    </li>

                    <!-- Aide & Support -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('aide*') ? 'active' : '' }}" 
                           href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-help-circle') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-help-circle') }}"></use>
                            </svg>
                            <span>Aide & Support</span>
                        </a>
                    </li>

                    <!-- Déconnexion -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav" 
                           href="#"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-log-out') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-log-out') }}"></use>
                            </svg>
                            <span>Déconnexion</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>

                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
<!-- Page Sidebar Ends-->