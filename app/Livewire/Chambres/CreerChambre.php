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
        $this->bien = BienImmobilier::findOrFail($this->bienId);
        
        // Vérifier les permissions
        $user = Auth::user();
        if (!$user->isAdmin() && $this->bien->proprietaire->user_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
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

            // Notification
            $user = Auth::user();
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

            DB::commit();

            session()->flash('success', $this->isEdit 
                ? 'La chambre a été modifiée avec succès !'
                : 'La chambre a été créée avec succès !');
            
            return redirect()->route('biens.detail', $this->bienId);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.chambres.creer-chambre')
            ->layout('layouts.app')
            ->title(($this->isEdit ? 'Modifier' : 'Ajouter') . ' une chambre - Wassou');
    }
}