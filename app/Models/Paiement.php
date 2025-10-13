<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'paiements';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'contrat_id',
        'locataire_id',
        'numero_facture',
        'numero_recu',
        'type_paiement',
        'montant',
        'periode_debut',
        'periode_fin',
        'mois_concerne',
        'date_echeance',
        'date_paiement',
        'methode_paiement',
        'details_paiement',
        'reference_transaction',
        'frais_transaction',
        'utilise_avance',
        'avance_utilisee_id',
        'montant_avance_utilise',
        'statut',
        'recu_url',
        'recu_pdf_genere',
        'ip_address',
        'user_agent',
        'notes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'datetime',
        'details_paiement' => 'array',
        'frais_transaction' => 'decimal:2',
        'utilise_avance' => 'boolean',
        'montant_avance_utilise' => 'decimal:2',
        'recu_pdf_genere' => 'boolean',
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

    public function avanceUtilisee()
    {
        return $this->belongsTo(AvancePrepayee::class, 'avance_utilisee_id');
    }

    public function consommationsAvances()
    {
        return $this->hasMany(ConsommationAvance::class, 'paiement_id');
    }

    // Scopes
    public function scopePaye($query)
    {
        return $query->where('statut', 'paye');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'en_retard')
            ->orWhere(function ($q) {
                $q->where('statut', 'en_attente')
                  ->where('date_echeance', '<', now());
            });
    }

    public function scopeLoyer($query)
    {
        return $query->where('type_paiement', 'loyer');
    }

    public function scopeByMois($query, $mois)
    {
        return $query->where('mois_concerne', $mois);
    }

    public function scopeByLocataire($query, $locataireId)
    {
        return $query->where('locataire_id', $locataireId);
    }

    // Accesseurs
    public function getEstEnRetardAttribute()
    {
        return $this->statut === 'en_attente' 
            && $this->date_echeance < now();
    }

    public function getJoursRetardAttribute()
    {
        if (!$this->est_en_retard) return 0;
        
        return now()->diffInDays($this->date_echeance);
    }

    public function getMontantNetAttribute()
    {
        return $this->montant - $this->frais_transaction;
    }

    // Méthodes
    public function marquerCommePaye($methodePaiement, $referenceTransaction = null, $detailsPaiement = [])
    {
        $this->update([
            'statut' => 'paye',
            'date_paiement' => now(),
            'methode_paiement' => $methodePaiement,
            'reference_transaction' => $referenceTransaction,
            'details_paiement' => $detailsPaiement,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Générer le numéro de reçu
        if (empty($this->numero_recu)) {
            $this->update([
                'numero_recu' => $this->genererNumeroRecu(),
            ]);
        }

        // Notifier le locataire et le propriétaire
        $this->notifierPaiementEffectue();
    }

    public function marquerCommeEnRetard()
    {
        if ($this->statut === 'en_attente' && $this->date_echeance < now()) {
            $this->update(['statut' => 'en_retard']);
            
            // Notifier le locataire
            $this->notifierRetardPaiement();
        }
    }

    public function utiliserAvance($avanceId, $montant)
    {
        $avance = AvancePrepayee::findOrFail($avanceId);
        
        if ($montant > $avance->montant_restant) {
            throw new \Exception("Le montant demandé dépasse le montant restant de l'avance.");
        }

        // Consommer l'avance
        $avance->consommer($montant, $this->id, "Paiement {$this->numero_facture}");

        // Mettre à jour le paiement
        $this->update([
            'utilise_avance' => true,
            'avance_utilisee_id' => $avanceId,
            'montant_avance_utilise' => $montant,
        ]);

        // Si l'avance couvre tout le paiement, marquer comme payé
        if ($montant >= $this->montant) {
            $this->marquerCommePaye('avance', null, ['avance_id' => $avanceId]);
        }
    }

    public function genererNumeroFacture()
    {
        $annee = date('Y');
        $mois = date('m');
        $count = self::whereYear('created_at', $annee)
            ->whereMonth('created_at', $mois)
            ->count() + 1;

        return "FACT-{$annee}{$mois}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function genererNumeroRecu()
    {
        $annee = date('Y');
        $mois = date('m');
        $count = self::whereYear('date_paiement', $annee)
            ->whereMonth('date_paiement', $mois)
            ->whereNotNull('numero_recu')
            ->count() + 1;

        return "RECU-{$annee}{$mois}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    protected function notifierPaiementEffectue()
    {
        // Notification au locataire
        Notification::create([
            'user_id' => $this->locataire->user_id,
            'titre' => 'Paiement confirmé',
            'message' => "Votre paiement de {$this->montant} FCFA a été confirmé. Numéro de reçu: {$this->numero_recu}",
            'type' => 'paiement',
            'reference_id' => $this->id,
            'reference_type' => 'paiement',
        ]);

        // Notification au propriétaire
        Notification::create([
            'user_id' => $this->contratLocation->proprietaire->user_id,
            'titre' => 'Nouveau paiement reçu',
            'message' => "Paiement de {$this->montant} FCFA reçu pour {$this->contratLocation->chambre->nom_complet}",
            'type' => 'paiement',
            'reference_id' => $this->id,
            'reference_type' => 'paiement',
        ]);
    }

    protected function notifierRetardPaiement()
    {
        Notification::create([
            'user_id' => $this->locataire->user_id,
            'titre' => 'Paiement en retard',
            'message' => "Votre paiement de {$this->montant} FCFA est en retard de {$this->jours_retard} jour(s).",
            'type' => 'paiement',
            'reference_id' => $this->id,
            'reference_type' => 'paiement',
        ]);
    }

    // Event pour générer automatiquement le numéro de facture
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paiement) {
            if (empty($paiement->numero_facture)) {
                $paiement->numero_facture = $paiement->genererNumeroFacture();
            }
        });
    }
}