<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Research;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\TerminalReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TerminalReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetching the filters from the request
        $title = $request->input('title');
        $relatedType = $request->input('related_type');
        $reviewStatusId = $request->input('review_status');
        $sdgIds = $request->input('sdg', []);
    
        // Initialize the query for TerminalReport
        $reportsQuery = TerminalReport::query()
        ->where('review_status_id', 6)
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
        return view('publisher.terminal_reports.index', compact('reports', 'reviewStatuses', 'sdgs'));
    }
    public function showProject(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
            ->findOrFail($id);
    
        // Remove the check for publication status
        // if (!$terminalReport->is_publish) {
        //     return redirect()->back()->with('error', 'This terminal report is not published.');
        // }
    
        // Get the first terminal report file
        $terminalReportFile = $terminalReport->terminalReportFiles->first();
    
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
    
        // Return the view for showing the terminal report details, including notification data
        return view('publisher.terminal_reports.projects.show', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function showProjectPublished(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
            ->findOrFail($id);
    
        // Remove the check for publication status
        // if (!$terminalReport->is_publish) {
        //     return redirect()->back()->with('error', 'This terminal report is not published.');
        // }
    
        // Get the first terminal report file
        $terminalReportFile = $terminalReport->terminalReportFiles->first();
    
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
    
        // Return the view for showing the terminal report details, including notification data
        return view('publisher.terminal_reports.projects.show_published', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function showResearch(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
            ->findOrFail($id);
    
        // Get the first terminal report file
        $terminalReportFile = $terminalReport->terminalReportFiles->first();
    
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
    
        // Return the view for showing the terminal report details, including notification data
        return view('publisher.terminal_reports.research.show', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function showResearchPublished(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
            ->findOrFail($id);
    
        // Get the first terminal report file
        $terminalReportFile = $terminalReport->terminalReportFiles->first();
    
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
    
        // Return the view for showing the terminal report details, including notification data
        return view('publisher.terminal_reports.research.show_published', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function published($id)
    {
        // Find the terminal report by ID
        $terminalReport = TerminalReport::findOrFail($id);
    
        // Update the review status to 'Forwarded to Approver'
        $terminalReport->update([
            'review_status_id' => 3,
            'is_publish' => 1
        ]); 
    
        // Log the action in the role_actions table
        RoleAction::create([
            'content_id' => $terminalReport->id,
            'content_type' => TerminalReport::class,
            'user_id' => auth()->user()->id,
            'role' => 'publisher',
            'action' => 'published',
            'created_at' => now(),
        ]);
    
        $type = 'terminal report';
        $statusContributor = 'published';
        $contributor = $terminalReport->user_id; // Assuming the contributor is the user who logged the report
        $reportTitle = $terminalReport->related_title; // Assuming related_title is the title of the report
    
        // Create a notification for the contributor
        Notification::create([
            'user_id' => $contributor,
            'notifiable_type' => User::class,
            'notifiable_id' => $contributor,
            'type' => $type,
            'related_type' => TerminalReport::class,
            'related_id' => $terminalReport->id,
            'data' => json_encode([
                'message' => "Your $type '" . addslashes($reportTitle) . "' has been approved and is now published.",
                'publisher' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'role' => 'publisher',
                'type' => $type,
                'status' => $statusContributor,
            ]),
            'created_at' => now(),
        ]);

    
        // Log the "published" action in the activity_log table
        ActivityLog::create([
            'log_name' => 'Terminal Report Published',
            'description' => 'Published the terminal report titled "' . addslashes($terminalReport->related_title) . '"',
            'subject_type' => TerminalReport::class,
            'subject_id' => $terminalReport->id,
            'event' => 'published',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'terminal_report_title' => $terminalReport->related_title,
                'review_status' => 'published',
                'role' => 'publisher',
            ]),
            'created_at' => now(),
        ]);
    
        // Redirect back with a success message
        session()->flash('alert-success', 'Terminal report published successfully.');
        return to_route('publisher.terminal_reports.index');
    }
    public function published_list(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user
        // Fetching the filters from the request
        $title = $request->input('title');
        $relatedType = $request->input('related_type');
        $reviewStatusId = $request->input('review_status');
        $sdgIds = $request->input('sdg', []);
    
        // Retrieve role actions for the current user that indicate a need for changes
        $roleActions = RoleAction::where('user_id', $user->id)
            ->where('role', 'publisher')
            ->where('action', 'published') // Assuming this action reflects the need for changes
            ->pluck('content_id') // Get the content IDs (i.e., terminalReport IDs)
            ->toArray(); // Convert to array for further use
    
       // Initialize the query for terminalReport
        $reportsQuery = terminalReport::query()
        ->whereIn('id', $roleActions)
        ->where('review_status_id', 3)
        ->where('is_publish', 1)
            ->when($title, function ($query, $title) {
                return $query->where('related_title', 'like', '%' . $title . '%');
            })
            ->when($relatedType, function ($query, $relatedType) {
                return $query->where('related_type', $relatedType);
            })
            ->when($reviewStatusId, function ($query, $reviewStatusId) {
                return $query->where('review_status_id', $reviewStatusId);
            })
            ->with('reviewStatus'); // Eager load review status
    
        // Fetching review statuses and SDGs for the filters
        $reviewStatuses = ReviewStatus::all();
        $sdgs = Sdg::all();
    
        // Fetch SDGs based on related type
        $terminalReports = $reportsQuery->with('user')->orderBy('id', 'desc')->get(); // Get all reports without pagination for filtering
    
        foreach ($terminalReports as $report) {
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
            $terminalReports = $terminalReports->filter(function ($report) use ($projectIds, $researchIds) {
                return ($report->related_type === 'App\Models\Project' && $projectIds->contains($report->related_id)) ||
                       ($report->related_type === 'App\Models\Research' && $researchIds->contains($report->related_id));
            });
        }
        // Paginate the filtered results
        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $terminalReports->forPage($request->input('page', 1), 10), // Get the current page items
            $terminalReports->count(), // Total items
            10, // Items per page
            $request->input('page', 1), // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Preserve query parameters
        );
    
        // Returning the view with the reports and filter data
        return view('publisher.terminal_reports.published', compact('reports', 'reviewStatuses', 'sdgs'));
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
    public function show(string $id)
    {
        //
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
