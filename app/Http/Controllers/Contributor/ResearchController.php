<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Research\ResearchRequest;
use App\Models\ActivityLog;
use App\Models\GenderImpact;
use App\Models\Notification;
use App\Models\ProjectResearchStatus;
use App\Models\Research;
use App\Models\Researchcategory;
use App\Models\Researchfile;
use App\Models\ReviewStatus;
use App\Models\RoleAction;
use App\Models\Sdg;
use App\Models\User;
use App\Services\SdgAiService;
use App\Services\GenderAnalysisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResearchController extends Controller
{
    protected $sdgAiService;
    protected $genderAnalysisService;

    public function __construct(SdgAiService $sdgAiService, GenderAnalysisService $genderAnalysisService)
    {
        $this->sdgAiService = $sdgAiService;
        $this->genderAnalysisService = $genderAnalysisService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
    
        // Initialize the query for the research entries
        $query = Research::with(['researchfiles', 'sdg'])
            ->where('user_id', $user->id) // Filter by the authenticated user ID
            ->whereIn('review_status_id', [3, 4, 5, 6]); // Filter for specific review statuses
    
        // Apply filters based on request parameters
    
        // Filter by title if present
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        // Filter by research category if present
        if ($request->filled('researchcategory_id')) {
            $query->where('researchcategory_id', $request->researchcategory_id);
        }
    
        // Filter by research status if present
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
    
        // Filter by review status if present
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
    
        // Fetch the filtered list of researches and paginate the results
        $researches = $query->orderBy('id', 'desc')->paginate(5);
    
        // Fetch all SDGs and review statuses for the filter dropdowns
        $reviewStatuses = ReviewStatus::all();
        $researchCategories = Researchcategory::all();
        $sdgs = SDG::all();
        $researchStatuses = ProjectResearchStatus::all(); 
    
        // Return the view with the filtered results and necessary data for the filter dropdowns
        return view('contributor.research_extension.index', [
            'researches' => $researches,
            'reviewStatuses' => $reviewStatuses,
            'researchCategories' => $researchCategories,
            'sdgs' => $sdgs,
            'researchStatuses'=>$researchStatuses
        ]);
    }
    


    public function show_request_changes($id, Request $request)
    {
        // Fetch the research by its ID, including feedbacks and user information
        $research = Research::with(['feedbacks.user', 'sdgSubCategories'])->findOrFail($id);
    
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
    
        // Pass the research and notification data to the view
        return view('contributor.feedbacks.research_extension', compact('research', 'notificationData'));
    }
    

    public function show_rejected($id, Request $request)
    {
        // Fetch the research by its ID, including feedbacks and user information
        $research = Research::with(['feedbacks.user', 'sdgSubCategories'])->findOrFail($id);
    
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
    
        // Pass the research and notification data to the view
        return view('contributor.feedbacks.rejected_research_extension', compact('research', 'notificationData'));
    }
    

        public function request_changes(Request $request)
    {
        $user = Auth::user();

        // Initialize the query with existing constraints
        $query = Research::with(['researchfiles', 'sdg'])
            ->where('user_id', $user->id) // Filter by the authenticated user ID
            ->where('is_publish', 0) // Only include unpublished entries
            ->where('review_status_id', 1); // Include only entries with review status '1'

        // Apply filters based on request parameters

        // Filter by title if present
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        // Filter by research category if present
        if ($request->filled('researchcategory_id')) {
            $query->where('researchcategory_id', $request->researchcategory_id);
        }

        // Filter by research status if present
        if ($request->filled('research_status')) {
            $query->where('research_status', $request->research_status);
        }

        // Apply SDG filter if present
        if ($request->filled('sdg')) {
            $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
            $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
            });
        }

        // Fetch the filtered list of researches and paginate the results
        $researches = $query->orderBy('id', 'desc')->paginate(5);

        // Fetch all SDGs and research categories for the filter dropdowns
        $researchCategories = Researchcategory::all();
        $sdgs = SDG::all();

        // Return the view with the filtered results and necessary data for the filter dropdowns
        return view('contributor.research_extension.request_changes', [
            'researches' => $researches,
            'researchCategories' => $researchCategories,
            'sdgs' => $sdgs,
        ]);
    }


    public function rejected(Request $request)
    {
        $user = Auth::user();
    
        // Initialize the query with existing constraints
        $query = Research::with(['researchfiles', 'sdg'])
            ->where('user_id', $user->id) // Filter by the authenticated user ID
            ->where('is_publish', 0) // Only include unpublished entries
            ->where('review_status_id', 2); // Include only entries with review status '2' (Rejected)
    
        // Apply filters based on request parameters
    
        // Filter by title if present
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }
    
        // Filter by research category if present
        if ($request->filled('researchcategory_id')) {
            $query->where('researchcategory_id', $request->researchcategory_id);
        }
    
        // Filter by research status if present
        if ($request->filled('research_status')) {
            $query->where('research_status', $request->research_status);
        }
    
        // Apply SDG filter if present
        if ($request->filled('sdg')) {
            $selectedSDGs = $request->sdg; // Get selected SDG IDs from the request
            $query->whereHas('sdg', function ($sdgQuery) use ($selectedSDGs) {
                $sdgQuery->whereIn('sdgs.id', $selectedSDGs); // Specify table name for the `id`
            });
        }
    
        // Fetch the filtered list of researches and paginate the results
        $researches = $query->orderBy('id', 'desc')->paginate(5);
    
        // Fetch all SDGs and research categories for the filter dropdowns
        $researchCategories = Researchcategory::all();
        $sdgs = SDG::all();
    
        // Return the view with the filtered results and necessary data for the filter dropdowns
        return view('contributor.research_extension.rejected', [
            'researches' => $researches,
            'researchCategories' => $researchCategories,
            'sdgs' => $sdgs,
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $researchcategories = Researchcategory::all();
        $sdgs = Sdg::all();
        $projectResearchStatuses = ProjectResearchStatus::all(); // Fetch all project research statuses
        return view('contributor.research_extension.create', compact('researchcategories', 'sdgs', 'projectResearchStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResearchRequest $request)
    {
        $user = Auth::user();
        $sdgIds = $request->sdg;
        
        // Remove duplicate sdg_sub_category IDs by converting to unique array
        $sdgSubCategoryIds = $request->has('sdg_sub_category') 
            ? array_unique($request->sdg_sub_category) 
            : [];
        
        // Initialize gender impact data
        $genderImpactData = [
            'benefits_men' => false,
            'benefits_women' => false,
            'benefits_all' => true, // Default to benefiting all if not specified
            'addresses_gender_inequality' => false,
            'men_count' => null,
            'women_count' => null,
            'gender_notes' => null,
            'target_beneficiaries' => $request->target_beneficiaries,
        ];
        
        try {
            DB::beginTransaction();
            
            // AI-based SDG detection if a PDF file is uploaded
            $aiDetectionUsed = false;
            $genderAnalysisUsed = false;
            
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = strtolower($file->getClientOriginalExtension());
                
                // Verify the file is a PDF
                if ($extension !== 'pdf') {
                    // Log non-PDF file attempt
                    Log::warning('Non-PDF file uploaded for AI analysis', [
                        'file_type' => $file->getMimeType(),
                        'file_extension' => $extension,
                        'user_id' => $user->id
                    ]);
                } else {
                    // Proceed with AI analysis for PDF files
                    $aiResults = $this->sdgAiService->analyzeResearchFile($file);
                    
                    if ($aiResults) {
                        $aiDetectionUsed = true;
                        $detectedIds = $this->sdgAiService->convertResultsToIds($aiResults);
                        
                        // Use AI-detected SDGs if available, otherwise use manual selection
                        if (!empty($detectedIds['sdgIds'])) {
                            $sdgIds = $detectedIds['sdgIds'];
                        }
                        
                        // Use AI-detected subcategories if available, otherwise use manual selection
                        if (!empty($detectedIds['subCategoryIds'])) {
                            // Ensure unique subcategory IDs
                            $sdgSubCategoryIds = array_unique($detectedIds['subCategoryIds']);
                        }
                        
                        // Log successful AI detection
                        Log::info('AI detection used for research submission', [
                            'user_id' => $user->id,
                            'sdg_count' => count($detectedIds['sdgIds']),
                            'subcategory_count' => count($detectedIds['subCategoryIds'])
                        ]);
                    } else {
                        Log::error('AI detection failed for PDF', [
                            'file_name' => $file->getClientOriginalName(),
                            'user_id' => $user->id
                        ]);
                    }
                    
                    // Now perform gender impact analysis
                    try {
                        $genderResults = $this->genderAnalysisService->analyzeGenderImpacts(
                            $file, 
                            $request->target_beneficiaries
                        );
                        
                        if ($genderResults) {
                            $genderAnalysisUsed = true;
                            $genderImpactData = [
                                'benefits_men' => $genderResults['benefits_men'],
                                'benefits_women' => $genderResults['benefits_women'],
                                'benefits_all' => $genderResults['benefits_all'],
                                'addresses_gender_inequality' => $genderResults['addresses_gender_inequality'],
                                'men_count' => $genderResults['men_count'],
                                'women_count' => $genderResults['women_count'],
                                'gender_notes' => $genderResults['gender_notes'],
                                'target_beneficiaries' => $request->target_beneficiaries,
                            ];
                            
                            Log::info('Gender analysis used for research submission', [
                                'user_id' => $user->id,
                                'benefits_women' => $genderResults['benefits_women'],
                                'benefits_men' => $genderResults['benefits_men'],
                                'addresses_inequality' => $genderResults['addresses_gender_inequality']
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Gender analysis failed', [
                            'file_name' => $file->getClientOriginalName(),
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Create the Research record
            $research = Research::create([
                'title' => $request->title,
                'description' => $request->description,
                'status_id' => $request->status_id,
                'is_publish' => 0, // 0 indicates draft
                'user_id' => $user->id, // Associate research with the current user
                'researchcategory_id' => $request->researchcategory_id, // Research Category
                'review_status_id'=> 4,
                'file_link' => $request->file_link,
                'target_beneficiaries' => $request->target_beneficiaries
            ]);

            // Create the GenderImpact record
            $research->genderImpact()->create($genderImpactData);

            // Attach SDGs to the research
            $research->sdg()->attach($sdgIds);
            
            // Attach selected sub-categories to the research
            if (!empty($sdgSubCategoryIds)) {
                $research->sdgSubCategories()->attach($sdgSubCategoryIds);
            }

            $sdgs = $research->sdg()->pluck('name')->implode(', ');
            $publishStatus = $research->is_publish == 1 ? 'Published' : 'Draft';

            RoleAction::create([
                'content_id' => $research->id,
                'content_type' => Research::class,
                'user_id' => $user->id,
                'role' => 'contributor',
                'action' => 'submitted for review',
                'created_at' => now()
            ]);

            $type = 'research';
            $status = 'submitted for review';
            $researchTitle = $research->title;

            // Retrieve all reviewers
            $reviewers = User::where('role', 'reviewer')->get();

            // Create notifications for each reviewer
            foreach ($reviewers as $reviewer) {
                Notification::create([
                    'user_id' => $reviewer->id,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $reviewer->id,
                    'type' => $type,
                    'related_type' => Research::class,
                    'related_id' => $research->id,
                    'data' => json_encode([
                        'message' => "The research titled '" . addslashes($researchTitle) . "' has been submitted for review.",
                        'contributor' => $user->first_name . ' ' . $user->last_name,
                        'role' => 'contributor',
                        'type' => $type,
                        'status' => $status,
                    ]),
                    'created_at' => now(),
                ]);
            }

            // Handle single file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileData = file_get_contents($file); // Read file as binary data
                $originalFilename = $file->getClientOriginalName(); // Original filename with extension
                $extension = $file->getClientOriginalExtension(); // File extension (e.g., pdf or docx)

                Researchfile::create([
                    'research_id' => $research->id,
                    'file' => $fileData,               // Store binary data
                    'original_filename' => $originalFilename, // Store original filename
                    'extension' => $extension,         // Store file extension 
                    ]);
            }

            // Add Activity Log entry
            ActivityLog::create([
                'log_name' => 'Research Submission',
                'description' => "Research titled '" . addslashes($research->title) . "' submitted for review by " . $user->first_name . ' ' . $user->last_name,
                'subject_type' => Research::class,
                'subject_id' => $research->id,
                'event' => 'submitted for review',
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'properties' => json_encode([
                    'research_title' => $research->title,
                    'description' => $research->description,
                    'status_id' => $research->status_id,
                    'research_category' => $research->researchcategory_id,
                    'sdgs' => $sdgs,
                    'status' => $publishStatus,
                    'ai_detected' => $aiDetectionUsed,
                    'gender_analysis' => $genderAnalysisUsed,
                    'target_beneficiaries' => $request->target_beneficiaries,
                ]),
                'created_at' => now(),
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            dd($ex->getMessage());
        }

        session()->flash('alert-success', 'Research Submitted Successfully!');
        return to_route('contributor.research.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        // Find the research by its ID, including related data
        $research = Research::with(['researchcategory', 'sdg', 'user', 'reviewStatus','sdgSubCategories'])->findOrFail($id);
    
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
    
        // Return the view for showing the research details along with notification data
        return view('contributor.research_extension.show', compact('research', 'notificationData'));
    }
    


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Research $research)
    {
        $sdgs = Sdg::all();
        $researchcategories = Researchcategory::all(); // Fetch all research categories
        $projectResearchStatuses = ProjectResearchStatus::all(); // Fetch all project research statuses
        // Load the selected SDG sub-categories
        $selectedSubCategories = $research->sdgSubCategories()->pluck('sdg_sub_categories.id')->toArray();
        return view('contributor.research_extension.edit', compact('research', 'sdgs', 'researchcategories', 'projectResearchStatuses','selectedSubCategories'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Research $research)
    {
        $request->validate([
            'title' => ['required', 'min:2', 'max:255'],
            'sdg' => ['required'],
            'status_id' => ['required', 'exists:project_research_statuses,id'], // Update validation rule
            'file.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx'],
            'description' => ['required', 'min:10'],
            'review_status_id' => ['nullable'],
            'file_link' => ['nullable', 'url'], // Validate file_link (URL validation)
            'is_publish' => ['nullable'],
            'review_feedback' => ['nullable', 'string'],
            'target_beneficiaries' => ['nullable', 'string', 'max:500'],
        ]);
    
        $user = Auth::user();
        $sdgIds = $request->sdg;
        
        // Remove duplicate sdg_sub_category IDs by converting to unique array
        $sdgSubCategoryIds = $request->has('sdg_sub_category') 
            ? array_unique($request->sdg_sub_category) 
            : [];
        
        // Initialize gender impact data
        $genderImpactData = [
            'benefits_men' => false,
            'benefits_women' => false,
            'benefits_all' => true, // Default to benefiting all if not specified
            'addresses_gender_inequality' => false,
            'men_count' => null,
            'women_count' => null,
            'gender_notes' => null,
            'target_beneficiaries' => $request->target_beneficiaries,
        ];
    
        try {
            DB::beginTransaction();
            
            // AI-based SDG detection if a file is uploaded
            $aiDetectionUsed = false;
            $genderAnalysisUsed = false;
            
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = strtolower($file->getClientOriginalExtension());
                
                // For PDF files, perform AI analysis
                if ($extension === 'pdf') {
                    $aiResults = $this->sdgAiService->analyzeResearchFile($file);
                
                if ($aiResults) {
                        $aiDetectionUsed = true;
                    $detectedIds = $this->sdgAiService->convertResultsToIds($aiResults);
                    
                    // Use AI-detected SDGs if available, otherwise use manual selection
                    if (!empty($detectedIds['sdgIds'])) {
                        $sdgIds = $detectedIds['sdgIds'];
                    }
                    
                    // Use AI-detected subcategories if available, otherwise use manual selection
                    if (!empty($detectedIds['subCategoryIds'])) {
                        // Ensure unique subcategory IDs
                        $sdgSubCategoryIds = array_unique($detectedIds['subCategoryIds']);
                        }
                    }
                    
                    // Perform gender impact analysis
                    try {
                        $genderResults = $this->genderAnalysisService->analyzeGenderImpacts(
                            $file, 
                            $request->target_beneficiaries
                        );
                        
                        if ($genderResults) {
                            $genderAnalysisUsed = true;
                            $genderImpactData = [
                                'benefits_men' => $genderResults['benefits_men'],
                                'benefits_women' => $genderResults['benefits_women'],
                                'benefits_all' => $genderResults['benefits_all'],
                                'addresses_gender_inequality' => $genderResults['addresses_gender_inequality'],
                                'men_count' => $genderResults['men_count'],
                                'women_count' => $genderResults['women_count'],
                                'gender_notes' => $genderResults['gender_notes'],
                                'target_beneficiaries' => $request->target_beneficiaries,
                            ];
                            
                            Log::info('Gender analysis used for research update', [
                                'user_id' => $user->id,
                                'research_id' => $research->id,
                                'benefits_women' => $genderResults['benefits_women'],
                                'benefits_men' => $genderResults['benefits_men'],
                                'addresses_inequality' => $genderResults['addresses_gender_inequality']
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Gender analysis failed during update', [
                            'file_name' => $file->getClientOriginalName(),
                            'user_id' => $user->id,
                            'research_id' => $research->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
    
            // Update research details
            $research->update([
                'title' => $request->title,
                'description' => $request->description,
                'status_id' => $request->status_id, // Update to use status_id
                'user_id' => $user->id,
                'review_status_id' => 4,
                'is_publish' => 0,
                'file_link' => $request->file_link, // Store file link
                'target_beneficiaries' => $request->target_beneficiaries
            ]);
            
            // Update or create gender impact assessment
            if ($research->genderImpact) {
                $research->genderImpact->update($genderImpactData);
            } else {
                $research->genderImpact()->create($genderImpactData);
            }
    
            // Update SDGs
            $research->sdg()->sync($sdgIds);
            $sdgs = $research->sdg()->pluck('name')->implode(', ');
            
            // Update subcategories
            if (!empty($sdgSubCategoryIds)) {
                $research->sdgSubCategories()->sync($sdgSubCategoryIds);
            } else {
                $research->sdgSubCategories()->detach(); // Detach if no sub-categories are selected
            }
            
            // Handle single file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file'); // Get the uploaded file
                $fileData = file_get_contents($file); // Read file as binary data
                $originalFilename = $file->getClientOriginalName(); // Get the original filename
                $extension = $file->getClientOriginalExtension(); // Get the file extension
    
                // Check if a file already exists for this research
                $existingFile = Researchfile::where('research_id', $research->id)->first();
    
                if ($existingFile) {
                    // If a file exists, delete the old file
                    $existingFile->delete();
                }
    
                // Create the record in the Researchfile table with the new file
                Researchfile::create([
                    'research_id' => $research->id, // Associate the file with the research
                    'file' => $fileData, // Store the file as binary data
                    'original_filename' => $originalFilename, // Store the original filename
                    'extension' => $extension, // Store the file extension
                ]);
            }
    
            RoleAction::create([
                'content_id' => $research->id,
                'content_type' => Research::class,
                'user_id' => $user->id,
                'role' => 'contributor',
                'action' => 'submitted for review',
                'created_at' => now()
            ]);
    
            $type = 'research';
            $status = 'resubmitted for review';
            $researchTitle = $research->title;
    
            // Retrieve all reviewers
            $reviewers = User::where('role', 'reviewer')->get();
    
            // Create notifications for each reviewer
            foreach ($reviewers as $reviewer) {
                Notification::create([
                    'user_id' => $reviewer->id,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $reviewer->id,
                    'type' => $type,
                    'related_type' => Research::class,
                    'related_id' => $research->id,
                    'data' => json_encode([
                        'message' => "The research titled '" . addslashes($researchTitle) . "' has been resubmitted for review.",
                        'contributor' => $user->first_name . ' ' . $user->last_name,
                        'role' => 'contributor',
                        'type' => $type,
                        'status' => $status,
                    ]),
                    'created_at' => now(),
                ]);
            }
    
            // Log the activity for the research update
            $properties = [
                'research_id' => $research->id,
                'title' => $research->title,
                'description' => $research->description,
                'status_id' => $research->status_id, // Update to log status_id
                'sdgs' => $sdgs,
                'publish_status' => $research->is_publish ? 'Published' : 'Draft',
                'updated_by' => [
                    'user_id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'role' => $user->role,
                ],
                'timestamp' => now(),
                'ai_detected' => $aiDetectionUsed,
                'gender_analysis' => $genderAnalysisUsed,
                'target_beneficiaries' => $request->target_beneficiaries,
            ];
    
            ActivityLog::create([
                'log_name' => 'Research Resubmission',
                'description' => 'Research titled "' . addslashes($research->title) . '" resubmitted for review by contributor',
                'subject_type' => Research::class,
                'subject_id' => $research->id,
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
    
        session()->flash('alert-success', 'Research Updated Successfully!');
        return to_route('contributor.research.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Analyze gender impacts from uploaded file
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeGenderImpacts(Request $request)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx|max:10240', // Max 10MB file
            'target_beneficiaries' => 'nullable|string|max:500',
        ]);
        
        // Extract gender impacts from the file
        try {
            $file = $request->file('file');
            $genderResults = $this->genderAnalysisService->analyzeGenderImpacts(
                $file, 
                $request->target_beneficiaries
            );
            
            return response()->json([
                'success' => true,
                'data' => $genderResults
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to analyze gender impacts', [
                'error' => $e->getMessage(),
                'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'none'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing gender impacts: ' . $e->getMessage()
            ], 500);
        }
    }
}
