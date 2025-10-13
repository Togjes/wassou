<!-- Page Header Start-->
<div class="page-header">
    <div class="header-wrapper row m-0">
        <form class="form-inline search-full col" action="#" method="get">
            <div class="form-group w-100">
                <div class="Typeahead Typeahead--twitterUsers">
                    <div class="u-posRelative">
                        <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text"
                            placeholder="Rechercher..." name="q" title="" autofocus>
                        <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Chargement...</span></div>
                        <i class="close-search" data-feather="x"></i>
                    </div>
                    <div class="Typeahead-menu"></div>
                </div>
            </div>
        </form>
        
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper">
                <a href="{{ route('dashboard') }}">
                    <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="Wassou">
                    <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="Wassou">
                </a>
            </div>
            <div class="toggle-sidebar">
                <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
            </div>
        </div>
        
        {{-- <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <div class="notification-slider">
                <div class="d-flex h-100">
                    <img src="{{ asset('assets/images/giftools.gif') }}" alt="gif">
                    <h6 class="mb-0 f-w-400">
                        <span class="font-primary">Bienvenue sur Wassou ! </span>
                        <span class="f-light">Votre plateforme de gestion immobilière</span>
                    </h6>
                </div>
            </div>
        </div> --}}
        
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <li class="fullscreen-body">
                    <span>
                        <svg id="maximize-screen">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#full-screen') }}"></use>
                        </svg>
                    </span>
                </li>
                
                <li>
                    <span class="header-search">
                        <svg>
                            <use href="{{ asset('assets/svg/icon-sprite.svg#search') }}"></use>
                        </svg>
                    </span>
                </li>
                
                <li>
                    <div class="mode">
                        <svg>
                            <use href="{{ asset('assets/svg/icon-sprite.svg#moon') }}"></use>
                        </svg>
                    </div>
                </li>
                
                <!-- Notifications Livewire -->
                <li class="onhover-dropdown">
                    @livewire('notifications.notification-badge')
                </li>
                
                <!-- Profil utilisateur -->
                <li class="profile-nav onhover-dropdown pe-0 py-0">
                    <div class="d-flex profile-media">
                        <img class="b-r-10" 
                             src="{{ auth()->user()->profile_image_url ?? asset('assets/images/dashboard/profile.png') }}" 
                             alt="{{ auth()->user()->full_name }}">
                        <div class="flex-grow-1">
                            <span>{{ auth()->user()->full_name }}</span>
                            <p class="mb-0">
                                {{ ucfirst(auth()->user()->user_type) }}
                                <i class="middle fa-solid fa-angle-down"></i>
                            </p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li>
                            <a href="{{ route('profil.index') }}">
                                <i data-feather="user"></i>
                                <span>Mon Profil</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('notifications.liste') }}">
                                <i data-feather="bell"></i>
                                <span>Notifications</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profil.parametres') }}">
                                <i data-feather="settings"></i>
                                <span>Paramètres</span>
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i data-feather="log-out"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Header Ends-->