<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proprietaire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'proprietaires';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'adresse',
        'ville',
        'pays',
        'profession',
        'mobile_money_number',
        'bank_account_info',
        'signature_proprietaire_url',
    ];

    protected $casts = [
        'bank_account_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations existantes
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function biensImmobiliers()
    {
        return $this->hasMany(BienImmobilier::class);
    }

    public function biens()
    {
        return $this->biensImmobiliers();
    }

    public function contratsLocation()
    {
        return $this->hasMany(ContratLocation::class);
    }

    // NOUVELLES RELATIONS POUR LES DÉMARCHEURS
    
    /**
     * Relation Many-to-Many avec les démarcheurs
     */
    public function demarcheurs()
    {
        return $this->belongsToMany(
            Demarcheur::class,
            'proprietaire_demarcheur',
            'proprietaire_id',
            'demarcheur_id'
        )
        ->using(ProprietaireDemarcheur::class)
        ->withPivot(['statut', 'permissions', 'demande_initiee_par', 'date_validation', 'notes'])
        ->withTimestamps();
    }

    /**
     * Démarcheurs actifs uniquement
     */
    public function demarcheursActifs()
    {
        return $this->demarcheurs()->wherePivot('statut', 'actif');
    }

    /**
     * Vérifier si un démarcheur est autorisé
     */
    public function hasDemarcheur($demarcheurId)
    {
        return $this->demarcheursActifs()
            ->where('demarcheurs.id', $demarcheurId)
            ->exists();
    }

    /**
     * Vérifier si un démarcheur a une permission spécifique
     */
    public function demarcheurHasPermission($demarcheurId, $permission)
    {
        $relation = $this->demarcheurs()
            ->where('demarcheurs.id', $demarcheurId)
            ->wherePivot('statut', 'actif')
            ->first();

        if (!$relation) {
            return false;
        }

        $permissions = $relation->pivot->permissions;
        
        // Si pas de permissions définies, toutes sont autorisées
        if (empty($permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Ajouter un démarcheur
     */
    public function ajouterDemarcheur($demarcheurId, array $options = [])
    {
        $data = array_merge([
            'statut' => 'actif',
            'demande_initiee_par' => 'proprietaire',
            'date_validation' => now(),
        ], $options);

        return $this->demarcheurs()->attach($demarcheurId, $data);
    }

    /**
     * Retirer un démarcheur
     */
    public function retirerDemarcheur($demarcheurId)
    {
        return $this->demarcheurs()->detach($demarcheurId);
    }

    /**
     * Suspendre un démarcheur
     */
    public function suspendreDemarcheur($demarcheurId)
    {
        return $this->demarcheurs()->updateExistingPivot($demarcheurId, [
            'statut' => 'suspendu',
        ]);
    }

    /**
     * Réactiver un démarcheur
     */
    public function reactiverDemarcheur($demarcheurId)
    {
        return $this->demarcheurs()->updateExistingPivot($demarcheurId, [
            'statut' => 'actif',
            'date_validation' => now(),
        ]);
    }

    // Scopes existants
    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });
    }

    // Accesseurs existants
    public function getFullNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getTotalBiensAttribute()
    {
        return $this->biensImmobiliers()->count();
    }

    public function getTotalChambresAttribute()
    {
        return $this->biensImmobiliers()
            ->withCount('chambres')
            ->get()
            ->sum('chambres_count');
    }

    public function getContratsActifsAttribute()
    {
        return $this->contratsLocation()
            ->where('statut', 'actif')
            ->count();
    }

    // NOUVEAUX ACCESSEURS
    
    public function getTotalDemarcheursAttribute()
    {
        return $this->demarcheursActifs()->count();
    }
}