<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnunciEstat extends Model
{
    protected $table = 'anuncisestats';

    protected $fillable = ['nom_estat'];

    public function anuncis(): HasMany
    {
        return $this->hasMany(Anunci::class, 'id_estat');
    }
}
