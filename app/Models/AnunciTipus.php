<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnunciTipus extends Model
{
    protected $table = 'anuncistipus';

    protected $fillable = ['nom_tipus', 'icona_fa'];

    public function anuncis(): HasMany
    {
        return $this->hasMany(Anunci::class, 'id_tipus');
    }
}
