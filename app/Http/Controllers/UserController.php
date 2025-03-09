<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public  function store(Request $request)
    {
        $id = $request->id;
        $item = $request->item;
        User::updateUserSavedData($id, $item);
        $allowedItems = ['equip', 'club', 'competicio','jugador'];

        if (!in_array($item, $allowedItems, true)) {
            abort(404);
        }
        
        return redirect(url("/{$item}/" . intval($id) . "/" . intval($id)));
    }
}
