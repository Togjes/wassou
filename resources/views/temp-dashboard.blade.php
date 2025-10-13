<!DOCTYPE html>
<html lang="fr">
<head>
    @include('layouts.head')
    @include('layouts.css')
</head>
<body>
    <div class="loader-wrapper">
        <div class="loader-index"><span></span></div>
        <svg>
            <defs></defs>
            <filter id="goo">
                <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
                <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"></fecolormatrix>
            </filter>
        </svg>
    </div>

    <div class="tap-top"><i data-feather="chevrons-up"></i></div>

    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('layouts.header')
        
        <div class="page-body-wrapper">
            @include('layouts.sidebar')
            
            <div class="page-body">
                <div class="container-fluid">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-6">
                                <h3>Dashboard {{ $type }}</h3>
                            </div>
                            <div class="col-6">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                        <svg class="stroke-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                        </svg></a>
                                    </li>
                                    <li class="breadcrumb-item active">Dashboard {{ $type }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <svg width="100" height="100" class="text-primary mb-4">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-widget') }}"></use>
                                    </svg>
                                    <h4>Dashboard {{ $type }}</h4>
                                    <p class="text-muted mb-4">Cette page est en cours de développement</p>
                                    
                                    @if($type === 'Propriétaire')
                                        <a href="{{ route('biens.creer') }}" class="btn btn-primary">
                                            <i data-feather="plus"></i>
                                            Ajouter un bien
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @include('layouts.footer')
        </div>
    </div>
    
    @include('layouts.scripts')
</body>
</html>