<?php

namespace App\Livewire\Chambres;

use App\Models\Chambre;
use App\Models\BienImmobilier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DetailChambre extends Component
{
    public $chambreId;
    public $chambre;
    public $bien;
    public $currentImageIndex = 0;
    public $showDeleteModal = false;
    
    // Permissions
    public $canEdit = false;
    public $canDelete = false;

    public function mount($bienId, $chambreId)
    {
        $this->chambreId = $chambreId;
        $this->loadChambre($bienId);
        $this->checkPermissions();
    }

    public function loadChambre($bienId)
    {
        $this->bien = BienImmobilier::with('proprietaire.user')->findOrFail($bienId);
        
        // Vérifier l'accès au bien
        if (!$this->hasAccessToBien()) {
            abort(403, 'Accès non autorisé à ce bien');
        }

        $this->chambre = Chambre::with(['contrats' => function($query) {
            $query->whereIn('statut', ['actif', 'en_attente']);
        }])
        ->where('bien_id', $bienId)
        ->findOrFail($this->chambreId);
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
     * Vérifier les permissions de l'utilisateur
     */
    private function checkPermissions()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $this->canEdit = true;
            $this->canDelete = true;
            return;
        }
        
        if ($user->isProprietaire() && $this->bien->proprietaire->user_id === $user->id) {
            $this->canEdit = true;
            $this->canDelete = true;
            return;
        }
        
        if ($user->isDemarcheur() && $user->demarcheur) {
            $demarcheur = $user->demarcheur;
            $this->canEdit = $demarcheur->hasPermissionFor($this->bien->proprietaire_id, 'modifier_chambre');
            
            // Pour supprimer une chambre, on vérifie soit 'supprimer_bien' soit on donne le droit si vide
            $permissions = $demarcheur->proprietaires()
                ->where('proprietaires.id', $this->bien->proprietaire_id)
                ->first();
            
            if ($permissions) {
                $permsList = $permissions->pivot->permissions;
                // Si pas de permissions définies (null ou vide), tout est autorisé
                if (empty($permsList)) {
                    $this->canDelete = true;
                } else {
                    // Sinon vérifier la permission spécifique
                    $this->canDelete = in_array('supprimer_bien', $permsList);
                }
            }
        }
    }

    public function selectImage($index)
    {
        $this->currentImageIndex = $index;
    }

    public function confirmDelete()
    {
        // Vérifier la permission de suppression
        if (!$this->canDelete) {
            session()->flash('error', 'Vous n\'avez pas la permission de supprimer cette chambre.');
            return;
        }

        // Vérifier si la chambre peut être supprimée
        if (!$this->chambre->canBeDeleted()) {
            session()->flash('error', $this->chambre->getDeletionErrorMessage());
            return;
        }

        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    public function deleteChambre()
    {
        // Vérification finale des permissions
        if (!$this->canDelete) {
            session()->flash('error', 'Vous n\'avez pas la permission de supprimer cette chambre.');
            $this->showDeleteModal = false;
            return;
        }

        try {
            DB::beginTransaction();

            // Vérifier si la chambre peut être supprimée
            if (!$this->chambre->canBeDeleted()) {
                $errorMessage = $this->chambre->getDeletionErrorMessage();
                session()->flash('error', $errorMessage);
                $this->showDeleteModal = false;
                DB::rollBack();
                return;
            }

            // Supprimer les photos
            if (!empty($this->chambre->photos)) {
                foreach ($this->chambre->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            // Supprimer la chambre
            $nomChambre = $this->chambre->nom_chambre;
            $bienId = $this->bien->id;
            $proprietaireId = $this->bien->proprietaire->user_id;
            
            $this->chambre->delete();

            // Notification pour l'utilisateur qui a supprimé
            $user = Auth::user();
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Chambre supprimée',
                'message' => "La chambre '{$nomChambre}' a été supprimée avec succès.",
                'type' => 'systeme',
                'reference_id' => $bienId,
                'reference_type' => 'bien_immobilier',
            ]);

            // Notification pour le propriétaire si c'est un admin/démarcheur qui supprime
            if (($user->isAdmin() || $user->isDemarcheur()) && $proprietaireId !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $proprietaireId,
                    'titre' => 'Chambre supprimée',
                    'message' => "{$user->name} a supprimé la chambre '{$nomChambre}' de votre bien.",
                    'type' => 'systeme',
                    'reference_id' => $bienId,
                    'reference_type' => 'bien_immobilier',
                ]);
            }

            DB::commit();

            session()->flash('success', 'La chambre a été supprimée avec succès.');
            return redirect()->route('biens.detail', $bienId);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
            $this->showDeleteModal = false;
            
            logger()->error('Erreur suppression chambre', [
                'error' => $e->getMessage(),
                'chambre_id' => $this->chambreId,
                'user_id' => Auth::id()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.chambres.detail-chambre', [
            'canEdit' => $this->canEdit,
            'canDelete' => $this->canDelete,
        ])
            ->layout('layouts.app')
            ->title($this->chambre->nom_chambre . ' - Wassou');
    }
}