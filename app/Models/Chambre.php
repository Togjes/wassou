<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Chambre extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'bien_id',
        'nom_chambre',
        'reference', // AJOUT
        'description',
        'surface_m2',
        'nombre_pieces',
        'type_chambre',
        'equipements',
        'photos',
        'loyer_mensuel',
        'avance',
        'prepaye',
        'caution',
        'disponible',
        'statut',
    ];

    protected $casts = [
        'equipements' => 'array',
        'photos' => 'array',
        'loyer_mensuel' => 'decimal:2',
        'avance' => 'decimal:2',
        'prepaye' => 'decimal:2',
        'caution' => 'decimal:2',
        'surface_m2' => 'decimal:2',
        'disponible' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Générer UUID
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            // Générer référence si absente
            if (empty($model->reference)) {
                $model->reference = self::generateReference($model->bien_id);
            }
        });
    }

    /**
     * Générer une référence unique pour la chambre
     * Format: CH-BIENXXX-001
     */
    public static function generateReference($bienId)
    {
        // Obtenir le bien
        $bien = BienImmobilier::find($bienId);
        if (!$bien) {
            return 'CH-' . strtoupper(Str::random(8));
        }

        // Compter les chambres du bien
        $count = self::where('bien_id', $bienId)->count() + 1;
        
        // Créer la référence : CH-{3 premiers caractères du bien ID}-{numéro}
        $bienPrefix = strtoupper(substr($bien->id, 0, 6));
        $reference = 'CH-' . $bienPrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        // Vérifier l'unicité
        while (self::where('reference', $reference)->exists()) {
            $count++;
            $reference = 'CH-' . $bienPrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return $reference;
    }

    // Relations
    public function bien()
    {
        return $this->belongsTo(BienImmobilier::class, 'bien_id');
    }

    public function contrats()
    {
        return $this->hasMany(\App\Models\ContratLocation::class, 'chambre_id');
    }

    public function contratActif()
    {
        return $this->hasOne(\App\Models\ContratLocation::class, 'chambre_id')
            ->where('statut', 'actif')
            ->latest();
    }

    public function isLouee()
    {
        return $this->statut === 'loue' || $this->contrats()->where('statut', 'actif')->exists();
    }

    public function canBeDeleted()
    {
        if ($this->statut === 'loue') {
            return false;
        }

        $hasActiveContracts = $this->contrats()
            ->whereIn('statut', ['actif', 'en_attente'])
            ->exists();

        if ($hasActiveContracts) {
            return false;
        }

        $hasPendingPayments = \App\Models\Paiement::whereIn('contrat_id', function($query) {
            $query->select('id')
                ->from('contrats_location')
                ->where('chambre_id', $this->id);
        })
        ->where('statut', 'en_attente')
        ->exists();

        return !$hasPendingPayments;
    }

    public function getDeletionErrorMessage()
    {
        if ($this->statut === 'loue') {
            return 'Cette chambre est actuellement louée. Résiliez d\'abord le contrat.';
        }

        $contratsActifs = $this->contrats()
            ->whereIn('statut', ['actif', 'en_attente'])
            ->count();

        if ($contratsActifs > 0) {
            return "Cette chambre a {$contratsActifs} contrat(s) actif(s) ou en attente.";
        }

        $paiementsEnAttente = \App\Models\Paiement::whereIn('contrat_id', function($query) {
            $query->select('id')
                ->from('contrats_location')
                ->where('chambre_id', $this->id);
        })
        ->where('statut', 'en_attente')
        ->count();

        if ($paiementsEnAttente > 0) {
            return "Cette chambre a {$paiementsEnAttente} paiement(s) en attente.";
        }

        return null;
    }
    /**
 * Vérifier si la chambre a un contrat actif
 */
public function hasActiveContract()
{
    return $this->contrats()
        ->whereIn('statut', ['actif', 'en_attente'])
        ->exists();
}

/**
 * Obtenir le contrat actif de la chambre
 */
public function getActiveContract()
{
    return $this->contrats()
        ->whereIn('statut', ['actif', 'en_attente'])
        ->latest()
        ->first();
}

/**
 * Relation avec les contrats
 */
// public function contrats()
// {
//     return $this->hasMany(ContratLocation::class, 'chambre_id');
// }

/**
 * Accesseur pour savoir si la chambre est sous contrat
 */
public function getEstSousContratAttribute()
{
    return $this->hasActiveContract();
}

/**
 * Accesseur pour le contrat actif
 */
public function getContratActifAttribute()
{
    return $this->getActiveContract();
}
}