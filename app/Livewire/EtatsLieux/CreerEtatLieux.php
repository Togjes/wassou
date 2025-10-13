<?php

namespace App\Livewire\EtatsLieux;

use App\Models\ContratLocation;
use App\Models\EtatLieux as EtatLieuxModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Barryvdh\DomPDF\Facade\Pdf;

class CreerEtatLieux extends Component
{
    use WithFileUploads;

    public $contratId;
    public $type; // 'entree' ou 'sortie'
    public $contrat;
    public $etatLieux;
    public $isEdit = false;

    // Informations générales
    public $date_etat;
    public $observations = '';

    // Photos
    public $photos = [];
    public $existing_photos = [];

    // Équipements standard
    public $equipements = [
        'Portes' => ['etat' => 'bon', 'observations' => ''],
        'Fenêtres' => ['etat' => 'bon', 'observations' => ''],
        'Murs' => ['etat' => 'bon', 'observations' => ''],
        'Sol' => ['etat' => 'bon', 'observations' => ''],
        'Plafond' => ['etat' => 'bon', 'observations' => ''],
        'Électricité' => ['etat' => 'bon', 'observations' => ''],
        'Plomberie' => ['etat' => 'bon', 'observations' => ''],
        'Sanitaires' => ['etat' => 'bon', 'observations' => ''],
    ];

    // Dégâts constatés (pour sortie)
    public $degats = [];
    public $cout_reparations = 0;

    // Signatures
    public $showSignatureModal = false;
    public $signature_type = '';

    protected function rules()
    {
        return [
            'date_etat' => 'required|date',
            'observations' => 'nullable|string',
            'photos.*' => 'nullable|image|max:2048',
            'equipements' => 'required|array',
            'cout_reparations' => 'nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'date_etat.required' => 'La date de l\'état des lieux est requise',
        'photos.*.image' => 'Les fichiers doivent être des images',
        'photos.*.max' => 'Les images ne doivent pas dépasser 2 Mo',
    ];

    public function mount($id, $type)
    {
        $this->contratId = $id;
        $this->type = $type;
        $this->date_etat = now()->format('Y-m-d');

        $this->loadContrat();
        $this->loadEtatLieux();
    }

    public function loadContrat()
    {
        $this->contrat = ContratLocation::with([
            'chambre.bien',
            'proprietaire.user',
            'locataire.user'
        ])->findOrFail($this->contratId);
    }

    public function loadEtatLieux()
    {
        if ($this->type === 'entree') {
            $this->etatLieux = $this->contrat->etatLieuxEntree;
        } else {
            $this->etatLieux = $this->contrat->etatLieuxSortie;
        }

        if ($this->etatLieux) {
            $this->isEdit = true;
            $this->date_etat = $this->etatLieux->date_etat->format('Y-m-d');
            $this->observations = $this->etatLieux->observations;
            $this->equipements = $this->etatLieux->details_equipements ?? $this->equipements;
            $this->existing_photos = $this->etatLieux->photos ?? [];
            $this->degats = $this->etatLieux->degats_constates ?? [];
            $this->cout_reparations = $this->etatLieux->cout_reparations ?? 0;
        } else {
            // Charger les équipements de la chambre
            if ($this->contrat->chambre->equipements) {
                foreach ($this->contrat->chambre->equipements as $equipement) {
                    $this->equipements[$equipement] = ['etat' => 'bon', 'observations' => ''];
                }
            }
        }
    }

    public function addDegat()
    {
        $this->degats[] = [
            'description' => '',
            'cout' => 0,
        ];
    }

    public function removeDegat($index)
    {
        unset($this->degats[$index]);
        $this->degats = array_values($this->degats);
        $this->calculateCoutReparations();
    }

    public function calculateCoutReparations()
    {
        $total = 0;
        foreach ($this->degats as $degat) {
            $total += floatval($degat['cout'] ?? 0);
        }
        $this->cout_reparations = $total;
    }

    public function saveEtatLieux()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Uploader les photos
            $photosPaths = [];
            if ($this->photos) {
                foreach ($this->photos as $photo) {
                    $path = $photo->store('etats_lieux', 'public');
                    $photosPaths[] = $path;
                }
            }

            // Fusionner avec les photos existantes
            $allPhotos = array_merge($this->existing_photos, $photosPaths);

            if ($this->isEdit) {
                // MODIFICATION
                $this->etatLieux->update([
                    'date_etat' => $this->date_etat,
                    'details_equipements' => $this->equipements,
                    'photos' => $allPhotos,
                    'observations' => $this->observations,
                    'degats_constates' => $this->type === 'sortie' ? $this->degats : null,
                    'cout_reparations' => $this->type === 'sortie' ? $this->cout_reparations : 0,
                ]);
            } else {
                // CRÉATION
                $this->etatLieux = EtatLieuxModel::create([
                    'contrat_id' => $this->contratId,
                    'type_etat' => $this->type,
                    'date_etat' => $this->date_etat,
                    'details_equipements' => $this->equipements,
                    'photos' => $allPhotos,
                    'observations' => $this->observations,
                    'degats_constates' => $this->type === 'sortie' ? $this->degats : null,
                    'cout_reparations' => $this->type === 'sortie' ? $this->cout_reparations : 0,
                ]);
            }

            // Notification
            \App\Models\Notification::create([
                'user_id' => Auth::id(),
                'titre' => 'État des lieux ' . ($this->type === 'entree' ? 'd\'entrée' : 'de sortie'),
                'message' => "L'état des lieux " . ($this->type === 'entree' ? 'd\'entrée' : 'de sortie') . " du contrat {$this->contrat->numero_contrat} a été " . ($this->isEdit ? 'modifié' : 'créé') . ".",
                'type' => 'contrat',
                'reference_id' => $this->contrat->id,
                'reference_type' => 'contrat',
            ]);

            DB::commit();

            session()->flash('success', 'L\'état des lieux a été enregistré avec succès.');
            
            // Recharger
            $this->loadEtatLieux();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function removeExistingPhoto($index)
    {
        if (isset($this->existing_photos[$index])) {
            // Supprimer le fichier
            Storage::disk('public')->delete($this->existing_photos[$index]);
            
            // Retirer de la liste
            unset($this->existing_photos[$index]);
            $this->existing_photos = array_values($this->existing_photos);
        }
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
        if (!$this->etatLieux) {
            session()->flash('error', 'Veuillez d\'abord enregistrer l\'état des lieux.');
            return;
        }

        if ($this->signature_type === 'proprietaire') {
            $this->etatLieux->date_signature_proprietaire = now();
        } elseif ($this->signature_type === 'locataire') {
            $this->etatLieux->date_signature_locataire = now();
        }

        $this->etatLieux->save();

        session()->flash('success', 'Signature enregistrée avec succès.');
        $this->showSignatureModal = false;
        $this->signature_type = '';
        
        $this->loadEtatLieux();
    }

    public function downloadPDF()
    {
        if (!$this->etatLieux) {
            session()->flash('error', 'Aucun état des lieux à télécharger.');
            return;
        }

        $pdf = Pdf::loadView('pdf.etat-lieux', [
            'etatLieux' => $this->etatLieux,
            'contrat' => $this->contrat,
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'etat-lieux-' . $this->type . '-' . $this->contrat->numero_contrat . '.pdf');
    }

    public function render()
    {
        return view('livewire.etats-lieux.creer-etat-lieux')
            ->layout('layouts.app')
            ->title('État des Lieux ' . ucfirst($this->type) . ' - Wassou');
    }
}