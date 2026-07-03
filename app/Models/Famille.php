<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Famille extends Model
{
    protected $table = 'familles';
    protected $primaryKey = 'familleid';
    public $timestamps = false;

    protected $fillable = [
        'famillelibelle',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'familleid', 'familleid');
    }
}
