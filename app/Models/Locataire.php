<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locataire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'locataires';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'adresse_actuelle',
        'profession',
        'salaire_mensuel',
        'mobile_money_number',
        'contact_urgence',
        'signature_locataire_url',
    ];

    protected $casts = [
        'salaire_mensuel' => 'decimal:2',
        'contact_urgence' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contratsLocation()
    {
        return $this->hasMany(ContratLocation::class);
    }

    // AJOUT DE L'ALIAS
    public function contrats()
    {
        return $this->contratsLocation();
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function avancesPrepayees()
    {
        return $this->hasMany(AvancePrepayee::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });
    }

    public function scopeWithContratActif($query)
    {
        return $query->whereHas('contratsLocation', function ($q) {
            $q->where('statut', 'actif');
        });
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getContratActifAttribute()
    {
        return $this->contratsLocation()
            ->where('statut', 'actif')
            ->first();
    }

    public function getTotalPaiementsAttribute()
    {
        return $this->paiements()
            ->where('statut', 'paye')
            ->sum('montant');
    }

    public function getPaiementsEnRetardAttribute()
    {
        return $this->paiements()
            ->where('statut', 'en_retard')
            ->count();
    }

    public function hasActiveContract()
    {
        return $this->contratsLocation()
            ->whereIn('statut', ['actif', 'en_attente'])
            ->exists();
    }
}