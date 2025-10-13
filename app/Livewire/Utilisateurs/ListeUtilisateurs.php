<?php

namespace App\Livewire\Utilisateurs;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class ListeUtilisateurs extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $status_filter = '';
    
    // Pour la suppression
    public $userToDelete = null;
    public $showDeleteModal = false;

    protected $queryString = ['search', 'type_filter', 'status_filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->type_filter = '';
        $this->status_filter = '';
        $this->resetPage();
    }

    public function confirmDeleteUser($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            session()->flash('error', 'Utilisateur non trouvé.');
            return;
        }

        // Ne pas permettre de se supprimer soi-même
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }

        $this->userToDelete = $userId;
        $this->showDeleteModal = true;
    }

    public function cancelDeleteUser()
    {
        $this->userToDelete = null;
        $this->showDeleteModal = false;
    }

    public function deleteUser()
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($this->userToDelete);
            
            // Supprimer la photo de profil
            if ($user->profile_image_url) {
                Storage::disk('public')->delete($user->profile_image_url);
            }

            // Supprimer les données associées selon le type
            if ($user->isProprietaire() && $user->proprietaire) {
                // Vérifier s'il a des biens
                $nbBiens = $user->proprietaire->biens()->count();
                if ($nbBiens > 0) {
                    session()->flash('error', "Ce propriétaire a {$nbBiens} bien(s). Supprimez d'abord ses biens.");
                    $this->showDeleteModal = false;
                    return;
                }
                
                if ($user->proprietaire->signature_proprietaire_url) {
                    Storage::disk('public')->delete($user->proprietaire->signature_proprietaire_url);
                }
                $user->proprietaire->delete();
            }

            if ($user->isLocataire() && $user->locataire) {
                // Vérifier s'il a des contrats actifs
                $hasActiveContract = $user->locataire->contrats()->where('statut', 'actif')->exists();
                if ($hasActiveContract) {
                    session()->flash('error', 'Ce locataire a un contrat actif. Résiliez d\'abord le contrat.');
                    $this->showDeleteModal = false;
                    return;
                }
                
                if ($user->locataire->signature_locataire_url) {
                    Storage::disk('public')->delete($user->locataire->signature_locataire_url);
                }
                $user->locataire->delete();
            }

            if ($user->isDemarcheur() && $user->demarcheur) {
                $user->demarcheur->delete();
            }

            $nomUser = $user->full_name;
            $user->delete();

            // Notification
            $authUser = Auth::user();
            \App\Models\Notification::create([
                'user_id' => $authUser->id,
                'titre' => 'Utilisateur supprimé',
                'message' => "L'utilisateur '{$nomUser}' a été supprimé avec succès.",
                'type' => 'systeme',
            ]);

            DB::commit();

            session()->flash('success', "L'utilisateur '{$nomUser}' a été supprimé avec succès.");
            $this->showDeleteModal = false;
            $this->userToDelete = null;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            $this->showDeleteModal = false;
        }
    }

    public function render()
    {
        $query = User::query();

        // Filtres
        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->type_filter) {
            $query->where('user_type', $this->type_filter);
        }

        if ($this->status_filter === 'actif') {
            $query->where('is_active', true);
        } elseif ($this->status_filter === 'inactif') {
            $query->where('is_active', false);
        }

        $users = $query->latest()->paginate(15);

        return view('livewire.utilisateurs.liste-utilisateurs', [
            'users' => $users,
        ])->layout('layouts.app')->title('Gestion des Utilisateurs - Wassou');
    }
}