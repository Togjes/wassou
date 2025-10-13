<?php

namespace App\Livewire\Contrats;

use App\Models\ContratLocation;
use App\Models\BienImmobilier;
use App\Models\Chambre;
use App\Models\Locataire;
use App\Models\User;
use App\Models\Paiement;
use App\Models\AvancePrepayee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreerContrat extends Component
{
    public $contratId = null;
    public $isEdit = false;

    // Recherche par référence et code
    public $reference_bien = '';
    public $bien_trouve = false;
    public $bien_selectionne = null;
    
    public $code_locataire = '';
    public $locataire_trouve = false;
    public $locataire_selectionne = null;

    // Sélections
    public $chambre_id = '';
    public $demarcheur_id = null;

    // Informations contrat
    public $date_etablissement = '';
    public $date_paiement_loyer = 1;

    // Paiements initiaux
    public $loyer_mensuel = 0;
    public $avance_loyer = 0;
    public $prepaye_loyer = 0;
    public $caution = 0;
    public $frais_dossier = 0;

    // Calculs
    public $total_a_payer = 0;

    // Listes
    public $chambres = [];
    public $demarcheurs = [];

    protected function rules()
    {
        return [
            'reference_bien' => 'required|string',
            'chambre_id' => 'required|exists:chambres,id',
            'code_locataire' => 'required|string',
            'demarcheur_id' => 'nullable|exists:demarcheurs,id',
            'date_etablissement' => 'required|date',
            'date_paiement_loyer' => 'required|integer|min:1|max:28',
            'avance_loyer' => 'nullable|numeric|min:0',
            'prepaye_loyer' => 'nullable|numeric|min:0',
            'caution' => 'nullable|numeric|min:0',
            'frais_dossier' => 'nullable|numeric|min:0',
        ];
    }

    public function mount($id = null)
    {
        $this->date_etablissement = now()->format('Y-m-d');
        
        if ($id) {
            $this->contratId = $id;
            $this->isEdit = true;
            $this->loadContrat();
        }

        $this->loadDemarcheurs();
    }

    public function loadDemarcheurs()
    {
        $user = Auth::user();
        if ($user->isAdmin() || $user->isProprietaire()) {
            $this->demarcheurs = \App\Models\Demarcheur::with('user')
                ->where('is_active', true)
                ->get();
        }
    }

    public function chercherBien()
    {
        $this->validate([
            'reference_bien' => 'required|string',
        ]);

        $bien = BienImmobilier::where('reference', $this->reference_bien)->first();

        if ($bien) {
            // Vérifier les permissions
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isDemarcheur()) {
                if ($user->isProprietaire() && $bien->proprietaire->user_id !== $user->id) {
                    session()->flash('error_bien', 'Vous n\'avez pas accès à ce bien.');
                    return;
                }
            }

            $this->bien_selectionne = $bien;
            $this->bien_trouve = true;
            $this->loadChambres();
            
            session()->flash('success_bien', 'Bien trouvé : ' . ($bien->titre ?? $bien->type_bien));
        } else {
            $this->bien_trouve = false;
            $this->bien_selectionne = null;
            $this->chambres = [];
            session()->flash('error_bien', 'Aucun bien trouvé avec cette référence.');
        }
    }

    public function resetBien()
    {
        $this->reference_bien = '';
        $this->bien_selectionne = null;
        $this->bien_trouve = false;
        $this->chambre_id = '';
        $this->chambres = [];
    }

    public function loadChambres()
    {
        if ($this->bien_selectionne) {
            $this->chambres = Chambre::where('bien_id', $this->bien_selectionne->id)
                ->where(function($query) {
                    $query->where('statut', 'disponible')
                          ->orWhere('id', $this->chambre_id);
                })
                ->get();
        }
    }

    public function chercherLocataire()
    {
        $this->validate([
            'code_locataire' => 'required|string',
        ]);

        $user = User::where('code_unique', $this->code_locataire)->first();

        if ($user && $user->isLocataire()) {
            // Vérifier qu'il n'a pas déjà un contrat actif
            $hasActiveContract = ContratLocation::where('locataire_id', $user->locataire->id)
                ->whereIn('statut', ['actif', 'en_attente'])
                ->exists();

            if ($hasActiveContract) {
                session()->flash('error_locataire', 'Ce locataire a déjà un contrat actif ou en attente.');
                return;
            }

            $this->locataire_selectionne = $user->locataire;
            $this->locataire_trouve = true;
            
            session()->flash('success_locataire', 'Locataire trouvé : ' . $user->full_name);
        } elseif ($user && !$user->isLocataire()) {
            $this->locataire_trouve = false;
            $this->locataire_selectionne = null;
            session()->flash('error_locataire', "Cet utilisateur n'est pas un locataire.");
        } else {
            $this->locataire_trouve = false;
            $this->locataire_selectionne = null;
            session()->flash('error_locataire', 'Aucun utilisateur trouvé avec ce code.');
        }
    }

    public function resetLocataire()
    {
        $this->code_locataire = '';
        $this->locataire_selectionne = null;
        $this->locataire_trouve = false;
    }

    public function updatedChambreId($value)
    {
        $this->loadChambreDetails();
    }

    public function loadChambreDetails()
    {
        if ($this->chambre_id) {
            $chambre = Chambre::find($this->chambre_id);
            if ($chambre) {
                // Vérifier qu'elle n'a pas de contrat actif
                $hasActiveContract = ContratLocation::where('chambre_id', $this->chambre_id)
                    ->whereIn('statut', ['actif', 'en_attente'])
                    ->exists();

                if ($hasActiveContract) {
                    session()->flash('error', 'Cette chambre a déjà un contrat actif.');
                    $this->chambre_id = '';
                    return;
                }

                $this->loyer_mensuel = $chambre->loyer_mensuel;
                $this->avance_loyer = $chambre->avance ?? 0;
                $this->prepaye_loyer = $chambre->prepaye ?? 0;
                $this->caution = $chambre->caution ?? 0;
                
                $this->calculateTotal();
            }
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['avance_loyer', 'prepaye_loyer', 'caution', 'frais_dossier'])) {
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->total_a_payer = 
            floatval($this->avance_loyer) + 
            floatval($this->prepaye_loyer) + 
            floatval($this->caution) + 
            floatval($this->frais_dossier);
    }

    public function saveContrat()
    {
        if (!$this->bien_trouve || !$this->bien_selectionne) {
            session()->flash('error', 'Veuillez rechercher et sélectionner un bien.');
            return;
        }

        if (!$this->locataire_trouve || !$this->locataire_selectionne) {
            session()->flash('error', 'Veuillez rechercher et sélectionner un locataire.');
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Vérifier que la chambre est disponible
            $chambre = Chambre::findOrFail($this->chambre_id);
            $hasActiveContract = ContratLocation::where('chambre_id', $this->chambre_id)
                ->whereIn('statut', ['actif', 'en_attente'])
                ->exists();

            if ($hasActiveContract) {
                session()->flash('error', 'Cette chambre a déjà un contrat actif.');
                return;
            }

            $contrat = new ContratLocation();
            $contrat->chambre_id = $this->chambre_id;
            $contrat->locataire_id = $this->locataire_selectionne->id;
            $contrat->proprietaire_id = $chambre->bien->proprietaire_id;
            $contrat->demarcheur_id = $this->demarcheur_id;
            $contrat->date_etablissement = $this->date_etablissement;
            $contrat->date_paiement_loyer = $this->date_paiement_loyer;
            $contrat->statut = 'brouillon';

            $contrat->save();

            // Créer les paiements initiaux
            $this->createInitialPayments($contrat);
            
            // Marquer la chambre comme réservée
            $chambre->statut = 'reserve';
            $chambre->save();

            // Notification
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Contrat créé',
                'message' => "Le contrat '{$contrat->numero_contrat}' (Réf: {$contrat->reference}) a été créé avec succès.",
                'type' => 'contrat',
                'reference_id' => $contrat->id,
                'reference_type' => 'contrat',
            ]);

            DB::commit();

            session()->flash('success', "Le contrat (Réf: {$contrat->reference}) a été créé avec succès !");
            
            return redirect()->route('contrats.detail', $contrat->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    private function createInitialPayments($contrat)
    {
        // Même logique que votre code existant
        if ($this->avance_loyer > 0) {
            AvancePrepayee::create([
                'contrat_id' => $contrat->id,
                'locataire_id' => $contrat->locataire_id,
                'type_avance' => 'avance_loyer',
                'montant_initial' => $this->avance_loyer,
                'montant_restant' => $this->avance_loyer,
                'montant_consomme' => 0,
                'statut' => 'actif',
            ]);

            Paiement::create([
                'contrat_id' => $contrat->id,
                'locataire_id' => $contrat->locataire_id,
                'numero_facture' => 'FAC-' . time() . '-' . rand(1000, 9999),
                'type_paiement' => 'avance_loyer',
                'montant' => $this->avance_loyer,
                'date_echeance' => $contrat->date_etablissement,
                'statut' => 'en_attente',
            ]);
        }

        // Répéter pour prepaye, caution, frais_dossier...
    }

    public function render()
    {
        return view('livewire.contrats.creer-contrat')
            ->layout('layouts.app')
            ->title('Créer un contrat - Wassou');
    }
}