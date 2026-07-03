<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'userid';
    public $timestamps = false;

    protected $fillable = [
        'login',
        'password',
        'userdroitid',
        'societeid',
        'agencebid',
        'employeeid',
        'clientid',
        'siteid',
        'plafonremise',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'userid';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Override: la table users n'a pas de colonne remember_token.
     * Évite l'erreur SQL lors du login.
     */
    public function setRememberToken($value)
    {
        // Ne rien faire - colonne absente en base
    }
}
