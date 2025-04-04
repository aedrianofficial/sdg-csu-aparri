<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Project\ProjectRequest;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Projectimg;
use App\Models\ProjectResearchStatus;
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
     *
     */

     public function rejected($id, Request $request)
     {
         // Find the project by ID, including related feedbacks and SDG subcategories
         $project = Project::with(['feedbacks.user', 'sdgSubCategories'])->findOrFail($id);
     
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
            // Find the project by ID, including related feedbacks and SDG subcategories
            $project = Project::with(['feedbacks.user', 'sdgSubCategories'])->findOrFail($id);
        
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
        
            // Replace project_status filter with ProjectResearchStatus
            if ($request->filled('status_id')) {
                $query->where('status_id', $request->status_id);
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
        
            // Handle sorting
            if ($request->filled('sort_by')) {
                $sortBy = $request->sort_by;
                $sortOrder = $request->sort_order ?? 'asc'; // Default to ascending order
                $allowedSortColumns = ['title', 'status_id', 'review_status_id', 'created_at']; // Add other sortable columns here
        
                // Check if sorting by review status name
                if ($sortBy === 'status') {
                    $query->join('review_statuses', 'projects.review_status_id', '=', 'review_statuses.id')
                        ->orderBy('review_statuses.name', $sortOrder); // Change 'status' to 'name'
                } elseif (in_array($sortBy, $allowedSortColumns)) {
                    $query->orderBy($sortBy, $sortOrder);
                }
            } else {
                // Default sorting
                $query->orderBy('id', 'desc');
            }
        
            // Fetch the filtered list of projects and paginate
            $projects = $query->with(['sdg', 'reviewStatus'])->paginate(5);
        
            // Fetch all SDGs for the filter dropdown
            $reviewStatuses = ReviewStatus::all();
            $projectStatuses = ProjectResearchStatus::all(); // Fetch all project statuses
            $sdgs = SDG::all();
        
            return view('auth.projects_programs.index', compact('projects', 'reviewStatuses', 'projectStatuses', 'sdgs'));
        }
     

     
     


     public function my_projects(Request $request)
     {
         // Retrieve all projects created by the authenticated user
         $query = Project::where('user_id', Auth::id()); // Filtering by authenticated user's ID
         
         // Apply filters based on request parameters
         if ($request->filled('title')) {
             $query->where('title', 'LIKE', '%' . $request->title . '%');
         }
       // Replace project_status filter with ProjectResearchStatus
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
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
        // Handle sorting
        if ($request->filled('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order ?? 'asc'; // Default to ascending order
            $allowedSortColumns = ['title', 'status_id', 'review_status_id', 'created_at']; // Add other sortable columns here

            // Check if sorting by review status name
            if ($sortBy === 'status') {
                $query->join('review_statuses', 'projects.review_status_id', '=', 'review_statuses.id')
                    ->orderBy('review_statuses.name', $sortOrder); // Change 'status' to 'name'
            } elseif (in_array($sortBy, $allowedSortColumns)) {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            // Default sorting
            $query->orderBy('id', 'desc');
        }

         // Fetch the filtered list of projects and paginate
         $projects = $query->with(['sdg', 'reviewStatus'])->orderBy('id', 'desc')->paginate(5);
     
         // Fetch all SDGs for the filter dropdown
         $reviewStatuses = ReviewStatus::all();
         $projectStatuses = ProjectResearchStatus::all(); 
         $sdgs = SDG::all();
     
         return view('auth.projects_programs.my_projects', compact('projects', 'projectStatuses','reviewStatuses', 'sdgs'));
     }
     
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = ProjectResearchStatus::where('is_active', 1)->get(); // Fetch active statuses
        $sdgs = Sdg::all();
        return view('auth.projects_programs.create', ['sdgs'=> $sdgs, 'statuses'=>$statuses]);
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
                'is_publish' => $isPublish,
                'user_id' => $user->id, // Set the user_id
                'projectimg_id' => $projectimg->id,
                'location_address' => $request->location_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'review_status_id' => $reviewStatusId, // Set review status based on the action
                'status_id' => $request->status_id // Save the selected status_id
            ]);

            // Attach SDGs to the project
            $project->sdg()->attach($request->sdg);
            $sdgs = $project->sdg()->pluck('name')->implode(', ');
            // Attach selected sub-categories to the project
            if ($request->has('sdg_sub_category')) {
                $project->sdgSubCategories()->attach($request->sdg_sub_category);
            }
            // Log the activity in the role_actions table
            if ($submitType === 'publish') {
                RoleAction::create([
                    'content_id' => $project->id,
                    'content_type' => Project::class,
                    'user_id' => $user->id,
                    'role' => 'admin',
                    'action' => 'approved',
                    'created_at' => now()
                ]);

                // Log the activity for publishing the project
                ActivityLog::create([
                    'log_name' => 'Project Approved',
                    'description' => 'Approved the project titled "' . addslashes($project->title) . '"',
                    'subject_type' => Project::class,
                    'subject_id' => $project->id,
                    'event' => 'approved',
                    'causer_type' => User::class,
                    'causer_id' => $user->id,
                    'properties' => json_encode([
                        'project_title' => $project->title,
                        'review_status' => 'approved',
                        'role' => 'approver',
                    ]),
                    'created_at' => now(),
                ]);
            } else if ($submitType === 'review') {
                RoleAction::create([
                    'content_id' => $project->id,
                    'content_type' => Project:: class,
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
                        'status_id' => $project->status_id,
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
        // Find the project by ID, including related SDG, project images, review status, and SDG subcategories
        $project = Project::with(['sdg', 'projectimg', 'reviewStatus', 'sdgSubCategories'])->findOrFail($id);
    
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
        $statuses = ProjectResearchStatus::where('is_active', 1)->get(); // Fetch active statuses
    
        // Load the existing image
        $existingImage = $project->projectimg->image ?? null;
    
        // Load the selected SDG sub-categories
        $selectedSubCategories = $project->sdgSubCategories()->pluck('sdg_sub_categories.id')->toArray();
    
        return view('auth.projects_programs.edit', [
            'project' => $project,
            'sdgs' => $sdgs,
            'reviewStatuses' => $reviewStatuses,
            'existingImage' => $existingImage,
            'statuses' => $statuses,
            'selectedSubCategories' => $selectedSubCategories, // Pass selected sub-categories
        ]);
    }
    
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        Log::info('Starting project update for project ID: ' . $project->id);
        $request->validate([
            'title' => ['required', 'min:2', 'max:255'],
            'sdg' => ['required'],
            'status_id' => ['required', 'exists:project_research_statuses,id'], // Update validation
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
    
            Log::info('About to handle image upload');
            $projectimg = $project->projectimg; // Initialize variable outside the conditional
    
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
    
            // Continue with the project update
            $is_publish = $request->review_status_id == 3 ? 1 : ($request->is_publish ?? 0);
            $project->update([
                'title' => $request->title,
                'description' => $request->description,
                'status_id' => $request->status_id, // Update to use status_id
                'review_status_id' => $request->review_status_id ?? 4,
                'is_publish' => $is_publish,
                'projectimg_id' => $projectimg->id, // Use the existing or newly created image ID
                'location_address' => $request->location_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
    
            Log::info('Project update handled successfully');
    
            $project->sdg()->sync($request->sdg);
            // Sync selected sub-categories
         // Sync selected sub-categories
            if ($request->has('sdg_sub_category')) {
                $project->sdgSubCategories()->sync($request->sdg_sub_category);
            } else {
                $project->sdgSubCategories()->detach(); // Detach if no sub-categories are selected
            }
            $sdgs = $project->sdg()->pluck('name')->implode(', ');
            $publishStatus = $project->is_publish == 1 ? 'Published' : 'Draft';
    
            // Action mapping and logging
            $actionMap = [
                1 => 'requested change',
                2 => 'rejected',
                3 => 'approved',
                4 => 'submitted for review',
                5 => 'reviewed',
            ];
            $action = $actionMap[$project->review_status_id] ?? 'updated';
            if ($project->review_status_id == 3) {
                $action = 'approved';
                $is_publish = 1;
            }
    
            RoleAction::create([
                'user_id' => $user->id,
                'content_id' => $project->id,
                'content_type' => Project::class,
                'role' => $user->role,
                'action' => $action
            ]);
    
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
                        'subject_type' => Project::class,
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
    
                case 3: // Approved
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'project',
                        'related_type' => Project::class,
                        'related_id' => $project->id,
                        'data' => json_encode([
                            'message' => "Your project '$projectTitle' has been approved and is now published.",
                            'approver' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'approver'],
                            'type' => 'project',
                            'status' => 'approved'
                        ]),
                        'created_at' => now(),
                    ]);
    
                    ActivityLog::create([
                        'log_name' => 'Project Published',
                        'description' => 'Published the project titled "' . addslashes($project->title) . '"',
                        'subject_type' => Project::class,
                        'subject_id' => $project->id,
                        'event' => 'approved',
                        'causer_type' => User::class,
                        'causer_id' => $user->id,
                        'properties' => json_encode([
                            'project_title' => $project->title,
                            'review_status' => 'approved',
                            'role' => 'approver',
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
                        'log_name'=>'Project Reviewed',
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
            }
    
            DB::commit();
            Log::info('Project updated successfully: ' . $project->id);
            session()->flash('alert-success', 'Project/Program Updated Successfully!');
            return to_route('projects.index');
    
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Update failed for project ID: ' . $project->id . ' - Error: ' . $ex->getMessage());
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
