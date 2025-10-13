<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ContratLocation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'contrats_location';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'chambre_id',
        'proprietaire_id',
        'locataire_id',
        'demarcheur_id',
        'numero_contrat',
        'reference',
        'date_etablissement',
        'date_paiement_loyer',
        'date_signature_proprietaire',
        'date_signature_locataire',
        'statut',
    ];

    protected $casts = [
        'date_etablissement' => 'date',
        'date_paiement_loyer' => 'integer',
        'date_signature_proprietaire' => 'datetime',
        'date_signature_locataire' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function demarcheur()
    {
        return $this->belongsTo(Demarcheur::class);
    }

    public function etatsLieux()
    {
        return $this->hasMany(EtatLieux::class, 'contrat_id');
    }

    public function etatLieuxEntree()
    {
        return $this->hasOne(EtatLieux::class, 'contrat_id')->where('type_etat', 'entree');
    }

    public function etatLieuxSortie()
    {
        return $this->hasOne(EtatLieux::class, 'contrat_id')->where('type_etat', 'sortie');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'contrat_id');
    }

    public function avancesPrepayees()
    {
        return $this->hasMany(AvancePrepayee::class, 'contrat_id');
    }

    public function avances()
    {
        return $this->avancesPrepayees();
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeExpire($query)
    {
        return $query->where('statut', 'expire');
    }

    public function scopeResilie($query)
    {
        return $query->where('statut', 'resilie');
    }

    public function scopeByProprietaire($query, $proprietaireId)
    {
        return $query->where('proprietaire_id', $proprietaireId);
    }

    public function scopeByLocataire($query, $locataireId)
    {
        return $query->where('locataire_id', $locataireId);
    }

    // Accesseurs
    public function getEstSigneAttribute()
    {
        return !is_null($this->date_signature_proprietaire) 
            && !is_null($this->date_signature_locataire);
    }

    public function getTotalPayeAttribute()
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

    public function getDerniereEcheanceAttribute()
    {
        return $this->paiements()
            ->orderBy('date_echeance', 'desc')
            ->first()?->date_echeance;
    }

    public function getProchaineEcheanceAttribute()
    {
        return $this->paiements()
            ->where('statut', 'en_attente')
            ->orderBy('date_echeance', 'asc')
            ->first()?->date_echeance;
    }

    public function hasUnpaidPayments()
    {
        return $this->paiements()
            ->whereIn('statut', ['en_attente', 'en_retard'])
            ->exists();
    }

    public function getTotalUnpaidAttribute()
    {
        return $this->paiements()
            ->whereIn('statut', ['en_attente', 'en_retard'])
            ->sum('montant');
    }

    public function getLoyerMensuelAttribute()
    {
        return $this->chambre->loyer_mensuel;
    }

    public function isSigne()
    {
        return $this->date_signature_proprietaire && $this->date_signature_locataire;
    }

    public function canBeActivated()
    {
        return $this->statut === 'en_attente' && $this->isSigne();
    }

    public function canBeTerminated()
    {
        return $this->statut === 'actif';
    }

    // Méthodes
    public function signerParProprietaire()
    {
        $this->update([
            'date_signature_proprietaire' => now(),
        ]);

        if ($this->date_signature_locataire) {
            $this->activer();
        }
    }

    public function signerParLocataire()
    {
        $this->update([
            'date_signature_locataire' => now(),
        ]);

        if ($this->date_signature_proprietaire) {
            $this->activer();
        }
    }

    public function activer()
    {
        $this->update(['statut' => 'actif']);
        $this->chambre->update(['statut' => 'loue']);
    }

    public function resilier()
    {
        $this->update(['statut' => 'resilie']);
        $this->chambre->update(['statut' => 'disponible']);
    }

    // Générer une référence unique (UNE SEULE FOIS)
    public static function generateReference($chambreId)
    {
        $chambre = Chambre::with('bien')->find($chambreId);
        if (!$chambre) {
            return 'CTR-' . strtoupper(Str::random(10));
        }

        $bienRef = str_replace('BIEN-', '', $chambre->bien->reference ?? 'BIEN');
        $chambreRef = str_replace('CH-', '', $chambre->reference ?? 'CH');
        
        $count = self::where('chambre_id', $chambreId)->count() + 1;
        
        $reference = 'CTR-' . $bienRef . '-' . $chambreRef . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        while (self::where('reference', $reference)->exists()) {
            $count++;
            $reference = 'CTR-' . $bienRef . '-' . $chambreRef . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return $reference;
    }

    // Générer le numéro de contrat (UNE SEULE FOIS)
    public static function generateNumeroContrat()
    {
        $year = date('Y');
        $attempts = 0;
        $maxAttempts = 100;
        
        do {
            // Compter tous les contrats de l'année (incluant supprimés)
            $count = self::withTrashed()
                ->whereYear('created_at', $year)
                ->count() + 1 + $attempts;
            
            $numeroContrat = 'CTR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            
            // Vérifier l'unicité
            $exists = self::withTrashed()->where('numero_contrat', $numeroContrat)->exists();
            
            if (!$exists) {
                return $numeroContrat;
            }
            
            $attempts++;
            
        } while ($attempts < $maxAttempts);
        
        // Si on atteint le maximum de tentatives, utiliser un identifiant unique
        return 'CTR-' . $year . '-' . strtoupper(substr(uniqid(), -4));
    }

    // Event boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contrat) {
            if (empty($contrat->numero_contrat)) {
                $contrat->numero_contrat = self::generateNumeroContrat();
            }
            
            if (empty($contrat->reference)) {
                $contrat->reference = self::generateReference($contrat->chambre_id);
            }
        });
    }
}