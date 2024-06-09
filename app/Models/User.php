<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function updateUserSavedData($idItem, $category)
    {
        $idUser=  Auth::user() ? Auth::user()->id : 0;
        $count = DB::table('usersaved')
            ->where('idUser', $idUser)
            ->where('category', $category)
            ->where('idItem', $idItem)
            ->count();

        if ($count == 1) {
            DB::table('usersaved')->where('idUser', $idUser)
                ->where('category', $category)
                ->where('idItem', $idItem)->delete();
        } else {
            DB::table('usersaved')->updateOrInsert(
                // Valores de las claves
                ['idUser' => $idUser, 'category' => $category, 'idItem' => $idItem],
                ['toDelete' => 0],
            );
        }
    }

    public static function userSavedData(){
       
        $idUser=  Auth::user() ? Auth::user()->id : 0;
       
        $results = DB::table('usersaved')->where('idUser',$idUser)
        ->leftJoin('phases','usersaved.idItem','phases.idGroup')
        ->leftJoin('teams','usersaved.idItem','teams.idTeam')
        ->leftJoin('players','usersaved.idItem','players.idPlayer')->get();
        return $results;
    }
}
