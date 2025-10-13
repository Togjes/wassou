<!-- Page Sidebar Start-->
<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
    <div>
        <div class="logo-wrapper">
            <a href="<?php echo e(route('dashboard')); ?>">
                <img class="img-fluid for-light" src="<?php echo e(asset('assets/images/logo/logo.png')); ?>" alt="Wassou">
                <img class="img-fluid for-dark" src="<?php echo e(asset('assets/images/logo/logo_dark.png')); ?>" alt="Wassou">
            </a>
            <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"></i></div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="<?php echo e(route('dashboard')); ?>">
                <img class="img-fluid" src="<?php echo e(asset('assets/images/logo/logo-icon.png')); ?>" alt="Wassou">
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
                        <a class="sidebar-link sidebar-title link-nav <?php echo e(request()->routeIs('dashboard*') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('dashboard')); ?>">
                            <svg class="stroke-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-home')); ?>"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#fill-home')); ?>"></use>
                            </svg>
                            <span>Tableau de bord</span>
                        </a>
                    </li>

                    <!-- Gestion -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Gestion</h6></div>
                    </li>

                    <!-- Biens Immobiliers (Propriétaire & Admin) -->
                    <?php if(auth()->user()->isProprietaire() || auth()->user()->isAdmin()): ?>
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title <?php echo e(request()->routeIs('biens*') ? 'active' : ''); ?>" href="#">
                            <svg class="stroke-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-home')); ?>"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#fill-home')); ?>"></use>
                            </svg>
                            <span>Biens Immobiliers</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="<?php echo e(route('biens.liste')); ?>" class="<?php echo e(request()->routeIs('biens.liste') ? 'active' : ''); ?>">Liste des biens</a></li>
                            <li><a href="<?php echo e(route('biens.creer')); ?>" class="<?php echo e(request()->routeIs('biens.creer') ? 'active' : ''); ?>">Ajouter un bien</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Gestion des Utilisateurs (Admin uniquement) -->
                    <?php if(auth()->user()->isAdmin()): ?>
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title <?php echo e(request()->routeIs('utilisateurs*') ? 'active' : ''); ?>" href="#">
                            <svg class="stroke-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-user')); ?>"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#fill-user')); ?>"></use>
                            </svg>
                            <span>Utilisateurs</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="<?php echo e(route('utilisateurs.liste')); ?>" class="<?php echo e(request()->routeIs('utilisateurs.liste') ? 'active' : ''); ?>">Liste des utilisateurs</a></li>
                            <li><a href="<?php echo e(route('utilisateurs.creer')); ?>" class="<?php echo e(request()->routeIs('utilisateurs.creer') ? 'active' : ''); ?>">Créer un utilisateur</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Contrats (Tous sauf locataire ou locataire avec contrat) -->
                    <?php if(!auth()->user()->isLocataire() || (auth()->user()->locataire && auth()->user()->locataire->hasActiveContract())): ?>
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title <?php echo e(request()->routeIs('contrats*') ? 'active' : ''); ?>" href="#">
                            <svg class="stroke-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-file-text')); ?>"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#fill-file-text')); ?>"></use>
                            </svg>
                            <span>Contrats</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="<?php echo e(route('contrats.liste')); ?>" class="<?php echo e(request()->routeIs('contrats.liste') ? 'active' : ''); ?>">Liste des contrats</a></li>
                            <?php if(!auth()->user()->isLocataire()): ?>
                                <li><a href="<?php echo e(route('contrats.creer')); ?>" class="<?php echo e(request()->routeIs('contrats.creer') ? 'active' : ''); ?>">Créer un contrat</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Mon Compte -->
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Mon Compte</h6></div>
                    </li>

                    <!-- Mon Profil -->
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav <?php echo e(request()->routeIs('profil*') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('profil.index')); ?>">
                            <svg class="stroke-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#stroke-user')); ?>"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="<?php echo e(asset('assets/svg/icon-sprite.svg#fill-user')); ?>"></use>
                            </svg>
                            <span>Mon Profil</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
<!-- Page Sidebar Ends--><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>