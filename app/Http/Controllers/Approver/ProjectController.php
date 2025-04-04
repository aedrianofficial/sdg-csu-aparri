<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ProjectController extends Controller
{
    public function show_approved($id)
    {
        // Fetch the project by its ID
        $project = Project::with('user', 'sdg')->findOrFail($id);

        // Pass the project to the view
        return view('approver.projects_programs.show_approved', compact('project'));
    }
    public function show_feedback_rejected($id)
    {
        // Fetch the project by its ID
        $project = Project::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the project to the view
        return view('approver.feedbacks.rejected.projects_programs', compact('project'));
    }
    public function rejected_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'approver')
            ->where('action', 'rejected') // Assuming this action reflects the need for changes
            ->pluck('content_id') 
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Project::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 2) // 'Needs Changes' status
            ->where('is_publish', 0); // Ensure they are not published
    
        // Apply filters based on request parameters
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        if ($request->filled('project_status')) {
            $query->where('project_status', $request->project_status);
        }
    
        
    
        // Apply SDG filter if present
        if ($request->filled('sdg')) {
            $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
            $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
            });
        }
    
        // Execute the query and get the filtered reports
        $projects = $query->with('user')->orderBy('id', 'desc')->paginate(5); // Include user relation and paginate results
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = SDG::all();
    
    
        // Return the view with filtered projects and SDG options for the dropdowns
        return view('approver.projects_programs.rejected', compact('projects', 'sdgs'));
    }
    
    public function reject_project(Request $request)
    {
    // Validate the form input
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'feedback' => 'nullable|string|max:2000',  // Feedback is optional for rejection
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Create the feedback entry (only if feedback is provided)
    if ($validated['feedback']) {
        $feedback = Feedback::create([
            'feedback' => $validated['feedback'],
            'users_id' => $user->id,  // Authenticated user's ID
        ]);

        // Find the project and attach feedback to it
        $project = Project::findOrFail($validated['project_id']);
        $project->feedbacks()->attach($feedback->id);
    }

    // Update the project's review status to 'Rejected'
    $project = Project::findOrFail($validated['project_id']);
    $project->update([
        'review_status_id' => 2,
    ]);
     // Log the action into the role_actions table using polymorphic relationship fields
     RoleAction::create([
        'content_id' => $project->id,          
        'content_type' => Project::class,      
        'user_id' => auth()->user()->id,       
        'role' => 'approver',                  
        'action' => 'rejected',                
        'created_at' => now(),                 
    ]);

    $type = 'project'; 
        $status = 'rejected';
        $contributor = $project->user_id;
        $projectTitle = $project->title;
  
        // Create a new notification for the contributor
        Notification::create([
            'user_id' => $contributor, // Specify who the notification is for
            'notifiable_type' => User::class, // Specify the type of notifiable
            'notifiable_id' => $contributor, // Specify the ID of the notifiable
            'type' => $type,
            'related_type' => Project::class,
            'related_id' => $project->id,
            'data' => json_encode([
                'message' => "Your  $type '" . addslashes($projectTitle) . "' has been rejected.", // Escape the project title
                'approver' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'approver',  // Specify the role
                'type' => $type, // Include the type
                'status' => $status, // Include the status
            ]),
            'created_at' => now(),
        ]);
        // Log the activity for rejecting the project
        ActivityLog::create([
            'log_name' => 'Project Rejected',
            'description' => 'Rejected the project titled "' . addslashes($project->title) . '"',
            'subject_type' => Project::class,
            'subject_id' => $project->id,
            'event' => 'rejected',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'project_title' => $project->title,
                'review_status' => 'rejected',
                'role' => 'approver',
            ]),
            'created_at' => now(),
        ]);

    session()->flash('alert-success', 'Project rejected successfully.');
    return to_route('approver.projects.index');
}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Factory|View
    {
        // Define the base query for fetching projects that are 'Forwarded to Approver' (review_status_id = 5) and not published (is_publish = 0)
        $query = Project::where('review_status_id', 5)
            ->where('is_publish', 0)
            ->with('user'); // Load the related contributor's user information
    
        // Apply filters based on request parameters
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        if ($request->filled('project_status')) {
            $query->where('project_status', $request->project_status);
        }
    
        // Apply SDG filter if present
        if ($request->filled('sdg')) {
            $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
            $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
            });
        }
    
        // Fetch the filtered list of projects and paginate (5 projects per page)
        $projects = $query->orderBy('id', 'desc')->paginate(5);
    
        // Fetch all SDGs for the filter dropdown
      
        $sdgs = SDG::all();
    
        // Pass the filtered projects and filter options to the view
        return view('approver.projects_programs.index', compact('projects', 'sdgs'));
    }
    
    public function approved_list(Request $request)
{
    $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'approver')
            ->where('action', 'approved') // Assuming this action reflects the need for changes
            ->pluck('content_id') 
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Project::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 3) 
            ->where('is_publish', 1); // Ensure they are not published
    
        // Apply filters based on request parameters
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        if ($request->filled('project_status')) {
            $query->where('project_status', $request->project_status);
        }
    
        
    
        // Apply SDG filter if present
        if ($request->filled('sdg')) {
            $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
            $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
            });
        }
    
        // Execute the query and get the filtered reports
        $projectsPaginated = $query->with('user')->orderBy('id', 'desc')->paginate(5); // Include user relation and paginate results
    
        // Fetch all SDGs for the filter dropdown
        $reviewStatuses = ReviewStatus::whereNotIn('status', ['Need Changes', 'Rejected','Pending Review'])->get(); // Exclude specific statuses
        $sdgs = SDG::all();
    
   
    // Pass the filtered content to the view with pagination
    return view('approver.projects_programs.approved', compact('projectsPaginated', 'reviewStatuses', 'sdgs'));
}

public function approved($id)
{
    // Find the project by ID
    $project = Project::findOrFail($id);

    // Update the review status to 'Forwarded to Approver'
    $project->update([
        'review_status_id' => 3,
        'is_publish' => 1
    ]);

    // Log the action into the role_actions table using polymorphic relationship fields
    RoleAction::create([
        'content_id' => $project->id,
        'content_type' => Project::class,
        'user_id' => auth()->user()->id,
        'role' => 'approver',
        'action' => 'approved',
        'created_at' => now(),
    ]);

    // Notify the project contributor
    $type = 'project'; 
    $status = 'approved';
    $contributor = $project->user_id;
    $projectTitle = $project->title;

    Notification::create([
        'user_id' => $contributor,
        'notifiable_type' => User::class,
        'notifiable_id' => $contributor,
        'type' => $type,
        'related_type' => Project::class,
        'related_id' => $project->id,
        'data' => json_encode([
            'message' => "Your $type '" . addslashes($projectTitle) . "' has been approved and is now published.",
            'approver' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'role' => 'approver',  // Specify the role
            'type' => $type,
            'status' => $status,
        ]),
        'created_at' => now(),
    ]);

    // Log the activity for approving the project
    ActivityLog::create([
        'log_name' => 'Project Approved',
        'description' => 'Approved the project titled "' . addslashes($project->title) . '"',
        'subject_type' => Project::class,
        'subject_id' => $project->id,
        'event' => 'approved',
        'causer_type' => User::class,
        'causer_id' => auth()->user()->id,
        'properties' => json_encode([
            'project_title' => $project->title,
            'review_status' => 'approved',
            'role' => 'approver',
        ]),
        'created_at' => now(),
    ]);
    // Redirect back with a success message
    session()->flash('alert-success', 'Project Approved Successfully.');
    return to_route('approver.projects.index');
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
     */
    public function show(string $id, Request $request)
{
    // Fetch the project by its ID, including related SDG and user data if needed
    $project = Project::with(['user', 'sdg'])->findOrFail($id);

    // Check if there's a notification ID in the request
    $notificationId = $request->query('notification_id');
    $notificationData = null;

    if ($notificationId) {
        // Fetch the notification related to the project and mark it as read
        $notification = Notification::where('notifiable_id', Auth::id())
            ->where('notifiable_type', User::class)
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notificationData = json_decode($notification->data, true);
            $notification->markAsRead();
        }
    }

    // Pass the project and notification data to the view
    return view('approver.projects_programs.show', compact('project', 'notificationData'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
