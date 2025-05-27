<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Research\ResearchRequest;
use App\Models\ActivityLog;
use App\Models\Feedback;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function downloadFile($id)
    {
        $file = Researchfile::findOrFail($id);
    
        // Determine the file name and MIME type
        $filename = $file->file_name ?? 'research_document'; // Default filename if not stored
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
        return response($file->file)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    

    public function rejected($id, Request $request)
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
        return view('auth.feedbacks.rejected_research_extension', compact('research', 'notificationData'));
    }

    public function need_changes($id, Request $request)
    {
        // Fetch the research by its ID, including feedbacks, user information, and SDG subcategories
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
        return view('auth.feedbacks.research_extension', compact('research', 'notificationData'));
    }

    public function my_research(Request $request)
    {
        $user = Auth::user();
    
        // Initialize the query for Research model
        $query = Research::query()
            ->with(['researchfiles', 'sdg']) // Load relationships
            ->where('user_id', $user->id); // Filter by current user
    
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
    
        // Handle sorting dynamically
        $sortBy = $request->get('sort_by', 'id'); // Default sort column
        $sortOrder = $request->get('sort_order', 'desc'); // Default sort order
    
        // Define allowed sortable columns
        $sortableColumns = ['title', 'researchcategory_id', 'review_status_id', 'status_id', 'created_at', 'id'];
    
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        }
    
        // Fetch the filtered list of researches and paginate the results
        $researches = $query->paginate(5);
    
        // Fetch all SDGs and review statuses for the filter dropdowns
        $reviewStatuses = ReviewStatus::all();
        $researchCategories = Researchcategory::all();
        $researchStatuses = ProjectResearchStatus::all(); 
        $sdgs = SDG::all();
    
        // Return the view with the filtered researches and filter options
        return view('auth.research.my_research', [
            'researches' => $researches,
            'researchCategories' => $researchCategories,
            'reviewStatuses' => $reviewStatuses,
            'sdgs' => $sdgs,
            'researchStatuses'=>$researchStatuses
        ]);
    }
    

    public function index(Request $request)
    {
        // Initialize the query with eager loading for related models
        $query = Research::with(['researchfiles', 'sdg']);

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

        // Sorting
        $sortBy = $request->get('sort_by', 'id'); // Default sort column
        $sortOrder = $request->get('sort_order', 'desc'); // Default sort order

        // Define allowed sortable columns
        $sortableColumns = ['title', 'researchcategory_id', 'review_status_id', 'status_id', 'created_at'];
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Fetch the filtered list of researches and paginate the results
        $researches = $query->orderBy('id', 'desc')->paginate(5);

        // Fetch all SDGs and review statuses for the filter dropdowns
        $reviewStatuses = ReviewStatus::all();
        $researchStatuses = ProjectResearchStatus::all(); 
        $researchCategories = Researchcategory::all();
        $sdgs = SDG::all();

        // Return view with the filtered results and necessary filter data
        return view('auth.research.index', compact('researches', 'researchStatuses','reviewStatuses', 'researchCategories', 'sdgs'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $researchcategories = Researchcategory::all();
        $sdgs = Sdg::all();
        $statuses = ProjectResearchStatus::where('is_active', 1)->get(); // Fetch active statuses
    
        return view('auth.research.create', compact('researchcategories', 'sdgs', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResearchRequest $request)
{
    $user = Auth::user();
    $submitType = $request->submit_type;

    try {
        DB::beginTransaction();

        // Create the Research record
        $research = Research::create([
            'title' => $request->title,
            'description' => $request->description,
            'status_id' => $request->status_id, // Update to use status_id
            'is_publish' => $submitType === 'publish' ? 1 : 0,
            'user_id' => $user->id,
            'researchcategory_id' => $request->researchcategory_id,
            'review_status_id' => $submitType === 'publish' ? 3 : 4,
            'file_link' => $request->file_link
        ]);
 if ($request->has('gender_benefits_men') || $request->has('gender_benefits_women')) {
                $genderImpact = new GenderImpact([
                    'research_id' => $research->id,
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
        // Attach SDGs to the research
        $research->sdg()->attach($request->sdg);
        $sdgs = $research->sdg()->pluck('name')->implode(', ');
        // Attach selected sub-categories to the research
        if ($request->has('sdg_sub_category')) {
            $research->sdgSubCategories()->attach($request->sdg_sub_category);
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

        // Log role action
        RoleAction::create([
            'content_id' => $research->id,
            'content_type' => Research::class,
            'user_id' => $user->id,
            'role' => 'admin',
            'action' => $submitType === 'publish' ? 'approved' : 'submitted for review',
            'created_at' => now()
        ]);

        // Log the activity for publishing or submission
        if ($submitType === 'publish') {
            ActivityLog::create([
                'log_name' => 'Research Approved',
                'description' => 'Approved the research titled "' . addslashes($research->title) . '"',
                'subject_type' => Research::class,
                'subject_id' => $research->id,
                'event' => 'approved',
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'properties' => json_encode([
                    'research_title' => $research->title,
                    'research_status' => 'approved',
                    'role' => 'approver',
                ]),
                'created_at' => now(),
            ]);
        } else if ($submitType === 'review') {
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
                    'research_status' => 'draft',
                    'sdgs' => $sdgs,
                    'researchcategory_id' => $research->researchcategory_id,
                ]),
                'created_at' => now(),
            ]);

            // Notification creation for reviewers when research is submitted for review
            $reviewers = User::where('role', 'reviewer')->get();
            foreach ($reviewers as $reviewer) {
                Notification::create([
                    'user_id' => $reviewer->id,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $reviewer->id,
                    'type' => 'research',
                    'related_type' => Research::class,
                    'related_id' => $research->id,
                    'data' => json_encode([
                        'message' => "A new research titled '" . addslashes($research->title) . "' has been submitted for review.",
                        'contributor' => $user->first_name . ' ' . $user->last_name,
                        'role' => ['admin', 'contributor'],
                        'type' => 'research',
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

    session()->flash('alert-success', 'Research Submitted Successfully!');
    return to_route('research.index');
}
    

    

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        // Find the research by its ID, including related categories, SDGs, user, review status, and SDG subcategories
        $research = Research::with(['researchcategory', 'sdg', 'user', 'reviewStatus', 'sdgSubCategories'])->findOrFail($id);
    
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
    
        // Return the view for showing the research details, including notification data
        return view('auth.research.show', compact('research', 'notificationData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Research $research)
    {
        $sdgs = Sdg:: all();
        $reviewStatuses = ReviewStatus::all();
        $researchcategories = Researchcategory::all(); // Fetch all research categories
        $projectResearchStatuses = ProjectResearchStatus::all(); // Fetch all project research statuses
    
        // Load the selected SDG sub-categories
        $selectedSubCategories = $research->sdgSubCategories()->pluck('sdg_sub_categories.id')->toArray();
    
        return view('auth.research.edit', [
            'research' => $research,
            'sdgs' => $sdgs,
            'researchcategories' => $researchcategories,
            'reviewStatuses' => $reviewStatuses,
            'projectResearchStatuses' => $projectResearchStatuses, // Pass statuses to the view
            'selectedSubCategories' => $selectedSubCategories // Pass selected sub-categories
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Research $research)
    {
        // Validate request input
        $request->validate([
            'title' => ['required', 'min:2', 'max:255'],
            'sdg' => ['required'],
            'status_id' => ['required', 'exists:project_research_statuses,id'], // Update validation rule
            'file' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx'],
            'description' => ['required', 'min:10'],
            'review_status_id' => ['nullable', 'exists:review_statuses,id'],
            'file_link' => ['nullable', 'url'], // Validate file_link (URL validation)
            'is_publish' => ['nullable'],
            'review_feedback' => ['nullable', 'string'],
            'feedback' => ['nullable', 'string', 'max:1000'],  // New feedback validation
            'research_id' => ['required', 'exists:research,id'] // Reports ID for feedback association
        ]);
    
        $user = Auth::user();
        $originalContributor = $research->user_id;
    
        try {
            DB::beginTransaction();
    
            $is_publish = $request->review_status_id == 3 ? 1 : ($request->is_publish ?? 0);
    
            // Creating and attaching feedback
            if ($request->filled('feedback')) {
                $feedback = Feedback::firstOrCreate([
                    'feedback' => $request->feedback,
                    'users_id' => $user->id,
                ]);
                
                // Then attach to the research
                $research->feedbacks()->syncWithoutDetaching($feedback->id);                
            }

            // Update research details
            $research->update([
                'title' => $request->title,
                'description' => $request->description,
                'status_id' => $request->status_id, // Update status_id 
                'review_status_id' => $request->review_status_id ?? 4,
                'is_publish' => $is_publish,
                'file_link' => $request->file_link // Store file link
            ]);
    
            // Update SDGs
            $research->sdg()->sync($request->sdg);
            $sdgs = $research->sdg()->pluck('name')->implode(', ');
            // Attach selected sub-categories to the research
            if ($request->has('sdg_sub_category')) {
                $research->sdgSubCategories()->sync($request->sdg_sub_category);
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

            $actionMap = [
                1 => 'requested change',
                2 => 'rejected',
                3 => 'approved',
                4 => 'submitted for review',
                5 => 'reviewed',
        
            ];
            $action = $actionMap[$research->review_status_id] ?? 'updated';
    
            RoleAction::create([
                'content_id' => $research->id,
                'content_type' => Research::class,
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => $action
            ]);
    
            $researchTitle = addslashes($research->title);
    
            switch ($research->review_status_id) {
                case 1:
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'research',
                        'related_type' => Research::class,
                        'related_id' => $research->id,
                        'data' => json_encode([
                            'message' => "Your research '$researchTitle' requires changes.",
                            'reviewer' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'reviewer'],
                            'type' => 'research',
                            'status' => 'request_changes'
                        ]),
                        'created_at' => now(),
                    ]);
                    ActivityLog::create([
                        'log_name' => 'Research Needs Changes',
                        'description' => 'Requested changes for the research titled "' . addslashes($research->title) . '"',
                        'subject_type' => Research::class,
                        'subject_id' => $research->id,
                        'event' => 'requested change',
                        'causer_type' => User::class,
                        'causer _id' => auth()->user()->id,
                        'properties' => json_encode([
                            'research_title' => $research->title,
                            'review_status' => 'needs changes',
                            'role' => 'reviewer',
                        ]),
                        'created_at' => now(),
                    ]);
                    break;
    
                case 2:
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'research',
                        'related_type' => Research::class,
                        'related_id' => $research->id,
                        'data' => json_encode([
                            'message' => "Your research '$researchTitle' has been rejected.",
                            'reviewer' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'reviewer'],
                            'type' => 'research',
                            'status' => 'rejected'
                        ]),
                        'created_at' => now(),
                    ]);
                    ActivityLog::create([
                        'log_name' => 'Research Rejected',
                        'description' => 'Rejected the research titled "' . addslashes($research->title) . '"',
                        'subject_type' => Research::class,
                        'subject_id' => $research->id,
                        'event' => 'rejected',
                        'causer_type' => User::class,
                        'causer_id' => auth()->user()->id,
                        'properties' => json_encode([
                            'research_title' => $research->title,
                            'review_status' => 'rejected',
                            'role' => 'reviewer',
                        ]),
                        'created_at' => now(),
                    ]);
                    break;
    
                case 3:
                    Notification::create([
                        'user_id' => $originalContributor,
                        'notifiable_type' => User::class,
                        'notifiable_id' => $originalContributor,
                        'type' => 'research',
                        'related_type' => Research::class,
                        'related_id' => $research->id,
                        'data' => json_encode([
                            'message' => "Your research '$researchTitle' has been approved and is now published.",
                            'approver' => $user->first_name . ' ' . $user->last_name,
                            'role' => ['admin', 'approver'],
                            'type' => 'research',
                            'status' => 'approved'
                        ]),
                        'created_at' => now(),
                    ]);
                    ActivityLog::create([
                        'log_name' => 'Research Approved',
                        'description' => 'Approved the research titled "' . addslashes($research->title) . '"',
                        'subject_type' => Research::class,
                        'subject_id' => $research->id,
                        'event' => 'approved',
                        'causer_type' => User::class,
                        'causer_id' => $user->id,
                        'properties' => json_encode([
                            'research_title' => $research->title,
                            'review_status' => 'approved',
                            'role' => 'approver',
                        ]),
                        'created_at' => now(),
                    ]);
                    break;
    
                case 4:
                    $reviewers = User::where('role', 'reviewer')->get();
                    foreach ($reviewers as $reviewer) {
                        Notification::create([
                            'user_id' => $reviewer->id,
                            'notifiable_type' => User::class,
                            'notifiable_id' => $reviewer->id,
                            'type' => 'research',
                            'related_type' => Research::class,
                            'related_id' => $research->id,
                            'data' => json_encode([
                                'message' => "A new research titled '$researchTitle' has been submitted for review.",
                                'contributor' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'contributor'],
                                'type' => 'research',
                                'status' => 'submitted for review'
                            ]),
                            'created_at' => now(),
                        ]);
                    }
                    ActivityLog::create([
                        'log_name' => 'Research Resubmission',
                        'description' => 'Research titled "' . addslashes($research->title) . '" resubmitted for review',
                        'subject_type' => Research::class,
                        'subject_id' => $research->id,
                        'event' => 'resubmitted for review',
                        'causer_type' => User::class,
                        'causer_id' => auth()->user()->id,
                        'properties' => json_encode([
                            'research_title' => $research->title,
                            'review_status' => 'submitted for review' // Fix: Added missing closing quote here
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
                            'type' => 'research',
                            'related_type' => Research::class,
                            'related_id' => $research->id,
                            'data' => json_encode([
                                'message' => "Your research '$researchTitle' has been reviewed.",
                                'reviewer' => $user->first_name . ' ' . $user->last_name,
                                'role' => ['admin', 'reviewer'],
                                'type' => 'research',
                                'status' => 'reviewed'
                            ]),
                            'created_at' => now(),
                        ]);
                    
                        // Notify all approvers about the research submission for approval
                        $approvers = User::where('role', 'approver')->get();
                        foreach ($approvers as $approver) {
                            Notification::create([
                                'user_id' => $approver->id,
                                'notifiable_type' => User::class,
                                'notifiable_id' => $approver->id,
                                'type' => 'research',
                                'related_type' => Research::class,
                                'related_id' => $research->id,
                                'data' => json_encode([
                                    'message' => "The research titled '$researchTitle' has been submitted for approval.",
                                    'reviewer' => $user->first_name . ' ' . $user->last_name,
                                    'role' => ['admin', 'reviewer'],
                                    'type' => 'research',
                                    'status' => 'submitted for approval'
                                ]),
                                'created_at' => now(),
                            ]);
                        }
                        ActivityLog::create([
                            'log_name' => 'Research Reviewed',
                            'description' => 'Reviewed the research titled "' . addslashes($research->title) . '"',
                            'subject_type' => Research::class,
                            'subject_id' => $research->id,
                            'event' => 'reviewed',
                            'causer_type' => User::class,
                            'causer_id' => auth()->user()->id,
                            'properties' => json_encode([
                                'research_title' => $research->title,
                                'review_status' => 'reviewed',
                                'role' => 'reviewer',
                            ]),
                            'created_at' => now(),
                        ]);
                    break;
                    
            }
    
            DB::commit();
    
            session()->flash('alert-success', 'Research Updated Successfully!');
            return to_route('research.index');
    
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
    
    /**
     * Analyze a research for gender impact
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeGender(Request $request)
    {
        // Log the request data for debugging
        Log::info('Gender analysis request received for research', [
            'has_file' => $request->hasFile('file'), 
            'has_target_beneficiaries' => $request->has('target_beneficiaries')
        ]);
    
        try {
            // Check if we have a file to analyze
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $targetBeneficiaries = $request->input('target_beneficiaries', '');
                
                // Analyze gender impact using the GenderAnalysisService
                $genderAnalysisService = new \App\Services\GenderAnalysisService();
                $analysisResults = $genderAnalysisService->analyzeGenderImpacts($file, $targetBeneficiaries);
                
                // Return the results as JSON
                return response()->json([
                    'success' => true,
                    'data' => $analysisResults
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No file was provided for analysis'
                ], 400);
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Gender analysis error for research: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing gender impact: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Analyze text content for gender impact
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeGenderText(Request $request)
    {
        // Log the request data for debugging
        Log::info('Gender text analysis request received for research', [
            'has_title' => $request->has('title'), 
            'has_description' => $request->has('description'),
            'has_target_beneficiaries' => $request->has('target_beneficiaries')
        ]);
    
        try {
            // Get the input data directly
            $title = $request->input('title', '');
            $description = strip_tags($request->input('description', ''));
            $targetBeneficiaries = $request->input('target_beneficiaries', '');
            
            // Create a combined text for analysis
            $textToAnalyze = $title . "\n" . $description;
            
            // Analyze gender impact using the GenderAnalysisService
            $genderAnalysisService = new \App\Services\GenderAnalysisService();
            $analysisResults = $genderAnalysisService->analyzeGenderFromText(
                $textToAnalyze, 
                $targetBeneficiaries
            );
            
            // Return the results as JSON
            return response()->json([
                'success' => true,
                'data' => $analysisResults
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Gender text analysis error for research: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing gender impact: ' . $e->getMessage()
            ], 500);
        }
    }
}
