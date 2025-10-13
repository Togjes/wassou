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

    // Pour l'admin : sélection du propriétaire
    public $code_proprietaire = '';
    public $proprietaire_selectionne = null;
    public $proprietaire_trouve = false;

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
        if ($id) {
            $this->bienId = $id;
            $this->isEdit = true;
            $this->loadBien();
        } else {
            $user = Auth::user();
            
            if ($user->isProprietaire() && $user->proprietaire) {
                $this->proprietaire_selectionne = $user->proprietaire;
                $this->proprietaire_trouve = true;
                $this->ville = $user->ville;
                $this->mobile_money_number = $user->proprietaire->mobile_money_number;
            }
            
            if ($user->isAdmin() || $user->isDemarcheur()) {
                $this->proprietaire_trouve = false;
            }
        }
    }

    public function chercherProprietaire()
    {
        $this->validate([
            'code_proprietaire' => 'required|string',
        ]);

        $user = \App\Models\User::where('code_unique', $this->code_proprietaire)->first();

        if ($user && $user->hasRole('proprietaire')) {
            $this->proprietaire_selectionne = $user->proprietaire;
            $this->proprietaire_trouve = true;
            
            $this->ville = $user->ville ?? '';
            $this->mobile_money_number = $user->proprietaire->mobile_money_number ?? '';
            
            session()->flash('success_search', 'Propriétaire trouvé : ' . $user->name);
        } elseif ($user && !$user->hasRole('proprietaire')) {
            $this->proprietaire_trouve = false;
            $this->proprietaire_selectionne = null;
            session()->flash('error_search', "Cet utilisateur (Code: {$user->code_unique}) n'est pas un propriétaire.");
        } else {
            $this->proprietaire_trouve = false;
            $this->proprietaire_selectionne = null;
            session()->flash('error_search', 'Aucun utilisateur trouvé avec ce code.');
        }
    }

    public function resetProprietaire()
    {
        $this->code_proprietaire = '';
        $this->proprietaire_selectionne = null;
        $this->proprietaire_trouve = false;
        $this->reset(['ville', 'mobile_money_number']);
    }

    public function loadBien()
    {
        $bien = BienImmobilier::findOrFail($this->bienId);
        
        $user = Auth::user();
        if (!$user->isAdmin() && $bien->proprietaire->user_id !== $user->id) {
            abort(403, 'Accès non autorisé');
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
                
                // Validation conditionnelle pour Mobile Money
                if (in_array('mobile_money', $this->moyens_paiement_acceptes)) {
                    $rules['mobile_money_number'] = 'required|string|regex:/^\+?[0-9\s]{10,15}$/';
                }
                
                // Validation conditionnelle pour Virement Bancaire
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

        // Vérifier que le propriétaire est sélectionné pour admin et démarcheur
        if (($user->isAdmin() || $user->isDemarcheur()) && !$this->proprietaire_selectionne) {
            session()->flash('error', 'Veuillez sélectionner un propriétaire avant de créer le bien.');
            $this->currentStep = 1; // Retour à l'étape 1 pour voir le message
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

        // Validation conditionnelle pour Mobile Money
        if (in_array('mobile_money', $this->moyens_paiement_acceptes)) {
            $rules['mobile_money_number'] = 'required|string|regex:/^\+?[0-9\s]{10,15}$/';
        }

        // Validation conditionnelle pour Virement Bancaire
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
                
                foreach ($this->photos_to_delete as $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }

                $allPhotos = $this->existing_photos;
            } else {
                $bien = new BienImmobilier();
                $bien->proprietaire_id = $this->proprietaire_selectionne->id;
                $allPhotos = [];
            }

            if (!empty($this->photos_generales)) {
                foreach ($this->photos_generales as $photo) {
                    if ($photo) {
                        $path = $photo->store('biens/photos', 'public');
                        $allPhotos[] = $path;
                    }
                }
            }

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
            \App\Models\Notification::create([
                'user_id' => $this->proprietaire_selectionne->user_id,
                'titre' => $this->isEdit ? 'Bien immobilier modifié' : 'Bien immobilier créé',
                'message' => $this->isEdit 
                    ? "Votre bien '{$bien->titre}' a été modifié avec succès."
                    : "Un nouveau bien '{$bien->titre}' a été créé pour vous.",
                'type' => 'systeme',
                'reference_id' => $bien->id,
                'reference_type' => 'bien_immobilier',
            ]);

            // Notification pour l'admin/démarcheur si c'est lui qui a créé
            if (($user->isAdmin() || $user->isDemarcheur()) && !$this->isEdit) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'titre' => 'Bien immobilier créé',
                    'message' => "Vous avez créé le bien '{$bien->titre}' pour {$this->proprietaire_selectionne->user->name}.",
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