<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    //Listar todos los usuarios
    public function index()
    {
        return response()->json(User::with('role:id,name')->get(), 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        return response()->json($user, 200);
    }
    
    
    // public function update(Request $request, $id)
    // {
    //     $user = User::find($id)
    // }
}