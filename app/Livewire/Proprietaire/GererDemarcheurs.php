<?php

namespace App\Livewire\Proprietaire;

use App\Models\Demarcheur;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class GererDemarcheurs extends Component
{
    use WithPagination;

    public $code_demarcheur = '';
    public $showAddModal = false;
    public $showPermissionsModal = false;
    
    // Pour la gestion des permissions
    public $demarcheur_en_cours = null;
    public $permissions_selectionnees = [];
    
    // Liste des permissions disponibles
    public $permissions_disponibles = [
        'creer_bien' => 'Créer des biens immobiliers',
        'modifier_bien' => 'Modifier des biens immobiliers',
        'supprimer_bien' => 'Supprimer des biens immobiliers',
        'creer_chambre' => 'Créer des chambres',
        'modifier_chambre' => 'Modifier des chambres',
        'creer_contrat' => 'Créer des contrats',
        'modifier_contrat' => 'Modifier des contrats',
        'gerer_paiements' => 'Gérer les paiements',
    ];

    public $notes = '';

    protected $messages = [
        'code_demarcheur.required' => 'Le code du démarcheur est requis',
    ];

    public function ajouterDemarcheur()
    {
        $this->validate([
            'code_demarcheur' => 'required|string',
        ]);

        try {
            $user = User::where('code_unique', $this->code_demarcheur)->first();

            if (!$user) {
                session()->flash('error_modal', 'Aucun utilisateur trouvé avec ce code.');
                return;
            }

            if (!$user->hasRole('demarcheur')) {
                session()->flash('error_modal', "Cet utilisateur (Code: {$user->code_unique}) n'est pas un démarcheur.");
                return;
            }

            $demarcheur = $user->demarcheur;
            $proprietaire = Auth::user()->proprietaire;

            // Vérifier si la relation existe déjà
            if ($proprietaire->hasDemarcheur($demarcheur->id)) {
                session()->flash('error_modal', 'Ce démarcheur est déjà associé à votre compte.');
                return;
            }

            DB::beginTransaction();

            // Ajouter le démarcheur avec toutes les permissions par défaut
            $proprietaire->ajouterDemarcheur($demarcheur->id, [
                'statut' => 'actif',
                'permissions' => [], // Vide = toutes les permissions
                'demande_initiee_par' => 'proprietaire',
                'notes' => $this->notes,
            ]);

            // Notification pour le démarcheur
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'titre' => 'Nouveau propriétaire',
                'message' => Auth::user()->name . " vous a ajouté comme gestionnaire de ses biens immobiliers.",
                'type' => 'systeme',
                'reference_id' => $proprietaire->id,
                'reference_type' => 'proprietaire',
            ]);

            // Notification pour le propriétaire
            \App\Models\Notification::create([
                'user_id' => Auth::id(),
                'titre' => 'Démarcheur ajouté',
                'message' => "Vous avez ajouté {$user->name} comme gestionnaire de vos biens.",
                'type' => 'systeme',
                'reference_id' => $demarcheur->id,
                'reference_type' => 'demarcheur',
            ]);

            DB::commit();

            session()->flash('success', 'Démarcheur ajouté avec succès !');
            $this->reset(['code_demarcheur', 'notes']);
            $this->showAddModal = false;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function ouvrirModalPermissions($demarcheurId)
    {
        try {
            $proprietaire = Auth::user()->proprietaire;
            $relation = $proprietaire->demarcheurs()->where('demarcheurs.id', $demarcheurId)->first();

            if ($relation) {
                $this->demarcheur_en_cours = $relation;
                $this->permissions_selectionnees = is_array($relation->pivot->permissions) ? $relation->pivot->permissions : [];
                $this->showPermissionsModal = true;
                
                // Log pour debug
                logger('Modal ouvert', [
                    'demarcheur_id' => $demarcheurId,
                    'demarcheur_nom' => $relation->user->name,
                    'permissions_actuelles' => $this->permissions_selectionnees,
                    'showPermissionsModal' => $this->showPermissionsModal
                ]);
            } else {
                session()->flash('error', 'Démarcheur introuvable.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ouverture du modal : ' . $e->getMessage());
            logger('Erreur ouverture modal', ['error' => $e->getMessage()]);
        }
    }

    public function fermerModalPermissions()
    {
        $this->showPermissionsModal = false;
        $this->reset(['demarcheur_en_cours', 'permissions_selectionnees']);
    }

    public function sauvegarderPermissions()
    {
        if (!$this->demarcheur_en_cours) {
            session()->flash('error', 'Démarcheur non sélectionné.');
            return;
        }

        try {
            DB::beginTransaction();

            $proprietaire = Auth::user()->proprietaire;
            
            $proprietaire->demarcheurs()->updateExistingPivot(
                $this->demarcheur_en_cours->id,
                [
                    'permissions' => $this->permissions_selectionnees,
                    'updated_at' => now(),
                ]
            );

            // Notification pour le démarcheur
            \App\Models\Notification::create([
                'user_id' => $this->demarcheur_en_cours->user_id,
                'titre' => 'Permissions mises à jour',
                'message' => Auth::user()->name . " a modifié vos permissions d'accès.",
                'type' => 'systeme',
                'reference_id' => $proprietaire->id,
                'reference_type' => 'proprietaire',
            ]);

            DB::commit();

            session()->flash('success', 'Permissions mises à jour avec succès !');
            $this->showPermissionsModal = false;
            $this->reset(['demarcheur_en_cours', 'permissions_selectionnees']);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function suspendre($demarcheurId)
    {
        try {
            DB::beginTransaction();

            $proprietaire = Auth::user()->proprietaire;
            $demarcheur = Demarcheur::findOrFail($demarcheurId);
            
            $proprietaire->suspendreDemarcheur($demarcheurId);

            // Notification
            \App\Models\Notification::create([
                'user_id' => $demarcheur->user_id,
                'titre' => 'Accès suspendu',
                'message' => Auth::user()->name . " a suspendu votre accès à ses biens.",
                'type' => 'systeme',
                'reference_id' => $proprietaire->id,
                'reference_type' => 'proprietaire',
            ]);

            DB::commit();

            session()->flash('success', 'Démarcheur suspendu avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur : ' . $e->getMessage());
            logger('Erreur suspension', ['error' => $e->getMessage()]);
        }
    }

    public function reactiver($demarcheurId)
    {
        try {
            DB::beginTransaction();

            $proprietaire = Auth::user()->proprietaire;
            $demarcheur = Demarcheur::findOrFail($demarcheurId);
            
            $proprietaire->reactiverDemarcheur($demarcheurId);

            // Notification
            \App\Models\Notification::create([
                'user_id' => $demarcheur->user_id,
                'titre' => 'Accès réactivé',
                'message' => Auth::user()->name . " a réactivé votre accès à ses biens.",
                'type' => 'systeme',
                'reference_id' => $proprietaire->id,
                'reference_type' => 'proprietaire',
            ]);

            DB::commit();

            session()->flash('success', 'Démarcheur réactivé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur : ' . $e->getMessage());
            logger('Erreur réactivation', ['error' => $e->getMessage()]);
        }
    }

    public function retirer($demarcheurId)
    {
        try {
            DB::beginTransaction();

            $proprietaire = Auth::user()->proprietaire;
            $demarcheur = Demarcheur::findOrFail($demarcheurId);
            
            // Notification avant suppression
            \App\Models\Notification::create([
                'user_id' => $demarcheur->user_id,
                'titre' => 'Accès retiré',
                'message' => Auth::user()->name . " vous a retiré de la liste de ses gestionnaires.",
                'type' => 'systeme',
                'reference_id' => $proprietaire->id,
                'reference_type' => 'proprietaire',
            ]);

            $proprietaire->retirerDemarcheur($demarcheurId);

            DB::commit();

            session()->flash('success', 'Démarcheur retiré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur : ' . $e->getMessage());
            logger('Erreur retrait', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $proprietaire = Auth::user()->proprietaire;
        
        $demarcheurs = $proprietaire->demarcheurs()
            ->withPivot(['statut', 'permissions', 'date_validation', 'notes'])
            ->paginate(10);

        return view('livewire.gerer-demarcheurs', [
            'demarcheurs' => $demarcheurs,
        ])
        ->layout('layouts.app')
        ->title('Gérer mes démarcheurs - Wassou');
    }
}