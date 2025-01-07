<?php

namespace App\Http\Controllers\Auth;

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
use App\Models\StatusReportFile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class StatusReportController extends Controller
{
    

    public function downloadFile($id)
{
    $statusReportFile = StatusReportFile::findOrFail($id);
    
    // Determine the file name and MIME type
    $filename = $statusReportFile->original_filename ?? 'status_report'; // Default filename if not stored
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    
    // Set default MIME type as PDF if not available
    $mimeType = 'application/pdf';
    
    // Set MIME type based on the file extension
    if ($extension === 'pdf') {
        $mimeType = 'application/pdf';
    } elseif ($extension === 'docx') {
        $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }
    
    // Serve the binary file as a download response
    return response($statusReportFile->file)
        ->header('Content-Type', $mimeType)
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}
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
        $logStatus = $request->input('log_status'); // New log_status filter
    
        // Initialize the query for StatusReport
        $reportsQuery = StatusReport::query()
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
        return view('auth.status_reports.index', compact('reports', 'reviewStatuses', 'sdgs'));
    }
    
    public function my_reports(Request $request)
{
    // Fetching the filters from the request
    $title = $request->input('title');
    $relatedType = $request->input('related_type');
    $reviewStatusId = $request->input('review_status');
    $sdgIds = $request->input('sdg', []);
    $logStatus = $request->input('log_status'); // New log_status filter
    $loggedById = $request->input('logged_by_id'); // New logged_by_id filter

    // Initialize the query for StatusReport
    $reportsQuery = StatusReport::query()
        ->when($title, function ($query, $title) {
            return $query->where('related_title', 'like', '%' . $title . '%');
        })
        ->when($relatedType, function ($query, $relatedType) {
            return $query->where('related_type', $relatedType);
        })
        ->when($reviewStatusId, function ($query, $reviewStatusId) {
            return $query->where('review_status_id', $reviewStatusId);
        })
        ->when($logStatus, function ($query, $logStatus) {
            return $query->where('log_status', $logStatus);
        })
        ->when($loggedById, function ($query, $loggedById) { // Add this line
            return $query->where('logged_by_id', $loggedById);
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
    return view('auth.status_reports.my_reports', compact('reports', 'reviewStatuses', 'sdgs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createProject()
    {
        // Fetch the project based on the related_id from the request
        $projectId = request('related_id');
        $project = Project::find($projectId); // Assuming you have a Project model
    
        // Check if the project exists
        if (!$project) {
            return redirect()->back()->withErrors(['Project not found.']);
        }
    
        // Define the statuses that require reports
        $statusesToCheck = [
            1 => 'Proposed', 
            2 => 'On-Going', 
            3 => 'On-Hold', 
            5 => 'Rejected'
        ]; // Map status IDs to their string representations
    
        // Pass the project and statuses to the view
        return view('auth.status_reports.projects.create', compact('project', 'statusesToCheck'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function storeProject(Request $request)
    {
        $user = Auth::user();
        $submitType = $request->submit_type; // Capture the button clicked value ('publish' or 'review')
    
        // Validate the incoming request
        $request->validate([
            'related_type' => 'required|string',
            'related_id' => 'required|integer',
            'related_title' => 'required|string',
            'log_status' => 'required|string',
            'remarks' => 'required|string',
            'related_link' => 'nullable|url',
        ]);
    
        try {
            DB::beginTransaction();
    
            
            // Determine the review status and publish status based on the button clicked
            $reviewStatusId = ($submitType === 'publish') ? 3 : 4; // 3 = Published, 4 = Pending Review
            $isPublish = ($submitType === 'publish') ? 1 : 0; // 1 = Published, 0 = Draft
    
            // Create status report record
            $statusReport = StatusReport::create([
                'related_type' => $request->related_type,
                'related_id' => $request->related_id,
                'related_title' => $request->related_title,
                'log_status' => $request->log_status,
                'remarks' => $request->remarks,
                'logged_by_id' => $user->id, // Set the user_id
                'review_status_id' => $reviewStatusId, // Set review status based on the action
                'is_publish' => $isPublish, // Set publish status
                'related_link' => $request->related_link, // Store the related link
            ]);

            // Handle file upload for status report
            if ($request->hasFile('status_report_file')) {
                $file = $request->file('status_report_file');
                $fileData = file_get_contents($file); // Read file as binary data
                $originalFilename = $file->getClientOriginalName(); // Original filename with extension
                $extension = $file->getClientOriginalExtension(); // File extension (e.g., pdf or docx)

                // Create a new StatusReportFile record
                StatusReportFile::create([
                    'status_report_id' => $statusReport->id,
                    'file' => $fileData,               // Store binary data
                    'original_filename' => $originalFilename, // Store original filename
                    'extension' => $extension,         // Store file extension
                ]);
            }
    
            // Log activity in the role_actions table
            if ($submitType === 'publish') {
                // Log the activity for publishing the status report
                ActivityLog::create([
                    'log_name' => 'Status Report Published',
                    'description' => 'Published the status report titled "' . addslashes($statusReport->related_title) . '"',
                    'subject_type' => StatusReport::class,
                    'subject_id' => $statusReport->id,
                    'event' => 'published',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'related_title' => $request->related_title,
                        'review_status' => 'published',
                        'role' => 'publisher',
                    ]),
                    'created_at' => now(),
                ]);
            } else if ($submitType === 'review') {
                // Log the activity for submission
                ActivityLog::create([
                    'log_name' => 'Status Report Submission',
                    'description' => "Status report titled '" . addslashes($statusReport->related_title) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
                    'subject_type' => StatusReport::class,
                    'subject_id' => $statusReport->id,
                    'event' => 'submitted for review',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'related_title' => $request->related_title,
                        'remarks' => $statusReport->remarks,
                        'is_publish' => $isPublish,
                        'related_type' => $request->related_type,
                        'related_id' => $request->related_id,
                    ]),
                    'created_at' => now(),
                ]);
    
                // Create notifications for each reviewer
                $reviewers = User::where('role', 'reviewer')->get();
                foreach ($reviewers as $reviewer) {
                    Notification::create([
                        'user_id' => $reviewer->id,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $reviewer->id,
                        'type' => 'status_report',
                        'related_type' => StatusReport::class,
                        'related_id' => $statusReport->id,
                        'data' => json_encode([
                            'message' => "A new status report titled '" . addslashes($statusReport->related_title) . "' has been submitted for review.",
                            'contributor' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'contributor'],
                            'type' => 'status_report',
                            'status' => 'submitted for review',
                        ]),
                        'created_at' => now(),
                    ]);
                }
            }
    
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            dd($ex->getMessage());
        }
    
        session()->flash('alert-success', 'Status Report Submitted Successfully!');
        return to_route('projects.index');
    }

    /**
     * Display the specified resource.
     */
    public function showProjectPublished(string $id, Request $request)
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
        return view('auth.status_reports.projects.show', compact('statusReport', 'notificationData'));
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
        return view('auth.feedbacks.status_report_project', compact('statusReport', 'notificationData'));
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
        return view('auth.feedbacks.rejected_status_report_project', compact('statusReport', 'notificationData'));
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
        return view('auth.feedbacks.status_report_research', compact('statusReport', 'notificationData'));
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
        return view('auth.feedbacks.rejected_status_report_research', compact('statusReport', 'notificationData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $statusReport = StatusReport::findOrFail($id);
        $reviewStatuses = ReviewStatus::all(); // Fetch all review statuses

        return view('auth.status_reports.edit', compact('statusReport', 'reviewStatuses'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StatusReport $statusReport)
{
    // Validate request input
    $request->validate([
        'related_title' => ['required', 'string', 'min:2', 'max:255'],
        'related_type' => ['required', 'string'],
        'log_status' => ['required', 'string'],
        'remarks' => ['required', 'string'],
        'related_link' => ['nullable', 'url'],
        'review_status_id' => ['nullable', 'exists:review_statuses,id'],
        'feedback' => ['nullable', 'string', 'max:1000'], // Feedback validation
        'status_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx'], // File validation
        'status_report_id' => ['required', 'exists:status_reports,id'] // Ensure the status report exists
    ]);

    $user = Auth::user();
    $originalContributor = $statusReport->logged_by_id; // Assuming this is the user who created the report
    
    try {
        DB::beginTransaction();

        // Creating and attaching feedback
        if ($request->filled('feedback')) {
            $feedback = Feedback::firstOrCreate([
                'feedback' => $request->feedback,
                'users_id' => $user->id,
            ]);
            
            // Then attach to the statusReport
            $statusReport->feedbacks()->syncWithoutDetaching($feedback->id);                
        }
        // Update status report details
        $statusReport->update([
            'related_title' => $request->related_title,
            'log_status' => $request->log_status,
            'remarks' => $request->remarks,
            'related_link' => $request->related_link,
            'review_status_id' => $request->review_status_id ?? 4,
        ]);
        // Check if review_status_id is 3, then set is_publish to 1
        if ($request->review_status_id == 3) {
            $statusReport->is_publish = 1; // Set to published
        } else {
            $statusReport->is_publish = 0; // Set to not published
        }

        // Save the changes
        $statusReport->save();

        // Handle feedback if provided
        if ($request->filled('feedback')) {
            $feedback = Feedback::firstOrCreate([
                'feedback' => $request->feedback,
                'users_id' => $user->id,
            ]);
            // Attach feedback to the status report
            $statusReport->feedbacks()->syncWithoutDetaching($feedback->id);
        }

        // Handle file upload
        if ($request->hasFile('status_report_file')) {
            $file = $request->file('status_report_file');
            $fileData = file_get_contents($file);
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Check if a file already exists for this status report
            $existingFile = StatusReportFile::where('status_report_id', $statusReport->id)->first();

            if ($existingFile) {
                // If a file exists, delete the old file
                $existingFile->delete();
            }

            // Create the record in the StatusReportFile table with the new file
            StatusReportFile::create([
                'status_report_id' => $statusReport->id,
                'file' => $fileData,
                'original_filename' => $originalFilename,
                'extension' => $extension,
            ]);
        }

        // Handle notifications based on review status
        $actionMap = [
            1 => 'requested change',
            2 => 'rejected',
            3 => 'published',
            4 => 'submitted for review',
            5 => 'reviewed',
            6 => 'approved'
        ];
        $action = $actionMap[$request->review_status_id] ?? 'updated';

        RoleAction::create([
            'content_id' => $statusReport->id,
            'content_type' => StatusReport::class,
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => $action
        ]);

        // Notify the original contributor based on the review status
        $statusReportTitle = addslashes($statusReport->related_title);
        switch ($statusReport->review_status_id) {
            case 1: // Requested changes
                Notification::create([
                    'user_id' => $originalContributor,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $originalContributor,
                    'type' => 'status_report',
                    'related_type' => StatusReport::class,
                    'related_id' => $statusReport->id,
                    'data' => json_encode([
                        'message' => "Your status report '$statusReportTitle' requires changes.",
                        'reviewer' => $user->first_name . ' ' . $user->last_name,
                        'role' => ['admin', 'reviewer'],
                        'type' => 'status_report',
                        'status' => 'request_changes'
                    ]),
                    'created_at' => now(),
                ]);
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
                break;

            case 2: // Rejected
                Notification::create([
                    'user_id' => $originalContributor,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $originalContributor,
                    'type' => 'status_report',
                    'related_type' => StatusReport::class,
                    'related_id' => $statusReport->id,
                    'data' => json_encode([
                        'message' => "Your status report '$statusReportTitle' has been rejected.",
                        'reviewer' => $user->first_name . ' ' . $user->last_name,
                        'role' => ['admin', 'reviewer'],
                        'type' => 'status_report',
                        'status' => 'rejected'
                    ]),
                    'created_at' => now(),
                ]);
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
                break;

            case 3: // Published
                Notification::create([
                    'user_id' => $originalContributor,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $originalContributor,
                    'type' => 'status_report',
                    'related_type' => StatusReport::class,
                    'related_id' => $statusReport->id,
                    'data' => json_encode([
                        'message' => "Your status report '$statusReportTitle' has been published.",
                        'publisher' => $user->first_name . ' ' . $user->last_name,
                        'role' => ['admin', 'publisher'],
                        'type' => 'status_report',
                        'status' => 'published'
                    ]),
                    'created_at' => now(),
                ]);
                ActivityLog::create([
                    'log_name' => 'Status Report Published',
                    'description' => 'Published the status report titled "' . addslashes($statusReport->related_title) . '"',
                    'subject_type' => StatusReport::class,
                    'subject_id' => $statusReport->id,
                    'event' => 'published',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'status_report_title' => $statusReport->related_title,
                        'review_status' => 'published',
                        'role' => 'publisher',
                    ]),
                    'created_at' => now(),
                ]);
                break;

            case 4: // Submitted for review
                $reviewers = User::where('role', 'reviewer')->get();
                foreach ($reviewers as $reviewer) {
                    Notification::create([
                        'user_id' => $reviewer->id,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $reviewer->id,
                        'type' => 'status_report',
                        'related_type' => StatusReport::class,
                        'related_id' => $statusReport->id,
                        'data' => json_encode([
                            'message' => "A new status report titled '$statusReportTitle' has been submitted for review.",
                            'contributor' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'contributor'],
                            'type' => 'status_report',
                            'status' => 'submitted for review'
                        ]),
                        'created_at' => now (),
                    ]);
                }
                ActivityLog::create([
                    'log_name' => 'Status Report Resubmission',
                    'description' => 'Status report titled "' . addslashes($statusReport->related_title) . '" resubmitted for review',
                    'subject_type' => StatusReport::class,
                    'subject_id' => $statusReport->id,
                    'event' => 'resubmitted for review',
                    'causer_type' => User::class,
                    'causer_id' => auth()->user()->id,
                    'properties' => json_encode([
                        'status_report_title' => $statusReport->related_title,
                        'review_status' => 'submitted for review'
                    ]),
                    'created_at' => now(),
                ]);
                break;

            case 5: // Reviewed
                Notification::create([
                    'user_id' => $originalContributor,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $originalContributor,
                    'type' => 'status_report',
                    'related_type' => StatusReport::class,
                    'related_id' => $statusReport->id,
                    'data' => json_encode([
                        'message' => "Your status report '$statusReportTitle' has been reviewed.",
                        'reviewer' => $user->first_name . ' ' . $user->last_name,
                        'role' => ['admin', 'reviewer'],
                        'type' => 'status_report',
                        'status' => 'reviewed'
                    ]),
                    'created_at' => now(),
                ]);
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
                break;

            case 6: // Approved
                Notification::create([
                    'user_id' => $originalContributor,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $originalContributor,
                    'type' => 'status_report',
                    'related_type' => StatusReport::class,
                    'related_id' => $statusReport->id,
                    'data' => json_encode([
                        'message' => "Your status report '$statusReportTitle' has been approved.",
                        'approver' => $user->first_name . ' ' . $user->last_name,
                        'role' => ['admin', 'approver'],
                        'type' => 'status_report',
                        'status' => 'approved'
                    ]),
                    'created_at' => now(),
                ]);
                ActivityLog::create([
                    'log_name' => 'Status Report Approved',
                    'description' => 'Approved the status report titled "' . addslashes($statusReport->related_title) . '"',
                    'subject_type' => StatusReport::class,
                    'subject_id' => $statusReport->id,
                    'event' => 'approved',
                    'causer_type' => User::class,
                    'causer_id' => auth()->user()->id,
                    'properties' => json_encode([
                        'status_report_title' => $statusReport->related_title,
                        'review_status' => 'approved',
                        'role' => 'approver',
                    ]),
                    'created_at' => now(),
                ]);
                break;
        }
        
        DB::commit();

        session()->flash('alert-success', 'Status Report Updated Successfully!');
        return to_route('auth.status_reports.index');

    } catch (\Exception $ex) {
        DB::rollBack();
        Log::error('Update failed: ' . $ex->getMessage());
        return back()->withErrors(['error' => $ex->getMessage()]);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function createResearch()
    {
        // Fetch the research based on the related_id from the request
        $researchId = request('related_id');
        $research = Research::find($researchId); // Assuming you have a Research model

        // Check if the research exists
        if (!$research) {
            return redirect()->back()->withErrors(['Research not found.']);
        }

        // Define the statuses that require reports
        $statusesToCheck = [
            1 => 'Proposed', 
            2 => 'On-Going', 
            3 => 'On-Hold', 
            5 => 'Rejected'
        ]; // Map status IDs to their string representations

        // Pass the research and statuses to the view
        return view('auth.status_reports.research.create', compact('research', 'statusesToCheck'));
    }
    public function storeResearch(Request $request)
    {
        $user = Auth::user();
        $submitType = $request->submit_type; // Capture the button clicked value ('publish' or 'review')

        // Validate the incoming request
        $request->validate([
            'related_type' => 'required|string',
            'related_id' => 'required|integer',
            'related_title' => 'required|string',
            'log_status' => 'required|string',
            'remarks' => 'required|string',
            'related_link' => 'nullable|url',
            'status_report_file' => 'required|file|max:2048', // Maximum file size of 2MB
        ]);

        try {
            DB::beginTransaction();

            // Determine the review status and publish status based on the button clicked
            $reviewStatusId = ($submitType === 'publish') ? 3 : 4; // 3 = Published, 4 = Pending Review
            $isPublish = ($submitType === 'publish') ? 1 : 0; // 1 = Published, 0 = Draft

            // Create status report record
            $statusReport = StatusReport::create([
                'related_type' => $request->related_type,
                'related_id' => $request->related_id,
                'related_title' => $request->related_title,
                'log_status' => $request->log_status,
                'remarks' => $request->remarks,
                'logged_by_id' => $user->id, // Set the user_id
                'review_status_id' => $reviewStatusId, // Set review status based on the action
                'is_publish' => $isPublish, // Set publish status
                'related_link' => $request->related_link, // Store the related link
            ]);

            // Handle file upload for status report
            if ($request->hasFile('status_report_file')) {
                $file = $request->file('status_report_file');
                $fileData = file_get_contents($file); // Read file as binary data
                $originalFilename = $file->getClientOriginalName(); // Original filename with extension
                $extension = $file->getClientOriginalExtension(); // File extension (e.g., pdf or docx)

                // Create a new StatusReportFile record
                StatusReportFile::create([
                    'status_report_id' => $statusReport->id,
                    'file' => $fileData,               // Store binary data
                    'original_filename' => $originalFilename, // Store original filename
                    'extension' => $extension,         // Store file extension
                ]);
            }

            // Log activity in the role_actions table
            if ($submitType === 'publish') {
                // Log the activity for publishing the status report
                ActivityLog::create([
                    'log_name' => 'Status Report Published',
                    'description' => 'Published the status report titled "' . addslashes($statusReport->related_title) . '"',
                    'subject_type' => StatusReport::class,
                    'subject_id' => $statusReport->id,
                    'event' => 'published',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'related_title' => $request->related_title,
                        'review_status' => 'published',
                        'role' => 'publisher',
                    ]),
                    'created_at' => now(),
                ]);
            } else if ($submitType === 'review') {
                // Log the activity for submission
                ActivityLog::create([
                    'log_name' => 'Status Report Submission',
                    'description' => "Status report titled '" . addslashes($statusReport->related_title) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
                    'subject_type' => StatusReport::class,
                    'subject_id' => $statusReport->id,
                    'event' => 'submitted for review',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'related_title' => $request->related_title,
                        'remarks' => $statusReport->remarks,
                        'is_publish' => $isPublish,
                        'related_type' => $request->related_type,
                        'related_id' => $request->related_id,
                    ]),
                    'created_at' => now(),
                ]);

                // Create notifications for each reviewer
                $reviewers = User::where('role', 'reviewer')->get();
                foreach ($reviewers as $reviewer) {
                    Notification::create([
                        'user_id' => $reviewer->id,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $reviewer->id,
                        'type' => 'status_report',
                        'related_type' => StatusReport::class,
                        'related_id' => $statusReport->id,
                        'data' => json_encode([
                            'message' => "A new status report titled '" . addslashes($statusReport->related_title) . "' has been submitted for review.",
                            'contributor' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'contributor'],
                            'type' => 'status_report',
                            'status' => 'submitted for review',
                        ]),
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            dd($ex->getMessage());
        }

        session()->flash('alert-success', 'Status Report Submitted Successfully!');
        return to_route('research.index');
    }
    public function showResearchPublished(string $id, Request $request)
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
    return view('auth.status_reports.research.show', compact('statusReport', 'notificationData'));
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
    return view('auth.status_reports.research.show', compact('statusReport', 'notificationData'));
    }
}
