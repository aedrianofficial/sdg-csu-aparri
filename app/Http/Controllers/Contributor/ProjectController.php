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
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'sdg' => 'required|array',
            'sdg.*' => 'exists:sdgs,id',
            'status_id' => 'required|exists:project_research_statuses,id',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location_address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'target_beneficiaries' => 'nullable|string|max:500'
        ]);
    
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->extension();
            $image->storeAs('projectimages', $imageName, 'public');
    
            // Create new image record
            $projectImg = Projectimg::create([
                'image_path' => $imageName,
            ]);
    
            // Create new project record
            $project = Project::create([
                'sdg_sub_category_id' => $request->sdg_sub_category ? json_encode($request->sdg_sub_category) : null,
                'projectimg_id' => $projectImg->id,
                'user_id' => Auth::id(),
                'review_status_id' => 1, // Set to "Pending" by default
                'status_id' => $request->status_id,
                'title' => $request->title,
                'description' => $request->description,
                'is_publish' => $request->is_publish,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_address' => $request->location_address,
                'target_beneficiaries' => $request->target_beneficiaries,
            ]);
    
            // Attach SDGs to project
            $project->sdg()->attach($request->sdg);
            
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
                ]);
                $genderImpact->save();
            }

            // Check if contributor exist to activity log
            $activityLog = new ActivityLog();
            $activityLog->user_id = auth()->id();
            $activityLog->contribution_type = 'Project';
            $activityLog->contribution_id = $project->id;
            $activityLog->save();
    
            // Create a notification for all admins
            $admins = User::where('user_role_id', 1)->get();
            foreach ($admins as $admin) {
                $notification = new Notification();
                $notification->user_id = $admin->id;
                $notification->message = 'New Project/Program has been submitted by ' . auth()->user()->name;
                $notification->save();
            }

            // Redirect to index page
            return redirect()->route('contributor.projects.index')->with('success', 'Projects/Programs created successfully');
            }
    
        return back()->with('error', 'Image upload required');
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
    public function update(Request $request, $id)
{
        // Validate the request
    $request->validate([
            'title' => 'required|string|max:255',
            'sdg' => 'required|array',
            'sdg.*' => 'exists:sdgs,id',
            'status_id' => 'required|exists:project_research_statuses,id',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location_address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'target_beneficiaries' => 'nullable|string|max:500'
        ]);

        // Find the project
        $project = Project::findOrFail($id);

        // Update project details
        $project->update([
            'sdg_sub_category_id' => $request->sdg_sub_category ? json_encode($request->sdg_sub_category) : null,
            'status_id' => $request->status_id,
            'title' => $request->title,
            'description' => $request->description,
            'is_publish' => $request->is_publish,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_address' => $request->location_address,
            'target_beneficiaries' => $request->target_beneficiaries,
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
                ]
            );
        }

        // Handle image upload if new image was provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->extension();
            $image->storeAs('projectimages', $imageName, 'public');

            // Update or create new image record
            if ($project->projectimg_id) {
                $projectImg = Projectimg::findOrFail($project->projectimg_id);
                $projectImg->update([
                    'image_path' => $imageName,
                ]);
            } else {
                $projectImg = Projectimg::create([
                    'image_path' => $imageName,
                ]);
                $project->update([
                    'projectimg_id' => $projectImg->id,
                ]);
            }
        }

        // Update SDGs
        $project->sdg()->sync($request->sdg);

        // Record activity log
        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id();
        $activityLog->contribution_type = 'Project Update';
        $activityLog->contribution_id = $project->id;
        $activityLog->save();

        // Redirect to index page
        return redirect()->route('contributor.projects.index')->with('success', 'Project/Program updated successfully');
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
