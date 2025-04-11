<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
     /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Ensure no previous role-based redirect path is stored
        session()->forget('url.intended');
    
        $request->authenticate();
    
        $request->session()->regenerate();
    
        // Log the login activity
        ActivityLog::create([
            'log_name' => 'User Login',
            'description' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' logged in.',
            'subject_type' => User::class,
            'subject_id' => auth()->user()->id,
            'event' => 'login',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'role' => auth()->user()->role,
                'first_name' => auth()->user()->first_name,
                'last_name' => auth()->user()->last_name,
                'username' => auth()->user()->username,
            ]),
            'created_at' => now(),
        ]);
    
       
        // If the email is not verified, dispatch the Registered event and redirect
        if (is_null(auth()->user()->email_verified_at)) {
            event(new Registered(auth()->user()));
            return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
        }
    
        return redirect()->intended($this->redirectTo());
    }
    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Clear the user session
        $user = Auth::guard('web')->user(); // Get the authenticated user

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Clear intended URL to prevent redirects to stale pages after logout
        session()->forget('url.intended');

        // Log the logout activity
        if ($user) { // Check if a user was authenticated
            ActivityLog::create([
                'log_name' => 'User Logout',
                'description' => $user->first_name . ' ' . $user->last_name . ' logged out.',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'event' => 'logout',
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'properties' => json_encode([
                    'role' => $user->role,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                ]),
                'created_at' => now(),
            ]);
        }

        return redirect('/login');
    }

    /**
     * Determine the redirect path based on user role.
     */
    protected function redirectTo()
    {
        switch (auth()->user()->role) {
            case 'admin':
                return '/auth/dashboard';
            case 'contributor':
                return '/contributor/dashboard';
            case 'reviewer':
                return '/reviewer/dashboard';
            case 'approver':
                return '/approver/dashboard';
            case 'publisher':
                return '/publisher/dashboard';
            default:
                return '/';
        }
    }
}
