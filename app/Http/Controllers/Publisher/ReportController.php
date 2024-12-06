<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Report;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show_published($id)
    {
        // Fetch the report by its ID
        $report = Report::with('user', 'sdg')->findOrFail($id);

        // Pass the report to the view
        return view('publisher.reports.show_published', compact('report'));
    }
    public function published_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'publisher')
            ->where('action', 'published') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Report::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 3)
            ->where('is_publish', 1);// 'Needs Changes' status
          // Ensure they are not published
    
        // Apply filters based on request parameters
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        // Filter by related_type (e.g., project or research)
        if ($request->filled('related_type')) {
            $query->where('related_type', $request->related_type);
        }
    
        // Apply SDG filter if present
        if ($request->filled('sdg')) {
            $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
            $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
            });
        }
    
        // Execute the query and get the filtered reports
        $reports = $query->with('user')->orderBy('id', 'desc')->paginate(5); // Include user relation and paginate results
    // Fetch review statuses excluding specific ones for filtering dropdowns

    // Fetch all SDGs for the SDG filter dropdown
    $sdgs = SDG::all();
    
        // Return the view with reports, reviewStatuses, and SDGs for filter dropdowns
        return view('publisher.reports.published', compact('reports', 'sdgs'));
    }
  
    public function index(Request $request)
    {
        
        // Start the query for fetching reports that are 'Forwarded to Reviewer'
        $query = Report::where('review_status_id', 6)
        ->where('is_publish', 0); // Filter for reports marked as "Under Review" and not published

    // Apply filters based on request parameters
    if ($request->filled('title')) {
        $query->where('title', 'LIKE', '%' . $request->title . '%');
    }

    // Filter by related_type (e.g., project or research)
    if ($request->filled('related_type')) {
        $query->where('related_type', $request->related_type);
    }

    // Apply SDG filter if present
    if ($request->filled('sdg')) {
        $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
        $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
            $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
        });
    }

    // Fetch the filtered list of reports and paginate with eager loading for related data
    $reports = $query->with(['reportimg', 'sdg', 'reviewStatus', 'user'])->orderBy('id', 'desc')->paginate(5);

    // Fetch all SDGs for the filter dropdown
    $sdgs = Sdg::all();

    // Pass the reports and SDGs to the view
    return view('publisher.reports.index', compact('reports', 'sdgs'));
    }

    
    public function published($id){
        // Find the project by ID
        $report = Report::findOrFail($id);

    // Log the action into the role_actions table using polymorphic relationship fields
        RoleAction::create([
        'content_id' => $report->id,          
        'content_type' => Report::class,      
        'user_id' => auth()->user()->id,       
        'role' => 'publisher',                  
        'action' => 'published',                
        'created_at' => now(),                 
    ]);
        // Update the review status to 'Forwarded to Approver'
        $report->update([
            'is_publish' => 1,
            'review_status_id' =>3 
        ]);
    
        $type = 'report'; 
        $status = 'published';
        $contributor = $report->user_id;
        $reportTitle = $report->title;
  
        // Create a new notification for the contributor
        Notification::create([
            'user_id' => $contributor, // Specify who the notification is for
            'notifiable_type' => User::class, // Specify the type of notifiable
            'notifiable_id' => $contributor, // Specify the ID of the notifiable
            'type' => $type,
            'related_type' => Report::class,
            'related_id' => $report->id,
            'data' => json_encode([
                'message' => "Your  $type '" . addslashes($reportTitle) . "' has been published.", // Escape the project title
                'publisher' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'publisher',  // Specify the role
                'type' => $type, // Include the type
                'status' => $status, // Include the status
            ]),
            'created_at' => now(),
        ]);

                // Log the activity for publishing the report
            ActivityLog::create([
                'log_name' => 'Report Published',
                'description' => 'Published the report titled "' . addslashes($reportTitle) . '"',
                'subject_type' => Report::class,
                'subject_id' => $report->id,
                'event' => 'published',
                'causer_type' => User::class,
                'causer_id' => auth()->user()->id,
                'properties' => json_encode([
                    'report_title' => $reportTitle,
                    'review_status' => 'published',
                    'role' => 'publisher',
                ]),
                'created_at' => now(),
            ]);
        // Redirect back with a success message
        session()->flash('alert-success', 'Report Published Successfully.');
        return to_route('publisher.reports.index');
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
    // Fetch the report by its ID
    $report = Report::findOrFail($id);

    // Check if there's a notification ID in the request
    $notificationId = $request->query('notification_id');
    $notificationData = null;

    if ($notificationId) {
        // Fetch the notification related to the report and mark it as read
        $notification = Notification::where('notifiable_id', Auth::id())
            ->where('notifiable_type', User::class)
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notificationData = json_decode($notification->data, true);
            $notification->markAsRead();
        }
    }

    // Pass the report and notification data to the view
    return view('publisher.reports.show', compact('report', 'notificationData'));
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
