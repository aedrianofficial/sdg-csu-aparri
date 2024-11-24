<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Report\CreateRequest;
use App\Http\Requests\Auth\Report\ReportRequest;
use App\Models\ActivityLog;
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
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function show_rejected($id, Request $request)
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
         return view('contributor.feedbacks.rejected_reports', compact('report', 'notificationData'));
     }
     
     
    public function index(Request $request)
    {
        $user = Auth::user();
    
        // Start the query for fetching reports based on the logged-in contributor's ID
        $query = Report::where('user_id', $user->id)
            ->whereIn('review_status_id', [3, 4, 5, 6]); // Filter by specific review statuses
    
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
    
        // Fetch the filtered list of reports and paginate with eager loading for related data
        $reports = $query->with(['reportimg', 'sdg', 'reviewStatus'])->paginate(5);

        $reviewStatuses = ReviewStatus::whereNotIn('status', ['Need Changes', 'Rejected'])->get(); // Exclude specific statuses
        $sdgs = SDG::all();
    
        // Pass the reports, review statuses, and SDGs to the view
        return view('contributor.reports.index', compact('reports', 'reviewStatuses', 'sdgs'));
    }
    

    public function rejected(Request $request)
{
    $user = Auth::user();

    // Start the query for fetching rejected reports
    $query = Report::where('user_id', $user->id)
        ->where('is_publish', 0)
        ->where('review_status_id', 2); // Filter for rejected reports

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

    // Fetch the filtered list of rejected reports and paginate with eager loading for related data
    $reports = $query->with(['reportimg', 'sdg',])->paginate(5);

    // Fetch all SDGs for the filter dropdown
    $sdgs = SDG::all();

    // Pass the reports and SDGs to the view
    return view('contributor.reports.rejected', compact('reports', 'sdgs'));
}



public function show_request_changes($id, Request $request)
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
    return view('contributor.feedbacks.reports', compact('report', 'notificationData'));
}

        public function request_changes(Request $request)
        {
            $user = Auth::user();
        
            // Start the query for fetching reports with requested changes
            $query = Report::where('user_id', $user->id)
                ->where('is_publish', 0)
                ->where('review_status_id', 1); // Filter for reports marked as "Request Changes"
        
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
            $reports = $query->with(['reportimg', 'sdg', 'reviewStatus'])->paginate(5);
        
            // Fetch all SDGs for the filter dropdown
            $sdgs = SDG::all();
        
            // Pass the reports and SDGs to the view
            return view('contributor.reports.request_changes', compact('reports', 'sdgs'));
        }
        

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sdgs = Sdg::all();
        return view('contributor.reports.create', ['sdgs'=> $sdgs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();

                    // Handle file upload as binary data
            $file = $request->file('image');
            $fileData = file_get_contents($file);

            // Create project image record with binary data
            $reportimg = Reportimg::create([
                'image' => $fileData, // Store binary data directly
            ]);

            // Determine the related title and SDGs
            $relatedTitle = null;
            $relatedSdgs = [];

            if ($request->related_type === 'project') {
                $relatedItem = Project::findOrFail($request->related_id);
                $relatedTitle = $relatedItem->title;
                $relatedSdgs = $relatedItem->sdg->pluck('id')->toArray(); // Assuming SDGs are related to the project
            } elseif ($request->related_type === 'research') {
                $relatedItem = Research::findOrFail($request->related_id);
                $relatedTitle = $relatedItem->title;
                $relatedSdgs = $relatedItem->sdg->pluck('id')->toArray(); // Assuming SDGs are related to the research
            }

            // Create the report
            $report = Report::create([
                'title' => $request->title,
                'description' => $request->description,
                'is_publish' => 0, // This will be 0 (Draft) for contributors
                'review_status_id' => 4, //This will be 4 (Pending Review) for contributors
                'reportimg_id' => $reportimg ? $reportimg->id : null, // Handle case where no image is uploaded
                'related_type' => $request->related_type,
                'related_id' => $request->related_id,
                'related_title' => $relatedTitle,
                'user_id' => $user->id, // Set the user_id
            ]);

            // Attach SDGs to the report
            $report->sdg()->attach($relatedSdgs);
            RoleAction::create([
                'content_id' => $report->id,           
                'content_type' => Report::class,       
                'user_id' => $user->id,                
                'role' => 'contributor',                     
                'action' => 'submitted for review',    
                'created_at' => now()                  
            ]);
            // Logging
            $publishStatus = $report->is_publish == 1 ? 'Published' : 'Draft';

            $type = 'report';
            $status = 'submitted for review';
            $reportTitle = $report->title;
    
            // Retrieve all reviewers
            $reviewers = User::where('role', 'reviewer')->get();
    
            // Create notifications for each reviewer
            foreach ($reviewers as $reviewer) {
                Notification::create([
                    'user_id' => $reviewer->id,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $reviewer->id,
                    'type' => $type,
                    'related_type' => Project::class,
                    'related_id' => $report->id,
                    'data' => json_encode([
                        'message' => "The report titled '" . addslashes($reportTitle) . "' has been submitted for review.",
                        'contributor' => $user->first_name . ' ' . $user->last_name,
                        'role' => 'contributor',
                        'type' => $type,
                        'status' => $status,
                    ]),
                    'created_at' => now(),
                ]);
            }

         // Add Activity Log entry
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
                'related_type' => $report->related_type,
                'related_title' => $relatedTitle,
                'reportimg_id' => $reportimg ? $reportimg->id : null,
                'sdgs' => $relatedSdgs,
                'status' => $publishStatus, // This will display 'Draft' initially
            ]),
            'created_at' => now(),
        ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            dd($ex->getMessage());
        }

        session()->flash('alert-success', 'Report Submitted Successfully!');
        return to_route('contributor.reports.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        // Retrieve the report by ID, including related SDGs and user
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
                $notification->markAsRead();
            }
        }
    
        // Pass the report, related title, and notification data to the view
        return view('contributor.reports.show', compact('report', 'notificationData'));
    }
    


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        $projects = Project::all(); // Retrieve all projects
        $researches = Research::all(); // Retrieve all research items
        $existingImage = $report->reportimg->image ?? null;
        $sdgs = Sdg::all();
        return view('contributor.reports.edit', [
            'report' => $report,
            'projects' => $projects,
            'researches' => $researches,
            'sdgs' => $sdgs,
            'existingImage'=>$existingImage
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
{
    // Base validation rules
    $rules = [
        'title' => ['required', 'min:2', 'max:255'],
        'sdg' => ['required'],
        'review_status_id' => ['nullable'],
        'is_publish' => ['nullable'],
        'related_type' => ['required', 'in:project,research'],
        'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif,svg,webp'],
        'description' => ['required', 'min:10']
    ];

    // Conditional validation for related_id based on related_type
    if ($request->related_type === 'project') {
        $rules['related_id'] = ['required', 'exists:projects,id'];
    } elseif ($request->related_type === 'research') {
        $rules['related_id'] = ['required', 'exists:research,id'];
    }

    // Validate the request
    $request->validate($rules);

    $user = Auth::user();

    try {
        DB::beginTransaction();

        // Handle file upload
        if ($request->hasFile('image')) {
            // Get the uploaded file and convert it to binary data
            $file = $request->file('image');
            $fileData = file_get_contents($file); // Convert the file to binary data
        
            // Check if a report image record already exists for this report
            $reportimg = $report->reportimg;
        
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
            // Optionally, handle cases where no file is uploaded
            return back()->with('error', 'No image file was uploaded.');
        }

        // Fetch related title
        $relatedTitle = null;
        if ($request->related_type === 'project') {
            $related = Project::find($request->related_id);
            $relatedTitle = $related ? $related->title : null;
        } elseif ($request->related_type === 'research') {
            $related = Research::find($request->related_id);
            $relatedTitle = $related ? $related->title : null;
        }

        // Update report details
        $report->update([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $user->id,
            'review_status_id' => 4,
            'is_publish' => 0,
            'reportimg_id' => $reportimg ? $reportimg->id : null,
            'related_type' => $request->related_type,
            'related_id' => $request->related_id,
            'related_title' => $relatedTitle
        ]);

        $report->sdg()->sync($request->sdg);
        $sdgs = $report->sdg()->pluck('name')->implode(', ');
        $publishStatus = $report->is_publish == 1 ? 'Published' : 'Draft';

        RoleAction::create([
            'content_id' => $report->id,           
            'content_type' => Report::class,       
            'user_id' => $user->id,                
            'role' => 'contributor',                     
            'action' => 'submitted for review',    
            'created_at' => now()                  
        ]);

        $type = 'report';
        $status = 'resubmitted for review';
        $reportTitle = $report->title;

        // Retrieve all reviewers
        $reviewers = User::where('role', 'reviewer')->get();

        // Create notifications for each reviewer
        foreach ($reviewers as $reviewer) {
            Notification::create([
                'user_id' => $reviewer->id,
                'notifiable_type' => User::class,
                'notifiable_id' => $reviewer->id,
                'type' => $type,
                'related_type' => Report::class,
                'related_id' => $report->id,
                'data' => json_encode([
                    'message' => "The report titled '" . addslashes($reportTitle) . "' has been resubmitted for review.",
                    'contributor' => $user->first_name . ' ' . $user->last_name,
                    'role' => 'contributor',
                    'type' => $type,
                    'status' => $status,
                ]),
                'created_at' => now(),
            ]);
        }

         // Log the activity for the report update
         $properties = [
            'report_id' => $report->id,
            'title' => $report->title,
            'description' => $report->description,
            'related_type' => $report->related_type,
            'related_id' => $report->related_id,
            'related_title' => $relatedTitle,
            'sdgs' => $sdgs,
            'publish_status' => $publishStatus,
            'review_status' => 'Resubmitted for Review',
            'updated_by' => [
                'user_id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'role' => $user->role
            ],
            'timestamp' => now(),
        ];
        
        ActivityLog::create([
            'log_name' => 'Report Resubmission',
            'description' => 'Report titled "' . addslashes($report->title) . '"  resubmitted for review by contributor',
            'subject_type' => Report::class,
            'subject_id' => $report->id,
            'event' => 'resubmitted for review',
            'causer_type' => User::class,
            'causer_id' => $user->id,
            'properties' => json_encode($properties),
            'created_at' => now(),
        ]);
       
        DB::commit();
    } catch (\Exception $ex) {
        DB::rollBack();
        dd($ex->getMessage());
    }

    session()->flash('alert-success', 'Report Updated Successfully!');
    return to_route('contributor.reports.index');
}





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
