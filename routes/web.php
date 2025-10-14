<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;

// Routes publiques (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
});

// Routes protégées (auth)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal (redirige selon le rôle)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match($user->user_type) {
            'admin' => redirect()->route('dashboard.admin'),
            'proprietaire' => redirect()->route('dashboard.proprietaire'),
            'locataire' => redirect()->route('dashboard.locataire'),
            'demarcheur' => redirect()->route('dashboard.demarcheur'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');
    
    // Dashboards spécifiques (sans middleware role temporairement)
    Route::get('/dashboard/admin', \App\Livewire\Dashboard\Admin\Index::class)
        ->name('dashboard.admin');
    
    Route::get('/dashboard/proprietaire', function() {
        return view('temp-dashboard', ['type' => 'Propriétaire']);
    })->name('dashboard.proprietaire');
    
    Route::get('/dashboard/locataire', function() {
        return view('temp-dashboard', ['type' => 'Locataire']);
    })->name('dashboard.locataire');
    
    Route::get('/dashboard/demarcheur', function() {
        return view('temp-dashboard', ['type' => 'Démarcheur']);
    })->name('dashboard.demarcheur');

    // Routes des biens immobiliers et chambres
    Route::prefix('biens')->name('biens.')->group(function () {
        Route::get('/', \App\Livewire\Biens\ListeBiens::class)->name('liste');
        Route::get('/creer', \App\Livewire\Biens\CreerBien::class)->name('creer');
        
        // Routes des chambres (AVANT les routes avec {id} pour éviter les conflits)
        Route::get('/{bienId}/chambres/creer', \App\Livewire\Chambres\CreerChambre::class)->name('chambres.creer');
        Route::get('/{bienId}/chambres/{chambreId}/modifier', \App\Livewire\Chambres\CreerChambre::class)->name('chambres.modifier');
        Route::get('/{bienId}/chambres/{chambreId}', \App\Livewire\Chambres\DetailChambre::class)->name('chambres.detail');
        
        // Routes des biens (à la fin pour éviter qu'elles capturent les routes chambres)
        Route::get('/{id}/modifier', \App\Livewire\Biens\CreerBien::class)->name('modifier');
        Route::get('/{id}', \App\Livewire\Biens\DetailBien::class)->name('detail');
    });



    // Routes Utilisateurs - ADMIN uniquement
    Route::prefix('utilisateurs')->name('utilisateurs.')->group(function () {
        Route::get('/', \App\Livewire\Utilisateurs\ListeUtilisateurs::class)->name('liste');
        Route::get('/creer', \App\Livewire\Utilisateurs\CreerUtilisateur::class)->name('creer');
        Route::get('/{id}/modifier', \App\Livewire\Utilisateurs\CreerUtilisateur::class)->name('modifier');
        Route::get('/{id}', \App\Livewire\Utilisateurs\DetailUtilisateur::class)->name('detail');
    });




    // Routes pour les propriétaires
Route::middleware(['auth', 'role:proprietaire'])->group(function () {
    Route::get('/proprietaire/demarcheurs', \App\Livewire\Proprietaire\GererDemarcheurs::class)
        ->name('proprietaire.demarcheurs');
});

// Routes pour les démarcheurs
Route::middleware(['auth', 'role:demarcheur'])->group(function () {
    Route::get('/demarcheur/proprietaires', \App\Livewire\Demarcheur\MesProprietaires::class)
        ->name('demarcheur.proprietaires');
});




    // Routes Contrats - Tous sauf Locataire basique
    // Route::prefix('contrats')->name('contrats.')->middleware('auth')->group(function () {
    //     Route::get('/', \App\Livewire\Contrats\ListeContrats::class)->name('liste');
    //     Route::get('/creer', \App\Livewire\Contrats\CreerContrat::class)->name('creer');
    //     Route::get('/{id}', \App\Livewire\Contrats\DetailContrat::class)->name('detail');
    //     Route::get('/{id}/modifier', \App\Livewire\Contrats\CreerContrat::class)->name('modifier');
    //     // Route::get('/{id}/etat-lieux/{type}', \App\Livewire\Contrats\EtatLieux::class)->name('etat-lieux');
    //     // Routes États des Lieux
    //     Route::get('/{id}/etat-lieux/{type}', \App\Livewire\EtatsLieux\CreerEtatLieux::class)->name('etat-lieux')
    //         ->where('type', 'entree|sortie');
    // });

    // Routes Contrats - Tous sauf Locataire basique
    Route::prefix('contrats')->name('contrats.')->middleware('auth')->group(function () {
        Route::get('/', \App\Livewire\Contrats\ListeContrats::class)->name('liste');
        Route::get('/creer', \App\Livewire\Contrats\CreerContrat::class)->name('creer');
        Route::get('/{id}', \App\Livewire\Contrats\DetailContrat::class)->name('detail');
        Route::get('/{id}/modifier', \App\Livewire\Contrats\CreerContrat::class)->name('modifier');
        
        // Routes États des Lieux
        Route::get('/{id}/etat-lieux/{type}', \App\Livewire\EtatsLieux\CreerEtatLieux::class)->name('etat-lieux')
            ->where('type', 'entree|sortie');
    });


    // MODULE UTILISATEURS - Réservé ADMIN uniquement
// Route::prefix('utilisateurs')->name('utilisateurs.')->middleware(['auth', 'admin'])->group(function () {
//     Route::get('/', \App\Livewire\Utilisateurs\ListeUtilisateurs::class)->name('liste');
//     Route::get('/creer', \App\Livewire\Utilisateurs\CreerUtilisateur::class)->name('creer');
//     Route::get('/{id}/modifier', \App\Livewire\Utilisateurs\CreerUtilisateur::class)->name('modifier');
//     Route::get('/{id}', \App\Livewire\Utilisateurs\DetailUtilisateur::class)->name('detail');
// });

// // MODULE LOCATAIRES - Propriétaire et Démarcheur (création uniquement)
// Route::prefix('locataires')->name('locataires.')->middleware('auth')->group(function () {
//     Route::get('/', \App\Livewire\Locataires\ListeLocataires::class)->name('liste');
//     Route::get('/creer', \App\Livewire\Locataires\CreerLocataire::class)->name('creer');
//     Route::get('/{id}', \App\Livewire\Locataires\DetailLocataire::class)->name('detail');
// });

// // MODULE PROPRIÉTAIRES - Démarcheur uniquement (création)
// Route::prefix('proprietaires')->name('proprietaires.')->middleware(['auth', 'demarcheur'])->group(function () {
//     Route::get('/', \App\Livewire\Proprietaires\ListeProprietaires::class)->name('liste');
//     Route::get('/creer', \App\Livewire\Proprietaires\CreerProprietaire::class)->name('creer');
//     Route::get('/{id}', \App\Livewire\Proprietaires\DetailProprietaire::class)->name('detail');
// });
    
    // Routes des biens immobiliers
    // Route::prefix('biens')->name('biens.')->group(function () {
    //     Route::get('/', \App\Livewire\Biens\ListeBiens::class)->name('liste');
    //     Route::get('/creer', \App\Livewire\Biens\CreerBien::class)->name('creer');
    //     Route::get('/{id}/modifier', \App\Livewire\Biens\CreerBien::class)->name('modifier');
    //     Route::get('/{id}', \App\Livewire\Biens\DetailBien::class)->name('detail');
    // });

    // Route::prefix('biens')->name('biens.')->group(function () {
    //     Route::get('/', \App\Livewire\Biens\ListeBiens::class)->name('liste');
    //     Route::get('/creer', \App\Livewire\Biens\CreerBien::class)->name('creer');
    //     Route::get('/{id}/modifier', \App\Livewire\Biens\CreerBien::class)->name('modifier');
    //     Route::get('/{id}', \App\Livewire\Biens\DetailBien::class)->name('detail');
        
    //     // Routes pour les chambres
    //     Route::get('/{bienId}/chambres/creer', \App\Livewire\Chambres\CreerChambre::class)->name('chambres.creer');
    //     Route::get('/{bienId}/chambres/{chambreId}', \App\Livewire\Chambres\DetailChambre::class)->name('chambres.detail');
    //     Route::get('/{bienId}/chambres/{chambreId}/modifier', \App\Livewire\Chambres\CreerChambre::class)->name('chambres.modifier');
    // });
    
    // Routes des notifications (temporaires)
    Route::get('/notifications', function() {
        return 'Page notifications en cours de développement';
    })->name('notifications.liste');
    
    // Routes du profil (temporaires)
    Route::get('/profil', function() {
        return 'Page profil en cours de développement';
    })->name('profil.index');
    
    Route::get('/profil/parametres', function() {
        return 'Page paramètres en cours de développement';
    })->name('profil.parametres');
    
    // Déconnexion
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

// Route par défaut
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});