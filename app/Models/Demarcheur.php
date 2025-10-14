<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Demarcheur extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'demarcheurs';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'default_mobile_money_number',
        'bank_account_info',
        'is_active',
    ];

    protected $casts = [
        'bank_account_info' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations existantes
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contratsLocation()
    {
        return $this->hasMany(ContratLocation::class);
    }

    // NOUVELLES RELATIONS POUR LES PROPRIÉTAIRES
    
    /**
     * Relation Many-to-Many avec les propriétaires
     */
    public function proprietaires()
    {
        return $this->belongsToMany(
            Proprietaire::class,
            'proprietaire_demarcheur',
            'demarcheur_id',
            'proprietaire_id'
        )
        ->using(ProprietaireDemarcheur::class)
        ->withPivot(['statut', 'permissions', 'demande_initiee_par', 'date_validation', 'notes'])
        ->withTimestamps();
    }

    /**
     * Propriétaires actifs uniquement
     */
    public function proprietairesActifs()
    {
        return $this->proprietaires()->wherePivot('statut', 'actif');
    }

    /**
     * Vérifier si le démarcheur est autorisé pour un propriétaire
     */
    public function isAuthorizedFor($proprietaireId)
    {
        return $this->proprietairesActifs()
            ->where('proprietaires.id', $proprietaireId)
            ->exists();
    }

    /**
     * Vérifier une permission spécifique pour un propriétaire
     */
    public function hasPermissionFor($proprietaireId, $permission)
    {
        $relation = $this->proprietaires()
            ->where('proprietaires.id', $proprietaireId)
            ->wherePivot('statut', 'actif')
            ->first();

        if (!$relation) {
            return false;
        }

        $permissions = $relation->pivot->permissions;
        
        if (empty($permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Obtenir tous les biens des propriétaires gérés
     */
    public function biensGeres()
    {
        $proprietaireIds = $this->proprietairesActifs()->pluck('proprietaires.id');
        
        return BienImmobilier::whereIn('proprietaire_id', $proprietaireIds);
    }

    // Scopes existants
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });
    }

    // Accesseurs existants
    public function getFullNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getTotalContratsAttribute()
    {
        return $this->contratsLocation()->count();
    }

    public function getCommissionsGagneesAttribute()
    {
        return $this->contratsLocation()
            ->with('paiements')
            ->get()
            ->flatMap(fn($contrat) => $contrat->paiements)
            ->where('type_paiement', 'commission')
            ->where('statut', 'paye')
            ->sum('montant');
    }

    // NOUVEAUX ACCESSEURS
    
    public function getTotalProprietairesAttribute()
    {
        return $this->proprietairesActifs()->count();
    }

    public function getTotalBiensGeresAttribute()
    {
        return $this->biensGeres()->count();
    }
}