<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Research;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\StatusReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function under_review(Request $request)
    {
        // Fetching the filters from the request
        $title = $request->input('title');
        $relatedType = $request->input('related_type');
        $reviewStatusId = $request->input('review_status');
        $sdgIds = $request->input('sdg', []);
        $logStatus = $request->input('log_status'); // New log_status filter
    
        // Initialize the query for StatusReport
        $reportsQuery = StatusReport::query()
        ->where('review_status_id', 4)
        ->where('is_publish', 0)
            ->when($title, function ($query, $title) {
                return $query->where('related_title', 'like', '%' . $title . '%');
            })
            ->when($relatedType, function ($query, $relatedType) {
                return $query->where('related_type', $relatedType);
            })
            ->when($reviewStatusId, function ($query, $reviewStatusId) {
                return $query->where('review_status_id', $reviewStatusId);
            })
            ->when($logStatus, function ($query, $logStatus) { // Add this line
                return $query->where('log_status', $logStatus);
            })
            ->with('reviewStatus'); // Eager load review status
    
        // Fetching review statuses and SDGs for the filters
        $reviewStatuses = ReviewStatus::all();
        $sdgs = Sdg::all();
    
        // Fetch SDGs based on related type
        $reports = $reportsQuery->orderBy('id', 'desc')->get(); // Get all reports without pagination for filtering
    
        foreach ($reports as $report) {
            if ($report->related_type === 'App\Models\Project') {
                $report->sdgs = Project::find($report->related_id)->sdg; // Fetch SDGs for the related project
            } elseif ($report->related_type === 'App\Models\Research') {
                $report->sdgs = Research::find($report->related_id)->sdg; // Fetch SDGs for the related research
            } else {
                $report->sdgs = []; // Default to an empty array if no related type matches
            }
        }
    
        // If no related type is selected, filter reports by selected SDGs
        if (empty($relatedType) && !empty($sdgIds)) {
            $projectIds = Project::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            $researchIds = Research::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            // Filter reports based on the related IDs from projects and research
            $reports = $reports->filter(function ($report) use ($projectIds, $researchIds) {
                return ($report->related_type === 'App\Models\Project' && $projectIds->contains($report->related_id)) ||
                       ($report->related_type === 'App\Models\Research' && $researchIds->contains($report->related_id));
            });
        }
    
        // Paginate the filtered results
        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $reports->forPage($request->input('page', 1), 10), // Get the current page items
            $reports->count(), // Total items
            10, // Items per page
            $request->input('page', 1), // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Preserve query parameters
        );
    
        // Returning the view with the reports and filter data
        return view('reviewer.status_reports.under_review', compact('reports', 'reviewStatuses', 'sdgs'));
    }
    public function showProject(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.status_reports.project.show', compact('statusReport', 'notificationData'));
    }
    public function showProjectReviewed(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.status_reports.project.show_reviewed', compact('statusReport', 'notificationData'));
    }
    public function showResearch(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.status_reports.research.show', compact('statusReport', 'notificationData'));
    }
    public function showResearchReviewed(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.status_reports.research.show_reviewed', compact('statusReport', 'notificationData'));
    }

    public function need_changes(Request $request)
    {
    // Validate the form input
    $validated = $request->validate([
        'status_report_id' => 'required|exists:status_reports,id',
        'feedback' => 'required|string|max:2000',
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Find the status report
    $statusReport = StatusReport::findOrFail($validated['status_report_id']);

    // Creating and attaching feedback
    if ($request->filled('feedback')) {
        $feedback = Feedback::firstOrCreate([
            'feedback' => $validated['feedback'],
            'users_id' => $user->id,
        ]);

        // Attach the feedback to the status report
        $statusReport->feedbacks()->syncWithoutDetaching($feedback->id);
    }

    // Update the status report's review status to 'Needs Changes'
    $statusReport->update([
        'review_status_id' => 1, 
        'is_publish' =>  0 
    ]);

    RoleAction::create([
        'user_id' => $user->id,
        'role' => 'reviewer',
        'action' => 'requested change',
        'content_id' => $statusReport->id,
        'content_type' => StatusReport::class,
        'created_at' => now()
    ]);

    $type = 'status report'; 
    $status = 'request_changes';
    $contributor = $statusReport->logged_by_id; // Assuming the contributor is the user who logged the report
    $reportTitle = $statusReport->related_title; // Assuming related_title is the title of the report

    // Create a new notification for the contributor
    Notification::create([
        'user_id' => $contributor, // Specify who the notification is for
        'notifiable_type' => User::class, // Specify the type of notifiable
        'notifiable_id' => $contributor, // Specify the ID of the notifiable
        'type' => $type,
        'related_type' => StatusReport::class,
        'related_id' => $statusReport->id,
        'data' => json_encode([
            'message' => "Your $type '" . addslashes($reportTitle) . "' requires changes.", // Escape the report title
            'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'role' => 'reviewer',  // Specify the role
            'type' => $type, // Include the type
            'status' => $status, // Include the status
        ]),
        'created_at' => now(),
    ]);

    // Log the activity for requesting changes on the status report
    ActivityLog::create([
        'log_name' => 'Status Report Needs Changes',
        'description' => 'Requested changes for the status report titled "' . addslashes($statusReport->related_title) . '"',
        'subject_type' => StatusReport::class,
        'subject_id' => $statusReport->id,
        'event' => 'requested change',
        'causer_type' => User::class,
        'causer_id' => auth()->user()->id,
        'properties' => json_encode([
            'status_report_title' => $statusReport->related_title,
            'review_status' => 'needs changes',
            'role' => 'reviewer',
        ]),
        'created_at' => now(),
    ]);

    // Redirect back with a success message
    session()->flash('alert-success', 'Feedback for changes submitted successfully.');
    return to_route('reviewer.status_reports.needchanges_list');
    }
    public function reject_report(Request $request)
    {
    // Validate the form input
    $validated = $request->validate([
        'status_report_id' => 'required|exists:status_reports,id',
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

        // Find the status report
        $statusReport = StatusReport::findOrFail($validated['status_report_id']);

        // Attach the feedback to the status report
        $statusReport->feedbacks()->syncWithoutDetaching($feedback->id);
    }

    // Update the status report's review status to 'Rejected'
    $statusReport = StatusReport::findOrFail($validated['status_report_id']);
    $statusReport->update([
        'review_status_id' => 2,  
        'is_publish' =>  0 
    ]);

    RoleAction::create([
        'user_id' => $user->id,  // ID of the authenticated user
        'content_id' => $statusReport->id,          
        'content_type' => StatusReport::class,  
        'role' => 'reviewer',
        'action' => 'rejected',  // Action name
        'created_at' => now(),    // Current timestamp
    ]);

    $type = 'status report'; 
    $status = 'rejected';
    $contributor = $statusReport->logged_by_id; // Assuming the contributor is the user who logged the report
    $reportTitle = $statusReport->related_title; // Assuming related_title is the title of the report

    // Create a new notification for the contributor
    Notification::create([
        'user_id' => $contributor, // Specify who the notification is for
        'notifiable_type' => User::class, // Specify the type of notifiable
        'notifiable_id' => $contributor, // Specify the ID of the notifiable
        'type' => $type,
        'related_type' => StatusReport::class,
        'related_id' => $statusReport->id,
        'data' => json_encode([
            'message' => "Your $type '" . addslashes($reportTitle) . "' has been rejected.", // Escape the report title
            'reviewer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'role' => 'reviewer',  // Specify the role
            'type' => $type, // Include the type
            'status' => $status, // Include the status
        ]),
        'created_at' => now(),
    ]);

    // Log the activity for rejecting the status report
    ActivityLog::create([
        'log_name' => 'Status Report Rejected',
        'description' => 'Rejected the status report titled "' . addslashes($statusReport->related_title) . '"',
        'subject_type' => StatusReport::class,
        'subject_id' => $statusReport->id,
        'event' => 'rejected',
        'causer_type' => User::class,
        'causer_id' => auth()->user()->id,
        'properties' => json_encode([
            'status_report_title' => $statusReport->related_title,
            'review_status' => 'rejected',
            'role' => 'reviewer',
        ]),
        'created_at' => now(),
    ]);

    session()->flash('alert-success', 'Status report rejected successfully.');
    return to_route('reviewer.status_reports.rejected_list');
    }
    public function reviewed($id)
    {
        // Find the status report by ID
        $statusReport = StatusReport::findOrFail($id);
    
        // Update the review status to 'Forwarded to Approver'
        $statusReport->update(['review_status_id' => 5]); // Assuming '5' represents 'Forwarded to Approver'
    
        // Log the action in the role_actions table
        RoleAction::create([
            'content_id' => $statusReport->id,
            'content_type' => StatusReport::class,
            'user_id' => auth()->user()->id,
            'role' => 'reviewer',
            'action' => 'reviewed',
            'created_at' => now(),
        ]);
    
        $type = 'status report';
        $statusContributor = 'reviewed';
        $contributor = $statusReport->logged_by_id; // Assuming the contributor is the user who logged the report
        $reportTitle = $statusReport->related_title; // Assuming related_title is the title of the report
    
        // Create a notification for the contributor
        Notification::create([
            'user_id' => $contributor,
            'notifiable_type' => User::class,
            'notifiable_id' => $contributor,
            'type' => $type,
            'related_type' => StatusReport::class,
            'related_id' => $statusReport->id,
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
                'related_type' => StatusReport::class,
                'related_id' => $statusReport->id,
                'data' => json_encode([
                    'message' => "The status report titled '" . addslashes($reportTitle) . "' has been submitted for approval.",
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
            'log_name' => 'Status Report Reviewed',
            'description' => 'Reviewed the status report titled "' . addslashes($statusReport->related_title) . '"',
            'subject_type' => StatusReport::class,
            'subject_id' => $statusReport->id,
            'event' => 'reviewed',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'status_report_title' => $statusReport->related_title,
                'review_status' => 'reviewed',
                'role' => 'reviewer',
            ]),
            'created_at' => now(),
        ]);
    
        // Redirect back with a success message
        session()->flash('alert-success', 'Status report reviewed successfully.');
        return to_route('reviewer.status_reports.under_review');
    }
    public function showProjectNeedChanges(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('feedbacks.user','loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.feedbacks.need_changes.status_report_projects', compact('statusReport', 'notificationData'));
    }
    public function showProjectRejected(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('feedbacks.user','loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.feedbacks.rejected.status_report_projects', compact('statusReport', 'notificationData'));
    }
    public function showResearchNeedChanges(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('feedbacks.user','loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.feedbacks.need_changes.status_report_research', compact('statusReport', 'notificationData'));
    }
    public function showResearchRejected(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('feedbacks.user','loggedBy')->findOrFail($id);
    
        // Check if there's a notification ID in the request
        $notificationId = $request->query('notification_id');
        $notificationData = null;
    
        if ($notificationId) {
            // Directly query the Notification model
            $notification = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', User::class) // Ensure this matches your notifiable type
                ->where('id', $notificationId)
                ->first();
    
            if ($notification) {
                $notificationData = json_decode($notification->data, true);
                $notification->markAsRead(); // Mark the notification as read
            }
        }
    
        // Return the view for showing the status report details, including notification data
        return view('reviewer.feedbacks.rejected.status_report_research', compact('statusReport', 'notificationData'));
    }
    public function need_changes_list(Request $request)
    {
         $user = Auth::user(); // Get the currently authenticated user
    
         // Fetching the filters from the request
         $title = $request->input('title');
         $relatedType = $request->input('related_type');
         $reviewStatusId = $request->input('review_status');
         $sdgIds = $request->input('sdg', []);
         $logStatus = $request->input('log_status'); // New log_status filter

        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'requested change') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., StatusReport IDs)
            ->toArray(); // Convert to array for further use
    
       // Initialize the query for StatusReport
        $reportsQuery = StatusReport::query()
        ->whereIn('id', $roleActions)
        ->where('review_status_id', 1)
        ->where('is_publish', 0)
            ->when($title, function ($query, $title) {
                return $query->where('related_title', 'like', '%' . $title . '%');
            })
            ->when($relatedType, function ($query, $relatedType) {
                return $query->where('related_type', $relatedType);
            })
            ->when($reviewStatusId, function ($query, $reviewStatusId) {
                return $query->where('review_status_id', $reviewStatusId);
            })
            ->when($logStatus, function ($query, $logStatus) { // Add this line
                return $query->where('log_status', $logStatus);
            })
            ->with('reviewStatus'); // Eager load review status
    
        // Fetching review statuses and SDGs for the filters
        $reviewStatuses = ReviewStatus::all();
        $sdgs = Sdg::all();
    
        // Fetch SDGs based on related type
        $statusReports = $reportsQuery->with('loggedBy')->orderBy('id', 'desc')->get(); // Get all reports without pagination for filtering
    
        foreach ($statusReports as $report) {
            if ($report->related_type === 'App\Models\Project') {
                $report->sdgs = Project::find($report->related_id)->sdg; // Fetch SDGs for the related project
            } elseif ($report->related_type === 'App\Models\Research') {
                $report->sdgs = Research::find($report->related_id)->sdg; // Fetch SDGs for the related research
            } else {
                $report->sdgs = []; // Default to an empty array if no related type matches
            }
        }
    
        // If no related type is selected, filter reports by selected SDGs
        if (empty($relatedType) && !empty($sdgIds)) {
            $projectIds = Project::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            $researchIds = Research::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            // Filter reports based on the related IDs from projects and research
            $statusReports = $statusReports->filter(function ($report) use ($projectIds, $researchIds) {
                return ($report->related_type === 'App\Models\Project' && $projectIds->contains($report->related_id)) ||
                       ($report->related_type === 'App\Models\Research' && $researchIds->contains($report->related_id));
            });
        }

        // Paginate the filtered results
        $statusReportsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $statusReports->forPage($request->input('page', 1), 5), // Get the current page items
            $statusReports->count(), // Total items
            5, // Items per page
            $request->input('page', 1), // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Preserve query parameters
        );
    
        // Fetch all related types for the filter dropdown (if needed)
        $relatedTypes = ['App\Models\Project', 'App\Models\Research']; // Example related types
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = Sdg::all();
    
        // Return the view with filtered status reports, related types options, and SDGs
        return view('reviewer.status_reports.need_changes', compact('statusReportsPaginated', 'relatedTypes', 'sdgs'));
    }
    public function reviewed_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
    
         // Fetching the filters from the request
         $title = $request->input('title');
         $relatedType = $request->input('related_type');
         $reviewStatusId = $request->input('review_status');
         $sdgIds = $request->input('sdg', []);
         $logStatus = $request->input('log_status'); // New log_status filter

        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'reviewed') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., StatusReport IDs)
            ->toArray(); // Convert to array for further use
    
       // Initialize the query for StatusReport
        $reportsQuery = StatusReport::query()
        ->whereIn('id', $roleActions)
        ->whereIn('review_status_id', [3,5,6])
            ->when($title, function ($query, $title) {
                return $query->where('related_title', 'like', '%' . $title . '%');
            })
            ->when($relatedType, function ($query, $relatedType) {
                return $query->where('related_type', $relatedType);
            })
            ->when($reviewStatusId, function ($query, $reviewStatusId) {
                return $query->where('review_status_id', $reviewStatusId);
            })
            ->when($logStatus, function ($query, $logStatus) { // Add this line
                return $query->where('log_status', $logStatus);
            })
            ->with('reviewStatus'); // Eager load review status
    
        // Fetching review statuses and SDGs for the filters
        $reviewStatuses = ReviewStatus::all();
        $sdgs = Sdg::all();
    
        // Fetch SDGs based on related type
        $statusReports = $reportsQuery->with('loggedBy')->orderBy('id', 'desc')->get(); // Get all reports without pagination for filtering
    
        foreach ($statusReports as $report) {
            if ($report->related_type === 'App\Models\Project') {
                $report->sdgs = Project::find($report->related_id)->sdg; // Fetch SDGs for the related project
            } elseif ($report->related_type === 'App\Models\Research') {
                $report->sdgs = Research::find($report->related_id)->sdg; // Fetch SDGs for the related research
            } else {
                $report->sdgs = []; // Default to an empty array if no related type matches
            }
        }
    
        // If no related type is selected, filter reports by selected SDGs
        if (empty($relatedType) && !empty($sdgIds)) {
            $projectIds = Project::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            $researchIds = Research::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            // Filter reports based on the related IDs from projects and research
            $statusReports = $statusReports->filter(function ($report) use ($projectIds, $researchIds) {
                return ($report->related_type === 'App\Models\Project' && $projectIds->contains($report->related_id)) ||
                       ($report->related_type === 'App\Models\Research' && $researchIds->contains($report->related_id));
            });
        }
        // Paginate the filtered results
        $statusReportsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $statusReports->forPage($request->input('page', 1), 5), // Get the current page items
            $statusReports->count(), // Total items
            5, // Items per page
            $request->input('page', 1), // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Preserve query parameters
        );
    
        // Fetch all related types for the filter dropdown (if needed)
        $relatedTypes = ['App\Models\Project', 'App\Models\Research']; // Example related types
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = Sdg::all();
    
        // Return the view with filtered status reports, related types options, and SDGs
        return view('reviewer.status_reports.reviewed', compact('statusReportsPaginated', 'relatedTypes', 'sdgs'));
    }
    public function rejected_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
    
         // Fetching the filters from the request
         $title = $request->input('title');
         $relatedType = $request->input('related_type');
         $reviewStatusId = $request->input('review_status');
         $sdgIds = $request->input('sdg', []);
         $logStatus = $request->input('log_status'); // New log_status filter

        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'reviewer')
            ->where('action', 'rejected') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., StatusReport IDs)
            ->toArray(); // Convert to array for further use
    
       // Initialize the query for StatusReport
        $reportsQuery = StatusReport::query()
        ->whereIn('id', $roleActions)
        ->where('review_status_id', 2)
        ->where('is_publish', 0)
            ->when($title, function ($query, $title) {
                return $query->where('related_title', 'like', '%' . $title . '%');
            })
            ->when($relatedType, function ($query, $relatedType) {
                return $query->where('related_type', $relatedType);
            })
            ->when($reviewStatusId, function ($query, $reviewStatusId) {
                return $query->where('review_status_id', $reviewStatusId);
            })
            ->when($logStatus, function ($query, $logStatus) { // Add this line
                return $query->where('log_status', $logStatus);
            })
            ->with('reviewStatus'); // Eager load review status
    
        // Fetching review statuses and SDGs for the filters
        $reviewStatuses = ReviewStatus::all();
        $sdgs = Sdg::all();
    
        // Fetch SDGs based on related type
        $statusReports = $reportsQuery->with('loggedBy')->orderBy('id', 'desc')->get(); // Get all reports without pagination for filtering
    
        foreach ($statusReports as $report) {
            if ($report->related_type === 'App\Models\Project') {
                $report->sdgs = Project::find($report->related_id)->sdg; // Fetch SDGs for the related project
            } elseif ($report->related_type === 'App\Models\Research') {
                $report->sdgs = Research::find($report->related_id)->sdg; // Fetch SDGs for the related research
            } else {
                $report->sdgs = []; // Default to an empty array if no related type matches
            }
        }
    
        // If no related type is selected, filter reports by selected SDGs
        if (empty($relatedType) && !empty($sdgIds)) {
            $projectIds = Project::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            $researchIds = Research::whereHas('sdg', function ($query) use ($sdgIds) {
                $query->whereIn('sdg_id', $sdgIds);
            })->pluck('id');
    
            // Filter reports based on the related IDs from projects and research
            $statusReports = $statusReports->filter(function ($report) use ($projectIds, $researchIds) {
                return ($report->related_type === 'App\Models\Project' && $projectIds->contains($report->related_id)) ||
                       ($report->related_type === 'App\Models\Research' && $researchIds->contains($report->related_id));
            });
        }
        // Paginate the filtered results
        $statusReportsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $statusReports->forPage($request->input('page', 1), 5), // Get the current page items
            $statusReports->count(), // Total items
            5, // Items per page
            $request->input('page', 1), // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Preserve query parameters
        );
    
        // Fetch all related types for the filter dropdown (if needed)
        $relatedTypes = ['App\Models\Project', 'App\Models\Research']; // Example related types
    
        // Fetch all SDGs for the filter dropdown
        $sdgs = Sdg::all();
    
        // Return the view with filtered status reports, related types options, and SDGs
        return view('reviewer.status_reports.rejected', compact('statusReportsPaginated', 'relatedTypes', 'sdgs'));
    }

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
     */
 
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
