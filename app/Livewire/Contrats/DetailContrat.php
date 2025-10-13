<?php

namespace App\Livewire\Contrats;

use App\Models\ContratLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class DetailContrat extends Component
{
    public $contratId;
    public $contrat; // Propriété publique
    public $showSignatureModal = false;
    public $signature_type = ''; // 'proprietaire' ou 'locataire'
    public $showTerminateModal = false;
    public $motif_resiliation = '';

    public function mount($id)
    {
        $this->contratId = $id;
        $this->loadContrat();
    }

    public function loadContrat()
    {
        $this->contrat = ContratLocation::with([
            'chambre.bien.proprietaire.user',
            'locataire.user',
            'proprietaire.user',
            'demarcheur.user',
            'paiements' => function($query) {
                $query->latest();
            },
            'avances',
            'etatsLieux',
            'etatLieuxEntree',
            'etatLieuxSortie'
        ])->findOrFail($this->contratId);
    }

    public function openSignatureModal($type)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($type === 'proprietaire' && !$user->isAdmin() && $this->contrat->proprietaire->user_id !== $user->id) {
            session()->flash('error', 'Vous n\'avez pas la permission de signer en tant que propriétaire.');
            return;
        }

        if ($type === 'locataire' && !$user->isAdmin() && $this->contrat->locataire->user_id !== $user->id) {
            session()->flash('error', 'Vous n\'avez pas la permission de signer en tant que locataire.');
            return;
        }

        $this->signature_type = $type;
        $this->showSignatureModal = true;
    }

    public function signer()
    {
        $contrat = ContratLocation::find($this->contratId);
        
        if ($this->signature_type === 'proprietaire') {
            $contrat->date_signature_proprietaire = now();
        } elseif ($this->signature_type === 'locataire') {
            $contrat->date_signature_locataire = now();
        }

        $contrat->save();

        // Si les deux ont signé, passer le contrat en attente
        if ($contrat->isSigne() && $contrat->statut === 'brouillon') {
            $contrat->statut = 'en_attente';
            $contrat->save();
        }

        session()->flash('success', 'Signature enregistrée avec succès.');
        $this->showSignatureModal = false;
        $this->signature_type = '';
        
        // Recharger le contrat
        $this->loadContrat();
    }

    public function activerContrat()
    {
        $contrat = ContratLocation::find($this->contratId);
        
        if (!$contrat->isSigne()) {
            session()->flash('error', 'Le contrat doit être signé par les deux parties avant activation.');
            return;
        }

        $contrat->statut = 'actif';
        $contrat->chambre->statut = 'loue';
        $contrat->chambre->save();
        $contrat->save();

        // Notification
        \App\Models\Notification::create([
            'user_id' => $contrat->locataire->user_id,
            'titre' => 'Contrat activé',
            'message' => "Votre contrat {$contrat->numero_contrat} est maintenant actif.",
            'type' => 'contrat',
            'reference_id' => $contrat->id,
            'reference_type' => 'contrat',
        ]);

        session()->flash('success', 'Le contrat a été activé avec succès.');
        $this->loadContrat();
    }

    public function openTerminateModal()
    {
        $this->showTerminateModal = true;
    }

    public function resilierContrat()
    {
        $this->validate([
            'motif_resiliation' => 'required|string|min:10',
        ], [
            'motif_resiliation.required' => 'Le motif de résiliation est requis',
            'motif_resiliation.min' => 'Le motif doit contenir au moins 10 caractères',
        ]);

        $contrat = ContratLocation::find($this->contratId);
        
        $contrat->statut = 'resilie';
        $contrat->chambre->statut = 'disponible';
        $contrat->chambre->save();
        $contrat->save();

        // Notification
        \App\Models\Notification::create([
            'user_id' => $contrat->locataire->user_id,
            'titre' => 'Contrat résilié',
            'message' => "Votre contrat {$contrat->numero_contrat} a été résilié. Motif: {$this->motif_resiliation}",
            'type' => 'contrat',
            'reference_id' => $contrat->id,
            'reference_type' => 'contrat',
        ]);

        session()->flash('success', 'Le contrat a été résilié avec succès.');
        $this->showTerminateModal = false;
        $this->motif_resiliation = '';
        $this->loadContrat();
    }

    public function downloadPDF()
    {
        $pdf = Pdf::loadView('pdf.contrat', [
            'contrat' => $this->contrat
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'contrat-' . $this->contrat->numero_contrat . '.pdf');
    }

    public function render()
    {
        return view('livewire.contrats.detail-contrat')
            ->layout('layouts.app')
            ->title('Détails du Contrat - Wassou');
    }
}