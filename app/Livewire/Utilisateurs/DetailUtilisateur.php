<?php

namespace App\Livewire\Utilisateurs;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailUtilisateur extends Component
{
    public $userId;
    public $user; // Propriété publique

    public function mount($id)
    {
        $this->userId = $id;
        $this->loadUser();
    }

    public function loadUser()
    {
        $this->user = User::with([
            'proprietaire.biens.chambres',
            'locataire.contrats.chambre.bien',
            'locataire.paiements',
            'demarcheur'
        ])->findOrFail($this->userId);
    }

    public function toggleStatus()
    {
        if ($this->user->id === Auth::id()) {
            session()->flash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return;
        }

        $this->user->is_active = !$this->user->is_active;
        $this->user->save();

        session()->flash('success', 'Le statut de l\'utilisateur a été modifié avec succès.');
        
        // Recharger l'utilisateur après modification
        $this->loadUser();
    }

    public function render()
    {
        return view('livewire.utilisateurs.detail-utilisateur')
            ->layout('layouts.app')
            ->title('Détails de ' . $this->user->full_name . ' - Wassou');
    }
}