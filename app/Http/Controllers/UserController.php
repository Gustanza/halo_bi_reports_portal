<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password']
        ]);
        $user->departments()->attach($data['departments']);
        $token = $user->createToken('main')->plainTextToken;
        return Response([
            'user' => $user,
            'token'=> $token,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $deps =  $user->departments;
        return response($deps);
        // return new UserResource($user);
    }

    /**
     * Update the specified resource in storage. 
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->departments()->detach();
        $user->departments()->attach($data['departments']);
        $user->update($data);
        // Refresh the model to get the latest data from the database
        $user->refresh();
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = User::findOrFail($id)->delete();
        return response($result, 203);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users,email"
        ]);
        $status = Password::sendResetLink($request->only("email"));
        return $status === Password::RESET_LINK_SENT ?
            response()->json([
                "error" => false,
                "data" => "RESET LINK SENT",
            ]): 
            response()->json([
                "error" => true,
                "data" => "SHEET HAPPENED",
            ]);
    }



    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => 'Password reset successful',
        ]);
    }

}
