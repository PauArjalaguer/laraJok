<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Merchandisings;
use App\Models\User;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
   public function index()
   {
      return view('agenda', [
         'agenda' => Agenda::get(),
         'userSavedData' => User::userSavedData(),
         'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
      ]);
   }
}
