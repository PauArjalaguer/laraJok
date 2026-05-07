<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnunciMida extends Model
{
    protected $table = 'anuncismides';

    protected $fillable = ['nom_mida', 'tipus_mida'];

    public function anuncis(): HasMany
    {
        return $this->hasMany(Anunci::class, 'id_mida');
    }
}
