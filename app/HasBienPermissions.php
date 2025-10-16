<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasBienPermissions
{
    /**
     * Vérifier si l'utilisateur a accès au bien
     */
    protected function hasAccessToBien($bien)
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
            return $user->demarcheur->isAuthorizedFor($bien->proprietaire_id);
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut créer un bien
     */
    protected function canCreateBien($proprietaireId = null)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire()) {
            return $proprietaireId === null || $proprietaireId === $user->proprietaire->id;
        }
        
        if ($user->isDemarcheur() && $proprietaireId) {
            return $user->demarcheur->hasPermissionFor($proprietaireId, 'creer_bien');
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut modifier un bien
     */
    protected function canEditBien($bien)
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
     * Vérifier si l'utilisateur peut supprimer un bien
     */
    protected function canDeleteBien($bien)
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
     * Vérifier si l'utilisateur peut créer une chambre
     */
    protected function canCreateChambre($bien)
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

    /**
     * Vérifier si l'utilisateur peut modifier une chambre
     */
    protected function canEditChambre($bien)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire() && $bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        if ($user->isDemarcheur()) {
            return $user->demarcheur->hasPermissionFor($bien->proprietaire_id, 'modifier_chambre');
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut supprimer une chambre
     */
    protected function canDeleteChambre($bien)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isProprietaire() && $bien->proprietaire->user_id === $user->id) {
            return true;
        }
        
        if ($user->isDemarcheur()) {
            // Pour supprimer une chambre, on utilise la même permission que supprimer un bien
            return $user->demarcheur->hasPermissionFor($bien->proprietaire_id, 'supprimer_bien');
        }
        
        return false;
    }
}