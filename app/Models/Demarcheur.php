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

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contratsLocation()
    {
        return $this->hasMany(ContratLocation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });
    }

    // Accesseurs
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
        // Calculer les commissions à partir des paiements de type commission
        return $this->contratsLocation()
            ->with('paiements')
            ->get()
            ->flatMap(fn($contrat) => $contrat->paiements)
            ->where('type_paiement', 'commission')
            ->where('statut', 'paye')
            ->sum('montant');
    }
}