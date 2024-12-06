<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Research\ResearchRequest;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Research;
use App\Models\Researchcategory;
use App\Models\Researchfile;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResearchController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * 
     * 
     */
    public function show_reviewed($id)
    {
        // Fetch the research by its ID
        $research = Research::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the research to the view
        return view('reviewer.research_extension.show_reviewed', compact('research'));
    }

    public function reviewed_list(Request $request)
     {
        $user = Auth::user();
    
        $roleActions = RoleAction::where('user_id', $user->id)
        ->where('role', 'reviewer')
        ->where('action', 'reviewed') // Assuming this action reflects the need for changes
        ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
        ->toArray(); // Convert to array for further use

    // Start querying the Report model
        $query = Research::query()
        ->whereIn('id', $roleActions) // Only include reports related to the role actions
        ->whereIn('review_status_id', [3,5,6]); // 'Needs Changes' status
       
        // Filter by title if present
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        // Filter by research category if present
        if ($request->filled('researchcategory_id')) {
            $query->where('researchcategory_id', $request->researchcategory_id);
        }
    
        // Filter by research status if present
        if ($request->filled('research_status')) {
            $query->where('research_status', $request->research_status);
        }
    
        // Filter by review status if present
        if ($request->filled('review_status')) {
            $query->where('review_status_id', $request->review_status);
        }
    
        // Apply SDG filter if present
        if ($request->filled('sdg')) {
            $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
            $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
            });
        }
    
        // Fetch the filtered list of researches and paginate the results
        $researches = $query->orderBy('id', 'desc')->paginate(5);
    
        // Fetch all SDGs and review statuses for the filter dropdowns
        $reviewStatuses = ReviewStatus::all();
        $researchCategories = Researchcategory::all();
        $sdgs = SDG::all();

        // Pass the reviewed content (research) to the view
        return view('reviewer.research_extension.reviewed', compact('researches','reviewStatuses','researchCategories','sdgs'));
     }

     public function show_feedback_changes($id)
     {
         // Fetch the research by its ID
         $research = Research::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);
 
         // Pass the research to the view
         return view('reviewer.feedbacks.need_changes.research', compact('research'));
     }
     public function show_feedback_rejected($id)
     {
         // Fetch the research by its ID
         $research = Research::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);
 
         // Pass the research to the view
         return view('reviewer.feedbacks.rejected.research', compact('research'));
     }
     public function rejected_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'rejected') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Research::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 2) // 'Needs Changes' status
            ->where('is_publish', 0); // Ensure they are not published
    
        // Apply filters based on request parameters
      
    // Filter by title if present
    if ($request->filled('title')) {
        $query->where('title', 'LIKE', '%' . $request->title . '%');
    }

    // Filter by research category if present
    if ($request->filled('researchcategory_id')) {
        $query->where('researchcategory_id', $request->researchcategory_id);
    }

    // Filter by research status if present
    if ($request->filled('research_status')) {
        $query->where('research_status', $request->research_status);
    }

    // Apply SDG filter if present
    if ($request->filled('sdg')) {
        $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
        $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
            $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
        });
    }


        // Execute the query and get the filtered reports
        $researches = $query->with('user')->orderBy('id', 'desc')->paginate(5); // Include user relation and paginate results
    
        // Fetch all SDGs for the filter dropdown
        $researchCategories = Researchcategory::all();
    $sdgs = SDG::all();
    // Pass the rejected items to the view
    return view('reviewer.research_extension.rejected', compact('researches','researchCategories','sdgs'));
    }


     public function need_changes_list(Request $request)
     {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'requested change') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Research::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 1) // 'Needs Changes' status
            ->where('is_publish', 0); // Ensure they are not published
    
        // Apply filters based on request parameters
      
    // Filter by title if present
    if ($request->filled('title')) {
        $query->where('title', 'LIKE', '%' . $request->title . '%');
    }

    // Filter by research category if present
    if ($request->filled('researchcategory_id')) {
        $query->where('researchcategory_id', $request->researchcategory_id);
    }

    // Filter by research status if present
    if ($request->filled('research_status')) {
        $query->where('research_status', $request->research_status);
    }

    // Apply SDG filter if present
    if ($request->filled('sdg')) {
        $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
        $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
            $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
        });
    }


        // Execute the query and get the filtered reports
        $researches = $query->with('user')->orderBy('id', 'desc')->paginate(5); // Include user relation and paginate results
    
        // Fetch all SDGs for the filter dropdown
        $researchCategories = Researchcategory::all();
    $sdgs = SDG::all();
        // Pass the filtered content to the view
        return view('reviewer.research_extension.need_changes', compact('researches','researchCategories','sdgs'));
        }
     
    public function need_changes(Request $request)
{
    // Validate the form input
    $validated = $request->validate([
        'research_id' => 'required|exists:research,id',
        'feedback' => 'required|string|max:2000',
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Find the research
    $research = Research::findOrFail($validated['research_id']);

    // Creating and attaching feedback
    if ($request->filled('feedback')) {
        $feedback = Feedback::firstOrCreate([
            'feedback' => $validated['feedback'],
            'users_id' => $user->id,
        ]);

        // Attach the feedback to the research
        $research->feedbacks()->syncWithoutDetaching($feedback->id);
    }

    // Update the research's review status to 'Needs Changes'
    $research->update([
        'review_status_id' => 1, // Assuming '1' corresponds to 'Needs Changes'
    ]);
    RoleAction::create([
        'user_id' => $user->id,
        'role' => 'reviewer',
        'action' => 'requested change',  
        'content_id' => $research->id,          
        'content_type' => Research::class,  
        'created_at' => now()
    ]);

    $type = 'research'; 
    $status = 'request_changes';
    $contributor = $research->user_id;
    $researchTitle = $research->title;

    // Create a new notification for the contributor
    Notification::create([
        'user_id' => $contributor, // Specify who the notification is for
        'notifiable_type' => User::class, // Specify the type of notifiable
        'notifiable_id' => $contributor, // Specify the ID of the notifiable
        'type' => $type,
        'related_type' => Research::class,
        'related_id' => $research->id,
        'data' => json_encode([
            'message' => "Your  $type '" . addslashes($researchTitle) . "' requires changes.", // Escape the project title
            'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'role' => 'reviewer',  // Specify the role
            'type' => $type, // Include the type
            'status' => $status, // Include the status
        ]),
        'created_at' => now(),
    ]);
        // Log the activity for requesting changes on the research
        ActivityLog::create([
            'log_name' => 'Research Needs Changes',
            'description' => 'Requested changes for the research titled "' . addslashes($research->title) . '"',
            'subject_type' => Research::class,
            'subject_id' => $research->id,
            'event' => 'requested change',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'research_title' => $research->title,
                'review_status' => 'needs changes',
                'role' => 'reviewer',
            ]),
            'created_at' => now(),
        ]);

    // Redirect back with a success message
    session()->flash('alert-success', 'Feedback for changes submitted successfully.');
    return to_route('reviewer.research.needchanges_list');
}

public function reject_research(Request $request)
{
    // Validate the form input
    $validated = $request->validate([
        'research_id' => 'required|exists:research,id',
        'feedback' => 'nullable|string|max:2000',  // Feedback is optional for rejection
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Create the feedback entry (only if feedback is provided)
    if ($validated['feedback']) {
        $feedback = Feedback::firstOrCreate([
            'feedback' => $validated['feedback'],
            'users_id' => $user->id,
        ]);

        // Find the research
        $research = Research::findOrFail($validated['research_id']);

        // Attach the feedback to the research
        $research->feedbacks()->syncWithoutDetaching($feedback->id);
    }

    // Update the research's review status to 'Rejected'
    $research = Research::findOrFail($validated['research_id']);
    $research->update([
        'review_status_id' => 2, // Assuming '2' corresponds to 'Rejected'
    ]);
    // Log the role action for rejection
    RoleAction::create([
        'user_id' => $user->id,  // ID of the authenticated user
        'content_id' => $research->id,          
        'content_type' => Research::class,  
        'role' => 'reviewer',
        'action' => 'rejected',  // Action name
        'created_at' => now(),    // Current timestamp
    ]);

    $type = 'research'; 
    $status = 'rejected';
    $contributor = $research->user_id;
    $researchTitle = $research->title;

    // Create a new notification for the contributor
    Notification::create([
        'user_id' => $contributor, // Specify who the notification is for
        'notifiable_type' => User::class, // Specify the type of notifiable
        'notifiable_id' => $contributor, // Specify the ID of the notifiable
        'type' => $type,
        'related_type' => Research::class,
        'related_id' => $research->id,
        'data' => json_encode([
            'message' => "Your  $type '" . addslashes($researchTitle) . "' has been rejected.", // Escape the project title
            'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'role' => 'reviewer',  // Specify the role
            'type' => $type, // Include the type
            'status' => $status, // Include the status
        ]),
        'created_at' => now(),
    ]);
    // Log the activity for rejecting the research
    ActivityLog::create([
        'log_name' => 'Research Rejected',
        'description' => 'Rejected the research titled "' . addslashes($research->title) . '"',
        'subject_type' => Research::class,
        'subject_id' => $research->id,
        'event' => 'rejected',
        'causer_type' => User::class,
        'causer_id' => auth()->user()->id,
        'properties' => json_encode([
            'research_title' => $research->title,
            'review_status' => 'rejected',
            'role' => 'reviewer',
        ]),
        'created_at' => now(),
    ]);

    // Redirect back with a success message
    session()->flash('alert-success', 'Research rejected successfully.');
    return to_route('reviewer.research.rejected');
}
public function under_review(Request $request)
{
    // Initialize the query with existing constraints
    $query = Research::where('review_status_id', 4) // Filter by 'Forwarded to Reviewer' status
        ->where('is_publish', 0) // Only include unpublished entries
        ->with('user'); // Load the related contributor's user information

    // Apply filters based on request parameters

    // Filter by title if present
    if ($request->filled('title')) {
        $query->where('title', 'LIKE', '%' . $request->title . '%');
    }

    // Filter by research category if present
    if ($request->filled('researchcategory_id')) {
        $query->where('researchcategory_id', $request->researchcategory_id);
    }

    // Filter by research status if present
    if ($request->filled('research_status')) {
        $query->where('research_status', $request->research_status);
    }

    // Apply SDG filter if present
    if ($request->filled('sdg')) {
        $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
        $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
            $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
        });
    }

    // Fetch the filtered list of researches and paginate the results
    $researches = $query->orderBy('id', 'desc')->paginate(5);

    // Fetch all research categories and SDGs for the filter dropdowns
    $researchCategories = Researchcategory::all();
    $sdgs = SDG::all();

    // Pass the filtered research to the view along with dropdown options
    return view('reviewer.research_extension.under_review', [
        'researches' => $researches,
        'researchCategories' => $researchCategories,
        'sdgs' => $sdgs,
    ]);
}

    
public function reviewed($id)
{
    // Find the research by ID
    $research = Research::findOrFail($id);

    // Update the review status to 'Forwarded to Approver'
    $research->update(['review_status_id' => 5]);

    // Log the action in the role_actions table
    RoleAction::create([
        'content_id' => $research->id,
        'content_type' => Research::class,
        'user_id' => auth()->user()->id,
        'role' => 'reviewer',
        'action' => 'reviewed',
        'created_at' => now(),
    ]);

    $type = 'research';
    $statusContributor = 'reviewed';
    $contributor = $research->user_id;
    $researchTitle = $research->title;

    // Create a notification for the contributor
    Notification::create([
        'user_id' => $contributor,
        'notifiable_type' => User::class,
        'notifiable_id' => $contributor,
        'type' => $type,
        'related_type' => Research::class,
        'related_id' => $research->id,
        'data' => json_encode([
            'message' => "Your $type '" . addslashes($researchTitle) . "' has been reviewed.",
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
            'related_type' => Research::class,
            'related_id' => $research->id,
            'data' => json_encode([
                'message' => "The research titled '" . addslashes($researchTitle) . "' has been submitted for approval.",
                'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'reviewer',
                'type' => $type,
                'status' => $statusApprover,
            ]),
            'created_at' => now(),
        ]);
    }
    // Log the activity for reviewing the research
    ActivityLog::create([
        'log_name' => 'Research Reviewed',
        'description' => 'Reviewed the research titled "' . addslashes($research->title) . '"',
        'subject_type' => Research::class,
        'subject_id' => $research->id,
        'event' => 'reviewed',
        'causer_type' => User::class,
        'causer_id' => auth()->user()->id,
        'properties' => json_encode([
            'research_title' => $research->title,
            'review_status' => 'reviewed',
            'role' => 'reviewer',
        ]),
        'created_at' => now(),
    ]);
    // Redirect back with a success message
    session()->flash('alert-success', 'Research reviewed successfully.');
    return to_route('reviewer.research.under_review');
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
        // Fetch the research by its ID
        $research = Research::findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Fetch the notification for the authenticated user and the specified notification ID
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class)
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead();
            }
        }
    
        // Pass the research and notification data to the view
        return view('reviewer.research_extension.show', compact('research', 'notificationData'));
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
