<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Project\ProjectRequest;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Projectimg;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
        $query = Project::with(['projectimg', 'sdg', 'reviewStatus'])
            ->where('user_id', $user->id) // Filter projects by authenticated user
            ->whereIn('review_status_id', [3, 4, 5, 6]); // Specific review statuses
    
        // Apply filters based on request parameters
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
        if ($request->filled('project_status')) {
            $query->where('project_status', $request->project_status);
        }
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
    
        // Fetch the filtered list of projects and paginate
        $projects = $query->orderBy('id', 'desc')->orderBy('id', 'desc')->paginate(5); // Paginate the results, 5 projects per page
    
        // Fetch all SDGs for the filter dropdown
        $reviewStatuses = ReviewStatus::whereNotIn('status', ['Need Changes', 'Rejected'])->get(); // Exclude specific statuses
        $sdgs = SDG::all();
    
        return view('contributor.projects_programs.index', compact('projects', 'reviewStatuses', 'sdgs'));
    }
    
    
        public function request_changes(Request $request)
        {
            
            $user = Auth::user(); // Get the authenticated user
            $query = Project::with(['projectimg', 'sdg', 'reviewStatus'])
                ->where('user_id', $user->id) // Filter projects by authenticated user
                ->where('is_publish', 0) // Only unpublished projects
                ->where('review_status_id', 1); // Specific review statuses
        
            // Apply filters based on request parameters
            if ($request->filled('title')) {
                $query->where('title', 'LIKE', '%' . $request->title . '%');
            }
            
            if ($request->filled('project_status')) {
                $query->where('project_status', $request->project_status);
            }
           
        
            // Apply SDG filter if present
            if ($request->filled('sdg')) {
                $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
                $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                    $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
                });
            }
        
            // Fetch the filtered list of projects and paginate
            $projects = $query->orderBy('id', 'desc')->paginate(5); // Paginate the results, 5 projects per page
        
           $sdgs = SDG::all();
        
            return view('contributor.projects_programs.request_changes', compact('projects', 'sdgs'));
        }

        public function rejected(Request $request)
        {
            
            $user = Auth::user(); // Get the authenticated user
            $query = Project::with(['projectimg', 'sdg', 'reviewStatus'])
                ->where('user_id', $user->id) // Filter projects by authenticated user
                ->where('is_publish', 0) // Only unpublished projects
                ->where('review_status_id', 2); // Specific review statuses
        
            // Apply filters based on request parameters
            if ($request->filled('title')) {
                $query->where('title', 'LIKE', '%' . $request->title . '%');
            }
            if ($request->filled('project_status')) {
                $query->where('project_status', $request->project_status);
            }
           
        
            // Apply SDG filter if present
            if ($request->filled('sdg')) {
                $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
                $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                    $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
                });
            }
        
            // Fetch the filtered list of projects and paginate
            $projects = $query->orderBy('id', 'desc')->paginate(5); // Paginate the results, 5 projects per page
        
           $sdgs = SDG::all();
        
            return view('contributor.projects_programs.rejected', compact('projects', 'sdgs'));
        }

        public function show_request_changes($id, Request $request)
        {
            // Find the project by ID, including related feedbacks
            $project = Project::with('feedbacks.user')->findOrFail($id);
        
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
        
            return view('contributor.feedbacks.projects_programs', compact('project', 'notificationData'));
        }
        

        public function show_rejected($id, Request $request)
        {
            // Find the project by ID, including related feedbacks
            $project = Project::with('feedbacks.user')->findOrFail($id);

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

            return view('contributor.feedbacks.rejected_projects_programs', compact('project', 'notificationData'));
        }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sdgs = Sdg::all();
        return view('contributor.projects_programs.create', ['sdgs'=> $sdgs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
    $user = Auth::user();

    try {
        DB::beginTransaction();

       // Handle file upload as binary data
       $file = $request->file('image');
       $fileData = file_get_contents($file);

       // Create project image record with binary data
       $projectimg = Projectimg::create([
           'image' => $fileData, // Store binary data directly
       ]);

        // Create project record with review_status_id as 4 (Pending Review)
        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_status' => $request->project_status,
            'is_publish' => 0,
            'user_id' => $user->id, // Set the user_id
            'projectimg_id' => $projectimg->id,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'review_status_id' => 4 // Set review status to 'Pending Review'
        ]);

        // Attach SDGs to the project
        $project->sdg()->attach($request->sdg);
        $sdgs = $project->sdg()->pluck('name')->implode(', ');
        $publishStatus = $project->is_publish == 1 ? 'Published' : 'Draft';

        RoleAction::create([
            'content_id' => $project->id,           
            'content_type' => Project::class,       
            'user_id' => $user->id,                
            'role' => 'contributor',                     
            'action' => 'submitted for review',    
            'created_at' => now()                  
        ]);

        $type = 'project';
        $status = 'submitted for review';
        $projectTitle = $project->title;

        // Retrieve all reviewers
        $reviewers = User::where('role', 'reviewer')->get();

        // Create notifications for each reviewer
        foreach ($reviewers as $reviewer) {
            Notification::create([
                'user_id' => $reviewer->id, // Set to each reviewer's user ID
                'notifiable_type' => User::class,
                'notifiable_id' => $reviewer->id,
                'type' => $type,
                'related_type' => Project::class,
                'related_id' => $project->id,
                'data' => json_encode([
                    'message' => "A new $type titled '" . addslashes($projectTitle) . "' has been submitted for review.",
                    'contributor' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'role' => 'contributor',  // Specify the role
                    'type' => $type,
                    'status' => $status,
                ]),
                'created_at' => now(),
            ]);
        }

     // Add Activity Log entry
     ActivityLog::create([
        'log_name' => 'Project Submission',
        'description' => "Project titled '" . addslashes($projectTitle) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
        'subject_type' => Project::class,
        'subject_id' => $project->id,
        'event' => 'submitted for review',
        'causer_type' => User::class,
        'causer_id' => $user->id,
        'properties' => json_encode([
            'project_title' => $projectTitle,
            'description' => $project->description,
            'project_status' => $project->project_status,
            'is_publish' => $publishStatus,
            'sdgs' => $sdgs,
            'location_address' => $project->location_address,
            'latitude' => $project->latitude,
            'longitude' => $project->longitude,
            
    ]),
        'created_at' => now(),
    ]);


        DB::commit();
    } catch (\Exception $ex) {
        DB::rollBack();
        dd($ex->getMessage());
    }

    session()->flash('alert-success', 'Project/Program Submitted Successfully!');
    return to_route('contributor.projects.index');
}




    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        // Find the project by ID, including related SDG and projectimg
        $project = Project::with(['sdg', 'projectimg', 'reviewStatus'])->findOrFail($id);
    
        // Check if the authenticated user is the owner of the project
        if (Auth::user()->id !== $project->user_id) {
            abort(403, 'Unauthorized action.');
        }
    
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
    
        return view('contributor.projects_programs.show', compact('project', 'notificationData'));
    }
    

    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $sdgs = Sdg::all();
         // The `projectimg` relationship should now return the full image URL from the accessor
         $existingImage = $project->projectimg->image ?? null;

        return view('contributor.projects_programs.edit', ['project' => $project, 'sdgs' => $sdgs,'existingImage'=>$existingImage]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => ['required', 'min:2', 'max:255'],
            'sdg' => ['required'],
            'project_status' => ['required', 'in:Proposed,On-Going,On-Hold,Completed,Rejected'],
            'is_publish' => ['nullable'],
            'image' => ['image', 'mimes:png,jpg,jpeg,gif,svg,webp'],
            'description' => ['required', 'min:10'],
            'review_status_id' =>['nullable'],
            'location_address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            Log::info('About to handle image upload');

            // Initialize the variable outside the conditional to avoid reference issues
            $projectimg = $project->projectimg;
            
            if ($request->hasFile('image')) {
                // Get the uploaded file and convert it to binary data
                $file = $request->file('image');
                $fileData = file_get_contents($file); // Convert the file to binary data
            
                if ($projectimg) {
                    // Update the existing project image with new binary data
                    $projectimg->update([
                        'image' => $fileData, // Replace the binary data
                    ]);
                } else {
                    // Create a new project image record with binary data
                    $projectimg = Projectimg::create([
                        'image' => $fileData, // Store binary data
                        'project_id' => $project->id, // Associate with the project
                    ]);
                }
                Log::info('Image upload handled successfully');
            } else {
                Log::info('No image file uploaded, proceeding without image');
            }
           
            $projectimgId = $projectimg ? $projectimg->id : $project->projectimg_id;
            // Update project details
            $project->update([
                'title' => $request->title,
                'description' => $request->description,
                'project_status' => $request->project_status,
                'review_status_id' => 4,
                'is_publish' => 0,
                'user_id' => $user->id, // Ensure the user_id remains the same
                'projectimg_id' => $projectimgId,
                'location_address' => $request->location_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // Update SDGs
            $project->sdg()->sync($request->sdg);
            $sdgs = $project->sdg()->pluck('name')->implode(', ');
            $publishStatus = $project->is_publish == 1 ? 'Published' : 'Draft';
            Log::info('Project update handled successfully');
            RoleAction::create([
                'content_id' => $project->id,           
                'content_type' => Project::class,       
                'user_id' => $user->id,                
                'role' => 'contributor',                     
                'action' => 'submitted for review',    
                'created_at' => now()                  
            ]);

        $type = 'project';
        $status = 'resubmitted for review';
        $projectTitle = $project->title;

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
                'related_id' => $project->id,
                'data' => json_encode([
                    'message' => "The project titled '" . addslashes($projectTitle) . "' has been resubmitted for review.",
                    'contributor' =>  $user->first_name . ' ' . $user->last_name,
                    'role' => 'contributor',
                    'type' => $type,
                    'status' => $status,
                ]),
                'created_at' => now(),
            ]);
        }

        // Log the activity for the project update
        $properties = [
            'project_id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'project_status' => $project->project_status,
            'sdgs' => $sdgs,
            'publish_status' => $publishStatus,
            'location' => [
                'address' => $project->location_address,
                'latitude' => $project->latitude,
                'longitude' => $project->longitude,
            ],
            'image' => $projectimg->image ?? null,
            'updated_by' => [
                'user_id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'role' => $user->role,
            ],
            'timestamp' => now(),
        ];

        ActivityLog::create([
            'log_name' => 'Project Resubmission',
            'description' => 'Project titled "' . addslashes($project->title) . '" resubmitted for review by contributor',
            'subject_type' => Project::class,
            'subject_id' => $project->id,
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

        session()->flash('alert-success', 'Project/Program Updated Successfully!');
        return to_route('contributor.projects.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
