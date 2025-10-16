<?php

namespace App\Livewire\Biens;

use App\Models\BienImmobilier;
use App\Models\Proprietaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreerBien extends Component
{
    use WithFileUploads;

    // Pour la modification
    public $bienId = null;
    public $isEdit = false;

    // Pour l'admin/démarcheur : sélection du propriétaire
    public $code_proprietaire = '';
    public $proprietaire_selectionne = null;
    public $proprietaire_trouve = false;
    public $demarcheur_autorise = false; // NOUVEAU

    // Étape 1 : Informations de base + Caractéristiques
    public $description = '';
    public $type_bien = 'appartement';
    public $ville = '';
    public $quartier = '';
    public $adresse = '';
    public $annee_construction = '';
    public $equipements_communs = [];
    
    // Étape 2 : Photos et Paiement
    public $photos_generales = [];
    public $existing_photos = [];
    public $photos_to_delete = [];
    public $documents = [];
    public $moyens_paiement_acceptes = [];
    public $mobile_money_number = '';
    public $bank_account_number = '';
    public $bank_name = '';
    public $statut = 'Location';
    
    // État actuel
    public $currentStep = 1;
    
    // Liste des équipements disponibles
    public $equipements_disponibles = [
        'Parking',
        'Gardien',
        'Eau courante',
        'Électricité',
        'Groupe électrogène',
        'Forage',
        'Clôture',
        'Portail automatique',
        'Vidéosurveillance',
        'Wifi',
        'Jardin',
        'Piscine',
    ];

    // Liste des moyens de paiement disponibles
    public $moyens_paiement_disponibles = [
        'mobile_money',
        'virement',
        'especes',
        'cheque',
        'carte_bancaire',
    ];

    protected $messages = [
        'code_proprietaire.required' => 'Le code propriétaire est requis',
        'type_bien.required' => 'Le type de bien est requis',
        'ville.required' => 'La ville est requise',
        'quartier.required' => 'Le quartier est requis',
        'adresse.required' => 'L\'adresse complète est requise',
        'annee_construction.required' => 'L\'année de construction est requise',
        'moyens_paiement_acceptes.required' => 'Sélectionnez au moins un moyen de paiement',
        'moyens_paiement_acceptes.min' => 'Sélectionnez au moins un moyen de paiement',
        'statut.required' => 'Le statut du bien est requis',
        'mobile_money_number.required' => 'Le numéro Mobile Money est requis',
        'mobile_money_number.regex' => 'Le format du numéro Mobile Money est invalide (ex: +229 XX XX XX XX)',
        'bank_name.required' => 'Le nom de la banque est requis',
        'bank_account_number.required' => 'Le numéro de compte bancaire est requis',
        'bank_account_number.min' => 'Le numéro de compte doit contenir au moins 10 caractères',
    ];

    public function mount($id = null)
    {
        $user = Auth::user();

        if ($id) {
            $this->bienId = $id;
            $this->isEdit = true;
            $this->loadBien();
        } else {
            if ($user->isProprietaire() && $user->proprietaire) {
                $this->proprietaire_selectionne = $user->proprietaire;
                $this->proprietaire_trouve = true;
                $this->demarcheur_autorise = true; // Propriétaire lui-même
                $this->ville = $user->ville;
                $this->mobile_money_number = $user->proprietaire->mobile_money_number;
            }
            
            if ($user->isAdmin()) {
                // Admin a tous les droits, pas besoin de vérifier l'autorisation
                $this->proprietaire_trouve = false;
                $this->demarcheur_autorise = true; // Admin peut créer pour tous
            }

            if ($user->isDemarcheur()) {
                $this->proprietaire_trouve = false;
                $this->demarcheur_autorise = false; // Sera vérifié après sélection
            }
        }
    }

    public function chercherProprietaire()
    {
        $this->validate([
            'code_proprietaire' => 'required|string',
        ]);

        $user = \App\Models\User::where('code_unique', $this->code_proprietaire)->first();

        if (!$user) {
            $this->resetSelection('Aucun utilisateur trouvé avec ce code.');
            return;
        }

        if (!$user->hasRole('proprietaire')) {
            $this->resetSelection("Cet utilisateur (Code: {$user->code_unique}) n'est pas un propriétaire.");
            return;
        }

        // Propriétaire trouvé
        $this->proprietaire_selectionne = $user->proprietaire;
        $this->proprietaire_trouve = true;

        $currentUser = Auth::user();

        // Vérifier les autorisations
        if ($currentUser->isAdmin()) {
            // Admin autorisé pour tous
            $this->demarcheur_autorise = true;
            $this->chargerDonneesProprietaire();
            session()->flash('success_search', 'Propriétaire trouvé : ' . $user->name);
        } 
        elseif ($currentUser->isDemarcheur()) {
            // Vérifier si le démarcheur est autorisé pour ce propriétaire
            $demarcheur = $currentUser->demarcheur;
            
            if ($demarcheur->isAuthorizedFor($this->proprietaire_selectionne->id)) {
                // Vérifier la permission spécifique de créer un bien
                if ($demarcheur->hasPermissionFor($this->proprietaire_selectionne->id, 'creer_bien')) {
                    $this->demarcheur_autorise = true;
                    $this->chargerDonneesProprietaire();
                    session()->flash('success_search', 'Propriétaire trouvé : ' . $user->name . ' - Vous êtes autorisé.');
                } else {
                    $this->demarcheur_autorise = false;
                    session()->flash('error_search', 'Vous n\'avez pas la permission de créer un bien pour ce propriétaire.');
                }
            } else {
                $this->demarcheur_autorise = false;
                session()->flash('error_search', 'Vous n\'êtes pas autorisé à gérer les biens de ce propriétaire. Veuillez contacter le propriétaire pour obtenir l\'autorisation.');
            }
        }
    }

    protected function chargerDonneesProprietaire()
    {
        $this->ville = $this->proprietaire_selectionne->user->ville ?? '';
        $this->mobile_money_number = $this->proprietaire_selectionne->mobile_money_number ?? '';
    }

    protected function resetSelection($message)
    {
        $this->proprietaire_trouve = false;
        $this->proprietaire_selectionne = null;
        $this->demarcheur_autorise = false;
        session()->flash('error_search', $message);
    }

    public function resetProprietaire()
    {
        $this->code_proprietaire = '';
        $this->proprietaire_selectionne = null;
        $this->proprietaire_trouve = false;
        $this->demarcheur_autorise = false;
        $this->reset(['ville', 'mobile_money_number']);
    }

    public function loadBien()
    {
        $bien = BienImmobilier::findOrFail($this->bienId);
        
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->isProprietaire()) {
            if ($bien->proprietaire->user_id !== $user->id) {
                abort(403, 'Accès non autorisé');
            }
            $this->demarcheur_autorise = true;
        } 
        elseif ($user->isDemarcheur()) {
            $demarcheur = $user->demarcheur;
            if (!$demarcheur->isAuthorizedFor($bien->proprietaire_id) || 
                !$demarcheur->hasPermissionFor($bien->proprietaire_id, 'modifier_bien')) {
                abort(403, 'Vous n\'êtes pas autorisé à modifier ce bien');
            }
            $this->demarcheur_autorise = true;
        }
        elseif ($user->isAdmin()) {
            $this->demarcheur_autorise = true;
        }

        $this->proprietaire_selectionne = $bien->proprietaire;
        $this->proprietaire_trouve = true;

        $this->description = $bien->description;
        $this->type_bien = $bien->type_bien;
        $this->ville = $bien->ville;
        $this->quartier = $bien->quartier;
        $this->adresse = $bien->adresse;
        $this->annee_construction = $bien->annee_construction;
        $this->equipements_communs = $bien->equipements_communs ?? [];
        $this->statut = $bien->statut;
        $this->existing_photos = $bien->photos_generales ?? [];
        $this->moyens_paiement_acceptes = $bien->moyens_paiement_acceptes ?? [];
        
        $detailsPaiement = $bien->details_paiement ?? [];
        if (isset($detailsPaiement['mobile_money']['number'])) {
            $this->mobile_money_number = $detailsPaiement['mobile_money']['number'];
        }
        if (isset($detailsPaiement['bank']['account_number'])) {
            $this->bank_account_number = $detailsPaiement['bank']['account_number'];
            $this->bank_name = $detailsPaiement['bank']['bank_name'] ?? '';
        }
    }

    public function deleteExistingPhoto($index)
    {
        if (isset($this->existing_photos[$index])) {
            $this->photos_to_delete[] = $this->existing_photos[$index];
            unset($this->existing_photos[$index]);
            $this->existing_photos = array_values($this->existing_photos);
        }
    }

    public function nextStep()
    {
        // Vérifier l'autorisation avant de passer à l'étape suivante
        if (!$this->demarcheur_autorise && !Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous devez sélectionner un propriétaire autorisé avant de continuer.');
            return;
        }

        $this->validateCurrentStep();
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    protected function validateCurrentStep()
    {
        $rules = [];

        switch ($this->currentStep) {
            case 1:
                $rules = [
                    'type_bien' => 'required|in:maison,appartement,bureau,terrain,commerce,magasin,autre',
                    'ville' => 'required|string|max:100',
                    'quartier' => 'required|string|max:100',
                    'adresse' => 'required|string|max:255',
                    'annee_construction' => 'required|date',
                ];
                break;
            case 2:
                $rules = [
                    'moyens_paiement_acceptes' => 'required|array|min:1',
                    'statut' => 'required|in:Location,Construction,Renovation',
                    'photos_generales.*' => 'nullable|image|max:5120',
                ];
                
                if (in_array('mobile_money', $this->moyens_paiement_acceptes)) {
                    $rules['mobile_money_number'] = 'required|string|regex:/^\+?[0-9\s]{10,15}$/';
                }
                
                if (in_array('virement', $this->moyens_paiement_acceptes)) {
                    $rules['bank_name'] = 'required|string|max:100';
                    $rules['bank_account_number'] = 'required|string|min:10|max:50';
                }
                break;
        }

        $this->validate($rules);
    }

    public function saveBien()
    {
        $user = Auth::user();

        // VÉRIFICATION STRICTE DES AUTORISATIONS
        if (!$this->demarcheur_autorise) {
            session()->flash('error', 'Vous n\'êtes pas autorisé à effectuer cette action.');
            return;
        }

        // Vérifier que le propriétaire est sélectionné
        if (!$this->proprietaire_selectionne) {
            session()->flash('error', 'Veuillez sélectionner un propriétaire avant de créer le bien.');
            $this->currentStep = 1;
            return;
        }

        // Validation finale complète
        $rules = [
            'type_bien' => 'required|in:maison,appartement,bureau,terrain,commerce,magasin,autre',
            'ville' => 'required|string|max:100',
            'quartier' => 'required|string|max:100',
            'adresse' => 'required|string|max:255',
            'annee_construction' => 'required|date',
            'moyens_paiement_acceptes' => 'required|array|min:1',
            'statut' => 'required|in:Location,Construction,Renovation',
        ];

        if (in_array('mobile_money', $this->moyens_paiement_acceptes)) {
            $rules['mobile_money_number'] = 'required|string|regex:/^\+?[0-9\s]{10,15}$/';
        }

        if (in_array('virement', $this->moyens_paiement_acceptes)) {
            $rules['bank_name'] = 'required|string|max:100';
            $rules['bank_account_number'] = 'required|string|min:10|max:50';
        }

        $this->validate($rules);

        try {
            DB::beginTransaction();

            // Préparer les détails de paiement
            $detailsPaiement = [];
            if (in_array('mobile_money', $this->moyens_paiement_acceptes)) {
                $detailsPaiement['mobile_money'] = ['number' => $this->mobile_money_number];
            }
            if (in_array('virement', $this->moyens_paiement_acceptes)) {
                $detailsPaiement['bank'] = [
                    'account_number' => $this->bank_account_number,
                    'bank_name' => $this->bank_name,
                ];
            }

            if ($this->isEdit) {
                $bien = BienImmobilier::findOrFail($this->bienId);
                
                // Supprimer les photos marquées pour suppression
                foreach ($this->photos_to_delete as $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }

                $allPhotos = $this->existing_photos;
            } else {
                // CRÉATION D'UN NOUVEAU BIEN
                $bien = new BienImmobilier();
                $bien->proprietaire_id = $this->proprietaire_selectionne->id;
                
                // ✅ AJOUT : Enregistrer qui a créé le bien
                $bien->created_by_user_id = $user->id;
                $bien->created_by_type = $user->user_type;
                
                $allPhotos = [];
            }

            // Upload des nouvelles photos
            if (!empty($this->photos_generales)) {
                foreach ($this->photos_generales as $photo) {
                    if ($photo) {
                        $path = $photo->store('biens/photos', 'public');
                        $allPhotos[] = $path;
                    }
                }
            }

            // Upload des documents
            $documentsUrls = $this->isEdit ? ($bien->documents ?? []) : [];
            if (!empty($this->documents)) {
                foreach ($this->documents as $document) {
                    if ($document) {
                        $path = $document->store('biens/documents', 'public');
                        $documentsUrls[] = [
                            'path' => $path,
                            'name' => $document->getClientOriginalName(),
                        ];
                    }
                }
            }

            // Générer le titre automatiquement si vide
            if (empty($bien->titre)) {
                $bien->titre = ucfirst($this->type_bien) . ' à ' . $this->ville . ' - ' . $this->quartier;
            }

            // Assigner toutes les propriétés
            $bien->description = $this->description;
            $bien->type_bien = $this->type_bien;
            $bien->ville = $this->ville;
            $bien->quartier = $this->quartier;
            $bien->adresse = $this->adresse;
            $bien->annee_construction = $this->annee_construction;
            $bien->equipements_communs = $this->equipements_communs;
            $bien->photos_generales = $allPhotos;
            $bien->documents = $documentsUrls;
            $bien->moyens_paiement_acceptes = $this->moyens_paiement_acceptes;
            $bien->details_paiement = $detailsPaiement;
            $bien->statut = $this->statut;

            $bien->save();

            // Notification pour le propriétaire
            if ($this->isEdit) {
                // Notification de modification
                \App\Models\Notification::create([
                    'user_id' => $this->proprietaire_selectionne->user_id,
                    'titre' => 'Bien immobilier modifié',
                    'message' => $user->id === $this->proprietaire_selectionne->user_id
                        ? "Vous avez modifié le bien '{$bien->titre}'."
                        : "{$user->name} a modifié votre bien '{$bien->titre}'.",
                    'type' => 'systeme',
                    'reference_id' => $bien->id,
                    'reference_type' => 'bien_immobilier',
                ]);
            } else {
                // Notification de création
                \App\Models\Notification::create([
                    'user_id' => $this->proprietaire_selectionne->user_id,
                    'titre' => 'Bien immobilier créé',
                    'message' => $user->id === $this->proprietaire_selectionne->user_id
                        ? "Vous avez créé le bien '{$bien->titre}' avec succès."
                        : "{$user->name} a créé le bien '{$bien->titre}' pour vous.",
                    'type' => 'systeme',
                    'reference_id' => $bien->id,
                    'reference_type' => 'bien_immobilier',
                ]);
            }

            // Notification pour l'admin/démarcheur si c'est lui qui a créé/modifié (et pas le propriétaire lui-même)
            if ($user->id !== $this->proprietaire_selectionne->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'titre' => $this->isEdit ? 'Bien immobilier modifié' : 'Bien immobilier créé',
                    'message' => $this->isEdit
                        ? "Vous avez modifié le bien '{$bien->titre}' de {$this->proprietaire_selectionne->user->name}."
                        : "Vous avez créé le bien '{$bien->titre}' pour {$this->proprietaire_selectionne->user->name}.",
                    'type' => 'systeme',
                    'reference_id' => $bien->id,
                    'reference_type' => 'bien_immobilier',
                ]);
            }

            DB::commit();

            session()->flash('success', $this->isEdit 
                ? 'Le bien immobilier a été modifié avec succès !'
                : 'Le bien immobilier a été créé avec succès !');
            
            return redirect()->route('biens.detail', $bien->id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log de l'erreur pour le débogage
            logger()->error('Erreur lors de la sauvegarde du bien', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'proprietaire_id' => $this->proprietaire_selectionne->id ?? null,
            ]);
            
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.biens.creer-bien')
            ->layout('layouts.app')
            ->title($this->isEdit ? 'Modifier le bien - Wassou' : 'Ajouter un bien - Wassou');
    }
}