<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Project\ProjectRequest;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Projectimg;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function rejected($id, Request $request)
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

            return view('auth.feedbacks.rejected_projects_programs', compact('project', 'notificationData'));
        }
     public function need_changes($id, Request $request)
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
     
         return view('auth.feedbacks.projects_programs', compact('project', 'notificationData'));
     }

     public function index(Request $request)
     {
         $query = Project::query();
         
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
         $projects = $query->with(['sdg', 'reviewStatus'])->paginate(5);
     
         // Fetch all SDGs for the filter dropdown
         $reviewStatuses = ReviewStatus::all();
         $sdgs = SDG::all();
     
         return view('auth.projects_programs.index', compact('projects', 'reviewStatuses', 'sdgs'));
     }
     
     


     public function my_projects(Request $request)
     {
         // Retrieve all projects created by the authenticated user
         $query = Project::where('user_id', Auth::id()); // Filtering by authenticated user's ID
         
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
         $projects = $query->with(['sdg', 'reviewStatus'])->paginate(5);
     
         // Fetch all SDGs for the filter dropdown
         $reviewStatuses = ReviewStatus::all();
         $sdgs = SDG::all();
     
         return view('auth.projects_programs.my_projects', compact('projects', 'reviewStatuses', 'sdgs'));
     }
     
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sdgs = Sdg::all();
        return view('auth.projects_programs.create', ['sdgs'=> $sdgs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $user = Auth::user();
        $submitType = $request->submit_type; // Capture the button clicked value ('publish' or 'review')
    
        try {
            DB::beginTransaction();
    
                    // Handle file upload as binary data
            $file = $request->file('image');
            $fileData = file_get_contents($file);

            // Create project image record with binary data
            $projectimg = Projectimg::create([
                'image' => $fileData, // Store binary data directly
            ]);

            // Determine the review status and publish status based on the button clicked
            $reviewStatusId = ($submitType === 'publish') ? 3 : 4; // 3 = Published, 4 = Pending Review
            $isPublish = ($submitType === 'publish') ? 1 : 0; // 1 = Published, 0 = Draft
    
            // Create project record with the appropriate review_status_id and is_publish
            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
                'project_status' => $request->project_status,
                'is_publish' => $isPublish,
                'user_id' => $user->id, // Set the user_id
                'projectimg_id' => $projectimg->id,
                'location_address' => $request->location_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'review_status_id' => $reviewStatusId // Set review status based on the action
            ]);
    
            // Attach SDGs to the project
            $project->sdg()->attach($request->sdg);
            $sdgs = $project->sdg()->pluck('name')->implode(', ');
    
            // Log the activity in the role_actions table
            if ($submitType === 'publish') {
                RoleAction::create([
                    'content_id' => $project->id,
                    'content_type' => Project::class,
                    'user_id' => $user->id,
                    'role' => 'admin',
                    'action' => 'published',
                    'created_at' => now()
                ]);
    
                // Log the activity for publishing the project
                ActivityLog::create([
                    'log_name' => 'Project Published',
                    'description' => 'Published the project titled "' . addslashes($project->title) . '"',
                    'subject_type' => Project::class,
                    'subject_id' => $project->id,
                    'event' => 'published',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'project_title' => $project->title,
                        'review_status' => 'published',
                        'role' => 'publisher',
                    ]),
                    'created_at' => now(),
                ]);
            } else if ($submitType === 'review') {
                RoleAction::create([
                    'content_id' => $project->id,
                    'content_type' => Project::class,
                    'user_id' => $user->id,
                    'role' => 'admin',
                    'action' => 'submitted for review',
                    'created_at' => now()
                ]);
                
                // Add Activity Log entry for submission
                ActivityLog::create([
                    'log_name' => 'Project Submission',
                    'description' => "Project titled '" . addslashes($project->title) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
                    'subject_type' => Project::class,
                    'subject_id' => $project->id,
                    'event' => 'submitted for review',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'project_title' => $project->title,
                        'description' => $project->description,
                        'project_status' => $project->project_status,
                        'is_publish' => 'draft',
                        'sdgs' => $sdgs,
                        'location_address' => $project->location_address,
                        'latitude' => $project->latitude,
                        'longitude' => $project->longitude,      
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
                        'type' => 'project',
                        'related_type' => Project::class,
                        'related_id' => $project->id,
                        'data' => json_encode([
                            'message' => "A new project titled '" . addslashes($project->title) . "' has been submitted for review.",
                            'contributor' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'contributor'],
                            'type' => 'project',
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
    
        session()->flash('alert-success', 'Project/Program Submitted Successfully!');
        return to_route('projects.index');
    }
    
    


    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        // Find the project by ID, including related SDG, project images, and review status
        $project = Project::with(['sdg', 'projectimg', 'reviewStatus'])->findOrFail($id);
    
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
    
        return view('auth.projects_programs.show', compact('project', 'notificationData'));
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $sdgs = Sdg::all();
        $reviewStatuses = ReviewStatus::all();

        // The `projectimg` relationship should now return the full image URL from the accessor
        $existingImage = $project->projectimg->image ?? null;

        return view('auth.projects_programs.edit', [
            'project' => $project, 
            'sdgs' => $sdgs, 
            'reviewStatuses' => $reviewStatuses,
            'existingImage' => $existingImage, // Pass the image URL to view
        ]);
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max size
            'description' => ['required', 'min:10'],
            'review_status_id' => ['nullable', 'exists:review_statuses,id'],
            'location_address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'feedback' => ['nullable', 'string', 'max:1000'],  // New feedback validation
            'project_id' => ['required', 'exists:projects,id'] // Project ID for feedback association
        ]);
    
        $user = Auth::user();
        $originalContributor = $project->user_id; // Keep the original contributor ID
    
        try {
            DB::beginTransaction();
    
             // Creating and attaching feedback
             if ($request->filled('feedback')) {
                $feedback = Feedback::firstOrCreate([
                    'feedback' => $request->feedback,
                    'users_id' => $user->id,
                ]);
                
                // Then attach to the research
                $project->feedbacks()->syncWithoutDetaching($feedback->id);                
            }


                        // Handle file upload
            if ($request->hasFile('image')) {
                // Get the uploaded file and convert it to binary data
                $file = $request->file('image');
                $fileData = file_get_contents($file); // Convert the file to binary data

                // Check if a project image record already exists for this project
                $projectimg = $project->projectimg;

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
            } else {
                // Optionally, handle cases where no file is uploaded
                return back()->with('error', 'No image file was uploaded.');
            }


    
            $is_publish = $request->review_status_id == 3 ? 1 : ($request->is_publish ?? 0);
            $project->update([
                'title' => $request->title,
                'description' => $request->description,
                'project_status' => $request->project_status,
                'review_status_id' => $request->review_status_id ?? 4,
                'is_publish' => $is_publish,
                'projectimg_id' => $projectimg->id,
                'location_address' => $request->location_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
    
            $project->sdg()->sync($request->sdg);
            $sdgs = $project->sdg()->pluck('name')->implode(', ');
            $publishStatus = $project->is_publish == 1 ? 'Published' : 'Draft';
    
            $actionMap = [
                1 => 'requested change',
                2 => 'rejected',
                3 => 'published',
                4 => 'submitted for review',
                5 => 'reviewed',
                6 => 'approved'
            ];
            $action = $actionMap[$project->review_status_id] ?? 'updated';
            if ($project->review_status_id == 3) {
                $action = 'published';
                $is_publish = 1;
            }
    
            RoleAction::create([
                'user_id' => $user->id,
                'content_id' => $project->id,
                'content_type' => Project::class,
                'role' => $user->role,
                'action' => $action
            ]);
    
            DB::commit();
    
            // Notification logic based on review_status_id
            $projectTitle = addslashes($project->title);
    
            switch ($project->review_status_id) {
                case 1: // Need Changes
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'project',
                        'related_type' => Project::class,
                        'related_id' => $project->id,
                        'data' => json_encode([
                            'message' => "Your project '$projectTitle' requires changes.",
                            'reviewer' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'reviewer'],  
                            'type' => 'project',
                            'status' => 'request_changes'
                        ]),
                        'created_at' => now(),
                    ]);

                    ActivityLog::create([
                        'log_name' => 'Project Needs Changes',
                        'description' => 'Requested changes for the project titled "' . addslashes($project->title) . '"',
                        'subject_type' => Project::class, // Fix: Added missing closing quote
                        'subject_id' => $project->id,
                        'event' => 'requested change',
                        'causer_type' => User::class,
                        'causer_id' => auth()->user()->id,
                        'properties' => json_encode([
                            'project_title' => $project->title,
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
                        'type' => 'project',
                        'related_type' => Project::class,
                        'related_id' => $project->id,
                        'data' => json_encode([
                            'message' => "Your project '$projectTitle' has been rejected.",
                            'reviewer' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'reviewer'],
                            'type' => 'project',
                            'status' => 'rejected'
                        ]),
                        'created_at' => now(),
                    ]);

                    ActivityLog::create([
                        'log_name' => 'Project Rejected',
                        'description' => 'Rejected the project titled "' . addslashes($project->title) . '"',
                        'subject_type' => Project::class,
                        'subject_id' => $project->id,
                        'event' => 'rejected',
                        'causer_type' => User::class,
                        'causer_id' => auth()->user()->id,
                        'properties' => json_encode([
                            'project_title' => $project->title,
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
                        'type' => 'project',
                        'related_type' => Project::class,
                        'related_id' => $project->id,
                        'data' => json_encode([
                            'message' => "Your project '$projectTitle' has been published.",
                            'publisher' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'publisher'],
                            'type' => 'project',
                            'status' => 'published'
                        ]),
                        'created_at' => now(),
                    ]);

                    ActivityLog::create([
                        'log_name' => 'Project Published',
                        'description' => 'Published the project titled "' . addslashes($project->title) . '"',
                        'subject_type' => Project::class,
                        'subject_id' => $project->id,
                        'event' => 'published',
                        'causer_type' => User::class,
                        'causer_id' => $user->id,
                        'properties' => json_encode([
                            'project_title' => $project->title,
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
                            'type' => 'project',
                            'related_type' => Project::class,
                            'related_id' => $project->id,
                            'data' => json_encode([
                                'message' => "A new project titled '$projectTitle' has been submitted for review.",
                                'contributor' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'contributor'],
                                'type' => 'project',
                                'status' => 'submitted for review'
                            ]),
                            'created_at' => now(),
                        ]);
                    }

                    ActivityLog::create([
                        'log_name' => 'Project Resubmission',
                        'description' => 'Project titled "' . addslashes($project->title) . '" resubmitted for review',
                        'subject_type' => Project::class,
                        'subject_id' => $project->id,
                        'event' => 'resubmitted for review',
                        'causer_type' => User::class,
                        'causer_id' => $user->id,
                        'properties' => json_encode([
                            'project_title' => $project->title,
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
                            'type' => 'project',
                            'related_type' => Project::class,
                            'related_id' => $project->id,
                            'data' => json_encode([
                                'message' => "Your project '$projectTitle' has been reviewed.",
                                'reviewer' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'reviewer'],
                                'type' => 'project',
                                'status' => 'reviewed'
                            ]),
                            'created_at' => now(),
                        ]);
                    
                        // Notify all approvers about the project submission for approval
                        $approvers = User::where('role', 'approver')->get();
                        foreach ($approvers as $approver) {
                            Notification::create([
                                'user_id' => $approver->id,
                                'notifiable_type' => User::class,
                                'notifiable_id' => $approver->id,
                                'type' => 'project',
                                'related_type' => Project::class,
                                'related_id' => $project->id,
                                'data' => json_encode([
                                    'message' => "The project titled '$projectTitle' has been submitted for approval.",
                                    'reviewer' => $user->first_name . ' ' . $user->last_name,
                                    'role' => ['admin', 'reviewer'],
                                    'type' => 'project',
                                    'status' => 'submitted for approval'
                                ]),
                                'created_at' => now(),
                            ]);
                        }

                        ActivityLog::create([
                            'log_name' => 'Project Reviewed',
                            'description' => 'Reviewed the project titled "' . addslashes($project->title) . '"',
                            'subject_type' => Project::class,
                            'subject_id' => $project->id,
                            'event' => 'reviewed',
                            'causer_type' => User::class,
                            'causer_id' => auth()->user()->id,
                            'properties' => json_encode([
                                'project_title' => $project->title,
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
                            'type' => 'project',
                            'related_type' => Project::class,
                            'related_id' => $project->id,
                            'data' => json_encode([
                                'message' => "Your project '$projectTitle' has been approved.",
                                'approver' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'approver'],
                                'type' => 'project',
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
                                'type' => 'project',
                                'related_type' => Project::class,
                                'related_id' => $project->id,
                                'data' => json_encode([
                                    'message' => "The project titled '" . addslashes($projectTitle) . "' has been submitted for publishing.",
                                    'approver' => $user->first_name . ' ' . $user->last_name,
                                    'role' => 'approver',
                                    'type' => 'project',
                                    'status' => $statusPublisher,
                                ]),
                                'created_at' => now(),
                            ]);
                        }

                        ActivityLog::create([
                            'log_name' => 'Project Approved',
                            'description' => 'Approved the project titled "' . addslashes($project->title) . '"',
                            'subject_type' => Project::class,
                            'subject_id' => $project->id,
                            'event' => 'approved',
                            'causer_type' => User::class,
                            'causer_id' => auth()->user()->id,
                            'properties' => json_encode([
                                'project_title' => $project->title,
                                'review_status' => 'approved',
                                'role' => 'approver',
                            ]),
                            'created_at' => now(),
                        ]);
                    break;
                    
            }
    
            session()->flash('alert-success', 'Project/Program Updated Successfully!');
            return to_route('projects.index');
    
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