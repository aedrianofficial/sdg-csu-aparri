<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Research;
use App\Models\Researchcategory;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show_approved($id)
    {
        // Fetch the research by its ID
        $research = Research::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the research to the view
        return view('approver.research_extension.show_approved', compact('research'));
    }
    public function approved_list(Request $request)
    {
          
        $user = Auth::user();
    
        $roleActions = RoleAction::where('user_id', $user->id)
        ->where('role', 'approver')
        ->where('action', 'approved') // Assuming this action reflects the need for changes
        ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
        ->toArray(); // Convert to array for further use

    // Start querying the Report model
        $query = Research::query()
        ->whereIn('id', $roleActions) // Only include reports related to the role actions
        ->whereIn('review_status_id', [3,6]); // 'Needs Changes' status
       
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
        $sdgs = Sdg::all();  
       
        return view('approver.research_extension.approved', compact('researches','reviewStatuses','researchCategories','sdgs'));
    }
    
     public function show_feedback_rejected($id)
     {
         // Fetch the research by its ID
         $research = Research::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);
 
         // Pass the research to the view
         return view('approver.feedbacks.rejected.research_extension', compact('research'));
     }
     public function rejected_list(Request $request)
     {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'approver')
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
        return view('approver.research_extension.rejected', compact('researches','researchCategories','sdgs'));
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
        $feedback = Feedback::create([
            'feedback' => $validated['feedback'],
            'users_id' => $user->id,  // Authenticated user's ID
        ]);

        // Find the research and attach feedback to it
        $research = Research::findOrFail($validated['research_id']);
        $research->feedbacks()->attach($feedback->id);
    }

    // Update the research's review status to 'Rejected'
    $research = Research::findOrFail($validated['research_id']);
    $research->update([
        'review_status_id' => 2, // Assuming '2' corresponds to 'Rejected'
    ]);
    RoleAction::create(attributes: [
        'content_id' => $research->id,          
        'content_type' => Research::class,      
        'user_id' => auth()->user()->id,       
        'role' => 'approver',                  
        'action' => 'rejected',                
        'created_at' => now(),                 
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
            'approver' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'role' => 'approver',  // Specify the role
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
            'role' => 'approver',
        ]),
        'created_at' => now(),
    ]);

    // Redirect back with a success message
    session()->flash('alert-success', 'Research rejected successfully.');
    return to_route('approver.research.index');
}
    public function index(Request $request)
    {
        // Initialize the query with existing constraints
    $query = Research::where('review_status_id', 5) // Filter by 'Forwarded to Reviewer' status
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
        // Pass the research to the view
        return view('approver.research_extension.index', compact('researches','researchCategories','sdgs'));
    }

    public function approved($id)
    {
        // Find the research by ID
        $research = Research::findOrFail($id);
    
        // Update the review status to 'Forwarded to Approver'
        $research->update([
            'review_status_id' => 6,
        ]);
    
        // Log the action into the role_actions table using polymorphic relationship fields
        RoleAction::create([
            'content_id' => $research->id,
            'content_type' => Research::class,
            'user_id' => auth()->user()->id,
            'role' => 'approver',
            'action' => 'approved',
            'created_at' => now(),
        ]);
    
        // Notify the research contributor
        $type = 'research'; 
        $status = 'approved';
        $contributor = $research->user_id;
        $researchTitle = $research->title;
    
        Notification::create([
            'user_id' => $contributor, // Specify who the notification is for
            'notifiable_type' => User::class, // Specify the type of notifiable
            'notifiable_id' => $contributor, // Specify the ID of the notifiable
            'type' => $type,
            'related_type' => Research::class,
            'related_id' => $research->id,
            'data' => json_encode([
                'message' => "Your $type '" . addslashes($researchTitle) . "' has been approved.", // Escape the research title
                'approver' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'approver',  // Specify the role
                'type' => $type, // Include the type
                'status' => $status, // Include the status
            ]),
            'created_at' => now(),
        ]);
    
        // Notify all publishers
        $statusPublisher = 'submitted for publishing';
    
        // Retrieve all publishers
        $publishers = User::where('role', 'publisher')->get();
    
        // Create notifications for each publisher
        foreach ($publishers as $publisher) {
            Notification::create([
                'user_id' => $publisher->id,
                'notifiable_type' => User::class,
                'notifiable_id' => $publisher->id,
                'type' => $type,
                'related_type' => Research::class,
                'related_id' => $research->id,
                'data' => json_encode([
                    'message' => "The research titled '" . addslashes($researchTitle) . "' has been submitted for publishing.",
                    'approver' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'role' => 'approver',
                    'type' => $type,
                    'status' => $statusPublisher,
                ]),
                'created_at' => now(),
            ]);
        }
        // Log the activity for approving the research
        ActivityLog::create([
            'log_name' => 'Research Approved',
            'description' => 'Approved the research titled "' . addslashes($research->title) . '"',
            'subject_type' => Research::class,
            'subject_id' => $research->id,
            'event' => 'approved',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'research_title' => $research->title,
                'review_status' => 'approved',
                'role' => 'approver',
            ]),
            'created_at' => now(),
        ]);
        // Redirect back with a success message
        session()->flash('alert-success', 'Research Approved Successfully.');
        return to_route('approver.research.index');
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
            // Fetch the notification related to the research and mark it as read
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
        return view('approver.research_extension.show', compact('research', 'notificationData'));
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
