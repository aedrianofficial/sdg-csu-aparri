<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use App\Models\Sdg;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
  public function index(Request $request)
  {
      $query = User::query();
      
      // Search and filter logic
      if ($request->filled('search')) {
          $query->where('username', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
      }
  
      // Filter by first name
      if ($request->filled('first_name')) {
          $query->where('first_name', 'like', '%' . $request->first_name . '%');
      }
  
      // Filter by last name
      if ($request->filled('last_name')) {
          $query->where('last_name', 'like', '%' . $request->last_name . '%');
      }
  
      if ($request->filled('role')) {
          $query->where('role', $request->role);
      }
    
      $users = $query->paginate(10);
  
      return view('auth.users.index', compact('users'));
  }
  
    public function show($id)
  {
      $user = User::findOrFail($id); // Find the user by ID, or return a 404 error if not found
      return view('auth.users.show', compact('user'));
  }
  public function edit($id)
  {
      $user = User::with('userImage')->findOrFail($id);
      return view('auth.users.edit', compact('user'));
  }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'bio' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'role' => 'required|in:user,admin,contributor,reviewer,approver,publisher',
        ]);

        try {
            $user = User::findOrFail($id);

            // Store the old values before updating
            $oldData = $user->only(['first_name', 'last_name', 'username', 'email', 'phone_number', 'address', 'date_of_birth', 'bio', 'role']);
            $oldRole = $user->role;

            // Update user fields except image
            $user->update($request->except('image'));

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                // Store the new image
                $imagePath = $request->file('image')->store('images/users', 'public');

                // Update or create user's image
                UserImage::updateOrCreate(
                    ['user_id' => $id],
                    ['image_path' => $imagePath]
                );
            }

            // Prepare the log description
            $changes = [];
            foreach ($oldData as $key => $oldValue) {
                if ($oldValue !== $user->$key) {
                    $changes[] = ucfirst(str_replace('_', ' ', $key)) . ' changed from "' . $oldValue . '" to "' . $user->$key . '"';
                }
            }

            // Log the activity for updating the user details
            $logDescription = 'Updated user details: ' . implode(', ', $changes);
            
            // Log the role change if applicable
            if ($oldRole !== $user->role) {
                $logDescription .= ' | Role changed from "' . $oldRole . '" to "' . $user->role . '"';
            }

            ActivityLog::create([
                'log_name' => 'User  Updated',
                'description' => $logDescription,
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'event' => 'user_updated',
                'causer_type' => User::class,
                'causer_id' => auth()->user()->id, // Assuming the user making the update is logged in
                'properties' => json_encode($request->all()), // Log the new values
                'created_at' => now(),
            ]);

            session()->flash('alert-success', 'User  Updated Successfully!');
            return to_route('users.index');

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('User  update failed: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()->withErrors(['error' => 'Failed to update user. Please try again later.']);
        }
    }
  
}
