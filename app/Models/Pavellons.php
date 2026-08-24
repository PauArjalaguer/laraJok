<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Matches;

class Pavellons extends Model
{
    use HasFactory;
    protected $table = 'places';
    protected $primaryKey = 'idPlace';
    public $timestamps = false;

    protected $fillable = [
        'idPlace',
        'placeName',
        'placeAddress',
        'lat',
        'lon',
        'guide_info'
    ];

    public function matches()
    {
        return $this->hasMany(Matches::class, 'idPlace','idPlace')
            ->where('matchDate', '>', date("Y-m-d", strtotime('yesterday')))
            ->where('matchDate', '<', date("Y-m-d", strtotime('tomorrow')));
    }
}
