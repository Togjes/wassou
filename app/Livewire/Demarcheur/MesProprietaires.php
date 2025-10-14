<?php

namespace App\Livewire\Demarcheur;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MesProprietaires extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $demarcheur = Auth::user()->demarcheur;
        
        $proprietaires = $demarcheur->proprietaires()
            ->withPivot(['statut', 'permissions', 'date_validation', 'notes'])
            ->when($this->search, function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('code_unique', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.mes-proprietaires', [
            'proprietaires' => $proprietaires,
        ])
        ->layout('layouts.app')
        ->title('Mes Propriétaires - Wassou');
    }
}