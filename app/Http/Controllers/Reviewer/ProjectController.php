<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Project\ProjectRequest;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Projectimg;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function show_reviewed($id)
     {
         // Fetch the project by its ID
         $project = Project::with('user', 'sdg')->findOrFail($id);
 
         // Pass the project to the view
         return view('reviewer.projects_programs.show_reviewed', compact('project'));
     }
     public function need_changes(Request $request)
     {
         // Validate the form input
         $validated = $request->validate([
             'project_id' => 'required|exists:projects,id',
             'feedback' => 'required|string|max:2000',
         ]);
     
         // Get the authenticated user
         $user = Auth::user();
     
                // Find the project
        $project = Project::findOrFail($validated['project_id']);

        // Creating and attaching feedback
        if ($request->filled('feedback')) {
            $feedback = Feedback::firstOrCreate([
                'feedback' => $validated['feedback'],
                'users_id' => $user->id,
            ]);

            // Attach the feedback to the project
            $project->feedbacks()->syncWithoutDetaching($feedback->id);
        }
     
         // Update the project's review status to 'Needs Changes'
         $project->update([
             'review_status_id' => 1,
         ]);
     
         // Log the action in the RoleAction table
         RoleAction::create([
             'user_id' => $user->id,
             'role' => 'reviewer',
             'action' => 'requested change',  
             'content_id' => $project->id,          
             'content_type' => Project::class,  
             'created_at' => now()
         ]);


         $type = 'project'; 
         $status = 'request_changes';
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
                 'message' => "Your  $type '" . addslashes($projectTitle) . "' requires changes.", // Escape the project title
                 'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                 'role' => 'reviewer',  // Specify the role
                 'type' => $type, // Include the type
                 'status' => $status, // Include the status
             ]),
             'created_at' => now(),
         ]);
        // Log the activity for requesting changes on the project
        ActivityLog::create([
            'log_name' => 'Project Needs Changes',
            'description' => 'Requested changes for the project titled "' . addslashes($project->title) . '"',
            'subject_type' => Project::class,
            'subject_id' => $project->id,
            'event' => 'requested change',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'project_title' => $project->title,
                'review_status' => 'needs changes',
                'role' => 'reviewer',
            ]),
            'created_at' => now(),
        ]);
     
         // Redirect back with a success message
         session()->flash('alert-success', 'Feedback for changes submitted successfully.');
         return to_route('reviewer.projects.needchanges_list');
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
            $feedback = Feedback::firstOrCreate([
                'feedback' => $validated['feedback'],
                'users_id' => $user->id,  // Authenticated user's ID
            ]);

            // Find the project
            $project = Project::findOrFail($validated['project_id']);

            // Attach the feedback to the project
            $project->feedbacks()->syncWithoutDetaching($feedback->id);
        }

     
         // Update the project's review status to 'Rejected'
         $project = Project::findOrFail($validated['project_id']);
         $project->update([
             'review_status_id' => 2,
         ]);
     
         // Log the role action for rejection
         RoleAction::create([
             'user_id' => $user->id,  // ID of the authenticated user
             'content_id' => $project->id,          
             'content_type' => Project::class,  
             'role' => 'reviewer',
             'action' => 'rejected',  // Action name
             'created_at' => now(),    // Current timestamp
         ]);

      // Get the contributor (assuming `user_id` is the creator's ID)
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
              'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
              'role' => 'reviewer',  // Specify the role
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
                'role' => 'reviewer',
            ]),
            'created_at' => now(),
        ]);
         session()->flash('alert-success', 'Project rejected successfully.');
         return to_route('reviewer.projects.rejected');
     }
     
    public function under_review(Request $request)
    {

        $query = Project::with(['projectimg', 'sdg', 'reviewStatus'])   
            ->where('is_publish', 0) // Only unpublished projects
            ->where('review_status_id', 4); // Specific review statuses
    
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

        $sdgs = SDG::all();
    
        return view('reviewer.projects_programs.under_review', compact('projects', 'sdgs'));
    }

    public function need_changes_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'requested change') // Assuming this action reflects the need for changes
            ->pluck('content_id') 
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Project::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 1) // 'Needs Changes' status
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
        $projectsPaginated = $query->with('user')->orderBy('id', 'desc')->paginate(5); // Include user relation and paginate results
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = SDG::all();
    
        // Pass the filtered content to the view
        return view('reviewer.projects_programs.need_changes', compact('projectsPaginated',  'sdgs'));
    }
    
    
    

    public function reviewed($id)
{
    // Find the project by ID
    $project = Project::findOrFail($id);

    // Update the review status
    $project->update(['review_status_id' => 5]);

    // Log the action in the role_actions table
    RoleAction::create([
        'content_id' => $project->id,
        'content_type' => Project::class,
        'user_id' => auth()->user()->id,
        'role' => 'reviewer',
        'action' => 'reviewed',
        'created_at' => now(),
    ]);

    // Get the contributor (assuming `user_id` is the creator's ID)
    $type = 'project';
    $statusContributor = 'reviewed';
    $contributor = $project->user_id;
    $projectTitle = $project->title;

    // Create a notification for the contributor
    Notification::create([
        'user_id' => $contributor,
        'notifiable_type' => User::class,
        'notifiable_id' => $contributor,
        'type' => $type,
        'related_type' => Project::class,
        'related_id' => $project->id,
        'data' => json_encode([
            'message' => "Your $type '" . addslashes($projectTitle) . "' has been reviewed.",
            'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'role' => 'reviewer',
            'type' => $type,
            'status' => $statusContributor,
        ]),
        'created_at' => now(),
    ]);

    // Notify all approvers
    $statusApprover = 'submitted for approval';

    // Retrieve all approvers
    $approvers = User::where('role', 'approver')->get();

    // Create notifications for each approver
    foreach ($approvers as $approver) {
        Notification::create([
            'user_id' => $approver->id,
            'notifiable_type' => User::class,
            'notifiable_id' => $approver->id,
            'type' => $type,
            'related_type' => Project::class,
            'related_id' => $project->id,
            'data' => json_encode([
                'message' => "The project titled '" . addslashes($projectTitle) . "' has been submitted for approval.",
                'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'reviewer',
                'type' => $type,
                'status' => $statusApprover,
            ]),
            'created_at' => now(),
        ]);
    }

    ActivityLog::create([
        'log_name' => 'Project Reviewed',
        'description' => 'Reviewed the project titled "' . addslashes($project->title) . '"',
        'subject_type' => Project::class,
        'subject_id' => $project->id,
        'event' => 'reviewed',
        'causer_type' => User::class,
        'causer_id' => auth()->user()->id,
        'properties' => json_encode([
            'project_title' => $project->title,
            'review_status' => 'reviewed',
            'role' => 'reviewer',
        ]),
        'created_at' => now(),
    ]);

    session()->flash('alert-success', 'Project Reviewed Successfully.');
    return to_route('reviewer.projects.under_review');
}

    

    
    public function reviewed_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'reviewed') // Assuming this action reflects the need for changes
            ->pluck('content_id') 
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Project::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->whereIN('review_status_id', [3,5,6]);
    
        // Apply filters based on request parameters
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        if ($request->filled('review_status')) {
            $query->where('review_status_id', $request->review_status);
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
    
        // Pass the reviewed content (projects) to the view with filtered results
        return view('reviewer.projects_programs.reviewed', compact('projectsPaginated', 'reviewStatuses', 'sdgs'));
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

     public function show_feedback_changes($id)
    {
        // Fetch the project by its ID
        $project = Project::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the project to the view
        return view('reviewer.feedbacks.need_changes.projects_programs', compact('project'));
    }
    public function show_feedback_rejected($id)
    {
        // Fetch the project by its ID
        $project = Project::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the project to the view
        return view('reviewer.feedbacks.rejected.projects_programs', compact('project'));
    }

    public function rejected_list(Request $request)
{
    $user = Auth::user(); // Get the currently authenticated user
    
    // Retrieve role actions for the current user that indicate a need for changes
    $roleActions = RoleAction::where('user_id', $user->id)
        ->where('role', 'reviewer')
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
    $projectsPaginated = $query->with('user')->orderBy('id', 'desc')->paginate(5); // Include user relation and paginate results

    // Fetch all SDGs for the filter dropdown
    $sdgs = SDG::all();

    // Pass the filtered content to the view
    return view('reviewer.projects_programs.rejected', compact('projectsPaginated', 'sdgs'));
}


    

public function show(string $id, Request $request)
{
    // Fetch the project by its ID, including related SDG and project images if needed
    $project = Project::with(['user', 'sdg', 'projectimg', 'reviewStatus'])->findOrFail($id);

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
    return view('reviewer.projects_programs.show', compact('project', 'notificationData'));
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
