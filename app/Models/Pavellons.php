<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Matches;

class Pavellons extends Model
{
    use HasFactory;
    protected $table = 'places';

    public function matches()
    {
        return $this->hasMany(Matches::class, 'idPlace','idPlace')
            ->where('matchDate', '>', date("Y-m-d", strtotime('yesterday')))
            ->where('matchDate', '<', date("Y-m-d", strtotime('tomorrow')))
            ;
    }
}
