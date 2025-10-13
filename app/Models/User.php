<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'email',
        'phone',
        'password_hash',
        'first_name',
        'last_name',
        'date_naissance',
        'user_type',
        'ville',
        'pays',
        'profile_image_url',
        'is_active',
        'is_verified',
        'email_verified_at',
        'phone_verified_at',
        'code_unique', // AJOUT DU CODE UNIQUE
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'date_naissance' => 'date',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // IMPORTANT : Laravel cherche 'password' par défaut
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Mutateur pour hasher automatiquement le mot de passe
    public function setPasswordHashAttribute($value)
    {
        // Ne hasher que si ce n'est pas déjà un hash
        if (strlen($value) !== 60 || !str_starts_with($value, '$2y$')) {
            $this->attributes['password_hash'] = Hash::make($value);
        } else {
            $this->attributes['password_hash'] = $value;
        }
    }

    // Relations avec les profils spécifiques
    public function proprietaire()
    {
        return $this->hasOne(Proprietaire::class);
    }

    public function locataire()
    {
        return $this->hasOne(Locataire::class);
    }

    public function demarcheur()
    {
        return $this->hasOne(Demarcheur::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('user_type', $type);
    }

    // Méthodes helper pour vérifier le type d'utilisateur
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isProprietaire()
    {
        return $this->user_type === 'proprietaire';
    }

    public function isLocataire()
    {
        return $this->user_type === 'locataire';
    }

    public function isDemarcheur()
    {
        return $this->user_type === 'demarcheur';
    }

    // Récupérer le profil spécifique selon le type
    public function getSpecificProfile()
    {
        return match($this->user_type) {
            'proprietaire' => $this->proprietaire,
            'locataire' => $this->locataire,
            'demarcheur' => $this->demarcheur,
            default => null,
        };
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getNotificationsNonLuesAttribute()
    {
        return $this->notifications()->where('lu', false)->count();
    }

    // NOUVELLES MÉTHODES POUR CODE UNIQUE
    
    /**
     * Générer un code unique selon le type d'utilisateur
     */
    public static function generateCodeUnique($user = null)
    {
        // Déterminer le préfixe selon le type
        $prefix = 'USER'; // Par défaut

        if ($user) {
            $prefix = match($user->user_type) {
                'admin' => 'ADM',
                'proprietaire' => 'PROP',
                'locataire' => 'LOC',
                'demarcheur' => 'DEM',
                default => 'USER',
            };
        }

        // Générer un code unique
        do {
            $code = $prefix . '-' . strtoupper(Str::random(8));
        } while (self::where('code_unique', $code)->exists());

        return $code;
    }

    /**
     * Obtenir le préfixe du code selon le type
     */
    public function getCodePrefix()
    {
        return match($this->user_type) {
            'admin' => 'ADM',
            'proprietaire' => 'PROP',
            'locataire' => 'LOC',
            'demarcheur' => 'DEM',
            default => 'USER',
        };
    }

    // FIN NOUVELLES MÉTHODES

    // Méthodes
    public function assignerRole()
    {
        if (!$this->hasAnyRole(['admin', 'proprietaire', 'locataire', 'demarcheur'])) {
            $this->assignRole($this->user_type);
        }
    }

    public function creerProfilSpecifique(array $data = [])
    {
        switch ($this->user_type) {
            case 'proprietaire':
                return $this->proprietaire()->create(array_merge([
                    'ville' => $this->ville,
                    'pays' => $this->pays,
                ], $data));
            
            case 'locataire':
                // La table locataires n'a pas de colonnes ville/pays
                return $this->locataire()->create($data);
            
            case 'demarcheur':
                return $this->demarcheur()->create($data);
            
            default:
                return null;
        }
    }

    // Event pour assigner automatiquement le rôle ET générer le code unique
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Générer le code unique si absent
            if (empty($user->code_unique)) {
                $user->code_unique = self::generateCodeUnique($user);
            }
        });

        static::created(function ($user) {
            // Assigner le rôle après création
            $user->assignerRole();
        });
    }
}