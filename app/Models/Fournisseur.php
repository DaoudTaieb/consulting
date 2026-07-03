<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $table = 'fournisseurs';
    protected $primaryKey = 'fournisseurid';
    public $timestamps = false; // The table has datecreation/datemodification but not standard Laravel ones
}
