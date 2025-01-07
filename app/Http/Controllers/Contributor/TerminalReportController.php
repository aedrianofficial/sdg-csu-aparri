<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CooperatingAgency;
use App\Models\Feedback;
use App\Models\FundingAgency;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Research;
use App\Models\Researcher;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\TerminalReport;
use App\Models\TerminalReportFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TerminalReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function my_reports(Request $request)
    {
    // Fetching the filters from the request
    $title = $request->input('title');
    $relatedType = $request->input('related_type');
    $reviewStatusId = $request->input('review_status');
    $sdgIds = $request->input('sdg', []);

    // Get the authenticated user's ID
    $userId = auth()->id(); // Assuming you are using Laravel's built-in authentication

    // Initialize the query for TerminalReport
    $reportsQuery = TerminalReport::query()
        ->where('user_id', $userId) // Filter by user_id
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
    return view('contributor.terminal_reports.index', compact('reports', 'reviewStatuses', 'sdgs'));
    }
    public function need_changes(Request $request)
    {
        // Fetching the filters from the request
        $title = $request->input('title');
        $relatedType = $request->input('related_type');
        $reviewStatusId = $request->input('review_status');
        $sdgIds = $request->input('sdg', []);

        // Get the authenticated user's ID
        $userId = auth()->id(); // Assuming you are using Laravel's built-in authentication

        // Initialize the query for TerminalReport
        $reportsQuery = TerminalReport::query()
            ->where('user_id', $userId) // Filter by user_id
            ->where('is_publish', 0) // Only unpublished reports
            ->where('review_status_id', 1) // Only reports with review status ID 1
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
        return view('contributor.terminal_reports.need_changes', compact('reports', 'sdgs'));
    }
    public function rejected(Request $request)
    {
        // Fetching the filters from the request
        $title = $request->input('title');
        $relatedType = $request->input('related_type');
        $reviewStatusId = $request->input('review_status');
        $sdgIds = $request->input('sdg', []);

        // Get the authenticated user's ID
        $userId = auth()->id(); // Assuming you are using Laravel's built-in authentication

        // Initialize the query for TerminalReport
        $reportsQuery = TerminalReport::query()
            ->where('user_id', $userId) // Filter by user_id
            ->where('is_publish', 0) // Only unpublished reports
            ->where('review_status_id', 2) // Only reports with review status ID 2
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
        return view('contributor.terminal_reports.rejected', compact('reports', 'sdgs'));
    }

    public function createProject()
    {
        // Fetch the project based on the related_id from the request
        $projectId = request('related_id');
        $project = Project::find($projectId); // Assuming you have a Project model
    
        // Check if the project exists
        if (!$project) {
            return redirect()->back()->withErrors(['Project not found.']);
        }
    
        // Fetch cooperating agencies, funding agencies, and researchers
        $cooperatingAgencies = CooperatingAgency::where('is_active', 1)->get();
        $fundingAgencies = FundingAgency::where('is_active', 1)->get();
        $researchers = Researcher::all(); // Adjust this if you need to filter researchers
    
        // Pass the project and other necessary data to the view
        return view('contributor.terminal_reports.project.create', compact('project', 'cooperatingAgencies', 'fundingAgencies', 'researchers'));
    }
    public function storeProject(Request $request)
    {
    $user = Auth::user();
    $submitType = $request->submit_type; // Capture the button clicked value ('review')

    // Validate the incoming request
    $request->validate([
        'related_type' => 'required|string',
        'related_id' => 'required|integer',
        'related_title' => 'required|string',
        'abstract' => 'required|string',
        'total_approved_budget' => 'required|numeric',
        'actual_released_budget' => 'required|numeric',
        'actual_expenditure' => 'required|numeric',
        'date_started' => 'required|date',
        'date_ended' => 'required|date',
        'cooperating_agency_id' => 'required|integer',
        'funding_agency_id' => 'required|integer',
        'researchers_id' => 'required|array',
        'researchers_id.*' => 'integer',
        'related_link' => 'nullable|url',
        'terminal_report_file' => 'nullable|file|max:2048',
    ]);
    
    try {
        DB::beginTransaction();


        // Set review status to pending review
        $reviewStatusId = 4; // 4 = Pending Review
        $isPublish = 0; // 0 = Draft

        // Create terminal report record
        $terminalReport = TerminalReport::create([
            'related_type' => $request->related_type,
            'related_id' => $request->related_id,
            'abstract' => $request->abstract,
            'related_title' => $request->related_title,
            'total_approved_budget' => $request->total_approved_budget,
            'actual_released_budget' => $request->actual_released_budget,
            'actual_expenditure' => $request->actual_expenditure,
            'date_started' => $request->date_started,
            'date_ended' => $request->date_ended,
            'cooperating_agency_id' => $request->cooperating_agency_id,
            'funding_agency_id' => $request->funding_agency_id,
            'review_status_id' => $reviewStatusId,
            'is_publish' => $isPublish,
            'related_link' => $request->related_link,
            'user_id' => $user->id,
        ]);

        // Handle researchers association
        if ($request->has('researchers_id')) {
            $terminalReport->researchers()->attach($request->researchers_id);
        }

        // Handle file upload for terminal report
        if ($request->hasFile('terminal_report_file')) {
            $file = $request->file('terminal_report_file');
            $fileData = file_get_contents($file);
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Create a new TerminalReportFile record
            TerminalReportFile::create([
                'terminal_report_id' => $terminalReport->id,
                'file' => $fileData,
                'original_filename' => $originalFilename,
                'extension' => $extension,
            ]);
        }

        // Log activity for submission
        ActivityLog::create([
            'log_name' => 'Terminal Report Submission',
            'description' => "Terminal report titled '" . addslashes($terminalReport->title) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
            'subject_type' => TerminalReport::class,
            'subject_id' => $terminalReport->id,
            'event' => 'submitted for review',
            'causer_type' => User::class,
            'causer_id' => $user->id,
            'properties' => json_encode([
                'related_title' => $request->related_title,
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
                'type' => 'terminal_report',
                'related_type' => TerminalReport::class,
                'related_id' => $terminalReport->id,
                'data' => json_encode([
                    'message' => "A new terminal report titled '" . addslashes($terminalReport->related_title) . "' has been submitted for review.",
                    'contributor' => $user->first_name . ' ' . $user->last_name,
                    'role' => 'contributor',
                    'type' => 'terminal_report',
                    'status' => 'submitted for review',
                ]),
                'created_at' => now(),
            ]);
        }

        DB::commit();
    } catch (\Exception $ex) {
        DB::rollBack();
        dd($ex->getMessage());
    }

    session()->flash('alert-success', 'Terminal Report Submitted Successfully!');
    return to_route('contributor.projects.index');
    }
    public function showProjectPublished(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
            ->findOrFail($id);

        // Check if the terminal report is published
        if (!$terminalReport->is_publish) {
            // Handle the case where the report is not published (e.g., redirect or show a message)
            return redirect()->back()->with('error', 'This terminal report is not published.');
        }

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
        return view('contributor.terminal_reports.project.show', compact('terminalReport', 'terminalReportFile', 'notificationData'));
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
        return view('contributor.terminal_reports.project.show', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function showProjectNeedChanges(string $id, Request $request)
    {
    // Find the terminal report by its ID, including the user who logged it
    $terminalReport = TerminalReport::with(['feedbacks.user','cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
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
    return view('contributor.feedbacks.terninal_report_project', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function showProjectRejected(string $id, Request $request)
    {
    // Find the terminal report by its ID, including the user who logged it
    $terminalReport = TerminalReport::with(['feedbacks.user','cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
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
    return view('contributor.feedbacks.rejected_terminal_report_project', compact('terminalReport', 'terminalReportFile', 'notificationData'));
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

        // Fetch cooperating agencies, funding agencies, and researchers
        $cooperatingAgencies = CooperatingAgency::where('is_active', 1)->get();
        $fundingAgencies = FundingAgency::where('is_active', 1)->get();
        $researchers = Researcher::all(); // Adjust this if you need to filter researchers

        // Pass the research and other necessary data to the view
        return view('contributor.terminal_reports.research.create', compact('research', 'cooperatingAgencies', 'fundingAgencies', 'researchers'));
    }
    public function storeResearch(Request $request)
    {
    $user = Auth::user();
    $submitType = $request->submit_type; // Capture the button clicked value ('review')

    // Validate the incoming request
    $request->validate([
        'related_type' => 'required|string',
        'related_id' => 'required|integer',
        'related_title' => 'required|string',
        'abstract' => 'required|string',
        'total_approved_budget' => 'required|numeric',
        'actual_released_budget' => 'required|numeric',
        'actual_expenditure' => 'required|numeric',
        'date_started' => 'required|date',
        'date_ended' => 'required|date',
        'cooperating_agency_id' => 'required|integer',
        'funding_agency_id' => 'required|integer',
        'researchers_id' => 'required|array',
        'researchers_id.*' => 'integer',
        'related_link' => 'nullable|url',
        'terminal_report_file' => 'nullable|file|max:2048',
    ]);
    
    try {
        DB::beginTransaction();


        // Set review status to pending review
        $reviewStatusId = 4; // 4 = Pending Review
        $isPublish = 0; // 0 = Draft

        // Create terminal report record
        $terminalReport = TerminalReport::create([
            'related_type' => $request->related_type,
            'related_id' => $request->related_id,
            'abstract' => $request->abstract,
            'related_title' => $request->related_title,
            'total_approved_budget' => $request->total_approved_budget,
            'actual_released_budget' => $request->actual_released_budget,
            'actual_expenditure' => $request->actual_expenditure,
            'date_started' => $request->date_started,
            'date_ended' => $request->date_ended,
            'cooperating_agency_id' => $request->cooperating_agency_id,
            'funding_agency_id' => $request->funding_agency_id,
            'review_status_id' => $reviewStatusId,
            'is_publish' => $isPublish,
            'related_link' => $request->related_link,
            'user_id' => $user->id,
        ]);

        // Handle researchers association
        if ($request->has('researchers_id')) {
            $terminalReport->researchers()->attach($request->researchers_id);
        }

        // Handle file upload for terminal report
        if ($request->hasFile('terminal_report_file')) {
            $file = $request->file('terminal_report_file');
            $fileData = file_get_contents($file);
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Create a new TerminalReportFile record
            TerminalReportFile::create([
                'terminal_report_id' => $terminalReport->id,
                'file' => $fileData,
                'original_filename' => $originalFilename,
                'extension' => $extension,
            ]);
        }

        // Log activity for submission
        ActivityLog::create([
            'log_name' => 'Terminal Report Submission',
            'description' => "Terminal report titled '" . addslashes($terminalReport->title) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
            'subject_type' => TerminalReport::class,
            'subject_id' => $terminalReport->id,
            'event' => 'submitted for review',
            'causer_type' => User::class,
            'causer_id' => $user->id,
            'properties' => json_encode([
                'related_title' => $request->related_title,
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
                'type' => 'terminal_report',
                'related_type' => TerminalReport::class,
                'related_id' => $terminalReport->id,
                'data' => json_encode([
                    'message' => "A new terminal report titled '" . addslashes($terminalReport->related_title) . "' has been submitted for review.",
                    'contributor' => $user->first_name . ' ' . $user->last_name,
                    'role' => 'contributor',
                    'type' => 'terminal_report',
                    'status' => 'submitted for review',
                ]),
                'created_at' => now(),
            ]);
        }

        DB::commit();
    } catch (\Exception $ex) {
        DB::rollBack();
        dd($ex->getMessage());
    }

    session()->flash('alert-success', value: 'Terminal Report Submitted Successfully!');
    return to_route('contributor.research.index');
    }
    
   public function showResearchPublished(string $id, Request $request)
   {
       // Find the terminal report by its ID, including the user who logged it
       $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
           ->findOrFail($id);
   
       // Check if the terminal report is published
       if (!$terminalReport->is_publish) {
           // Handle the case where the report is not published (e.g., redirect or show a message)
           return redirect()->back()->with('error', 'This terminal report is not published.');
       }
   
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
       return view('contributor.terminal_reports.research.show', compact('terminalReport', 'terminalReportFile', 'notificationData'));
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
        return view('contributor.terminal_reports.research.show', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function showResearchNeedChanges(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['feedbacks.user','cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
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
        return view('contributor.feedbacks.terminal_report_research', compact('terminalReport', 'terminalReportFile', 'notificationData'));
    }
    public function showResearchRejected(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['feedbacks.user','cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
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
        return view('contributor.feedbacks.rejected_terminal_report_research', compact('terminalReport', 'terminalReportFile', 'notificationData'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Retrieve the terminal report by ID
        $terminalReport = TerminalReport::findOrFail($id);

        // Fetch all cooperating agencies, funding agencies, and researchers
        $cooperatingAgencies = CooperatingAgency::all();
        $fundingAgencies = FundingAgency::all();
        $researchers = Researcher::all();

        // Return the edit view with the terminal report and related data
        return view('contributor.terminal_reports.edit', compact('terminalReport', 'cooperatingAgencies', 'fundingAgencies', 'researchers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TerminalReport $terminalReport)
    {
    // Validate request input
    $request->validate([
        'cooperating_agency_id' => ['required', 'exists:cooperating_agencies,id'],
        'funding_agency_id' => ['required', 'exists:funding_agencies,id'],
        'total_approved_budget' => ['required', 'numeric'],
        'actual_released_budget' => ['required', 'numeric'],
        'actual_expenditure' => ['required', 'numeric'],
        'abstract' => ['required', 'string'],
        'review_status_id' => ['nullable', 'exists:review_statuses,id'],
        'feedback' => ['nullable', 'string', 'max:1000'], // Feedback validation
        'terminal_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx'], // File validation
        'date_started' => ['required', 'date'],
        'date_ended' => ['required', 'date'],
        'researchers_id' => ['required', 'array'], // Validate researchers_id as an array
        'researchers_id.*' => ['integer'], // Each researcher ID should be an integer
    ]);

    $user = Auth::user();
    $originalContributor = $terminalReport->user_id; // Assuming this is the user who created the report

    try {
        DB::beginTransaction();

        // Update terminal report details
        $terminalReport->update([
            'cooperating_agency_id' => $request->cooperating_agency_id,
            'funding_agency_id' => $request->funding_agency_id,
            'total_approved_budget' => $request->total_approved_budget,
            'actual_released_budget' => $request->actual_released_budget,
            'actual_expenditure' => $request->actual_expenditure,
            'abstract' => $request->abstract,
            'date_started' => $request->date_started,
            'date_ended' => $request->date_ended,
            'related_link' => $request->related_link,
            'review_status_id' => 4,
            'is_publish' => 0,
        ]);


        // Handle researchers association
        if ($request->has('researchers_id')) {
            // Sync researchers, this will attach new ones and detach those not in the array
            $terminalReport->researchers()->sync($request->researchers_id);
        }

        // Handle file upload
        if ($request->hasFile('terminal_report_file')) {
            $file = $request->file('terminal_report_file');
            $fileData = file_get_contents($file);
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Check if a file already exists for this terminal report
            $existingFile = TerminalReportFile::where('terminal_report_id', $terminalReport->id)->first();

            if ($existingFile) {
                // If a file exists, delete the old file
                $existingFile->delete();
            }

            // Create the record in the TerminalReportFile table with the new file
            TerminalReportFile::create([
                'terminal_report_id' => $terminalReport->id,
                'file' => $fileData,
                'original_filename' => $originalFilename,
                'extension' => $extension,
            ]);
        }

        
        RoleAction::create([
            'content_id' => $terminalReport->id,
            'content_type' => TerminalReport::class,
            'user_id' => $user->id,
            'role' => 'contributor',
            'action' => 'submitted for review',
            'created_at' => now()
        ]);

        $terminalReportTitle = addslashes($terminalReport->related_title);
       
        $reviewers = User::where('role', 'reviewer')->get();
        foreach ($reviewers as $reviewer) {
            Notification::create([
                'user_id' => $reviewer->id,
                'notifiable_type' => User::class,
                'notifiable_id' => $reviewer->id,
                'type' => 'terminal_report',
                'related_type' => TerminalReport::class,
                'related_id' => $terminalReport->id,
                'data' => json_encode([
                    'message' => "A new terminal report titled '$terminalReportTitle' has been submitted for review.",
                    'contributor' => $user->first_name . ' ' . $user->last_name,
                    'role' => ['admin', 'contributor'],
                    'type' => 'terminal_report',
                    'status' => 'submitted for review'
                ]),
                'created_at' => now(),
            ]);
        }

        ActivityLog::create([
            'log_name' => 'Terminal Report Resubmission',
            'description' => 'Terminal report titled "' . addslashes($terminalReport->related_title) . '" resubmitted for review',
            'subject_type' => TerminalReport::class,
            'subject_id' => $terminalReport->id,
            'event' => 'resubmitted for review',
            'causer_type' => User::class,
            'causer_id' => auth()->user()->id,
            'properties' => json_encode([
                'terminal_report_title' => $terminalReport->related_title,
                'review_status' => 'submitted for review'
            ]),
            'created_at' => now(),
        ]);
     
        DB::commit();

        session()->flash('alert-success', 'Terminal Report Updated Successfully!');
        return to_route('contributor.terminal_reports.my_reports');

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
}
