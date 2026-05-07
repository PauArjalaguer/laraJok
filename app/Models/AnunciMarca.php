<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnunciMarca extends Model
{
    protected $table = 'anuncismarques';

    protected $fillable = ['nom_marca'];

    public function anuncis(): HasMany
    {
        return $this->hasMany(Anunci::class, 'id_marca');
    }
}
