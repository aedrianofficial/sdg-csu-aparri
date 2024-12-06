<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use App\Models\RoleAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Analytics extends Controller
{
    public function getSdgLineChartData(Request $request)
    {
        try {
            // Get the year from the request, or default to current year
            $year = $request->get('year', now()->year);
    
            // Months labels
            $months = [
                'January', 'February', 'March', 'April', 'May', 
                'June', 'July', 'August', 'September', 'October', 'November', 'December'
            ];
    
            // Initialize arrays to hold data
            $reportsData = [];
            $projectsData = [];
            $researchData = [];
    
            foreach (range(1, 12) as $month) {
                // Count published reports, projects, and research for each month
                $reportsCount = Report::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('is_publish', 1)
                    ->count();
    
                $projectsCount = Project::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('is_publish', Project::Published)
                    ->count();
    
                $researchCount = Research::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('is_publish', 1)
                    ->count();
    
                // Append counts to the respective arrays
                $reportsData[] = $reportsCount;
                $projectsData[] = $projectsCount;
                $researchData[] = $researchCount;
            }
    
            return response()->json([
                'months' => $months,
                'reportsData' => $reportsData,
                'projectsData' => $projectsData,
                'researchData' => $researchData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching line chart data: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch data'], 500);
        }
    }
    


    public function reviewStatusAnalytics()
    {
        $totalProjects = Project::count();
        $totalReports = Report::count();
        $totalResearch = Research::count();
    
        // Fetch and count review statuses for Projects
        $projectStatusCounts = Project::select('review_status_id', DB::raw('count(*) as count'))
            ->groupBy('review_status_id')
            ->get()
            ->pluck('count', 'review_status_id');
        
        // Fetch and count review statuses for Reports
        $reportStatusCounts = Report::select('review_status_id', DB::raw('count(*) as count'))
            ->groupBy('review_status_id')
            ->get()
            ->pluck('count', 'review_status_id');
        
        // Fetch and count review statuses for Research
        $researchStatusCounts = Research::select('review_status_id', DB::raw('count(*) as count'))
            ->groupBy('review_status_id')
            ->get()
            ->pluck('count', 'review_status_id');
        
        // Status mapping
        $statuses = [
            1 => 'Needs Changes',
            2 => 'Rejected',
            3 => 'Published',
            4 => 'Pending Review',
            5 => 'Pending Approval',
            6 => 'Pending Publishing'
        ];

        // Return the counts and statuses as JSON for the charts
        return response()->json([
            'projectStatusCounts' => $projectStatusCounts,
            'reportStatusCounts' => $reportStatusCounts,
            'researchStatusCounts' => $researchStatusCounts,
            'statuses' => $statuses,
            'totalProjects' =>$totalProjects,
            'totalReports' =>$totalReports,
            'totalResearch'=>$totalResearch
        
        ]);
        
    }
    public function myStatusAnalytics()
{
    // Fetch and count statuses for Projects
    $projectStatusCounts = Project::select('review_status_id', DB::raw('count(*) as count'))
        ->where('user_id', auth()->id()) // Filter by the authenticated user
        ->groupBy('review_status_id')
        ->get()
        ->pluck('count', 'review_status_id');

    // Fetch and count statuses for Reports
    $reportStatusCounts = Report::select('review_status_id', DB::raw('count(*) as count'))
        ->where('user_id', auth()->id()) // Filter by the authenticated user
        ->groupBy('review_status_id')
        ->get()
        ->pluck('count', 'review_status_id');

    // Fetch and count statuses for Research
    $researchStatusCounts = Research::select('review_status_id', DB::raw('count(*) as count'))
        ->where('user_id', auth()->id()) // Filter by the authenticated user
        ->groupBy('review_status_id')
        ->get()
        ->pluck('count', 'review_status_id');


         // Get total counts for Projects, Reports, and Research
    $myTotalProjects = Project::where('user_id', auth()->id())->count();
    $myTotalReports = Report::where('user_id', auth()->id())->count();
    $myTotalResearch = Research::where('user_id', auth()->id())->count();

    // Status mapping
    $myStatuses = [
        1 => 'Needs Changes',
        2 => 'Rejected',
        3 => 'Published',
        4 => 'Pending Review',
        5 => 'Pending Approval',
        6 => 'Pending Publishing'
    ];

    // Prepare final counts including status names
    $myProjectStatus = [];
    foreach ($myStatuses as $id => $name) {
        $myProjectStatus[$name] = $projectStatusCounts->get($id, 0); // Default to 0 if not found
    }

    $myReportStatus = [];
    foreach ($myStatuses as $id => $name) {
        $myReportStatus[$name] = $reportStatusCounts->get($id, 0); // Default to 0 if not found
    }

    $myResearchStatus = [];
    foreach ($myStatuses as $id => $name) {
        $myResearchStatus[$name] = $researchStatusCounts->get($id, 0); // Default to 0 if not found
    }

    // Return the counts and myStatuses as JSON for the charts
    return response()->json([
        'myProjectStatusCounts' => $myProjectStatus,
        'myReportStatusCounts' => $myReportStatus,
        'myResearchStatusCounts' => $myResearchStatus,
        'myStatuses' => $myStatuses,
        'myTotalProjects'=>$myTotalProjects,
        'myTotalReports' =>$myTotalReports,
        'myTotalResearch' =>$myTotalResearch
    ]);
}
public function myReviewActivity()
{
    $userId = Auth::id();

    // Count the actions for each type and status for the logged-in reviewer
    $reviewedCounts = RoleAction::where('user_id', $userId)
        ->whereIn('action', ['reviewed', 'rejected', 'requested change'])
        ->selectRaw('content_type, action, COUNT(*) as count')
        ->groupBy('content_type', 'action')
        ->get()
        ->groupBy('content_type')
        ->map(function ($actions) {
            return $actions->pluck('count', 'action');
        });

    // Prepare the response with zero counts for missing statuses
    $response = [
        'projects' => [
            'reviewed' => $reviewedCounts->get(Project::class)['reviewed'] ?? 0,
            'rejected' => $reviewedCounts->get(Project::class)['rejected'] ?? 0,
            'requested_change' => $reviewedCounts->get(Project::class)['requested change'] ?? 0,
        ],
        'reports' => [
            'reviewed' => $reviewedCounts->get(Report::class)['reviewed'] ?? 0,
            'rejected' => $reviewedCounts->get(Report::class)['rejected'] ?? 0,
            'requested_change' => $reviewedCounts->get(Report::class)['requested change'] ?? 0,
        ],
        'research' => [
            'reviewed' => $reviewedCounts->get(Research::class)['reviewed'] ?? 0,
            'rejected' => $reviewedCounts->get(Research::class)['rejected'] ?? 0,
            'requested_change' => $reviewedCounts->get(Research::class)['requested change'] ?? 0,
        ],
    ];

    // Return data as JSON for AJAX requests
    return response()->json($response);
}
public function myApprovalActivity()
{
    $userId = Auth::id();

    // Count the actions for each type and status for the logged-in approver
    $approvedCounts = RoleAction::where('user_id', $userId)
        ->whereIn('action', ['approved', 'rejected'])
        ->selectRaw('content_type, action, COUNT(*) as count')
        ->groupBy('content_type', 'action')
        ->get()
        ->groupBy('content_type')
        ->map(function ($actions) {
            return $actions->pluck('count', 'action');
        });

    // Prepare the response with zero counts for missing statuses
    $response = [
        'projects' => [
            'approved' => $approvedCounts->get(Project::class)['approved'] ?? 0,
            'rejected' => $approvedCounts->get(Project::class)['rejected'] ?? 0,
        ],
        'reports' => [
            'approved' => $approvedCounts->get(Report::class)['approved'] ?? 0,
            'rejected' => $approvedCounts->get(Report::class)['rejected'] ?? 0,
        ],
        'research' => [
            'approved' => $approvedCounts->get(Research::class)['approved'] ?? 0,
            'rejected' => $approvedCounts->get(Research::class)['rejected'] ?? 0,
        ],
    ];

    // Return data as JSON for AJAX requests
    return response()->json($response);
}

public function myPublishActivity() 
{
    $userId = Auth::id();

    // Count the actions for each type and status for the logged-in publisher
    $publishedCounts = RoleAction::where('user_id', $userId)
        ->where('action', 'published')
        ->selectRaw('content_type, COUNT(*) as count')
        ->groupBy('content_type')
        ->get()
        ->groupBy('content_type')
        ->map(function ($actions) {
            return $actions->pluck('count');
        });

    // Prepare the response with zero counts for missing statuses
    $response = [
        'projects' => [
            'published' => $publishedCounts->get(Project::class)->first() ?? 0,
        ],
        'reports' => [
            'published' => $publishedCounts->get(Report::class)->first() ?? 0,
        ],
        'research' => [
            'published' => $publishedCounts->get(Research::class)->first() ?? 0,
        ],
    ];

    // Return data as JSON for AJAX requests
    return response()->json($response);
}

public function getSdgComparisonData()
{
    try {
        // Define SDG labels for easy reference
        $sdgs = [
            1 => '01 - No Poverty',
            2 => '02 - Zero Hunger',
            3 => '03 - Good Health & Well-Being',
            4 => '04 - Quality Education',
            5 => '05 - Gender Equality',
            6 => '06 - Clean Water & Sanitation',
            7 => '07 - Affordable & Clean Energy',
            8 => '08 - Decent Work & Economic Growth',
            9 => '09 - Industry, Innovation & Infrastructure',
            10 => '10 - Reduced Inequalities',
            11 => '11 - Sustainable Cities & Communities',
            12 => '12 - Responsible Consumption & Production',
            13 => '13 - Climate Action',
            14 => '14 - Life Below Water',
            15 => '15 - Life on Land',
            16 => '16 - Peace, Justice & Strong Institutions',
            17 => '17 - Partnerships for the Goals'
        ];

        // Initialize arrays to store the count of each component by SDG
        $projectData = [];
        $researchData = [];
        $reportData = [];

        // Fetch data for projects, research, and reports (only published ones)
        foreach ($sdgs as $id => $label) {
            // Log the current SDG being processed
            Log::info("Processing SDG: $label (ID: $id)");

            // Count projects, research, and reports related to the SDG
            $projectCount = Project::where('is_publish', Project::Published)
                ->whereHas('sdg', function ($query) use ($id) {
                    $query->where('sdgs.id', $id);
                })->count();

            $researchCount = Research::where('is_publish', 1)
                ->whereHas('sdg', function ($query) use ($id) {
                    $query->where('sdgs.id', $id);
                })->count();

            $reportCount = Report::where('is_publish', 1)
                ->whereHas('sdg', function ($query) use ($id) {
                    $query->where('sdgs.id', $id);
                })->count();

            // Append counts to arrays
            $projectData[] = $projectCount;
            $researchData[] = $researchCount;
            $reportData[] = $reportCount;

            // Log the count results
            Log::info("Counts for SDG $label: Projects - $projectCount, Research - $researchCount, Reports - $reportCount");
        }

        // Return data in JSON format for the AJAX request
        return response()->json([
            'sdgLabels' => array_values($sdgs),
            'projectData' => $projectData,
            'researchData' => $researchData,
            'reportData' => $reportData
        ]);
    } catch (\Exception $e) {
        // Log error details for debugging
        Log::error('Error fetching SDG comparison data: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch data'], 500);
    }
}

}
