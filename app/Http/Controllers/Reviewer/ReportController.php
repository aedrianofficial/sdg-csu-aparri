<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Report\ReportRequest;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Report;
use App\Models\Project;
use App\Models\Research;
use App\Models\Reportimg;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function show_reviewed($id)
     {
         // Fetch the report by its ID
         $report = Report::with('user', 'sdg')->findOrFail($id);
 
         // Pass the report to the view
         return view('reviewer.reports.show_reviewed', compact('report'));
     }
     public function reviewed_list(Request $request)
{
    $user = Auth::user(); // Get the currently authenticated user
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'reviewed') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., Report IDs)
            ->toArray(); // Convert to array for further use
    
        // Start querying the Report model
        $query = Report::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->whereIn('review_status_id', [3,5,6]);// 'Needs Changes' status
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
        $reports = $query->with('user')->paginate(5); // Include user relation and paginate results
    // Fetch review statuses excluding specific ones for filtering dropdowns
    $reviewStatuses = ReviewStatus::whereNotIn('status', ['Need Changes', 'Rejected', 'Pending Review'])->get();

    // Fetch all SDGs for the SDG filter dropdown
    $sdgs = SDG::all();

    // Return the view with reports, reviewStatuses, and SDGs for filter dropdowns
    return view('reviewer.reports.reviewed', compact('reports', 'reviewStatuses', 'sdgs'));
}


    public function need_changes(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'report_id' => 'required|exists:reports,id',
            'feedback' => 'required|string|max:2000',
        ]);
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Find the report
        $report = Report::findOrFail($validated['report_id']);

        // Creating and attaching feedback
        if ($request->filled('feedback')) {
            $feedback = Feedback::firstOrCreate([
                'feedback' => $validated['feedback'],
                'users_id' => $user->id,
            ]);

            // Attach the feedback to the report
            $report->feedbacks()->syncWithoutDetaching($feedback->id);
        }

    
        // Update the report's review status to 'Needs Changes'
        $report->update([
            'review_status_id' => 1,  // Assuming '1' represents 'Needs Changes'
        ]);
        RoleAction::create([
            'user_id' => $user->id,
            'role' => 'reviewer',
            'action' => 'requested change',  
            'content_id' => $report->id,          
            'content_type' => Report::class,  
            'created_at' => now()
        ]);

        $type = 'report'; 
        $status = 'request_changes';
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
                'message' => "Your  $type '" . addslashes($reportTitle) . "' requires changes.", // Escape the project title
                'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'reviewer',  // Specify the role
                'type' => $type, // Include the type
                'status' => $status, // Include the status
            ]),
            'created_at' => now(),
        ]);
        // Log the activity for requesting changes on the report
        ActivityLog::create([
            'log_name' => 'Report Needs Changes',
            'description' => 'Requested changes for the report titled "' . addslashes($report->title) . '"',
            'subject_type' => Report::class,
            'subject_id' => $report->id,
            'event' => 'requested change',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'report_title' => $report->title,
                'review_status' => 'needs changes',
                'role' => 'reviewer',
            ]),
            'created_at' => now(),
        ]);
        // Redirect back with a success message
        session()->flash('alert-success', 'Feedback for changes submitted successfully.');
        return to_route('reviewer.reports.needchanges_list');
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
            $feedback = Feedback::firstOrCreate([
                'feedback' => $validated['feedback'],
                'users_id' => $user->id,
            ]);

            // Find the report
            $report = Report::findOrFail($validated['report_id']);

            // Attach the feedback to the report
            $report->feedbacks()->syncWithoutDetaching($feedback->id);
        }
    
        // Update the report's review status to 'Rejected'
        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'review_status_id' => 2,  // Assuming '2' represents 'Rejected'
        ]);
        RoleAction::create([
            'user_id' => $user->id,  // ID of the authenticated user
            'content_id' => $report->id,          
            'content_type' => Report::class,  
            'role' => 'reviewer',
            'action' => 'rejected',  // Action name
            'created_at' => now(),    // Current timestamp
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
                'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'reviewer',  // Specify the role
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
                'role' => 'reviewer',
            ]),
            'created_at' => now(),
        ]);
        session()->flash('alert-success', 'Report rejected successfully.');
        return to_route('reviewer.reports.rejected');
    }

    public function show_feedback_changes($id)
    {
        // Fetch the report by its ID
        $report = Report::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the report to the view
        return view('reviewer.feedbacks.need_changes.reports', compact('report'));
    }
    
    public function show_feedback_rejected($id)
    {
        // Fetch the report by its ID
        $report = Report::with('user', 'sdg', 'feedbacks.user')->findOrFail($id);

        // Pass the report to the view
        return view('reviewer.feedbacks.rejected.reports', compact('report'));
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
        $query = Report::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 2) // 'Needs Changes' status
            ->where('is_publish', 0); // Ensure they are not published
    
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
        $reportsPaginated = $query->with('user')->paginate(5); // Include user relation and paginate results
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = SDG::all();
    
        // Return the view with filtered reports and SDG options
        return view('reviewer.reports.rejected', compact('reportsPaginated', 'sdgs'));
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
        $query = Report::query()
            ->whereIn('id', $roleActions) // Only include reports related to the role actions
            ->where('review_status_id', 1) // 'Needs Changes' status
            ->where('is_publish', 0); // Ensure they are not published
    
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
        $reportsPaginated = $query->with('user')->paginate(5); // Include user relation and paginate results
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = SDG::all();
    
        // Return the view with filtered reports and SDG options
        return view('reviewer.reports.need_changes', compact('reportsPaginated', 'sdgs'));
    }
    
    
    
    

    public function under_review(Request $request)
    {
        // Start the query for fetching reports that are 'Forwarded to Reviewer'
        $query = Report::where('review_status_id', 4)
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
        $reports = $query->with(['reportimg', 'sdg', 'reviewStatus', 'user'])->paginate(5);
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = SDG::all();
    
        // Pass the reports and SDGs to the view
        return view('reviewer.reports.under_review', compact('reports', 'sdgs'));
    }
    
    
    public function reviewed($id)
{
    // Find the report by ID
    $report = Report::findOrFail($id);

    // Update the review status to 'Forwarded to Approver'
    $report->update(['review_status_id' => 5]);

    // Log the action in the role_actions table
    RoleAction::create([
        'content_id' => $report->id,
        'content_type' => Report::class,
        'user_id' => auth()->user()->id,
        'role' => 'reviewer',
        'action' => 'reviewed',
        'created_at' => now(),
    ]);

    $type = 'report';
    $statusContributor = 'reviewed';
    $contributor = $report->user_id;
    $reportTitle = $report->title;

    // Create a notification for the contributor
    Notification::create([
        'user_id' => $contributor,
        'notifiable_type' => User::class,
        'notifiable_id' => $contributor,
        'type' => $type,
        'related_type' => Report::class,
        'related_id' => $report->id,
        'data' => json_encode([
            'message' => "Your $type '" . addslashes($reportTitle) . "' has been reviewed.",
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
            'related_type' => Report::class,
            'related_id' => $report->id,
            'data' => json_encode([
                'message' => "The report titled '" . addslashes($reportTitle) . "' has been submitted for approval.",
                'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'reviewer',
                'type' => $type,
                'status' => $statusApprover,
            ]),
            'created_at' => now(),
        ]);
    }
    // Log the "reviewed" action in the activity_log table
    ActivityLog::create([
        'log_name' => 'Report Reviewed',
        'description' => 'Reviewed the report titled "' . addslashes($report->title) . '"',
        'subject_type' => Report::class,
        'subject_id' => $report->id,
        'event' => 'reviewed',
        'causer_type' => User::class,
        'causer_id' => auth()->user()->id,
        'properties' => json_encode([
            'report_title' => $report->title,
            'review_status' => 'reviewed',
            'role' => 'reviewer',
        ]),
        'created_at' => now(),
    ]);
    // Redirect back with a success message
    session()->flash('alert-success', 'Report reviewed successfully.');
    return to_route('reviewer.reports.under_review');
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
    
        // Pass the report and notification data to the view
        return view('reviewer.reports.show', compact('report', 'notificationData'));
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