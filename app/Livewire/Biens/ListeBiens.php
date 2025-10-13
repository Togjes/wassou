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
    
    // Pour la suppression
    public $bienToDelete = null;
    public $showDeleteModal = false;

    protected $queryString = ['search', 'type_bien_filter', 'ville_filter', 'statut_filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->type_bien_filter = '';
        $this->ville_filter = '';
        $this->statut_filter = '';
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
        if (!$user->isAdmin() && $bien->proprietaire->user_id !== $user->id) {
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
        
        $query = BienImmobilier::with(['proprietaire.user', 'chambres']);

        // Si propriétaire, afficher uniquement ses biens
        if ($user->isProprietaire()) {
            $query->where('proprietaire_id', $user->proprietaire->id);
        }

        // Filtres
        if ($this->search) {
            $query->where(function($q) {
                $q->where('titre', 'like', '%' . $this->search . '%')
                  ->orWhere('ville', 'like', '%' . $this->search . '%')
                  ->orWhere('quartier', 'like', '%' . $this->search . '%');
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

        $biens = $query->latest()->paginate(10);

        // Récupérer les villes uniques pour le filtre
        $villes = BienImmobilier::distinct()->pluck('ville');

        return view('livewire.biens.liste-biens', [
            'biens' => $biens,
            'villes' => $villes,
        ])->layout('layouts.app')->title('Liste des Biens - Wassou');
    }
}