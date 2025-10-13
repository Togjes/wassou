<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Wassou - Plateforme de gestion immobilière">
    <meta name="keywords" content="gestion immobilière, location, biens immobiliers">
    <meta name="author" content="Wassou">
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <title>{{ $title ?? 'Wassou - Gestion Immobilière' }}</title>
    
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    
    @include('layouts.css')
    @livewireStyles
</head>
<body>
    <div class="container-fluid p-0">
        {{ $slot }}
    </div>

    @include('layouts.scripts')
    @livewireScripts
</body>
</html>