<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show_published($id)
    {
        // Fetch the project by its ID
        $project = Project::with('user', 'sdg')->findOrFail($id);

        // Pass the project to the view
        return view('publisher.projects_programs.show_published', compact('project'));
    }
    public function published_list(Request $request)
{
    $user = Auth::user(); // Get the currently authenticated user
    
    // Retrieve role actions for the current user that indicate a need for changes
    $roleActions = RoleAction::where('user_id', $user->id)
        ->where('role', 'publisher')
        ->where('action', 'published') // Assuming this action reflects the need for changes
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
    $sdgs = SDG::all();


// Pass the filtered content to the view with pagination
return view('publisher.projects_programs.published', compact('projectsPaginated', 'sdgs'));

    // Return the view with projects and SDGs
   
}
    public function index(Request $request)
    {
        // Start the query for projects with specific review status and publication state
        $query = Project::where('review_status_id', 6) // Status for 'Published'
            ->where('is_publish', 0) // Only unpublished projects
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
    
        // Fetch the filtered list of projects and paginate
        $projects = $query->orderBy('id', 'desc')->paginate(5); // Paginate the results, 5 projects per page
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = Sdg::all();
    
        // Pass the projects and SDGs to the view
        return view('publisher.projects_programs.index', compact('projects', 'sdgs'));
    }
    

    public function published($id){
        // Find the project by ID
        $project = Project::findOrFail($id);
        // Log the action into the role_actions table using polymorphic relationship fields
        RoleAction::create([
            'content_id' => $project->id,          
            'content_type' => Project::class,      
            'user_id' => auth()->user()->id,       
            'role' => 'publisher',                  
            'action' => 'published',                
            'created_at' => now(),                 
        ]);
    
        $project->update([
            'is_publish' => 1,
            'review_status_id' => 3
        ]);

        $type = 'project'; 
        $status = 'published';
        $contributor = $project->user_id;
        $projectTitle = $project->title;
  
        // Create a new notification for the contributor
        Notification::create([
            'user_id' => $contributor,
            'notifiable_type' => User::class,
            'notifiable_id' => $contributor,
            'type' => $type,
            'related_type' => Project::class,
            'related_id' => $project->id,
            'data' => json_encode([
                'message' => "Your $type '" . addslashes($projectTitle) . "' has been approved and is now published.",
                'publisher' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'publisher',  // Specify the role
                'type' => $type,
                'status' => $status,
            ]),
            'created_at' => now(),
        ]);
        // Log the activity for publishing the project
            ActivityLog::create([
                'log_name' => 'Project Published',
                'description' => 'Published the project titled "' . addslashes($projectTitle) . '"',
                'subject_type' => Project::class,
                'subject_id' => $project->id,
                'event' => 'published',
                'causer_type' => User::class,
                'causer_id' => auth()->user()->id,
                'properties' => json_encode([
                    'project_title' => $projectTitle,
                    'review_status' => 'published',
                    'role' => 'publisher',
                ]),
                'created_at' => now(),
            ]);
    
        // Redirect back with a success message
        session()->flash('alert-success', 'Project Published Successfully.');
        return to_route('publisher.projects.index');
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
    // Fetch the project by its ID, including related user and SDG data
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
    return view('publisher.projects_programs.show', compact('project', 'notificationData'));
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
