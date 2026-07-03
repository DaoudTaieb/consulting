<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $table = 'produits';
    protected $primaryKey = 'produitid';
    public $timestamps = false;

    protected $fillable = [
        'produitlibelle',
        'produitcode',
        'familleid',
        'sousfamilleid',
        'fournisseurid',
        'pmarque',
        'prixachatht',
        'prixventeht',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'produitid', 'produitid');
    }

    public function famille()
    {
        return $this->belongsTo(Famille::class, 'familleid', 'familleid');
    }

    public function sousfamille()
    {
        return $this->belongsTo(Sousfamille::class, 'sousfamilleid', 'sousfamilleid');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseurid', 'fournisseurid');
    }
}
