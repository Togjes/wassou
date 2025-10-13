<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvancePrepayee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'avances_prepayees';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'contrat_id',
        'locataire_id',
        'type_avance',
        'montant_initial',
        'montant_restant',
        'montant_consomme',
        'nb_mois_couverts',
        'date_debut_utilisation',
        'date_fin_prevue',
        'statut',
        'date_epuisement',
        'notes',
    ];

    protected $casts = [
        'montant_initial' => 'decimal:2',
        'montant_restant' => 'decimal:2',
        'montant_consomme' => 'decimal:2',
        'nb_mois_couverts' => 'integer',
        'date_debut_utilisation' => 'date',
        'date_fin_prevue' => 'date',
        'date_epuisement' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class, 'contrat_id');
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function consommations()
    {
        return $this->hasMany(ConsommationAvance::class, 'avance_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'avance_utilisee_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif')
            ->where('montant_restant', '>', 0);
    }

    public function scopeConsomme($query)
    {
        return $query->where('statut', 'consomme');
    }

    public function scopeExpire($query)
    {
        return $query->where('statut', 'expire');
    }

    public function scopeAvanceLoyer($query)
    {
        return $query->where('type_avance', 'avance_loyer');
    }

    public function scopePrepayeLoyer($query)
    {
        return $query->where('type_avance', 'prepaye_loyer');
    }

    // Accesseurs
    public function getPourcentageConsommeAttribute()
    {
        if ($this->montant_initial == 0) return 0;
        return round(($this->montant_consomme / $this->montant_initial) * 100, 2);
    }

    public function getEstEpuiseAttribute()
    {
        return $this->montant_restant <= 0;
    }

    public function getMoisRestantsAttribute()
    {
        if (!$this->date_fin_prevue) return null;
        
        $today = now();
        $finPrevue = $this->date_fin_prevue;
        
        if ($today->greaterThan($finPrevue)) return 0;
        
        return $today->diffInMonths($finPrevue);
    }

    // Méthodes
    public function consommer($montant, $paiementId = null, $description = null)
    {
        if ($montant > $this->montant_restant) {
            throw new \Exception("Le montant à consommer dépasse le montant restant.");
        }

        // Créer l'enregistrement de consommation
        $consommation = $this->consommations()->create([
            'paiement_id' => $paiementId,
            'montant_consomme' => $montant,
            'description' => $description,
        ]);

        // Mettre à jour les montants
        $nouveauMontantRestant = $this->montant_restant - $montant;
        $nouveauMontantConsomme = $this->montant_consomme + $montant;

        $this->update([
            'montant_restant' => $nouveauMontantRestant,
            'montant_consomme' => $nouveauMontantConsomme,
            'statut' => $nouveauMontantRestant <= 0 ? 'consomme' : 'actif',
            'date_epuisement' => $nouveauMontantRestant <= 0 ? now() : null,
        ]);

        // Notifier si l'avance est presque épuisée (moins de 20%)
        if ($this->pourcentage_consomme >= 80 && $this->pourcentage_consomme < 100) {
            $this->notifierAvancePresqueEpuisee();
        }

        // Notifier si l'avance est complètement épuisée
        if ($this->est_epuise) {
            $this->notifierAvanceEpuisee();
        }

        return $consommation;
    }

    protected function notifierAvancePresqueEpuisee()
    {
        // Créer une notification pour le locataire
        Notification::create([
            'user_id' => $this->locataire->user_id,
            'titre' => 'Avance presque épuisée',
            'message' => "Votre {$this->type_avance} est presque épuisée. Il reste {$this->montant_restant} FCFA.",
            'type' => 'avance_epuisee',
            'reference_id' => $this->id,
            'reference_type' => 'avance_prepayee',
        ]);
    }

    protected function notifierAvanceEpuisee()
    {
        // Créer une notification pour le locataire
        Notification::create([
            'user_id' => $this->locataire->user_id,
            'titre' => 'Avance épuisée',
            'message' => "Votre {$this->type_avance} a été complètement consommée.",
            'type' => 'avance_epuisee',
            'reference_id' => $this->id,
            'reference_type' => 'avance_prepayee',
        ]);
    }
}