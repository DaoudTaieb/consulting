<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';
    protected $primaryKey = 'stockid';
    public $timestamps = false;

    protected $fillable = [
        'produitid',
        'siteid',
        'qtestock',
        'stockvirtuel',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produitid', 'produitid');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'siteid', 'siteid');
    }
}
