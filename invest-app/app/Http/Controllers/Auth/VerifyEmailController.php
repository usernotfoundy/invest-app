<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    // public function __invoke(EmailVerificationRequest $request): RedirectResponse
    // {
    //     if ($request->user()->hasVerifiedEmail()) {
    //         return redirect()->intended(
    //             config('app.app_url').'/login'
    //         );
    //     }

    //     if ($request->user()->markEmailAsVerified()) {
    //         event(new Verified($request->user()));
    //     }

    //     return redirect()->intended(
    //         config('app.app_url').'/login'
    //     );
    // }

    public function verify(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Verify hash from URL matches user’s email
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link');
        }

        // If already verified, just redirect
        if ($user->hasVerifiedEmail()) {
            return redirect(config('app.app_url').'/email-verified');
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        // Redirect to frontend change-password page
        return redirect(config('app.app_url').'/email-verified');
    }
}
