<?php

namespace App\Livewire\Contrats;

use App\Models\ContratLocation;
use App\Models\BienImmobilier;
use App\Models\Chambre;
use App\Models\Locataire;
use App\Models\Proprietaire;
use App\Models\Demarcheur;
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

    // Moyens de paiement
    public $methode_paiement_initial = '';
    public $details_paiement = [];
    
    // Détails selon le moyen de paiement
    public $mobile_money_number = '';
    public $reference_transaction = '';
    public $numero_cheque = '';
    public $bank_name = '';

    // Consentement
    public $consentement_paiement = false;

    // Calculs
    public $total_a_payer = 0;

    // Listes
    public $chambres = [];
    public $demarcheurs = [];
    public $proprietaires = [];
    
    public $moyens_paiement = [
        'mobile_money' => 'Mobile Money',
        'virement' => 'Virement Bancaire',
        'especes' => 'Espèces',
        'cheque' => 'Chèque',
    ];

    // Pour gérer la sélection du propriétaire (Admin seulement)
    public $proprietaire_selectionne_id = null;

    protected function rules()
    {
        $rules = [
            'chambre_id' => 'required|exists:chambres,id',
            'date_etablissement' => 'required|date',
            'date_paiement_loyer' => 'required|integer|min:1|max:28',
            'methode_paiement_initial' => 'required|in:' . implode(',', array_keys($this->moyens_paiement)),
            'consentement_paiement' => 'required|accepted',
        ];

        // Validation conditionnelle selon le moyen de paiement
        if ($this->methode_paiement_initial === 'mobile_money') {
            $rules['mobile_money_number'] = 'required|string';
        }
        
        if ($this->methode_paiement_initial === 'virement') {
            $rules['bank_name'] = 'required|string';
            $rules['reference_transaction'] = 'required|string';
        }
        
        if ($this->methode_paiement_initial === 'cheque') {
            $rules['numero_cheque'] = 'required|string';
            $rules['bank_name'] = 'required|string';
        }

        // Pour l'admin qui crée au nom d'un propriétaire
        if (Auth::user()->isAdmin()) {
            $rules['proprietaire_selectionne_id'] = 'required|exists:proprietaires,id';
        }

        return $rules;
    }

    protected $messages = [
        'chambre_id.required' => 'Veuillez sélectionner une chambre',
        'methode_paiement_initial.required' => 'Veuillez sélectionner un moyen de paiement',
        'consentement_paiement.accepted' => 'Vous devez confirmer la réception des paiements initiaux',
        'mobile_money_number.required' => 'Le numéro Mobile Money est requis',
        'reference_transaction.required' => 'La référence de transaction est requise',
        'numero_cheque.required' => 'Le numéro de chèque est requis',
        'bank_name.required' => 'Le nom de la banque est requis',
        'proprietaire_selectionne_id.required' => 'Veuillez sélectionner un propriétaire',
    ];

    public function mount($id = null)
    {
        $this->date_etablissement = now()->format('Y-m-d');
        
        if ($id) {
            $this->contratId = $id;
            $this->isEdit = true;
            $this->loadContrat();
        }

        $this->loadDemarcheurs();
        $this->loadProprietaires();
    }

    public function loadDemarcheurs()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // L'admin voit tous les démarcheurs
            $this->demarcheurs = Demarcheur::with('user')
                ->where('is_active', true)
                ->get();
        } elseif ($user->isProprietaire()) {
            // Le propriétaire voit ses démarcheurs autorisés
            $this->demarcheurs = $user->proprietaire->demarcheursActifs;
        } elseif ($user->isDemarcheur()) {
            // Le démarcheur ne peut pas sélectionner de démarcheur
            $this->demarcheurs = collect();
        }
    }

    public function loadProprietaires()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // L'admin voit tous les propriétaires
            $this->proprietaires = Proprietaire::with('user')
                ->whereHas('user', function($q) {
                    $q->where('is_active', true);
                })
                ->get();
        } elseif ($user->isDemarcheur()) {
            // Le démarcheur voit uniquement ses propriétaires autorisés
            $this->proprietaires = $user->demarcheur->proprietairesActifs;
        }
    }

    public function chercherBien()
    {
        $this->validate([
            'reference_bien' => 'required|string',
        ]);

        $bien = BienImmobilier::with('proprietaire.user')->where('reference', $this->reference_bien)->first();

        if ($bien) {
            // Vérifier les permissions
            $user = Auth::user();
            
            if ($user->isAdmin()) {
                // Admin peut créer pour n'importe quel bien
                $this->bien_selectionne = $bien;
                $this->bien_trouve = true;
                
                // Si admin, présélectionner le propriétaire du bien
                $this->proprietaire_selectionne_id = $bien->proprietaire_id;
            } elseif ($user->isProprietaire()) {
                // Propriétaire peut créer uniquement pour ses biens
                if ($bien->proprietaire->user_id !== $user->id) {
                    session()->flash('error_bien', 'Vous n\'avez pas accès à ce bien.');
                    return;
                }
                $this->bien_selectionne = $bien;
                $this->bien_trouve = true;
                $this->proprietaire_selectionne_id = $bien->proprietaire_id;
            } elseif ($user->isDemarcheur()) {
                // Démarcheur peut créer pour les biens de ses propriétaires autorisés
                if (!$user->demarcheur->isAuthorizedFor($bien->proprietaire_id)) {
                    session()->flash('error_bien', 'Vous n\'êtes pas autorisé à créer des contrats pour ce bien.');
                    return;
                }
                
                // Vérifier la permission de créer des contrats
                if (!$user->demarcheur->hasPermissionFor($bien->proprietaire_id, 'creer_contrat')) {
                    session()->flash('error_bien', 'Vous n\'avez pas la permission de créer des contrats pour ce propriétaire.');
                    return;
                }
                
                $this->bien_selectionne = $bien;
                $this->bien_trouve = true;
                $this->proprietaire_selectionne_id = $bien->proprietaire_id;
            } else {
                session()->flash('error_bien', 'Vous n\'avez pas la permission de créer des contrats.');
                return;
            }
            
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
        $this->proprietaire_selectionne_id = null;
        $this->loyer_mensuel = 0;
        $this->avance_loyer = 0;
        $this->prepaye_loyer = 0;
        $this->caution = 0;
        $this->calculateTotal();
    }

    public function loadChambres()
    {
        if ($this->bien_selectionne) {
            // Charger TOUTES les chambres du bien avec leur statut de contrat
            $this->chambres = Chambre::where('bien_id', $this->bien_selectionne->id)
                ->withCount(['contrats as has_active_contract' => function($query) {
                    $query->whereIn('statut', ['actif', 'en_attente']);
                }])
                ->with(['contrats' => function($query) {
                    $query->whereIn('statut', ['actif', 'en_attente'])
                          ->latest()
                          ->limit(1);
                }])
                ->orderBy('nom_chambre')
                ->get()
                ->map(function($chambre) {
                    // Ajouter des informations utiles sur le contrat actif
                    $chambre->contrat_actif = $chambre->contrats->first();
                    $chambre->est_sous_contrat = $chambre->has_active_contract > 0;
                    return $chambre;
                });

            // Logger pour debug
            logger()->info('Chambres chargées', [
                'bien_id' => $this->bien_selectionne->id,
                'nombre_chambres' => $this->chambres->count(),
                'chambres' => $this->chambres->pluck('nom_chambre', 'id')
            ]);
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
            $chambre = Chambre::with(['contrats' => function($query) {
                $query->whereIn('statut', ['actif', 'en_attente'])
                      ->latest()
                      ->limit(1);
            }])->find($this->chambre_id);
            
            if ($chambre) {
                // Vérifier qu'elle n'a pas de contrat actif
                $contratActif = $chambre->contrats->first();
                
                if ($contratActif) {
                    session()->flash('error', "Cette chambre est déjà sous contrat (N° {$contratActif->numero_contrat}) avec le locataire {$contratActif->locataire->user->full_name}. Statut: {$contratActif->statut_libelle}");
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
                session()->flash('error', 'Cette chambre a déjà un contrat actif ou en attente.');
                DB::rollBack();
                return;
            }

            // Préparer les détails de paiement
            $detailsPaiement = [
                'methode' => $this->methode_paiement_initial,
            ];

            if ($this->methode_paiement_initial === 'mobile_money') {
                $detailsPaiement['numero'] = $this->mobile_money_number;
            } elseif ($this->methode_paiement_initial === 'virement') {
                $detailsPaiement['banque'] = $this->bank_name;
                $detailsPaiement['reference'] = $this->reference_transaction;
            } elseif ($this->methode_paiement_initial === 'cheque') {
                $detailsPaiement['banque'] = $this->bank_name;
                $detailsPaiement['numero_cheque'] = $this->numero_cheque;
            }

            // Créer le contrat
            $contrat = new ContratLocation();
            $contrat->chambre_id = $this->chambre_id;
            $contrat->locataire_id = $this->locataire_selectionne->id;
            $contrat->proprietaire_id = $this->proprietaire_selectionne_id ?? $chambre->bien->proprietaire_id;
            $contrat->demarcheur_id = $this->demarcheur_id;
            $contrat->date_etablissement = $this->date_etablissement;
            $contrat->date_paiement_loyer = $this->date_paiement_loyer;
            $contrat->statut = 'brouillon';

            $contrat->save();

            // Créer les paiements initiaux (tous marqués comme payés)
            $dateNow = now();
            
            // 1. Avance sur loyer
            if ($this->avance_loyer > 0) {
                $avanceRecord = AvancePrepayee::create([
                    'contrat_id' => $contrat->id,
                    'locataire_id' => $contrat->locataire_id,
                    'type_avance' => 'avance_loyer',
                    'montant_initial' => $this->avance_loyer,
                    'montant_restant' => $this->avance_loyer,
                    'montant_consomme' => 0,
                    'statut' => 'actif',
                    'date_debut_utilisation' => $contrat->date_etablissement,
                ]);

                Paiement::create([
                    'contrat_id' => $contrat->id,
                    'locataire_id' => $contrat->locataire_id,
                    'numero_facture' => 'FAC-' . strtoupper(uniqid()),
                    'numero_recu' => 'REC-' . strtoupper(uniqid()),
                    'type_paiement' => 'avance_loyer',
                    'montant' => $this->avance_loyer,
                    'date_echeance' => $contrat->date_etablissement,
                    'date_paiement' => $dateNow,
                    'methode_paiement' => $this->methode_paiement_initial,
                    'details_paiement' => $detailsPaiement,
                    'reference_transaction' => $this->reference_transaction ?? null,
                    'avance_utilisee_id' => $avanceRecord->id,
                    'statut' => 'paye',
                    'recu_pdf_genere' => false,
                ]);
            }

            // 2. Prépayé loyer
            if ($this->prepaye_loyer > 0) {
                $nbMoisPrepaye = floor($this->prepaye_loyer / $this->loyer_mensuel);
                
                $prepayeRecord = AvancePrepayee::create([
                    'contrat_id' => $contrat->id,
                    'locataire_id' => $contrat->locataire_id,
                    'type_avance' => 'prepaye_loyer',
                    'montant_initial' => $this->prepaye_loyer,
                    'montant_restant' => $this->prepaye_loyer,
                    'montant_consomme' => 0,
                    'nb_mois_couverts' => $nbMoisPrepaye,
                    'statut' => 'actif',
                    'date_debut_utilisation' => $contrat->date_etablissement,
                ]);

                Paiement::create([
                    'contrat_id' => $contrat->id,
                    'locataire_id' => $contrat->locataire_id,
                    'numero_facture' => 'FAC-' . strtoupper(uniqid()),
                    'numero_recu' => 'REC-' . strtoupper(uniqid()),
                    'type_paiement' => 'prepaye_loyer',
                    'montant' => $this->prepaye_loyer,
                    'date_echeance' => $contrat->date_etablissement,
                    'date_paiement' => $dateNow,
                    'methode_paiement' => $this->methode_paiement_initial,
                    'details_paiement' => $detailsPaiement,
                    'reference_transaction' => $this->reference_transaction ?? null,
                    'avance_utilisee_id' => $prepayeRecord->id,
                    'statut' => 'paye',
                    'recu_pdf_genere' => false,
                ]);
            }

            // 3. Caution
            if ($this->caution > 0) {
                Paiement::create([
                    'contrat_id' => $contrat->id,
                    'locataire_id' => $contrat->locataire_id,
                    'numero_facture' => 'FAC-' . strtoupper(uniqid()),
                    'numero_recu' => 'REC-' . strtoupper(uniqid()),
                    'type_paiement' => 'caution',
                    'montant' => $this->caution,
                    'date_echeance' => $contrat->date_etablissement,
                    'date_paiement' => $dateNow,
                    'methode_paiement' => $this->methode_paiement_initial,
                    'details_paiement' => $detailsPaiement,
                    'reference_transaction' => $this->reference_transaction ?? null,
                    'statut' => 'paye',
                    'recu_pdf_genere' => false,
                ]);
            }

            // 4. Frais de dossier
            if ($this->frais_dossier > 0) {
                Paiement::create([
                    'contrat_id' => $contrat->id,
                    'locataire_id' => $contrat->locataire_id,
                    'numero_facture' => 'FAC-' . strtoupper(uniqid()),
                    'numero_recu' => 'REC-' . strtoupper(uniqid()),
                    'type_paiement' => 'frais_dossier',
                    'montant' => $this->frais_dossier,
                    'date_echeance' => $contrat->date_etablissement,
                    'date_paiement' => $dateNow,
                    'methode_paiement' => $this->methode_paiement_initial,
                    'details_paiement' => $detailsPaiement,
                    'reference_transaction' => $this->reference_transaction ?? null,
                    'statut' => 'paye',
                    'recu_pdf_genere' => false,
                ]);
            }
            
            // Marquer la chambre comme réservée
            $chambre->statut = 'reserve';
            $chambre->disponible = false;
            $chambre->save();

            // Notifications
            $proprietaire = Proprietaire::find($contrat->proprietaire_id);
            
            // Notification au locataire
            \App\Models\Notification::create([
                'user_id' => $contrat->locataire->user_id,
                'titre' => 'Contrat créé',
                'message' => "Un contrat de location ({$contrat->numero_contrat}) a été créé pour vous. Paiements initiaux enregistrés: " . number_format($this->total_a_payer, 0, ',', ' ') . " FCFA.",
                'type' => 'contrat',
                'reference_id' => $contrat->id,
                'reference_type' => 'contrat',
            ]);

            // Notification au propriétaire
            \App\Models\Notification::create([
                'user_id' => $proprietaire->user_id,
                'titre' => 'Nouveau contrat créé',
                'message' => "Un contrat ({$contrat->numero_contrat}) a été créé par " . $user->name . " pour votre bien. Paiements initiaux: " . number_format($this->total_a_payer, 0, ',', ' ') . " FCFA.",
                'type' => 'contrat',
                'reference_id' => $contrat->id,
                'reference_type' => 'contrat',
            ]);

            // Notification au créateur (si différent du propriétaire)
            if ($user->id !== $proprietaire->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'titre' => 'Contrat créé',
                    'message' => "Le contrat ({$contrat->numero_contrat}) a été créé avec succès. Total des paiements initiaux: " . number_format($this->total_a_payer, 0, ',', ' ') . " FCFA.",
                    'type' => 'contrat',
                    'reference_id' => $contrat->id,
                    'reference_type' => 'contrat',
                ]);
            }

            DB::commit();

            session()->flash('success', "Le contrat ({$contrat->numero_contrat}) a été créé avec succès ! Total payé: " . number_format($this->total_a_payer, 0, ',', ' ') . " FCFA");
            
            return redirect()->route('contrats.detail', $contrat->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
            
            logger()->error('Erreur création contrat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.contrats.creer-contrat')
            ->layout('layouts.app')
            ->title('Créer un contrat - Wassou');
    }
}