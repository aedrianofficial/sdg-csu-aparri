<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReviewStatus;
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
    public function show_approved($id)
    {
        // Fetch the report by its ID
        $report = Report::with('user', 'sdg')->findOrFail($id);

        // Pass the report to the view
        return view('approver.reports.show_approved', compact('report'));
    }
    public function approved_list(Request $request)
    {
          
        $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'approver')
            ->where('action', 'approved') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Report::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->whereIn('review_status_id', [3,6]);// 'Needs Changes' status
          // Ensure they are not published
    
        // Apply filters based on request parameters
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        if ($request->filled('review_status')) {
            $query->where('review_status_id', $request->review_status);
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
    $reviewStatuses = ReviewStatus::whereNotIn('status', ['Need Changes', 'Rejected', 'Pending Review'])->get();

    // Fetch all SDGs for the SDG filter dropdown
    $sdgs = SDG::all();
    
        // Return the view with reports, reviewStatuses, and SDGs for filter dropdowns
        return view('approver.reports.approved', compact('reports', 'reviewStatuses', 'sdgs'));
    
    }

    public function show_feedback_rejected($id)
    {
        // Fetch the report by its ID
        $report = Report::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the report to the view
        return view('approver.feedbacks.rejected.reports', compact('report'));
    }
    public function rejected_list(Request $request)
    {
         // Start the query for fetching reports that are 'Forwarded to Reviewer'
         $query = Report::where('review_status_id', 5)
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
     $sdgs = SDG::all();
 
     // Pass the reports and SDGs to the view
     return view('approver.reports.rejected', compact('reports', 'sdgs'));
    }

     public function reject_report(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'report_id' => 'required|exists:reports,id',
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
    
            // Find the report and attach feedback to it
            $report = Report::findOrFail($validated['report_id']);
            $report->feedbacks()->attach($feedback->id);
        }
    
        // Update the report's review status to 'Rejected'
        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'review_status_id' => 2,  // Assuming '2' represents 'Rejected'
        ]);
        RoleAction::create(attributes: [
            'content_id' => $report->id,          
            'content_type' => Report::class,      
            'user_id' => auth()->user()->id,       
            'role' => 'approver',                  
            'action' => 'rejected',                
            'created_at' => now(),                 
        ]);
    
        $type = 'report'; 
        $status = 'rejected';
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
                'message' => "Your  $type '" . addslashes($reportTitle) . "' has been rejected.", // Escape the project title
                'approver' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'approver',  // Specify the role
                'type' => $type, // Include the type
                'status' => $status, // Include the status
            ]),
            'created_at' => now(),
        ]);

        // Log the activity for rejecting the report
        ActivityLog::create([
            'log_name' => 'Report Rejected',
            'description' => 'Rejected the report titled "' . addslashes($report->title) . '"',
            'subject_type' => Report::class,
            'subject_id' => $report->id,
            'event' => 'rejected',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'report_title' => $report->title,
                'review_status' => 'rejected',
                'role' => 'approver',
            ]),
            'created_at' => now(),
        ]);

        session()->flash('alert-success', 'Report rejected successfully.');
        return to_route('approver.reports.index');
    }

    public function index(Request $request)
    {
         // Start the query for fetching reports that are 'Forwarded to Reviewer'
         $query = Report::where('review_status_id', 5)
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
     $sdgs = SDG::all();
 
     // Pass the reports and SDGs to the view
     return view('approver.reports.index', compact('reports', 'sdgs'));
    }


    public function approved($id)
    {
        // Find the report by ID
        $report = Report::findOrFail($id);
    
        // Update the review status to 'Forwarded to Approver'
        $report->update([
            'review_status_id' => '6',
        ]);
    
        // Log the action into the role_actions table using polymorphic relationship fields
        RoleAction::create([
            'content_id' => $report->id,
            'content_type' => Report::class,
            'user_id' => auth()->user()->id,
            'role' => 'approver',
            'action' => 'approved',
            'created_at' => now(),
        ]);
    
        // Notify the report contributor
        $type = 'report'; 
        $status = 'approved';
        $contributor = $report->user_id;
        $reportTitle = $report->title;
    
        Notification::create([
            'user_id' => $contributor, // Specify who the notification is for
            'notifiable_type' => User::class, // Specify the type of notifiable
            'notifiable_id' => $contributor, // Specify the ID of the notifiable
            'type' => $type,
            'related_type' => Report::class,
            'related_id' => $report->id,
            'data' => json_encode([
                'message' => "Your $type '" . addslashes($reportTitle) . "' has been approved and is now published.", // Escape the report title
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
                'related_type' => Report::class,
                'related_id' => $report->id,
                'data' => json_encode([
                    'message' => "The report titled '" . addslashes($reportTitle) . "' has been submitted for publishing.",
                    'approver' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'role' => 'approver',
                    'type' => $type,
                    'status' => $statusPublisher,
                ]),
                'created_at' => now(),
            ]);
        }
        // Log the activity for approving the report
        ActivityLog::create([
            'log_name' => 'Report Approved',
            'description' => 'Approved the report titled "' . addslashes($report->title) . '"',
            'subject_type' => Report::class,
            'subject_id' => $report->id,
            'event' => 'approved',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'report_title' => $report->title,
                'review_status' => 'approved',
                'role' => 'approver',
            ]),
            'created_at' => now(),
        ]);

        // Redirect back with a success message
        session()->flash('alert-success', 'Report Approved Successfully.');
        return to_route('approver.reports.index');
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
        return view('approver.reports.show', compact('report', 'notificationData'));
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
