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
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function roleOneUser(Request $request)
    {
        $currentUser = auth()->user();

        if ($currentUser->role != 2) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Optional search term from frontend
        $search = $request->query('q', ''); // use '' if nothing provided

        $myDepartmentIds = $currentUser
            ->departments()
            ->pluck('departments.id');

        $users = User::with('departments')
            ->where('role', 1)
            ->whereDoesntHave('departments', function ($q) use ($myDepartmentIds) {
                $q->whereIn('departments.id', $myDepartmentIds);
            })
            ->when($search, function($query, $search) {
                $query->where('email', 'like', $search . '%'); // search by starting letters
            })
            ->get();

        return response()->json($users);
    }


    // public function roleOneUser()
    // {
    //     $currentUser = auth()->user();

    //     if ($currentUser->role != 2) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $myDepartmentIds = $currentUser
    //         ->departments()
    //         ->pluck('departments.id');

    //     $users = User::with('departments')
    //         ->where('role', 1)
    //         ->whereDoesntHave('departments', function ($q) use ($myDepartmentIds) {
    //             $q->whereIn('departments.id', $myDepartmentIds);
    //         })
    //         ->when($search, function($query) use ($search) {
    //             $query->where('email', 'LIKE', $search . '%');
    //         })
    //         ->limit(10)
    //         ->get('id', 'email');

    //     return response()->json($users);
    // }

       public function addMember(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
                'departments' => 'required|array|min:1',
                'departments.*' => 'exists:departments,id',
            ]);

            $user = User::where('email', $validated['email'])->firstOrFail();

            $user->departments()->syncWithoutDetaching($validated['departments']);

            return response()->json([
                'message' => 'Member added to department(s) successfully',
                'user' => $user->load('departments')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    public function index()
    {
        $currentUser = auth()->user();

        if ($currentUser->role > 2) {
            $departmentIds = $currentUser->departments()->pluck('departments.id');
            $users = User::where('id', '!=', $currentUser->id)
                // ->where('role', '<=', $currentUser->role)
                // ->whereHas('departments', function ($query) use ($departmentIds) {
                //     $query->whereIn('departments.id', $departmentIds);
                // })
                ->get();
            
            return UserResource::collection($users);
        }

        $myDepartmentIds = $currentUser
            ->departments()
            ->pluck('departments.id');

        $users = User::with(['departments' => function ($q) use ($myDepartmentIds) {
                $q->whereIn('departments.id', $myDepartmentIds);
            }])
            ->whereKeyNot($currentUser->id)
            ->where('role', '<=', $currentUser->role)
            ->whereHas('departments', function ($q) use ($myDepartmentIds) {
                $q->whereIn('departments.id', $myDepartmentIds);
            })
            ->get();

        return UserResource::collection($users);
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
        if ($user->role > 2) {
            return response(Department::get());
        }
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

    $user = User::where('email', $request->email)->first();
    
    // Create token and store in database
    $token = app('auth.password.broker')->createToken($user);
    
    // Optionally send email
    Password::sendResetLink($request->only("email"));
    
    return response()->json([
        "error" => false,
        "email" => $request->email,
        "token" => $token,
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
