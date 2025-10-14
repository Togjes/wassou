<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProprietaireDemarcheur extends Pivot
{
    use HasUuids, SoftDeletes;

    protected $table = 'proprietaire_demarcheur';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'proprietaire_id',
        'demarcheur_id',
        'statut',
        'permissions',
        'demande_initiee_par',
        'date_validation',
        'notes',
    ];

    protected $casts = [
        'permissions' => 'array',
        'date_validation' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function demarcheur()
    {
        return $this->belongsTo(Demarcheur::class);
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeSuspendu($query)
    {
        return $query->where('statut', 'suspendu');
    }

    // Méthodes helper
    public function isActif()
    {
        return $this->statut === 'actif';
    }

    public function hasPermission($permission)
    {
        if (empty($this->permissions)) {
            return true; // Toutes les permissions par défaut
        }
        
        return in_array($permission, $this->permissions);
    }

    public function activer()
    {
        $this->update([
            'statut' => 'actif',
            'date_validation' => now(),
        ]);
    }

    public function suspendre()
    {
        $this->update(['statut' => 'suspendu']);
    }
}