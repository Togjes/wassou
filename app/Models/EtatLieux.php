<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtatLieux extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'etats_lieux';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'contrat_id',
        'type_etat',
        'date_etat',
        'details_equipements',
        'photos',
        'observations',
        'degats_constates',
        'cout_reparations',
        'date_signature_locataire',
        'date_signature_proprietaire',
        'document_url',
    ];

    protected $casts = [
        'date_etat' => 'date',
        'details_equipements' => 'array',
        'photos' => 'array',
        'degats_constates' => 'array',
        'cout_reparations' => 'decimal:2',
        'date_signature_locataire' => 'datetime',
        'date_signature_proprietaire' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class, 'contrat_id');
    }

    // Scopes
    public function scopeEntree($query)
    {
        return $query->where('type_etat', 'entree');
    }

    public function scopeSortie($query)
    {
        return $query->where('type_etat', 'sortie');
    }

    public function scopeSigne($query)
    {
        return $query->whereNotNull('date_signature_locataire')
            ->whereNotNull('date_signature_proprietaire');
    }

    // Accesseurs
    public function getEstSigneAttribute()
    {
        return !is_null($this->date_signature_locataire) 
            && !is_null($this->date_signature_proprietaire);
    }

    public function getADesdegatsAttribute()
    {
        return !empty($this->degats_constates) && count($this->degats_constates) > 0;
    }

    public function getNombreDegatsAttribute()
    {
        return is_array($this->degats_constates) ? count($this->degats_constates) : 0;
    }

    // Méthodes
    public function signerParLocataire()
    {
        $this->update([
            'date_signature_locataire' => now(),
        ]);
    }

    public function signerParProprietaire()
    {
        $this->update([
            'date_signature_proprietaire' => now(),
        ]);
    }

    public function ajouterDegat(array $degat)
    {
        $degats = $this->degats_constates ?? [];
        $degats[] = $degat;
        
        $this->update([
            'degats_constates' => $degats,
        ]);
    }
}