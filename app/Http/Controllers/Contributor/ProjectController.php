<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Project\ProjectRequest;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Projectimg;
use App\Models\ProjectResearchStatus;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\GenderImpact;
use App\Services\GenderAnalysisService;
use App\Services\SdgAiService;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller
{
    protected $genderAnalysisService;
    protected $sdgAiService;
    public function __construct(GenderAnalysisService $genderAnalysisService, SdgAiService $sdgAiService)

    {
        $this->genderAnalysisService = $genderAnalysisService;
        $this->sdgAiService = $sdgAiService;
    }

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
    
        // Fetch the filtered list of projects and paginate
        $projects = $query->orderBy('id', 'desc')->orderBy('id', 'desc')->paginate(5); // Paginate the results, 5 projects per page
    
        // Fetch all SDGs for the filter dropdown
        $reviewStatuses = ReviewStatus::whereNotIn('status', ['Need Changes', 'Rejected'])->get(); // Exclude specific statuses
        $sdgs = SDG::all();
        $projectStatuses = ProjectResearchStatus::all(); 

        return view('contributor.projects_programs.index', compact('projectStatuses','projects', 'reviewStatuses', 'sdgs'));
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
            $project = Project::with('feedbacks.user','sdgSubCategories')->findOrFail($id);
        
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
            $project = Project::with('feedbacks.user','sdgSubCategories')->findOrFail($id);

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
    $projectStatuses = ProjectResearchStatus::all(); // Fetch all project statuses
    return view('contributor.projects_programs.create', ['sdgs' => $sdgs, 'projectStatuses' => $projectStatuses]);
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
                'status_id' => $request->status_id, // Update to use status_id
                'is_publish' => 0,
                'user_id' => $user->id, // Set the user_id
                'projectimg_id' => $projectimg->id,
                'location_address' => $request->location_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'review_status_id' => 4 // Set review status to 'Pending Review'
            ]);
     // Create gender impact record if data is available
            if ($request->has('gender_benefits_men') || $request->has('gender_benefits_women')) {
                $genderImpact = new GenderImpact([
                    'project_id' => $project->id,
                    'benefits_men' => $request->gender_benefits_men ? true : false,
                    'benefits_women' => $request->gender_benefits_women ? true : false,
                    'benefits_all' => $request->gender_benefits_all ? true : false,
                    'addresses_gender_inequality' => $request->gender_addresses_inequality ? true : false,
                    'men_count' => $request->gender_men_count ? (int)$request->gender_men_count : null,
                    'women_count' => $request->gender_women_count ? (int)$request->gender_women_count : null,
                    'gender_notes' => $request->gender_notes,
                    'target_beneficiaries' => $request->target_beneficiaries,
                ]);
                $genderImpact->save();
            }
            // Attach SDGs to the project
            $project->sdg()->attach($request->sdg);
            
            // Attach selected sub-categories to the project, ensuring unique values
            if ($request->has('sdg_sub_category')) {
                $uniqueSubCategories = array_unique($request->sdg_sub_category);
                $project->sdgSubCategories()->attach($uniqueSubCategories);
            }
            
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
                    'user_id' => $reviewer->id,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $reviewer->id,
                    'type' => $type,
                    'related_type' => Project::class,
                    'related_id' => $project->id,
                    'data' => json_encode([
                        'message' => "A new $type titled '" . addslashes($projectTitle) . "' has been submitted for review.",
                        'contributor' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'role' => 'contributor',
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
                ' subject_type' => Project::class,
                'subject_id' => $project->id,
                'event' => 'submitted for review',
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'properties' => json_encode([
                    'project_title' => $projectTitle,
                    'description' => $project->description,
                    'status_id' => $project->status_id, // Update to log status_id
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
     * Analyze gender impacts from the project title and description
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeGenderImpacts(Request $request)
    {
        try {
            // Get input from the request
            $title = $request->input('title', '');
            $description = $request->input('description', '');
            $targetBeneficiaries = $request->input('target_beneficiaries', '');
            
            // For text content, combine title and description
            $textContent = $title . ' ' . strip_tags($description);
            
            // Analyze using the gender analysis service
            $analysisResults = $this->genderAnalysisService->analyzeGenderFromText(
                $textContent, 
                $targetBeneficiaries
            );
            
            return response()->json([
                'success' => true,
                'data' => $analysisResults
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing gender impact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        // Find the project by ID, including related SDG and projectimg
        $project = Project::with(['sdg', 'projectimg', 'reviewStatus','sdgSubCategories'])->findOrFail($id);
    
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
        $projectStatuses = ProjectResearchStatus::all(); // Fetch all project statuses
        $existingImage = $project->projectimg->image ?? null;
    
        // Load the selected SDG sub-categories
        $selectedSubCategories = $project->sdgSubCategories()->pluck('sdg_sub_categories.id')->toArray();
    
        return view('contributor.projects_programs.edit', [
            'project' => $project,
            'sdgs' => $sdgs,
            'existingImage' => $existingImage,
            'projectStatuses' => $projectStatuses, // Pass project statuses to the view
            'selectedSubCategories' => $selectedSubCategories, // Pass selected sub-categories
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
        'status_id' => ['required', 'exists:project_research_statuses,id'], // Update validation rule
        'is_publish' => ['nullable'],
        'image' => ['image', 'mimes:png,jpg,jpeg,gif,svg,webp'],
        'description' => ['required', 'min:10'],
        'review_status_id' => ['nullable'],
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
            'status_id' => $request->status_id, // Update to use status_id
            'review_status_id' => 4,
            'is_publish' => 0,
            'user_id' => $user->id, // Ensure the user_id remains the same
            'projectimg_id' => $projectimgId,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
 // Update or create gender impact record if data is available
        if ($request->has('gender_benefits_men') || $request->has('gender_benefits_women')) {
            $genderImpact = GenderImpact::updateOrCreate(
                ['project_id' => $project->id],
                [
                    'benefits_men' => $request->gender_benefits_men ? true : false,
                    'benefits_women' => $request->gender_benefits_women ? true : false,
                    'benefits_all' => $request->gender_benefits_all ? true : false,
                    'addresses_gender_inequality' => $request->gender_addresses_inequality ? true : false,
                    'men_count' => $request->gender_men_count ? (int)$request->gender_men_count : null,
                    'women_count' => $request->gender_women_count ? (int)$request->gender_women_count : null,
                    'gender_notes' => $request->gender_notes,
                    'target_beneficiaries' => $request->target_beneficiaries,
                ]
            );
        }
        // Update SDGs
        $project->sdg()->sync($request->sdg);
        
        // Sync selected sub-categories, ensuring unique values
        if ($request->has('sdg_sub_category')) {
            $uniqueSubCategories = array_unique($request->sdg_sub_category);
            $project->sdgSubCategories()->sync($uniqueSubCategories);
        } else {
            $project->sdgSubCategories()->detach(); // Detach if no sub-categories are selected
        }
        
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
                    'contributor' => $user->first_name . ' ' . $user->last_name,
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
            'status_id' => $project->status_id, // Update to log status_id
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

    /**
     * Analyze a project for SDG relevance
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeSdgs(Request $request)
    {
        try {
            // Get input from the request
            $title = $request->input('title', '');
            $description = $request->input('description', '');
            
            // Log the request data for debugging
            Log::info('Contributor SDG analysis request received', [
                'title_length' => strlen($title),
                'description_length' => strlen(strip_tags($description))
            ]);
            
            // Combine title and description
            $textContent = $title . ' ' . strip_tags($description);
            
            // If text content is too short, return an error
            if (strlen($textContent) < 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Text content is too short for analysis. Please provide more details.'
                ], 400);
            }

            // Use the SdgAiService to analyze the text
            Log::info('Using SdgAiService to analyze text in contributor interface', [
                'text_length' => strlen($textContent)
            ]);
            
            $aiResults = $this->sdgAiService->analyzeText($textContent);
            
            if (!$aiResults) {
                Log::warning('SdgAiService returned null results in contributor interface');
                return response()->json([
                    'success' => false,
                    'message' => 'Error analyzing project content. Please try again or select SDGs manually.'
                ], 500);
            }
            
            // Log the raw AI results for debugging
            Log::info('Received AI results in contributor interface', [
                'has_matched_sdgs' => isset($aiResults['matched_sdgs']),
                'matched_sdgs_count' => isset($aiResults['matched_sdgs']) ? count($aiResults['matched_sdgs']) : 0,
                'source' => $aiResults['metadata']['source'] ?? 'unknown'
            ]);
            
            // Transform AI results to the expected format
            $transformedResults = [
                'sdgs' => [],
                'subcategories' => []
            ];
            
            // Convert the AI results to our application format (SDG IDs and subcategory IDs)
            if (isset($aiResults['matched_sdgs']) && is_array($aiResults['matched_sdgs'])) {
                $processedIds = $this->sdgAiService->convertResultsToIds($aiResults);
                
                // Ensure processedIds has the expected structure
                if (!isset($processedIds['sdgIds']) || !isset($processedIds['subCategoryIds'])) {
                    Log::warning('ProcessedIds does not have the expected structure in contributor interface', [
                        'processedIds' => $processedIds
                    ]);
                    $sdgIds = [];
                    $subCategoryIds = [];
                } else {
                    $sdgIds = $processedIds['sdgIds'];
                    $subCategoryIds = $processedIds['subCategoryIds'];
                    
                    // Log the processed IDs
                    Log::info('Processed AI results into IDs in contributor interface', [
                        'sdg_ids' => $sdgIds,
                        'subcategory_ids' => $subCategoryIds
                    ]);
                }
                
                // Get the SDG details for each ID
                foreach ($sdgIds as $sdgId) {
                    $sdg = \App\Models\Sdg::find($sdgId);
                    if ($sdg) {
                        // Find the original confidence from the AI results
                        $confidence = 0.7; // Default confidence
                        foreach ($aiResults['matched_sdgs'] as $match) {
                            if ((int)ltrim($match['sdg_number'], '0') === $sdgId) {
                                $confidence = $match['confidence'] ?? 0.7;
                                break;
                            }
                        }
                        
                        $transformedResults['sdgs'][] = [
                            'id' => $sdg->id,
                            'name' => $sdg->name,
                            'confidence' => $confidence
                        ];
                    }
                }
                
                // Get the subcategory details for each ID
                foreach ($subCategoryIds as $subCategoryId) {
                    $subCategory = \App\Models\SdgSubCategory::find($subCategoryId);
                    if ($subCategory) {
                        // Find the original confidence from the AI results
                        $confidence = 0.6; // Default confidence
                        foreach ($aiResults['matched_sdgs'] as $match) {
                            if (isset($match['subcategories']) && is_array($match['subcategories'])) {
                                foreach ($match['subcategories'] as $sub) {
                                    if (isset($sub['subcategory']) && $sub['subcategory'] === $subCategory->sub_category_name) {
                                        $confidence = $sub['confidence'] ?? 0.6;
                                        break 2;
                                    }
                                }
                            }
                        }
                        
                        $transformedResults['subcategories'][] = [
                            'id' => $subCategory->id,
                            'name' => $subCategory->sub_category_name,
                            'description' => $subCategory->sub_category_description,
                            'confidence' => $confidence
                        ];
                    }
                }
            } else {
                Log::warning('AI results did not contain matched_sdgs array in contributor interface');
            }
            
            // Check if we got any results
            if (empty($transformedResults['sdgs'])) {
                Log::warning('No SDGs were found in the transformed results for contributor');
                return response()->json([
                    'success' => true,
                    'data' => [
                        'message' => 'No relevant SDGs were detected in your project content. Please select SDGs manually.'
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => $transformedResults
            ]);
        } catch (\Exception $e) {
            Log::error('Error analyzing SDGs (contributor): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'exception_class' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing SDGs: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate simulated SDG matches based on text analysis
     * This is a simple implementation that looks for keywords related to each SDG
     * 
     * @param string $textContent The text content to analyze
     * @return array Matched SDGs with confidence scores
     */
    private function getSimulatedSdgMatches($textContent)
    {
        $textContent = strtolower($textContent);
        $matches = [];
        
        // Define SDG keywords with their corresponding SDG numbers
        $sdgKeywords = [
            '01' => ['poverty', 'poor', 'wealth distribution', 'basic needs', 'income'],
            '02' => ['hunger', 'food', 'nutrition', 'agriculture', 'farming', 'crop'],
            '03' => ['health', 'wellbeing', 'medical', 'disease', 'healthcare', 'wellness', 'mental health'],
            '04' => ['education', 'learning', 'teaching', 'school', 'training', 'literacy', 'student'],
            '05' => ['gender', 'women', 'girl', 'equality', 'female', 'empowerment', 'discrimination'],
            '06' => ['water', 'sanitation', 'hygiene', 'clean water', 'drinking water', 'sewage'],
            '07' => ['energy', 'renewable', 'electricity', 'power', 'solar', 'wind', 'sustainable energy'],
            '08' => ['economy', 'economic', 'work', 'job', 'employment', 'labor', 'decent work', 'growth'],
            '09' => ['industry', 'innovation', 'infrastructure', 'manufacturing', 'technology', 'industrial'],
            '10' => ['inequality', 'social inclusion', 'income inequality', 'discrimination', 'equity'],
            '11' => ['cities', 'urban', 'community', 'housing', 'transportation', 'sustainable cities'],
            '12' => ['consumption', 'production', 'sustainable consumption', 'recycling', 'waste', 'resource'],
            '13' => ['climate', 'carbon', 'emission', 'global warming', 'greenhouse gas', 'climate change'],
            '14' => ['ocean', 'marine', 'sea', 'aquatic', 'fish', 'coral', 'coastal', 'water resources'],
            '15' => ['forest', 'biodiversity', 'ecosystem', 'land', 'desertification', 'wildlife', 'species'],
            '16' => ['peace', 'justice', 'institution', 'governance', 'accountability', 'transparency', 'corruption'],
            '17' => ['partnership', 'cooperation', 'global', 'international', 'collaboration', 'sustainable development']
        ];
        
        // Define some SDG subcategories (simplified)
        $sdgSubcategories = [
            '01' => [['subcategory' => '1.1', 'keywords' => ['extreme poverty', 'basic needs']]],
            '03' => [['subcategory' => '3.1', 'keywords' => ['maternal', 'pregnancy']]],
            '04' => [['subcategory' => '4.1', 'keywords' => ['primary education', 'secondary education', 'quality education']]],
            '05' => [
                ['subcategory' => '5.1', 'keywords' => ['discrimination against women', 'gender discrimination']],
                ['subcategory' => '5.5', 'keywords' => ['women leadership', 'gender equality']]
            ],
            '13' => [['subcategory' => '13.2', 'keywords' => ['climate change measures', 'national policies']]],
        ];
        
        // Count keyword matches for each SDG
        $keywordCounts = [];
        $totalKeywordMatches = 0;
        
        foreach ($sdgKeywords as $sdgNumber => $keywords) {
            $count = 0;
            $matchedKeywords = [];
            
            foreach ($keywords as $keyword) {
                if (strpos($textContent, $keyword) !== false) {
                    $count++;
                    $matchedKeywords[] = $keyword;
                    $totalKeywordMatches++;
                }
            }
            
            if ($count > 0) {
                $keywordCounts[$sdgNumber] = [
                    'count' => $count,
                    'keywords' => $matchedKeywords
                ];
            }
        }
        
        // If no keywords matched, return an empty array
        if ($totalKeywordMatches === 0) {
            // Default to SDG 17 if nothing matches
            return [[
                'sdg_number' => '17',
                'sdg_name' => 'Partnerships for the Goals',
                'confidence' => 0.3,
                'matched_keywords' => ['partnership'],
                'subcategories' => []
            ]];
        }
        
        // Convert keyword counts to SDG matches with confidence scores
        foreach ($keywordCounts as $sdgNumber => $data) {
            // Calculate a simple confidence score based on keyword frequency
            $confidence = min(0.9, 0.5 + ($data['count'] / 10)); 
            
            // Get matching subcategories
            $matchedSubcategories = [];
            if (isset($sdgSubcategories[$sdgNumber])) {
                foreach ($sdgSubcategories[$sdgNumber] as $subcategory) {
                    foreach ($subcategory['keywords'] as $keyword) {
                        if (strpos($textContent, $keyword) !== false) {
                            $matchedSubcategories[] = [
                                'subcategory' => $subcategory['subcategory'],
                                'confidence' => $confidence - 0.1 // Slightly lower confidence for subcategories
                            ];
                            break; // One keyword match is enough for this subcategory
                        }
                    }
                }
            }
            
            // Add to matches array
            $sdgName = $this->getSdgNameByNumber($sdgNumber);
            $matches[] = [
                'sdg_number' => $sdgNumber,
                'sdg_name' => $sdgName,
                'confidence' => $confidence,
                'matched_keywords' => $data['keywords'],
                'subcategories' => $matchedSubcategories
            ];
        }
        
        // Sort matches by confidence score (descending)
        usort($matches, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });
        
        // Limit to top 3 matches (changed from 5 to ensure consistency with Auth controller)
        return array_slice($matches, 0, 3);
    }
    
    /**
     * Get the SDG name by its number
     * 
     * @param string $sdgNumber The SDG number (01-17)
     * @return string The SDG name
     */
    private function getSdgNameByNumber($sdgNumber)
    {
        $sdgNames = [
            '01' => 'No Poverty',
            '02' => 'Zero Hunger',
            '03' => 'Good Health and Well-being',
            '04' => 'Quality Education',
            '05' => 'Gender Equality',
            '06' => 'Clean Water and Sanitation',
            '07' => 'Affordable and Clean Energy',
            '08' => 'Decent Work and Economic Growth',
            '09' => 'Industry, Innovation and Infrastructure',
            '10' => 'Reduced Inequalities',
            '11' => 'Sustainable Cities and Communities',
            '12' => 'Responsible Consumption and Production',
            '13' => 'Climate Action',
            '14' => 'Life Below Water', 
            '15' => 'Life on Land',
            '16' => 'Peace, Justice and Strong Institutions',
            '17' => 'Partnerships for the Goals'
        ];
        
        return $sdgNames[$sdgNumber] ?? "SDG $sdgNumber";
    }
}
