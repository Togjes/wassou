<?php

namespace App\Livewire\Contrats;

use App\Models\ContratLocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListeContrats extends Component
{
    use WithPagination;

    public $search = '';
    public $statut_filter = '';
    public $bien_filter = '';

    protected $queryString = ['search', 'statut_filter', 'bien_filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statut_filter = '';
        $this->bien_filter = '';
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $query = ContratLocation::with([
            'chambre.bien',
            'proprietaire.user',
            'locataire.user',
            'demarcheur.user'
        ]);

        // Filtrage selon le rôle
        if ($user->isProprietaire()) {
            $query->where('proprietaire_id', $user->proprietaire->id);
        } elseif ($user->isLocataire()) {
            $query->where('locataire_id', $user->locataire->id);
        } elseif ($user->isDemarcheur()) {
            $query->where('demarcheur_id', $user->demarcheur->id);
        }

        // Filtres de recherche
        if ($this->search) {
            $query->where(function($q) {
                $q->where('numero_contrat', 'like', '%' . $this->search . '%')
                  ->orWhereHas('locataire.user', function($q2) {
                      $q2->where('first_name', 'like', '%' . $this->search . '%')
                         ->orWhere('last_name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('chambre.bien', function($q2) {
                      $q2->where('titre', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statut_filter) {
            $query->where('statut', $this->statut_filter);
        }

        if ($this->bien_filter) {
            $query->whereHas('chambre', function($q) {
                $q->where('bien_id', $this->bien_filter);
            });
        }

        $contrats = $query->latest()->paginate(15);

        // Pour le filtre des biens (si propriétaire)
        $biens = collect();
        if ($user->isProprietaire()) {
            $biens = $user->proprietaire->biens;
        }

        return view('livewire.contrats.liste-contrats', [
            'contrats' => $contrats,
            'biens' => $biens,
        ])->layout('layouts.app')->title('Liste des Contrats - Wassou');
    }
}