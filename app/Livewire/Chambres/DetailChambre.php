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

    public function mount($bienId, $chambreId)
    {
        $this->chambreId = $chambreId;
        $this->loadChambre($bienId);
    }

    public function loadChambre($bienId)
    {
        $this->bien = BienImmobilier::findOrFail($bienId);
        
        // Vérifier les permissions
        $user = Auth::user();
        if (!$user->isAdmin() && $this->bien->proprietaire->user_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $this->chambre = Chambre::with(['contrats' => function($query) {
            $query->whereIn('statut', ['actif', 'en_attente']);
        }])
        ->where('bien_id', $bienId)
        ->findOrFail($this->chambreId);
    }

    public function selectImage($index)
    {
        $this->currentImageIndex = $index;
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    public function deleteChambre()
    {
        try {
            DB::beginTransaction();

            // Vérifier si la chambre peut être supprimée
            if (!$this->chambre->canBeDeleted()) {
                $errorMessage = $this->chambre->getDeletionErrorMessage();
                session()->flash('error', $errorMessage);
                $this->showDeleteModal = false;
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
            $this->chambre->delete();

            // Notification
            $user = Auth::user();
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Chambre supprimée',
                'message' => "La chambre '{$nomChambre}' a été supprimée avec succès.",
                'type' => 'systeme',
                'reference_id' => $this->bien->id,
                'reference_type' => 'bien_immobilier',
            ]);

            DB::commit();

            session()->flash('success', 'La chambre a été supprimée avec succès.');
            return redirect()->route('biens.detail', $this->bien->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
            $this->showDeleteModal = false;
        }
    }

    public function render()
    {
        return view('livewire.chambres.detail-chambre')
            ->layout('layouts.app')
            ->title($this->chambre->nom_chambre . ' - Wassou');
    }
}