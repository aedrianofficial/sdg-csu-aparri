<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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
    public function show_published($id)
     {
         // Fetch the research by its ID
         $research = Research::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);
 
         // Pass the research to the view
         return view('publisher.research_extension.show_published', compact('research'));
     }
     public function published_list(Request $request)
     {
        $user = Auth::user();
    
        $roleActions = RoleAction::where('user_id', $user->id)
        ->where('role', 'publisher')
        ->where('action', 'published') // Assuming this action reflects the need for changes
        ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
        ->toArray(); // Convert to array for further use

    // Start querying the Report model
        $query = Research::query()
        ->whereIn('id', $roleActions) // Only include reports related to the role actions
        ->where('review_status_id', 3)
        ->where('is_publish', 1); 
       
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
        $researches = $query->paginate(5);
    
        // Fetch all SDGs and review statuses for the filter dropdowns
        $reviewStatuses = ReviewStatus::all();
        $researchCategories = Researchcategory::all();
        $sdgs = Sdg::all();  
       
        return view('publisher.research_extension.published', compact('researches','reviewStatuses','researchCategories','sdgs'));
     }
    public function index(Request $request)
    {
        // Initialize the query with existing constraints
    $query = Research::where('review_status_id', 6) // Filter by 'Forwarded to Reviewer' status
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
    $researches = $query->paginate(5);

    // Fetch all research categories and SDGs for the filter dropdowns
    $researchCategories = Researchcategory::all();
    $sdgs = Sdg::all();

// Pass the research to the view
        return view('publisher.research_extension.index', compact('researches','researchCategories','sdgs'));
    }

    public function published($id)
    {
        // Find the research by ID
        $research = Research::findOrFail($id);
        // Log the action into the role_actions table using polymorphic relationship fields
        RoleAction::create([
            'content_id' => $research->id,          
            'content_type' => Research::class,      
            'user_id' => auth()->user()->id,       
            'role' => 'publisher',                  
            'action' => 'published',                
            'created_at' => now(),                 
        ]);
        // Update the review status to 'Forwarded to Approver'
        $research->update([
            'is_publish' => 1,
            'review_status_id' =>3
        ]);

        $type = 'research'; 
        $status = 'published';
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
                'message' => "Your  $type '" . addslashes($researchTitle) . "' has been published.", // Escape the project title
                'publisher' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'publisher',  // Specify the role
                'type' => $type, // Include the type
                'status' => $status, // Include the status
            ]),
            'created_at' => now(),
        ]);
        // Log the activity for publishing the research
        ActivityLog::create([
            'log_name' => 'Research Published',
            'description' => 'Published the research titled "' . addslashes($researchTitle) . '"',
            'subject_type' => Research::class,
            'subject_id' => $research->id,
            'event' => 'published',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'research_title' => $researchTitle,
                'review_status' => 'published',
                'role' => 'publisher',
            ]),
            'created_at' => now(),
        ]);

        // Redirect back with a success message
        session()->flash('alert-success', 'Research Published Successfully.');
        return to_route('publisher.research.index');
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
        return view('publisher.research_extension.show', compact('research', 'notificationData'));
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
