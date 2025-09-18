<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    //
    public function getUsers(Request $request)
    {
        //     $users = User::all();
        //     return response()->json($users);

        $users = User::select('id', 'name', 'email', 'assigned_sector', 'email_verified_at')
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

        activity('assign')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['assign' => $user->getChanges()])
            ->log('assign role to user');  

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

        activity('assign')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['assign' => $user->getChanges()])
            ->log('assign user to sector');          

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
        $agencyCount = User::role('agency')->count();
        $adminCount = User::role('admin')->count();

        return response()->json([
            'agency' => $agencyCount,
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

    public function getLogs()
    {
        $logs = Activity::with(['causer', 'subject'])->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'description' => $log->description,
                'subject' => $log->subject ? [
                    'type' => class_basename($log->subject_type),
                    'id'   => $log->subject_id,
                    'name' => $log->subject->name ?? null,  // example: ChildSector name
                    'file_path' => $log->subject->file_path ?? null,
                ] : null,
                'causer' => $log->causer ? [
                    'id'    => $log->causer_id,
                    'name'  => $log->causer->name ?? null,
                    'email' => $log->causer->email ?? null,
                ] : null,
                'properties' => $log->properties,
                'created_at' => $log->created_at,
            ];
        });

        return response()->json($logs);
    }
}
