<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.guest')]
#[Title('Connexion - Wassou')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'email.required' => 'L\'email est requis',
        'email.email' => 'L\'email doit être valide',
        'password.required' => 'Le mot de passe est requis',
        'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
    ];

    public function login()
    {
        $this->validate();

        // Protection contre les attaques par force brute
        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            session()->flash('error', "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes.");
            return;
        }

        // Tenter la connexion
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            RateLimiter::clear($throttleKey);
            
            request()->session()->regenerate();

            $user = Auth::user();

            // Vérifier si l'utilisateur est actif
            if (!$user->is_active) {
                Auth::logout();
                session()->flash('error', 'Votre compte est désactivé. Veuillez contacter l\'administrateur.');
                return;
            }

            // Créer une notification de connexion
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Nouvelle connexion',
                'message' => 'Connexion réussie depuis ' . request()->ip(),
                'type' => 'systeme',
            ]);

            // Rediriger selon le type d'utilisateur
            return $this->redirectBasedOnUserType($user);
        }

        // Incrémenter le compteur de tentatives
        RateLimiter::hit($throttleKey, 60);

        $this->addError('email', 'Les identifiants sont incorrects.');
    }

    protected function redirectBasedOnUserType($user)
    {
        return match($user->user_type) {
            'admin' => redirect()->route('dashboard.admin'),
            'proprietaire' => redirect()->route('dashboard.proprietaire'),
            'locataire' => redirect()->route('dashboard.locataire'),
            'demarcheur' => redirect()->route('dashboard.demarcheur'),
            default => redirect()->route('dashboard'),
        };
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}