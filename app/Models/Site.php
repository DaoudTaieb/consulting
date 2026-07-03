<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'sites';
    protected $primaryKey = 'siteid';
    public $timestamps = false;

    protected $fillable = [
        'sitelibelle',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'siteid', 'siteid');
    }
}
