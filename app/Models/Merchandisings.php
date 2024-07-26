<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchandisings extends Model
{
    use HasFactory;

    public static function merchandisingReturnFiveRandomItems()
    {
        return Merchandisings::inRandomOrder()->limit(12)->get();
    }

    public static function merchandisingReturnCategories()
    {
        return Merchandisings::select('assetCategory')->whereNotNull('assetCategory')->distinct()->get();
    }
}
