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

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function biensImmobiliers()
    {
        return $this->hasMany(BienImmobilier::class);
    }

    // ⚠️ AJOUT : Alias pour la relation biens (utilisé dans DetailUtilisateur)
    public function biens()
    {
        return $this->biensImmobiliers();
    }

    public function contratsLocation()
    {
        return $this->hasMany(ContratLocation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });
    }

    // Accesseurs
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
}