<?php

namespace App\Livewire\Utilisateurs;

use App\Models\User;
use App\Models\Proprietaire;
use App\Models\Locataire;
use App\Models\Demarcheur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompteCreeMail;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreerUtilisateur extends Component
{
    use WithFileUploads;

    public $userId = null;
    public $isEdit = false;

    // Informations de base
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $date_naissance = '';
    public $ville = '';
    public $pays = 'Bénin';
    public $user_type = 'locataire';
    public $profile_image = null;
    public $existing_profile_image = null;
    public $password = '';
    public $password_confirmation = '';
    public $is_active = true;
    public $send_email = true;

    // Informations spécifiques propriétaire
    public $adresse_proprietaire = '';
    public $profession_proprietaire = '';
    public $mobile_money_proprietaire = '';
    public $bank_account_number = '';
    public $bank_name = '';

    // Informations spécifiques locataire
    public $adresse_locataire = '';
    public $profession_locataire = '';
    public $salaire_mensuel = '';
    public $mobile_money_locataire = '';
    public $contact_urgence_nom = '';
    public $contact_urgence_phone = '';
    public $contact_urgence_relation = '';

    // Informations démarcheur
    public $mobile_money_demarcheur = '';

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:100',
                'unique:users,email,' . ($this->isEdit ? $this->userId : 'NULL')
            ],
            'phone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'user_type' => 'required|in:admin,proprietaire,locataire,demarcheur',
            'profile_image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];

        if (!$this->isEdit) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        // Règles spécifiques selon le type
        if ($this->user_type === 'proprietaire') {
            $rules['profession_proprietaire'] = 'nullable|string|max:100';
            $rules['mobile_money_proprietaire'] = 'nullable|string|max:20';
        } elseif ($this->user_type === 'locataire') {
            $rules['profession_locataire'] = 'nullable|string|max:100';
            $rules['salaire_mensuel'] = 'nullable|numeric|min:0';
            $rules['mobile_money_locataire'] = 'nullable|string|max:20';
        } elseif ($this->user_type === 'demarcheur') {
            $rules['mobile_money_demarcheur'] = 'nullable|string|max:20';
        }

        return $rules;
    }

    protected $messages = [
        'first_name.required' => 'Le prénom est requis',
        'last_name.required' => 'Le nom est requis',
        'email.required' => 'L\'email est requis',
        'email.unique' => 'Cet email est déjà utilisé',
        'ville.required' => 'La ville est requise',
        'user_type.required' => 'Le type d\'utilisateur est requis',
        'password.required' => 'Le mot de passe est requis',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
        'password.confirmed' => 'Les mots de passe ne correspondent pas',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->userId = $id;
            $this->isEdit = true;
            $this->loadUser();
        }
    }

    public function loadUser()
    {
        $user = User::findOrFail($this->userId);
        
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->date_naissance = $user->date_naissance;
        $this->ville = $user->ville;
        $this->pays = $user->pays;
        $this->user_type = $user->user_type;
        $this->existing_profile_image = $user->profile_image_url;
        $this->is_active = $user->is_active;

        // Charger les données spécifiques
        if ($user->isProprietaire() && $user->proprietaire) {
            $this->adresse_proprietaire = $user->proprietaire->adresse;
            $this->profession_proprietaire = $user->proprietaire->profession;
            $this->mobile_money_proprietaire = $user->proprietaire->mobile_money_number;
            
            $bankInfo = $user->proprietaire->bank_account_info ?? [];
            $this->bank_account_number = $bankInfo['account_number'] ?? '';
            $this->bank_name = $bankInfo['bank_name'] ?? '';
        } elseif ($user->isLocataire() && $user->locataire) {
            $this->adresse_locataire = $user->locataire->adresse_actuelle;
            $this->profession_locataire = $user->locataire->profession;
            $this->salaire_mensuel = $user->locataire->salaire_mensuel;
            $this->mobile_money_locataire = $user->locataire->mobile_money_number;
            
            $contact = $user->locataire->contact_urgence ?? [];
            $this->contact_urgence_nom = $contact['nom'] ?? '';
            $this->contact_urgence_phone = $contact['phone'] ?? '';
            $this->contact_urgence_relation = $contact['relation'] ?? '';
        } elseif ($user->isDemarcheur() && $user->demarcheur) {
            $this->mobile_money_demarcheur = $user->demarcheur->default_mobile_money_number;
        }
    }

    public function saveUser()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $plainPassword = $this->password ?: null;

            if ($this->isEdit) {
                // MODIFICATION
                $user = User::findOrFail($this->userId);
                
                $user->first_name = $this->first_name;
                $user->last_name = $this->last_name;
                $user->email = $this->email;
                $user->phone = $this->phone;
                $user->date_naissance = $this->date_naissance;
                $user->ville = $this->ville;
                $user->pays = $this->pays;
                $user->is_active = $this->is_active;

                if ($this->password) {
                    $user->password_hash = Hash::make($this->password);
                }

                // Gérer la photo
                if ($this->profile_image) {
                    if ($user->profile_image_url) {
                        Storage::disk('public')->delete($user->profile_image_url);
                    }
                    $user->profile_image_url = $this->profile_image->store('profiles', 'public');
                }

                $user->save();

            } else {
                // CRÉATION
                $user = User::create([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'password_hash' => Hash::make($this->password),
                    'date_naissance' => $this->date_naissance,
                    'user_type' => $this->user_type,
                    'ville' => $this->ville,
                    'pays' => $this->pays,
                    'is_active' => $this->is_active,
                    'is_verified' => false,
                ]);

                if ($this->profile_image) {
                    $user->profile_image_url = $this->profile_image->store('profiles', 'public');
                    $user->save();
                }
            }

            // Créer/Mettre à jour les données spécifiques
            $this->saveSpecificData($user);

            // Envoyer l'email si c'est une création et que l'option est cochée
            // if (!$this->isEdit && $this->send_email && $plainPassword) {
            //     try {
            //         Mail::to($user->email)->send(new CompteCreeMail($user, $plainPassword, Auth::user()));
            //     } catch (\Exception $e) {
            //         // Ne pas bloquer la création si l'email échoue
            //         session()->flash('warning', 'Utilisateur créé mais l\'email n\'a pas pu être envoyé : ' . $e->getMessage());
            //     }
            // }

            // Notification
            // \App\Models\Notification::create([
            //     'user_id' => Auth::id(),
            //     'titre' => $this->isEdit ? 'Utilisateur modifié' : 'Utilisateur créé',
            //     'message' => $this->isEdit 
            //         ? "L'utilisateur '{$user->full_name}' a été modifié avec succès."
            //         : "L'utilisateur '{$user->full_name}' a été créé avec succès.",
            //     'type' => 'systeme',
            //     'reference_id' => $user->id,
            //     'reference_type' => 'user',
            // ]);

            DB::commit();

            session()->flash('success', $this->isEdit 
                ? 'L\'utilisateur a été modifié avec succès !'
                : 'L\'utilisateur a été créé avec succès !');
            
            return redirect()->route('utilisateurs.detail', $user->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    private function saveSpecificData($user)
    {
        if ($user->user_type === 'proprietaire') {
            $bankInfo = null;
            if ($this->bank_account_number) {
                $bankInfo = [
                    'account_number' => $this->bank_account_number,
                    'bank_name' => $this->bank_name,
                ];
            }

            if ($user->proprietaire) {
                $user->proprietaire->update([
                    'adresse' => $this->adresse_proprietaire,
                    'profession' => $this->profession_proprietaire,
                    'mobile_money_number' => $this->mobile_money_proprietaire,
                    'bank_account_info' => $bankInfo,
                ]);
            } else {
                Proprietaire::create([
                    'user_id' => $user->id,
                    'adresse' => $this->adresse_proprietaire,
                    'ville' => $user->ville,
                    'pays' => $user->pays,
                    'profession' => $this->profession_proprietaire,
                    'mobile_money_number' => $this->mobile_money_proprietaire,
                    'bank_account_info' => $bankInfo,
                ]);
            }
        } elseif ($user->user_type === 'locataire') {
            $contactUrgence = null;
            if ($this->contact_urgence_nom) {
                $contactUrgence = [
                    'nom' => $this->contact_urgence_nom,
                    'phone' => $this->contact_urgence_phone,
                    'relation' => $this->contact_urgence_relation,
                ];
            }

            if ($user->locataire) {
                $user->locataire->update([
                    'adresse_actuelle' => $this->adresse_locataire,
                    'profession' => $this->profession_locataire,
                    'salaire_mensuel' => $this->salaire_mensuel ?: null,
                    'mobile_money_number' => $this->mobile_money_locataire,
                    'contact_urgence' => $contactUrgence,
                ]);
            } else {
                Locataire::create([
                    'user_id' => $user->id,
                    'adresse_actuelle' => $this->adresse_locataire,
                    'profession' => $this->profession_locataire,
                    'salaire_mensuel' => $this->salaire_mensuel ?: null,
                    'mobile_money_number' => $this->mobile_money_locataire,
                    'contact_urgence' => $contactUrgence,
                ]);
            }
        } elseif ($user->user_type === 'demarcheur') {
            if ($user->demarcheur) {
                $user->demarcheur->update([
                    'default_mobile_money_number' => $this->mobile_money_demarcheur,
                ]);
            } else {
                Demarcheur::create([
                    'user_id' => $user->id,
                    'default_mobile_money_number' => $this->mobile_money_demarcheur,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.utilisateurs.creer-utilisateur')
            ->layout('layouts.app')
            ->title(($this->isEdit ? 'Modifier' : 'Créer') . ' un utilisateur - Wassou');
    }
}