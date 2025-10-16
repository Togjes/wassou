<?php

namespace App\Livewire\Biens;

use App\Models\BienImmobilier;
use App\Models\Chambre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailBien extends Component
{
    public $bienId;
    public $currentImageIndex = 0;
    
    // Pour la suppression de chambre
    public $chambreToDelete = null;
    public $showDeleteModal = false;

    public function mount($id)
    {
        $this->bienId = $id;
    }

    public function getBienProperty()
    {
        return BienImmobilier::with([
            'proprietaire.user',
            'chambres' => function($query) {
                $query->orderBy('nom_chambre');
            },
            'createdBy' // Ajouter la relation du créateur
        ])->findOrFail($this->bienId);
    }

    public function selectImage($index)
    {
        $this->currentImageIndex = $index;
    }

    /**
     * Vérifier si l'utilisateur a accès au bien
     */
    private function hasAccessToBien($bien)
    {
        $user = Auth::user();
        
        // Admin a accès à tout
        if ($user->isAdmin()) {
            return true;
        }
        
        // Propriétaire a accès à ses biens
        if ($user->isProprietaire() && $bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        // Démarcheur a accès aux biens des propriétaires qu'il gère
        if ($user->isDemarcheur() && $user->demarcheur) {
            // ✅ CORRECTION : Utiliser isAuthorizedFor au lieu de biens()
            return $user->demarcheur->isAuthorizedFor($bien->proprietaire_id);
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut modifier le bien
     */
    private function canEditBien($bien)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire() && $bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        if ($user->isDemarcheur()) {
            return $user->demarcheur->hasPermissionFor($bien->proprietaire_id, 'modifier_bien');
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut supprimer une chambre
     */
    private function canDeleteChambre($bien)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire() && $bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        if ($user->isDemarcheur()) {
            return $user->demarcheur->hasPermissionFor($bien->proprietaire_id, 'supprimer_bien');
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut ajouter une chambre
     */
    private function canAddChambre($bien)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire() && $bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        if ($user->isDemarcheur()) {
            return $user->demarcheur->hasPermissionFor($bien->proprietaire_id, 'creer_chambre');
        }
        
        return false;
    }

    public function confirmDeleteChambre($chambreId)
    {
        $chambre = Chambre::find($chambreId);
        
        if (!$chambre) {
            session()->flash('error', 'Chambre introuvable.');
            return;
        }

        // Vérifier l'accès au bien
        if (!$this->hasAccessToBien($chambre->bien)) {
            session()->flash('error', 'Accès non autorisé.');
            return;
        }

        // Vérifier la permission de suppression
        if (!$this->canDeleteChambre($chambre->bien)) {
            session()->flash('error', 'Vous n\'avez pas la permission de supprimer des chambres pour ce bien.');
            return;
        }

        // Vérifier si la chambre peut être supprimée
        if (!$chambre->canBeDeleted()) {
            session()->flash('error', $chambre->getDeletionErrorMessage());
            return;
        }

        $this->chambreToDelete = $chambreId;
        $this->showDeleteModal = true;
    }

    public function cancelDeleteChambre()
    {
        $this->chambreToDelete = null;
        $this->showDeleteModal = false;
    }

    public function deleteChambre()
    {
        try {
            DB::beginTransaction();

            $chambre = Chambre::findOrFail($this->chambreToDelete);
            
            // Vérification finale de l'accès
            if (!$this->hasAccessToBien($chambre->bien)) {
                session()->flash('error', 'Accès non autorisé.');
                $this->showDeleteModal = false;
                DB::rollBack();
                return;
            }

            // Vérifier la permission
            if (!$this->canDeleteChambre($chambre->bien)) {
                session()->flash('error', 'Vous n\'avez pas la permission de supprimer des chambres pour ce bien.');
                $this->showDeleteModal = false;
                DB::rollBack();
                return;
            }
            
            // Vérification si la chambre peut être supprimée
            if (!$chambre->canBeDeleted()) {
                session()->flash('error', $chambre->getDeletionErrorMessage());
                $this->showDeleteModal = false;
                DB::rollBack();
                return;
            }

            // Supprimer les photos
            if (!empty($chambre->photos)) {
                foreach ($chambre->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            $user = Auth::user();
            $nomChambre = $chambre->nom_chambre;
            $proprietaireId = $chambre->bien->proprietaire->user_id;
            
            $chambre->delete();

            // Notification pour l'utilisateur qui a supprimé
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Chambre supprimée',
                'message' => "La chambre '{$nomChambre}' a été supprimée avec succès.",
                'type' => 'systeme',
                'reference_id' => $this->bienId,
                'reference_type' => 'bien_immobilier',
            ]);

            // Notification pour le propriétaire si c'est un admin/démarcheur qui supprime
            if (($user->isAdmin() || $user->isDemarcheur()) && $proprietaireId !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $proprietaireId,
                    'titre' => 'Chambre supprimée',
                    'message' => "La chambre '{$nomChambre}' de votre bien a été supprimée par " . $user->name . ".",
                    'type' => 'systeme',
                    'reference_id' => $this->bienId,
                    'reference_type' => 'bien_immobilier',
                ]);
            }

            DB::commit();

            session()->flash('success', "La chambre '{$nomChambre}' a été supprimée avec succès.");
            $this->showDeleteModal = false;
            $this->chambreToDelete = null;

            // Rafraîchir la page pour recharger les données
            $this->dispatch('chambreDeleted');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            $this->showDeleteModal = false;
            logger()->error('Erreur suppression chambre', [
                'error' => $e->getMessage(),
                'chambre_id' => $this->chambreToDelete
            ]);
        }
    }

    public function render()
    {
        $bien = $this->bien;
        // dd($bien);
        
        // Vérifier l'accès au bien
        if (!$this->hasAccessToBien($bien)) {
            abort(403, 'Vous n\'avez pas accès à ce bien immobilier.');
        }

        // Passer les permissions à la vue
        $canEdit = $this->canEditBien($bien);
        $canAddChambre = $this->canAddChambre($bien);
        $canDeleteChambre = $this->canDeleteChambre($bien);

        return view('livewire.biens.detail-bien', [
            'bien' => $bien,
            'canEdit' => $canEdit,
            'canAddChambre' => $canAddChambre,
            'canDeleteChambre' => $canDeleteChambre,
        ])
            ->layout('layouts.app')
            ->title($bien->titre . ' - Wassou');
    }
}