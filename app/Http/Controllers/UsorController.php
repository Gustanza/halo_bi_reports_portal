<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UsorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            "name" => "required|string",
            "role" => "required|integer",
            "email" => "required|string|email|unique:users,email",
            "password" => [
                'required',
                // 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ]
            ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password']
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return Response([
            'user' => $user,
            'token'=> $token,
        ]);
    }


    public function login(Request $request)
    {
        $data = $request->validate([
            "email" => "required",
            "password" => [
                "required"
            ]
        ]);

        if (!Auth::attempt($data, true)) {
            return response([
                "error" => "Incorrect credentials"
            ], 422);
        }

        $user = Auth::user();
        $role = $user->role;
        $token = $user->createToken('main')->plainTextToken;

        return response([
            "role" => $role,
            "token" => $token,
            "user" => $user,
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
