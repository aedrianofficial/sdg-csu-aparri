<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\College;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $colleges = College::all();
        return view('auth.register', compact( 'colleges'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'college_id' => ['required', 'exists:colleges,id'],
            'campus_id' => ['required', 'exists:campuses,id'],
        ]);
    
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => 'contributor',
            'password' => Hash::make($request->password),
            'college_id' => $request->college_id,
            'campus_id' => $request->campus_id,
        ]);
    
        Auth::login($user);
    
        ActivityLog::create([
            'log_name' => 'User Registration',
            'description' => $user->first_name . ' ' . $user->last_name . ' registered an account.',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'event' => 'registration',
            'causer_type' => User::class,
            'causer_id' => $user->id,
            'properties' => json_encode([
                'role' => $user->role,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'email' => $user->email,
                'college_id' => $user->college_id,
                'campus_id' => $user->campus_id,
            ]),
            'created_at' => now(),
        ]);
    
        event(new Registered($user));
    
        // Redirect the user based on email verification status
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
    
        return redirect()->route('contributor.dashboard');
    }
    
    
     public function checkUsername(Request $request)
    {
        $exists = User::where('username', $request->username)->exists();
        return response()->json(['exists' => $exists]);
    }
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
    
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
