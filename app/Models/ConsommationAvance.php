<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsommationAvance extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'consommation_avances';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'avance_id',
        'paiement_id',
        'montant_consomme',
        'periode_couverte_debut',
        'periode_couverte_fin',
        'description',
    ];

    protected $casts = [
        'montant_consomme' => 'decimal:2',
        'periode_couverte_debut' => 'date',
        'periode_couverte_fin' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function avancePrepayee()
    {
        return $this->belongsTo(AvancePrepayee::class, 'avance_id');
    }

    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    // Accesseurs
    public function getPeriodeCouverteAttribute()
    {
        if (!$this->periode_couverte_debut || !$this->periode_couverte_fin) {
            return null;
        }

        return $this->periode_couverte_debut->format('d/m/Y') . ' - ' . $this->periode_couverte_fin->format('d/m/Y');
    }
}