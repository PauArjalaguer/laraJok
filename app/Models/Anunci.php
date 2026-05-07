<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anunci extends Model
{
    protected $table = 'anuncis';

    protected $fillable = [
        'titol',
        'descripcio',
        'preu',
        'id_usuari',
        'id_marca',
        'id_estat',
        'id_mida',
        'id_tipus',
    ];

    public function usuari(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuari');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(AnunciMarca::class, 'id_marca');
    }

    public function estat(): BelongsTo
    {
        return $this->belongsTo(AnunciEstat::class, 'id_estat');
    }

    public function mida(): BelongsTo
    {
        return $this->belongsTo(AnunciMida::class, 'id_mida');
    }

    public function tipus(): BelongsTo
    {
        return $this->belongsTo(AnunciTipus::class, 'id_tipus');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(AnunciFoto::class, 'id_anunci')->orderBy('ordre');
    }

    public function fotoPortada()
    {
        return $this->fotos()->first();
    }
}
