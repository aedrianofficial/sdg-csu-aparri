<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */  public function show(string $id)
    {
        // Retrieve the user by ID
        $user = User::with('userImage')->findOrFail($id);

        // Pass the user data to the view
        return view('publisher.user_profile.show', compact('user'));
    }
    public function edit(string $id)
    {
        // Retrieve the user by ID
        $user = User::with('userImage')->findOrFail($id);

        // Pass the user data to the edit view
        return view('publisher.user_profile.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource in storage.
 */
public function update(Request $request, string $id)
{
    // Validate the request data
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'=> 'required|string|max:255',
        'username' => 'required|string|max:255',
        'phone_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'date_of_birth' => 'nullable|date',
        'bio' => 'nullable|string|max:500',
        'image_path' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,jfif|max:2048',
    ]);

    // Get the user by ID
    $user = User::findOrFail($id); // Fetch user by ID

    // Update user profile details
    $user->update($request->only('first_name','last_name', 'username', 'phone_number', 'address', 'date_of_birth', 'bio'));

    // Handle image upload if provided
    if ($request->hasFile('avatar')) {
        $image = $request->file('avatar');
        $imagePath = time() . '.' . $image->getClientOriginalExtension();

        // Save the image to the designated directory
        $image->move(public_path('images/users'), $imagePath);

        // If the user already has a profile image, delete the old one
        if ($user->userImage) {
            $oldImagePath = public_path('images/users/' . $user->userImage->image_path);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete old image
            }
            $user->userImage->update(['image_path' => $imagePath]); // Update existing image
        } else {
            // Otherwise, create a new record for the profile image
            $user->userImage()->create(['image_path' => $imagePath]);
        }
    }

    // Redirect back with a success message
    
    session()->flash('alert-success', 'Profile updated successfully.');

    // Assuming you have the user object after the update
    return to_route('publisher.profile.show', ['id' => $user->id]);
    
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
