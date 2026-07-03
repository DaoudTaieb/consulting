<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sousfamille extends Model
{
    protected $table = 'sousfamilles';
    protected $primaryKey = 'sousfamilleid';
    public $timestamps = false;

    protected $fillable = [
        'sousfamillelibelle',
        'familleid',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'sousfamilleid', 'sousfamilleid');
    }

    public function famille()
    {
        return $this->belongsTo(Famille::class, 'familleid', 'familleid');
    }
}
