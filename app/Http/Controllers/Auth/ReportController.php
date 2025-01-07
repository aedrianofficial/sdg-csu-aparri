<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Report\CreateRequest;
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
use Illuminate\Http\Request;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function rejected($id, Request $request)
    {
        // Fetch the report by its ID, including feedbacks and associated users
        $report = Report::with('feedbacks.user')->findOrFail($id);
    
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
                $notification->markAsRead();
            }
        }
    
        // Pass the report and notification data to the view
        return view('auth.feedbacks.rejected_reports', compact('report', 'notificationData'));
    }

    public function need_changes($id, Request $request)
{
    // Fetch the report by its ID, including feedbacks and associated users
    $report = Report::with('feedbacks.user')->findOrFail($id);

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
            $notification->markAsRead();
        }
    }

    // Pass the report and notification data to the view
    return view('auth.feedbacks.reports', compact('report', 'notificationData'));
}

public function my_reports(Request $request)
{
    // Start query for user's reports
    $query = Report::where('user_id', Auth::id());

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

    // Handle sorting
    if ($request->filled('sort_by')) {
        $sortBy = $request->sort_by;
        $sortOrder = $request->sort_order ?? 'asc'; // Default to ascending order
        $allowedSortColumns = ['title', 'status', 'review_status_id', 'related_title', 'created_at', 'is_publish','related_type']; // Add other sortable columns here

        if ($sortBy === 'status') {
            // Join review_statuses table to sort by status
            $query->join('review_statuses', 'reports.review_status_id', '=', 'review_statuses.id')
                ->orderBy('review_statuses.name', $sortOrder);
        } elseif (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        }
    } else {
        // Default sorting
        $query->orderBy('id', 'desc');
    }

    // Fetch the filtered list of reports and paginate with eager loading
    $reports = $query->with(['reportimg', 'sdg', 'reviewStatus'])->paginate(5);

    // Fetch all review statuses and SDGs for the filter dropdowns
    $reviewStatuses = ReviewStatus::all();
    $sdgs = SDG::all();

    // Pass the reports, review statuses, and SDGs to the view
    return view('auth.reports.my_reports', compact('reports', 'reviewStatuses', 'sdgs'));
}


    public function index(Request $request)
    {
        // Start the query for fetching reports
        $query = Report::query();
    
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
    
        // Handle sorting
        if ($request->filled('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order ?? 'asc'; // Default to ascending order
            $allowedSortColumns = ['title', 'status', 'review_status_id', 'related_title', 'created_at','is_publish','related_type']; // Add other sortable columns here
    
            if (in_array($sortBy, $allowedSortColumns)) {
                $query->orderBy($sortBy, $sortOrder);
            }
             // Check if sorting by review status name
            if ($sortBy === 'status') {
                $query->join('review_statuses', 'reports.review_status_id', '=', 'review_statuses.id')
                    ->orderBy('review_statuses.name', $sortOrder);
            } elseif (in_array($sortBy, $allowedSortColumns)) {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            // Default sorting
            $query->orderBy('id', 'desc');
        }
    
        // Fetch the filtered list of reports and paginate with eager loading
        $reports = $query->with(['reportimg', 'sdg', 'reviewStatus'])->paginate(5);
    
        // Fetch all review statuses and SDGs for the filter dropdowns
        $reviewStatuses = ReviewStatus::all();
        $sdgs = SDG::all();
    
        // Pass the reports, review statuses, and SDGs to the view
        return view('auth.reports.index', compact('reports', 'reviewStatuses', 'sdgs'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sdgs = Sdg::all();
        return view('auth.reports.create', ['sdgs'=> $sdgs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request)
{
    $user = Auth::user();
    $submitType = $request->submit_type; // Capture the button clicked value ('publish' or 'review')

    try {
        DB::beginTransaction();

       // Handle file upload as binary data
       $file = $request->file('image');
       $fileData = file_get_contents($file);

       // Create project image record with binary data
       $reportimg = Reportimg::create([
           'image' => $fileData, // Store binary data directly
       ]);

        // Determine related title and SDGs
        $relatedTitle = null;
        $relatedSdgs = [];

        if ($request->related_type === 'project') {
            $relatedItem = Project::findOrFail($request->related_id);
            $relatedTitle = $relatedItem->title;
            $relatedSdgs = $relatedItem->sdg->pluck('id')->toArray();
        } elseif ($request->related_type === 'research') {
            $relatedItem = Research::findOrFail($request->related_id);
            $relatedTitle = $relatedItem->title;
            $relatedSdgs = $relatedItem->sdg->pluck('id')->toArray();
        }

        // Determine the review status and publish status based on the button clicked
        $reviewStatusId = ($submitType === 'publish') ? 3 : 4; // 3 = Published, 4 = Pending Review
        $isPublish = ($submitType === 'publish') ? 1 : 0; // 1 = Published, 0 = Draft

        // Create report record
        $report = Report::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_publish' => $isPublish,
            'review_status_id' => $reviewStatusId, // Set review status based on the action
            'reportimg_id' => $reportimg ? $reportimg->id : null, // Handle case where no image is uploaded
            'related_type' => $request->related_type,
            'related_id' => $request->related_id,
            'related_title' => $relatedTitle,
            'user_id' => $user->id, // Set the user_id
        ]);

        // Attach SDGs to the report
        $report->sdg()->attach($relatedSdgs);

        // Log activity in the role_actions table
        if ($submitType === 'publish') {
            RoleAction::create([
                'content_id' => $report->id,
                'content_type' => Report::class,
                'user_id' => $user->id,
                'role' => 'admin',
                'action' => 'published',
                'created_at' => now()
            ]);

            // Log the activity for publishing the report
            ActivityLog::create([
                'log_name' => 'Report Published',
                'description' => 'Published the report titled "' . addslashes($report->title) . '"',
                'subject_type' => Report::class,
                'subject_id' => $report->id,
                'event' => 'published',
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'properties' => json_encode([
                    'report_title' => $report->title,
                    'review_status' => 'published',
                    'role' => 'publisher',
                ]),
                'created_at' => now(),
            ]);
        } else if ($submitType === 'review') {
            RoleAction::create([
                'content_id' => $report->id,
                'content_type' => Report::class,
                'user_id' => $user->id,
                'role' => 'admin',
                'action' => 'submitted for review',
                'created_at' => now()
            ]);

            // Log the activity for submission
            ActivityLog::create([
                'log_name' => 'Report Submission',
                'description' => "Report titled '" . addslashes($report->title) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
                'subject_type' => Report::class,
                'subject_id' => $report->id,
                'event' => 'submitted for review',
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'properties' => json_encode([
                    'report_title' => $report->title,
                    'description' => $report->description,
                    'is_publish' => 'draft',
                    'related_title' => $relatedTitle,
                    'related_type' => $request->related_type,
                    'related_id' => $request->related_id,
                    'image' => $reportimg ? $reportimg->image : null, // File name of the uploaded image
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
                    'type' => 'report',
                    'related_type' => Report::class,
                    'related_id' => $report->id,
                    'data' => json_encode([
                        'message' => "A new report titled '" . addslashes($report->title) . "' has been submitted for review.",
                        'contributor' => $user->first_name . ' ' . $user->last_name,
                        'role' => ['admin', 'contributor'],
                        'type' => 'report',
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

    session()->flash('alert-success', 'Report Submitted Successfully!');
    return to_route('reports.index');
}
    

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        // Retrieve the report by ID, including related SDGs, user, and review status
        $report = Report::with(['sdg', 'user', 'reviewStatus'])->findOrFail($id);
    
        // Get the related item title based on the related type and ID
        if ($report->related_type === 'project') {
            $relatedItem = Project::find($report->related_id);
        } elseif ($report->related_type === 'research') {
            $relatedItem = Research::find($report->related_id);
        } else {
            $relatedItem = null;
        }
    
        // Set the related title if the related item is found
        $report->related_title = $relatedItem ? $relatedItem->title : 'Unknown';
    
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
    
        // Pass the report, its related data, and notification data to the view
        return view('auth.reports.show', compact('report', 'notificationData'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        $projects = Project::all(); // Retrieve all projects
        $researches = Research::all(); // Retrieve all research items
        $reviewStatuses = ReviewStatus::all();

        $existingImage = $report->reportimg->image ?? null;
        $sdgs = Sdg::all();
        return view('auth.reports.edit', [
            'report' => $report,
            'projects' => $projects,
            'researches' => $researches,
            'sdgs' => $sdgs,
            'reviewStatuses' => $reviewStatuses,
            'existingImage' => $existingImage, 
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $request->validate([
            'title' => ['required', 'min:2', 'max:255'],
            'sdg' => ['required'],
            'related_type' => ['required'],
            'related_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->related_type === 'project') {
                        if (!Project::where('id', $value)->exists()) {
                            $fail('The selected project does not exist.');
                        }
                    } elseif ($request->related_type === 'research') {
                        if (!Research::where('id', $value)->exists()) {
                            $fail('The selected research does not exist.');
                        }
                    }
                }
            ],
            'review_status_id' => ['nullable', 'exists:review_statuses,id'], // Added validation for review_status_id
            'is_publish' => ['nullable'],
            'description' => ['required', 'min:10'],
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max size
            'feedback' => ['nullable', 'string', 'max:1000'],  // New feedback validation
            'report_id' => ['required', 'exists:reports,id'] // Reports ID for feedback association
        ]);
    
        $user = Auth::user();
        $originalContributor = $report->user_id; // Get the original contributor
        $reportTitle = $request->title; // Get the report title
    
        try {
            DB::beginTransaction();

             // Creating and attaching feedback
             if ($request->filled('feedback')) {
                $feedback = Feedback::firstOrCreate([
                    'feedback' => $request->feedback,
                    'users_id' => $user->id,
                ]);
                
                // Then attach to the research
                $report->feedbacks()->syncWithoutDetaching($feedback->id);                
            }
            // Initialize reportimg variable outside the conditional to use it later
            $reportimg = $report->reportimg; 

            // Check if an image file has been uploaded
            if ($request->hasFile('image')) {
                // Get the uploaded file and convert it to binary data
                $file = $request->file('image');
                $fileData = file_get_contents($file); // Convert the file to binary data

                if ($reportimg) {
                    // Update the existing report image with new binary data
                    $reportimg->update([
                        'image' => $fileData, // Replace the binary data
                    ]);
                } else {
                    // Create a new report image record with binary data
                    $reportimg = Reportimg::create([
                        'image' => $fileData, // Store binary data
                        'report_id' => $report->id, // Associate with the report
                    ]);
                }
            } else {
                // If no image file is uploaded, the existing reportimg remains unchanged
                Log::info('No image file uploaded, proceeding without image update');
            }
            

    
            // Determine if the report should be marked as published based on the review status
            $is_publish = $request->review_status_id == 3 ? 1 : ($request->is_publish ?? 0); // Set `is_publish` to 1 if review_status_id is 3
    
            // Determine the related title
            $relatedTitle = null;
            if ($request->related_type === 'project') {
                $relatedTitle = Project::findOrFail($request->related_id)->title;
            } elseif ($request->related_type === 'research') {
                $relatedTitle = Research::findOrFail($request->related_id)->title;
            }
    
            // Update report fields
            $report->update([
                'title' => $request->title,
                'description' => $request->description,
                'related_type' => $request->related_type,
                'related_id' => $request->related_id,
                'related_title' => $relatedTitle,
                'review_status_id' => $request->review_status_id ?? 4, // Use the provided review_status_id or default to 4
                'is_publish' => $is_publish, // Set is_publish based on review status
                'reportimg_id' => $reportimg ? $reportimg->id : null, // Ensure the image is linked correctly if present
            ]);
    
            // Sync SDGs
            $report->sdg()->sync($request->sdg);
            $sdgs = $report->sdg()->pluck('name')->implode(', ');
            $publishStatus = $report->is_publish == 1 ? 'Published' : 'Draft';
    
            // Map review_status_id to actions
            $actionMap = [
                1 => 'requested change',
                2 => 'rejected',
                3 => 'published',
                4 => 'submitted for review',
                5 => 'reviewed',
                6 => 'approved'
            ];
    
            // Determine the action based on review_status_id
            $action = $actionMap[$request->review_status_id] ?? 'updated';
    
            // Log the action to the role_actions table
            RoleAction::create([
                'user_id' => $user->id,
                'content_id' => $report->id,
                'content_type' => Report::class,
                'role' => $user->role,
                'action' => $action // Log dynamic action based on review_status_id
            ]);
    
            // Send notifications based on the updated review status
            switch ($request->review_status_id) {
                case 1: // Need Changes
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'report',
                        'related_type' => Report::class,
                        'related_id' => $report->id,
                        'data' => json_encode([
                            'message' => "Your report '$reportTitle' requires changes.",
                            'reviewer' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'reviewer'],
                            'type' => 'report',
                            'status' => 'request_changes'
                        ]),
                        'created_at' => now(),
                    ]);
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
                    break;
    
                case 2: // Rejected
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'report',
                        'related_type' => Report::class,
                        'related_id' => $report->id,
                        'data' => json_encode([
                            'message' => "Your report '$reportTitle' has been rejected.",
                            'reviewer' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'reviewer'],
                            'type' => 'report',
                            'status' => 'rejected'
                        ]),
                        'created_at' => now(),
                    ]);
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
                    break;
    
                case 3: // Published
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'report',
                        'related_type' => Report::class,
                        'related_id' => $report->id,
                        'data' => json_encode([
                            'message' => "Your report '$reportTitle' has been published.",
                            'publisher' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'publisher'],
                            'type' => 'report',
                            'status' => 'published'
                        ]),
                        'created_at' => now(),
                    ]);
                    ActivityLog::create([
                        'log_name' => 'Report Published',
                        'description' => 'Published the report titled "' . addslashes($report->title) . '"',
                        'subject_type' => Report::class,
                        'subject_id' => $report->id,
                        'event' => 'published',
                        'causer_type' => User::class,
                        'causer_id' => $user->id,
                        'properties' => json_encode([
                            'report_title' => $report->title,
                            'review_status' => 'published',
                            'role' => 'publisher',
                        ]),
                        'created_at' => now(),
                    ]);
                    break;
    
                case 4: // Submitted for Review
                    $reviewers = User::where('role', 'reviewer')->get();
                    foreach ($reviewers as $reviewer) {
                        Notification::create([
                            'user_id' => $reviewer->id,
                            'notifiable_type' => User::class,
                            'notifiable_id' => $reviewer->id,
                            'type' => 'report',
                            'related_type' => Report::class,
                            'related_id' => $report->id,
                            'data' => json_encode([
                                'message' => "A new report titled '$reportTitle' has been submitted for review.",
                                'contributor' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'contributor'],
                                'type' => 'report',
                                'status' => 'submitted for review'
                            ]),
                            'created_at' => now(),
                        ]);
                    }
                    ActivityLog::create([
                        'log_name' => 'Report Resubmission',
                        'description' => 'Report titled "' . addslashes($report->title) . '" resubmitted for review',
                        'subject_type' => Report::class,
                        'subject_id' => $report->id,
                        'event' => 'resubmitted for review',
                        'causer_type' => User::class,
                        'causer_id' => $user->id,
                        'properties' => json_encode([
                            'report_title' => $report->title,
                            'review_status' => 'submitted for review',
                        ]),
                        'created_at' => now(),
                    ]);
                    break;
    
                case 5: // Reviewed
                        // Notify the original contributor about the review
                        Notification::create([
                            'user_id' => $originalContributor,
                            'notifiable_type' => User::class,
                            'notifiable_id' => $originalContributor,
                            'type' => 'report',
                            'related_type' => Report::class,
                            'related_id' => $report->id,
                            'data' => json_encode([
                                'message' => "Your report '$reportTitle' has been reviewed.",
                                'reviewer' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'reviewer'],
                                'type' => 'report',
                                'status' => 'reviewed'
                            ]),
                            'created_at' => now(),
                        ]);
                    
                        // Notify all approvers about the report submission for approval
                        $approvers = User::where('role', 'approver')->get();
                        foreach ($approvers as $approver) {
                            Notification::create([
                                'user_id' => $approver->id,
                                'notifiable_type' => User::class,
                                'notifiable_id' => $approver->id,
                                'type' => 'report',
                                'related_type' => Report::class,
                                'related_id' => $report->id,
                                'data' => json_encode([
                                    'message' => "The report titled '$reportTitle' has been submitted for approval.",
                                    'reviewer' => $user->first_name . ' ' . $user->last_name,
                                    'role' => ['admin', 'reviewer'],
                                    'type' => 'report',
                                    'status' => 'submitted for approval'
                                ]),
                                'created_at' => now(),
                            ]);
                        }
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
                    break;
                    
    
                case 6: // Approved
                        // Notify the original contributor
                        Notification::create([
                            'user_id' => $originalContributor,
                            'notifiable_type' => User::class,
                            'notifiable_id' => $originalContributor,
                            'type' => 'report',
                            'related_type' => Report::class,
                            'related_id' => $report->id,
                            'data' => json_encode([
                                'message' => "Your report '$reportTitle' has been approved.",
                                'reviewer' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'reviewer'],
                                'type' => 'report',
                                'status' => 'approved'
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
                                'type' => 'report',
                                'related_type' => Report::class,
                                'related_id' => $report->id,
                                'data' => json_encode([
                                    'message' => "The report titled '" . addslashes($reportTitle) . "' has been submitted for publishing.",
                                    'reviewer' => $user->first_name . ' ' . $user->last_name,
                                    'role' => 'reviewer',
                                    'type' => 'report',
                                    'status' => $statusPublisher,
                                ]),
                                'created_at' => now(),
                            ]);
                        }
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
                    break;
                    
                default:
                    // Handle other cases if needed
                    break;
            }
    
            DB::commit();
            session()->flash('alert-success', 'Report Updated Successfully!');
            return redirect()->route('reports.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update the report: ' . $e->getMessage());
        }
    }
    
    


    public function get_related_records(Request $request)
    {
        $type = $request->get('type');
        $records = [];

        if ($type === 'project') {
            $records = Project::all(['id', 'title']); // Adjust the attributes as needed
        } elseif ($type === 'research') {
            $records = Research::all(['id', 'title']); // Adjust the attributes as needed
        }

        // Prepare the data for the select options
        $data = $records->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
            ];
        });

        return response()->json($data); // Return prepared data as JSON
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
