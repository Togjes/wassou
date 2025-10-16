<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BienImmobilier extends Model
{
    use SoftDeletes;

    protected $table = 'biens_immobiliers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'proprietaire_id',
        'reference',
        'titre',
        'description',
        'type_bien',
        'ville',
        'quartier',
        'adresse',
        'annee_construction',
        'equipements_communs',
        'photos_generales',
        'documents',
        'moyens_paiement_acceptes',
        'details_paiement',
        'statut',
        'created_by_user_id',
        'created_by_type',
    ];

    protected $casts = [
        'equipements_communs' => 'array',
        'photos_generales' => 'array',
        'documents' => 'array',
        'moyens_paiement_acceptes' => 'array',
        'details_paiement' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            if (empty($model->reference)) {
                $model->reference = self::generateReference($model->proprietaire_id);
            }
        });
    }

    public static function generateReference($proprietaireId)
    {
        $proprietaire = \App\Models\Proprietaire::find($proprietaireId);
        if (!$proprietaire) {
            return 'BIEN-' . strtoupper(Str::random(8));
        }

        $count = self::where('proprietaire_id', $proprietaireId)->count() + 1;
        $propPrefix = strtoupper(substr($proprietaire->id, 0, 6));
        $reference = 'BIEN-' . $propPrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        while (self::where('reference', $reference)->exists()) {
            $count++;
            $reference = 'BIEN-' . $propPrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return $reference;
    }

    // Relations
    public function proprietaire()
    {
        return $this->belongsTo(\App\Models\Proprietaire::class, 'proprietaire_id');
    }

    public function chambres()
    {
        return $this->hasMany(\App\Models\Chambre::class, 'bien_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    // Accesseurs
    public function getTauxOccupationAttribute()
    {
        $totalChambres = $this->chambres->count();
        if ($totalChambres === 0) return 0;
        
        $chambresLouees = $this->chambres->where('statut', 'loue')->count();
        return round(($chambresLouees / $totalChambres) * 100);
    }

    public function getChambresLoueesAttribute()
    {
        return $this->chambres->where('statut', 'loue')->count();
    }

    public function getCreatedByNameAttribute()
    {
        if (!$this->createdBy) {
            return 'Système';
        }
        return $this->createdBy->name;
    }

    public function getCreatedByRoleAttribute()
    {
        if (!$this->created_by_type) {
            return null;
        }
        
        return match($this->created_by_type) {
            'admin' => 'Administrateur',
            'proprietaire' => 'Propriétaire',
            'demarcheur' => 'Démarcheur',
            default => 'Système',
        };
    }

    public function canBeDeleted()
    {
        if ($this->chambres()->count() > 0) {
            return false;
        }

        $hasActiveContracts = \App\Models\ContratLocation::whereIn('chambre_id', function($query) {
            $query->select('id')
                ->from('chambres')
                ->where('bien_id', $this->id);
        })
        ->whereIn('statut', ['actif', 'en_attente'])
        ->exists();

        return !$hasActiveContracts;
    }

    public function getDeletionErrorMessage()
    {
        $nombreChambres = $this->chambres()->count();
        
        if ($nombreChambres > 0) {
            return "Ce bien possède {$nombreChambres} chambre(s). Supprimez d'abord toutes les chambres.";
        }

        $contratsActifs = \App\Models\ContratLocation::whereIn('chambre_id', function($query) {
            $query->select('id')
                ->from('chambres')
                ->where('bien_id', $this->id);
        })
        ->whereIn('statut', ['actif', 'en_attente'])
        ->count();

        if ($contratsActifs > 0) {
            return "Ce bien a {$contratsActifs} contrat(s) actif(s) ou en attente.";
        }

        return null;
    }
}