<!DOCTYPE html>
<html lang="fr">
<head>
    @include('layouts.head')
    @include('layouts.css')
    @livewireStyles
    @stack('styles')
</head>
<body>
    <!-- loader starts-->
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
    <!-- loader ends-->

    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">

        <!-- Page header start -->
        @include('layouts.header')
        <!-- Page header end-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page sidebar start-->
            @include('layouts.sidebar')
            <!-- Page sidebar end-->

            <div class="page-body">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>

            @include('layouts.footer')
        </div>
    </div>

    @include('layouts.scripts')
    @livewireScripts
    @stack('scripts')
</body>
</html>