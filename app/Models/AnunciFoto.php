<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnunciFoto extends Model
{
    protected $table = 'anuncisfotos';

    protected $fillable = ['id_anunci', 'foto_ruta', 'ordre'];

    public function anunci(): BelongsTo
    {
        return $this->belongsTo(Anunci::class, 'id_anunci');
    }
}
