<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // $request->authenticate();

        // $request->session()->regenerate();

        // return response()->noContent();

        // try {
        //     $request->authenticate();
        //     $request->session()->regenerate();

        //     return response()->json([
        //         'message' => 'Login successful',
        //     ], 200);

        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     return response()->json([
        //         'message' => 'Invalid email or password',
        //     ], 401);
        // }

        try {
            // Find the user first
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }

            // Check if email is verified
            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'message' => 'Please verify your email before logging in.',
                ], 403); // Forbidden
            }

            // Authenticate user
            $request->authenticate();
            $request->session()->regenerate();

            return response()->json([
                    'message' => 'Login successful',
                ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
