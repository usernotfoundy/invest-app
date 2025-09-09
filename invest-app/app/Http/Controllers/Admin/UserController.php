<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //
    public function getUsers(Request $request)
    {
        //     $users = User::all();
        //     return response()->json($users);

        $users = User::select('id', 'name', 'email', 'assigned_sector')
            ->with([
                'roles:name',
                'sector:id,name' // load sector with only id and name
            ])
            ->get()
            ->map(function ($user) {
                $user->role = $user->roles->pluck('name')->first(); // get first role
                $user->assigned_sector = $user->sector?->name; // replace ID with sector name
                unset($user->roles, $user->sector); // remove unnecessary data
                return $user;
            });

        return response()->json($users);
    }

    public function setUserRole($userId, $roleName)
    {
        $user = User::findOrFail($userId);
        $user->assignRole($roleName);

        return response()->json(['message' => "Role {$roleName} assigned to {$user->name}"]);
    }

    public function getUserRoles($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'user' => $user->name,
            'roles' => $user->getRoleNames() // returns a collection of role names
        ]);
    }

    public function assignSectorToUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'sector_id' => 'required|exists:sectors,id',
                'user_id' => 'required|exists:users,id',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::findOrFail($validated['user_id']);
        $user->assigned_sector = $validated['sector_id'];
        $user->save();

        return response()->json([
            'message' => "Sector assigned successfully to {$user->name}",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'assigned_sector' => $user->sector?->name,
            ]
        ]);
    }

    public function userProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'assigned_sector' => $user->sector?->id, // Assuming sector relationship exists
            'role' => $user->getRoleNames()->first(), // returns a collection of role names
            'email_verified_at' => $user->email_verified_at
        ]);
    }

    public function getUserCount()
    {
        $encoderCount = User::role('encoder')->count();
        $adminCount = User::role('admin')->count();

        return response()->json([
            'encoder' => $encoderCount,
            'admin' => $adminCount,
            // 'total' => $encoderCount + $adminCount,
        ]);
    }

    public function viewRoles()
    {
        $roles = \Spatie\Permission\Models\Role::all()->pluck('name');

        return response()->json(['roles' => $roles]);
    } 

        public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'You cannot delete your own account'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
