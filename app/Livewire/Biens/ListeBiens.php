<?php

namespace App\Livewire\Biens;

use App\Models\BienImmobilier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListeBiens extends Component
{
    use WithPagination;

    public $search = '';
    public $type_bien_filter = '';
    public $ville_filter = '';
    public $statut_filter = '';
    public $proprietaire_filter = ''; // Pour l'admin
    
    // Pour la suppression
    public $bienToDelete = null;
    public $showDeleteModal = false;

    protected $queryString = [
        'search', 
        'type_bien_filter', 
        'ville_filter', 
        'statut_filter',
        'proprietaire_filter'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeBienFilter()
    {
        $this->resetPage();
    }

    public function updatingVilleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatutFilter()
    {
        $this->resetPage();
    }

    public function updatingProprietaireFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->type_bien_filter = '';
        $this->ville_filter = '';
        $this->statut_filter = '';
        $this->proprietaire_filter = '';
        $this->resetPage();
    }

    public function confirmDeleteBien($bienId)
    {
        $bien = BienImmobilier::find($bienId);
        
        if (!$bien) {
            session()->flash('error', 'Bien non trouvé.');
            return;
        }

        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->isDemarcheur()) {
            $demarcheur = $user->demarcheur;
            if (!$demarcheur->isAuthorizedFor($bien->proprietaire_id) || 
                !$demarcheur->hasPermissionFor($bien->proprietaire_id, 'supprimer_bien')) {
                session()->flash('error', 'Vous n\'avez pas la permission de supprimer ce bien.');
                return;
            }
        } elseif ($user->isProprietaire() && $bien->proprietaire->user_id !== $user->id) {
            session()->flash('error', 'Vous n\'avez pas la permission de supprimer ce bien.');
            return;
        }

        // Vérifier si le bien peut être supprimé
        if (!$bien->canBeDeleted()) {
            session()->flash('error', $bien->getDeletionErrorMessage());
            return;
        }

        $this->bienToDelete = $bienId;
        $this->showDeleteModal = true;
    }

    public function cancelDeleteBien()
    {
        $this->bienToDelete = null;
        $this->showDeleteModal = false;
    }

    public function deleteBien()
    {
        try {
            DB::beginTransaction();

            $bien = BienImmobilier::findOrFail($this->bienToDelete);
            
            // Vérification finale
            if (!$bien->canBeDeleted()) {
                session()->flash('error', $bien->getDeletionErrorMessage());
                $this->showDeleteModal = false;
                return;
            }

            // Supprimer les photos
            if (!empty($bien->photos_generales)) {
                foreach ($bien->photos_generales as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            // Supprimer les documents
            if (!empty($bien->documents)) {
                foreach ($bien->documents as $document) {
                    if (isset($document['path'])) {
                        Storage::disk('public')->delete($document['path']);
                    }
                }
            }

            $titreBien = $bien->titre;
            $bien->delete();

            // Notification
            $user = Auth::user();
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Bien supprimé',
                'message' => "Le bien '{$titreBien}' a été supprimé avec succès.",
                'type' => 'systeme',
            ]);

            // Notifier le propriétaire si c'est un admin ou démarcheur qui supprime
            if (($user->isAdmin() || $user->isDemarcheur()) && $bien->proprietaire->user_id !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $bien->proprietaire->user_id,
                    'titre' => 'Bien supprimé',
                    'message' => "{$user->name} a supprimé votre bien '{$titreBien}'.",
                    'type' => 'systeme',
                ]);
            }

            DB::commit();

            session()->flash('success', "Le bien '{$titreBien}' a été supprimé avec succès.");
            $this->showDeleteModal = false;
            $this->bienToDelete = null;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            $this->showDeleteModal = false;
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = BienImmobilier::with(['proprietaire.user', 'chambres', 'createdBy']);

        // Filtrage selon le rôle
        if ($user->isProprietaire()) {
            // Propriétaire : voir uniquement ses biens
            $query->where('proprietaire_id', $user->proprietaire->id);
        } elseif ($user->isDemarcheur()) {
            // Démarcheur : voir les biens des propriétaires qu'il gère
            $demarcheur = $user->demarcheur;
            $proprietaireIds = $demarcheur->proprietairesActifs()->pluck('proprietaires.id');
            $query->whereIn('proprietaire_id', $proprietaireIds);
        }
        // Admin : voir tous les biens (pas de filtre)

        // Filtres de recherche
        if ($this->search) {
            $query->where(function($q) {
                $q->where('titre', 'like', '%' . $this->search . '%')
                  ->orWhere('ville', 'like', '%' . $this->search . '%')
                  ->orWhere('quartier', 'like', '%' . $this->search . '%')
                  ->orWhere('reference', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->type_bien_filter) {
            $query->where('type_bien', $this->type_bien_filter);
        }

        if ($this->ville_filter) {
            $query->where('ville', $this->ville_filter);
        }

        if ($this->statut_filter) {
            $query->where('statut', $this->statut_filter);
        }

        // Filtre par propriétaire (Admin uniquement)
        if ($user->isAdmin() && $this->proprietaire_filter) {
            $query->where('proprietaire_id', $this->proprietaire_filter);
        }

        $biens = $query->latest()->paginate(10);

        // Récupérer les villes uniques pour le filtre
        $villesQuery = BienImmobilier::query();
        if ($user->isProprietaire()) {
            $villesQuery->where('proprietaire_id', $user->proprietaire->id);
        } elseif ($user->isDemarcheur()) {
            $demarcheur = $user->demarcheur;
            $proprietaireIds = $demarcheur->proprietairesActifs()->pluck('proprietaires.id');
            $villesQuery->whereIn('proprietaire_id', $proprietaireIds);
        }
        $villes = $villesQuery->distinct()->pluck('ville');

        // Récupérer les propriétaires pour le filtre (Admin uniquement)
        $proprietaires = collect();
        if ($user->isAdmin()) {
            $proprietaires = \App\Models\Proprietaire::with('user')
                ->whereHas('biensImmobiliers')
                ->get()
                ->map(function($prop) {
                    return [
                        'id' => $prop->id,
                        'name' => $prop->user->name,
                        'code' => $prop->user->code_unique,
                    ];
                });
        }

        return view('livewire.biens.liste-biens', [
            'biens' => $biens,
            'villes' => $villes,
            'proprietaires' => $proprietaires,
        ])->layout('layouts.app')->title('Liste des Biens - Wassou');
    }
}