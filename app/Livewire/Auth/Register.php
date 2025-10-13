<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.guest')]
#[Title('Inscription - Wassou')]
class Register extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $user_type = 'locataire';
    public $ville = '';
    public $pays = 'Bénin';
    public $accept_terms = false;

    // Champs additionnels pour les propriétaires
    public $profession = '';
    public $mobile_money_number = '';

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
            ],
            'user_type' => 'required|in:proprietaire,locataire,demarcheur',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'accept_terms' => 'accepted',
            'profession' => 'nullable|string|max:100',
            'mobile_money_number' => 'nullable|string|max:20',
        ];
    }

    protected $messages = [
        'first_name.required' => 'Le prénom est requis',
        'last_name.required' => 'Le nom est requis',
        'email.required' => 'L\'email est requis',
        'email.email' => 'L\'email doit être valide',
        'email.unique' => 'Cet email est déjà utilisé',
        'phone.unique' => 'Ce numéro de téléphone est déjà utilisé',
        'password.required' => 'Le mot de passe est requis',
        'password.confirmed' => 'Les mots de passe ne correspondent pas',
        'user_type.required' => 'Le type d\'utilisateur est requis',
        'ville.required' => 'La ville est requise',
        'pays.required' => 'Le pays est requis',
        'accept_terms.accepted' => 'Vous devez accepter les conditions d\'utilisation',
    ];

    public function register()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Créer l'utilisateur (le code unique sera généré automatiquement via boot())
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password_hash' => $this->password,
                'user_type' => $this->user_type,
                'ville' => $this->ville,
                'pays' => $this->pays,
                'is_active' => true,
                'is_verified' => false,
            ]);

            // Le code unique est automatiquement généré ici par le boot() du modèle

            // Créer le profil spécifique selon le type
            $profileData = [];

            if ($this->user_type === 'proprietaire') {
                $profileData = [
                    'profession' => $this->profession,
                    'mobile_money_number' => $this->mobile_money_number,
                ];
            }

            if ($this->user_type === 'locataire') {
                $profileData = [
                    'profession' => $this->profession,
                    'mobile_money_number' => $this->mobile_money_number,
                ];
            }

            if ($this->user_type === 'demarcheur') {
                $profileData = [
                    'default_mobile_money_number' => $this->mobile_money_number,
                ];
            }

            $user->creerProfilSpecifique($profileData);

            // Créer une notification de bienvenue avec le code unique
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Bienvenue sur Wassou !',
                'message' => "Votre compte a été créé avec succès. Votre code unique est : {$user->code_unique}. Conservez-le précieusement.",
                'type' => 'systeme',
            ]);

            // Envoyer l'email de bienvenue avec le code unique
            // try {
            //     \Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
            // } catch (\Exception $e) {
            //     \Log::error('Erreur envoi email de bienvenue: ' . $e->getMessage());
            //     // Ne pas bloquer l'inscription si l'email échoue
            // }

            DB::commit();

            // Connecter l'utilisateur
            Auth::login($user);

            // Flash le code unique pour l'afficher sur le dashboard
            session()->flash('new_user_code', $user->code_unique);
            session()->flash('registration_success', true);

            // Rediriger vers le dashboard approprié
            return $this->redirectBasedOnUserType($user);

        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.');
            
            \Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
        }
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
        return view('livewire.auth.register');
    }
}