<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'notifications';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'titre',
        'message',
        'type',
        'reference_id',
        'reference_type',
        'lu',
        'date_lecture',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'date_lecture' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    // Scopes
    public function scopeNonLu($query)
    {
        return $query->where('lu', false);
    }

    public function scopeLu($query)
    {
        return $query->where('lu', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Méthodes
    public function marquerCommeLu()
    {
        if (!$this->lu) {
            $this->update([
                'lu' => true,
                'date_lecture' => now(),
            ]);
        }
    }

    public function marquerCommeNonLu()
    {
        $this->update([
            'lu' => false,
            'date_lecture' => null,
        ]);
    }

    // Accesseurs
    public function getEstRecenteAttribute()
    {
        return $this->created_at->diffInHours(now()) < 24;
    }

    public function getTempsEcouleAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Méthodes statiques pour créer des notifications spécifiques
    public static function creerNotificationPaiement($userId, $paiementId, $message)
    {
        return self::create([
            'user_id' => $userId,
            'titre' => 'Nouveau paiement',
            'message' => $message,
            'type' => 'paiement',
            'reference_id' => $paiementId,
            'reference_type' => 'paiement',
        ]);
    }

    public static function creerNotificationContrat($userId, $contratId, $titre, $message)
    {
        return self::create([
            'user_id' => $userId,
            'titre' => $titre,
            'message' => $message,
            'type' => 'contrat',
            'reference_id' => $contratId,
            'reference_type' => 'contrat',
        ]);
    }

    public static function creerNotificationReparation($userId, $message)
    {
        return self::create([
            'user_id' => $userId,
            'titre' => 'Réparation nécessaire',
            'message' => $message,
            'type' => 'reparation',
        ]);
    }
}