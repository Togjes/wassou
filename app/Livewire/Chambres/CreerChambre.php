<?php

namespace App\Livewire\Chambres;

use App\Models\BienImmobilier;
use App\Models\Chambre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreerChambre extends Component
{
    use WithFileUploads;

    // Pour la modification
    public $chambreId = null;
    public $isEdit = false;

    // Bien immobilier
    public $bienId;
    public $bien;

    // Informations de base
    public $nom_chambre = '';
    public $description = '';
    public $type_chambre = 'chambre_simple';
    public $surface_m2 = '';
    public $nombre_pieces = 1;

    // Équipements et photos
    public $equipements = [];
    public $photos = [];
    public $existing_photos = [];
    public $photos_to_delete = [];

    // Tarification
    public $loyer_mensuel = '';
    public $avance = '';
    public $prepaye = '';
    public $caution = '';

    // Disponibilité
    public $disponible = true;
    public $statut = 'disponible';

    // Listes des options
    public $types_chambre = [
        'chambre_simple' => 'Chambre Simple',
        'chambre_salon' => 'Chambre avec Salon',
        'magasin' => 'Magasin',
        'bureau' => 'Bureau',
    ];

    public $statuts = [
        'disponible' => 'Disponible',
        'loue' => 'Louée',
        'renovation' => 'En Rénovation',
        'reserve' => 'Réservée',
    ];

    public $equipements_disponibles = [
        'Lit',
        'Armoire',
        'Climatisation',
        'Ventilateur',
        'Télévision',
        'Réfrigérateur',
        'Cuisinière',
        'Douche interne',
        'WC interne',
        'Eau courante',
        'Électricité',
        'Wifi',
        'Balcon',
        'Parking',
    ];

    protected $messages = [
        'nom_chambre.required' => 'Le nom de la chambre est requis',
        'nom_chambre.unique' => 'Ce nom de chambre existe déjà pour ce bien',
        'type_chambre.required' => 'Le type de chambre est requis',
        'loyer_mensuel.required' => 'Le loyer mensuel est requis',
        'loyer_mensuel.numeric' => 'Le loyer doit être un nombre',
        'loyer_mensuel.min' => 'Le loyer doit être supérieur à 0',
    ];

    public function mount($bienId, $chambreId = null)
    {
        $this->bienId = $bienId;
        $this->loadBien();

        if ($chambreId) {
            $this->chambreId = $chambreId;
            $this->isEdit = true;
            $this->loadChambre();
        }
    }

    public function loadBien()
    {
        $this->bien = BienImmobilier::with('proprietaire.user')->findOrFail($this->bienId);
        
        // Vérifier les permissions
        if (!$this->hasAccessToBien()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions spécifiques
        if ($this->isEdit && !$this->canEditChambre()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des chambres pour ce bien');
        }

        if (!$this->isEdit && !$this->canCreateChambre()) {
            abort(403, 'Vous n\'avez pas la permission de créer des chambres pour ce bien');
        }
    }

    /**
     * Vérifier si l'utilisateur a accès au bien
     */
    private function hasAccessToBien()
    {
        $user = Auth::user();
        
        // Admin a accès à tout
        if ($user->isAdmin()) {
            return true;
        }
        
        // Propriétaire a accès à ses biens
        if ($user->isProprietaire() && $this->bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        // Démarcheur a accès aux biens des propriétaires qu'il gère
        if ($user->isDemarcheur() && $user->demarcheur) {
            return $user->demarcheur->isAuthorizedFor($this->bien->proprietaire_id);
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut créer une chambre
     */
    private function canCreateChambre()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire() && $this->bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        if ($user->isDemarcheur()) {
            return $user->demarcheur->hasPermissionFor($this->bien->proprietaire_id, 'creer_chambre');
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut modifier une chambre
     */
    private function canEditChambre()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire() && $this->bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        if ($user->isDemarcheur()) {
            return $user->demarcheur->hasPermissionFor($this->bien->proprietaire_id, 'modifier_chambre');
        }
        
        return false;
    }

    public function loadChambre()
    {
        $chambre = Chambre::where('bien_id', $this->bienId)
            ->findOrFail($this->chambreId);

        $this->nom_chambre = $chambre->nom_chambre;
        $this->description = $chambre->description;
        $this->type_chambre = $chambre->type_chambre;
        $this->surface_m2 = $chambre->surface_m2;
        $this->nombre_pieces = $chambre->nombre_pieces;
        $this->equipements = $chambre->equipements ?? [];
        $this->existing_photos = $chambre->photos ?? [];
        $this->loyer_mensuel = $chambre->loyer_mensuel;
        $this->avance = $chambre->avance;
        $this->prepaye = $chambre->prepaye;
        $this->caution = $chambre->caution;
        $this->disponible = $chambre->disponible;
        $this->statut = $chambre->statut;
    }

    public function deleteExistingPhoto($index)
    {
        if (isset($this->existing_photos[$index])) {
            $this->photos_to_delete[] = $this->existing_photos[$index];
            unset($this->existing_photos[$index]);
            $this->existing_photos = array_values($this->existing_photos);
        }
    }

    public function saveChambre()
    {
        // Vérification finale des permissions
        if ($this->isEdit && !$this->canEditChambre()) {
            session()->flash('error', 'Vous n\'avez pas la permission de modifier cette chambre.');
            return;
        }

        if (!$this->isEdit && !$this->canCreateChambre()) {
            session()->flash('error', 'Vous n\'avez pas la permission de créer une chambre pour ce bien.');
            return;
        }

        $rules = [
            'nom_chambre' => [
                'required',
                'string',
                'max:20',
                'unique:chambres,nom_chambre,' . ($this->isEdit ? $this->chambreId : 'NULL') . ',id,bien_id,' . $this->bienId
            ],
            'type_chambre' => 'required|in:' . implode(',', array_keys($this->types_chambre)),
            'nombre_pieces' => 'required|integer|min:1',
            'surface_m2' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'required|numeric|min:0',
            'avance' => 'nullable|numeric|min:0',
            'prepaye' => 'nullable|numeric|min:0',
            'caution' => 'nullable|numeric|min:0',
            'statut' => 'required|in:' . implode(',', array_keys($this->statuts)),
            'photos.*' => 'nullable|image|max:5120',
        ];

        $this->validate($rules);

        try {
            DB::beginTransaction();

            if ($this->isEdit) {
                // MODIFICATION
                $chambre = Chambre::findOrFail($this->chambreId);
                
                // Supprimer les photos marquées pour suppression
                foreach ($this->photos_to_delete as $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }

                $allPhotos = $this->existing_photos;
            } else {
                // CRÉATION
                $chambre = new Chambre();
                $chambre->bien_id = $this->bienId;
                $allPhotos = [];
            }

            // Gérer les nouvelles photos
            if (!empty($this->photos)) {
                foreach ($this->photos as $photo) {
                    if ($photo) {
                        $path = $photo->store('chambres/photos', 'public');
                        $allPhotos[] = $path;
                    }
                }
            }

            // Mettre à jour les données
            $chambre->nom_chambre = $this->nom_chambre;
            $chambre->description = $this->description;
            $chambre->type_chambre = $this->type_chambre;
            $chambre->surface_m2 = $this->surface_m2 ?: null;
            $chambre->nombre_pieces = $this->nombre_pieces;
            $chambre->equipements = $this->equipements;
            $chambre->photos = $allPhotos;
            $chambre->loyer_mensuel = $this->loyer_mensuel;
            $chambre->avance = $this->avance ?: null;
            $chambre->prepaye = $this->prepaye ?: null;
            $chambre->caution = $this->caution ?: null;
            $chambre->disponible = $this->disponible;
            $chambre->statut = $this->statut;

            $chambre->save();

            $user = Auth::user();
            $proprietaireId = $this->bien->proprietaire->user_id;

            // Notification pour l'utilisateur qui a créé/modifié
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => $this->isEdit ? 'Chambre modifiée' : 'Chambre créée',
                'message' => $this->isEdit 
                    ? "La chambre '{$chambre->nom_chambre}' a été modifiée avec succès."
                    : "La chambre '{$chambre->nom_chambre}' a été créée avec succès.",
                'type' => 'systeme',
                'reference_id' => $chambre->id,
                'reference_type' => 'chambre',
            ]);

            // Notification pour le propriétaire si c'est un admin/démarcheur qui a créé/modifié
            if (($user->isAdmin() || $user->isDemarcheur()) && $proprietaireId !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $proprietaireId,
                    'titre' => $this->isEdit ? 'Chambre modifiée' : 'Chambre créée',
                    'message' => $this->isEdit
                        ? "{$user->name} a modifié la chambre '{$chambre->nom_chambre}' dans votre bien."
                        : "{$user->name} a créé la chambre '{$chambre->nom_chambre}' dans votre bien.",
                    'type' => 'systeme',
                    'reference_id' => $chambre->id,
                    'reference_type' => 'chambre',
                ]);
            }

            DB::commit();

            session()->flash('success', $this->isEdit 
                ? 'La chambre a été modifiée avec succès !'
                : 'La chambre a été créée avec succès !');
            
            return redirect()->route('biens.detail', $this->bienId);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
            
            logger()->error('Erreur création/modification chambre', [
                'error' => $e->getMessage(),
                'bien_id' => $this->bienId,
                'chambre_id' => $this->chambreId ?? null,
                'user_id' => Auth::id()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.chambres.creer-chambre')
            ->layout('layouts.app')
            ->title(($this->isEdit ? 'Modifier' : 'Ajouter') . ' une chambre - Wassou');
    }
}