<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnuncisFotos extends Model
{
    use HasFactory;
    protected $table = 'anuncis_fotos';
    protected $fillable = ['id', 'id_anunci', 'foto_ruta'];

    
}
