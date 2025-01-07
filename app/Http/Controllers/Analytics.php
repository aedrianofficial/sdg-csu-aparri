<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use App\Models\RoleAction;
use App\Models\StatusReport;
use App\Models\TerminalReport;
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
            $statusReportsData = [];
            $terminalReportsData = [];
            $projectsData = [];
            $researchData = [];
    
            foreach (range(1, 12) as $month) {
                // Count published status reports, terminal reports, projects, and research for each month
                $statusReportsCount = StatusReport::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('is_publish', 1)
                    ->count();
    
                $terminalReportsCount = TerminalReport::whereYear('created_at', $year)
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
                $statusReportsData[] = $statusReportsCount;
                $terminalReportsData[] = $terminalReportsCount;
                $projectsData[] = $projectsCount;
                $researchData[] = $researchCount;
            }
    
            return response()->json([
                'months' => $months,
                'statusReportsData' => $statusReportsData,
                'terminalReportsData' => $terminalReportsData,
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
        $totalResearch = Research::count();
        $totalStatusReports = StatusReport::count(); // Count of status reports
        $totalTerminalReports = TerminalReport::count(); // Count of terminal reports
    
        // Fetch and count review statuses for Projects
        $projectStatusCounts = Project::select('review_status_id', DB::raw('count(*) as count'))
            ->groupBy('review_status_id')
            ->get()
            ->pluck('count', 'review_status_id');
    
        // Fetch and count review statuses for Status Reports
        $statusReportStatusCounts = StatusReport::select('review_status_id', DB::raw('count(*) as count'))
            ->groupBy('review_status_id')
            ->get()
            ->pluck('count', 'review_status_id');
    
        // Fetch and count review statuses for Terminal Reports
        $terminalReportStatusCounts = TerminalReport::select('review_status_id', DB::raw('count(*) as count'))
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
            'statusReportStatusCounts' => $statusReportStatusCounts,
            'terminalReportStatusCounts' => $terminalReportStatusCounts,
            'researchStatusCounts' => $researchStatusCounts, // Add research status counts
            'statuses' => $statuses,
            'totalProjects' => $totalProjects,
            'totalResearch' => $totalResearch,
            'totalStatusReports' => $totalStatusReports,
            'totalTerminalReports' => $totalTerminalReports
        ]);
    }
  //controller
    public function myStatusAnalytics()
    {
        // Fetch and count statuses for Projects
        $projectStatusCounts = Project::select('review_status_id', DB::raw('count(*) as count'))
            ->where('user_id', auth()->id()) // Filter by the authenticated user
            ->groupBy('review_status_id')
            ->get()
            ->pluck('count', 'review_status_id');

        // Fetch and count statuses for Status Reports
        $statusReportStatusCounts = StatusReport::select('review_status_id', DB::raw('count(*) as count'))
            ->where('logged_by_id', auth()->id()) // Filter by the authenticated user
            ->groupBy('review_status_id')
            ->get()
            ->pluck('count', 'review_status_id');

        // Fetch and count statuses for Terminal Reports
        $terminalReportStatusCounts = TerminalReport::select('review_status_id', DB::raw('count(*) as count'))
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

        // Get total counts for Projects, Status Reports, Terminal Reports, and Research
        $myTotalProjects = Project::where('user_id', auth()->id())->count();
        $myTotalStatusReports = StatusReport::where('logged_by_id', auth()->id())->count();
        $myTotalTerminalReports = TerminalReport::where('user_id', auth()->id())->count();
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

        $myStatusReportStatus = [];
        foreach ($myStatuses as $id => $name) {
            $myStatusReportStatus[$name] = $statusReportStatusCounts->get($id, 0); // Default to 0 if not found
        }

        $myTerminalReportStatus = [];
        foreach ($myStatuses as $id => $name) {
            $myTerminalReportStatus[$name] = $terminalReportStatusCounts->get($id, 0); // Default to 0 if not found
        }

        $myResearchStatus = [];
        foreach ($myStatuses as $id => $name) {
            $myResearchStatus[$name] = $researchStatusCounts->get($id, 0); // Default to 0 if not found
        }

        // Return the counts and myStatuses as JSON for the charts
        return response()->json([
            'myProjectStatusCounts' => $myProjectStatus,
            'myStatusReportStatusCounts' => $myStatusReportStatus,
            'myTerminalReportStatusCounts' => $myTerminalReportStatus,
            'myResearchStatusCounts' => $myResearchStatus,
            'myStatuses' => $myStatuses,
            'myTotalProjects' => $myTotalProjects,
            'myTotalStatusReports' => $myTotalStatusReports,
            'myTotalTerminalReports' => $myTotalTerminalReports,
            'myTotalResearch' => $myTotalResearch
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
            'status_reports' => [
                'reviewed' => $reviewedCounts->get(StatusReport::class)['reviewed'] ?? 0,
                'rejected' => $reviewedCounts->get(StatusReport::class)['rejected'] ?? 0,
                'requested_change' => $reviewedCounts->get(StatusReport::class)['requested change'] ?? 0,
            ],
            'terminal_reports' => [
                'reviewed' => $reviewedCounts->get(TerminalReport::class)['reviewed'] ?? 0,
                'rejected' => $reviewedCounts->get(TerminalReport::class)['rejected'] ?? 0,
                'requested_change' => $reviewedCounts->get(TerminalReport::class)['requested change'] ?? 0,
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
            'status_reports' => [
                'approved' => $approvedCounts->get(StatusReport::class)['approved'] ?? 0,
                'rejected' => $approvedCounts->get(StatusReport::class)['rejected'] ?? 0,
            ],
            'terminal_reports' => [
                'approved' => $approvedCounts->get(TerminalReport::class)['approved'] ?? 0,
                'rejected' => $approvedCounts->get(TerminalReport::class)['rejected'] ?? 0,
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
            'status_reports' => [
                'published' => $publishedCounts->get(StatusReport::class)->first() ?? 0,
            ],
            'terminal_reports' => [
                'published' => $publishedCounts->get(TerminalReport::class)->first() ?? 0,
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
        $statusReportData = [];
        $terminalReportData = [];

        // Fetch data for projects, research, status reports, and terminal reports (only published ones)
        foreach ($sdgs as $id => $label) {
            Log::info("Processing SDG: $label (ID: $id)");

            // Count projects related to the SDG
            $projectCount = Project::where('is_publish', Project::Published)
                ->whereHas('sdg', function ($query) use ($id) {
                    $query->where('sdgs.id', $id);
                })->count();
            Log::info("Project Count for $label: $projectCount");

            // Count research related to the SDG
            $researchCount = Research::where('is_publish', 1)
                ->whereHas('sdg', function ($query) use ($id) {
                    $query->where('sdgs.id', $id);
                })->count();
            Log::info("Research Count for $label: $researchCount");

                // Count status reports related to the SDG
                $statusReportCount = StatusReport::where('is_publish', 1)
                    ->where(function ($query) use ($id) {
                        $query->where('related_type', 'App\Models\Project')
                            ->whereHas('related', function ($query) use ($id) {
                                $query->whereHas('sdg', function ($query) use ($id) {
                                    $query->where('sdgs.id', $id);
                                });
                            })
                            ->orWhere(function ($query) use ($id) {
                                $query->where('related_type', 'App\Models\Research')
                                    ->whereHas('related', function ($query) use ($id) {
                                        $query->whereHas('sdg', function ($query) use ($id) {
                                            $query->where('sdgs.id', $id);
                                        });
                                    });
                            });
                    })->count();
                Log::info("Status Report Count for $label: $statusReportCount");

                // Count terminal reports related to the SDG
                $terminalReportCount = TerminalReport::where('is_publish', 1)
                    ->where(function ($query) use ($id) {
                        $query->where('related_type', 'App\Models\Project')
                            ->whereHas('related', function ($query) use ($id) {
                                $query->whereHas('sdg', function ($query) use ($id) {
                                    $query->where('sdgs.id', $id);
                                });
                            })
                            ->orWhere(function ($query) use ($id) {
                                $query->where('related_type', 'App\Models\Research')
                                    ->whereHas('related', function ($query) use ($id) {
                                        $query->whereHas('sdg', function ($query) use ($id) {
                                            $query->where('sdgs.id', $id);
                                        });
                                    });
                            });
                    })->count();
                Log::info("Terminal Report Count for $label: $terminalReportCount");
            
            // Append counts to arrays
            $projectData[] = $projectCount;
            $researchData[] = $researchCount;
            $statusReportData [] = $statusReportCount;
            $terminalReportData[] = $terminalReportCount;
        }

        // Return data in JSON format for the AJAX request
        return response()->json([
            'sdgLabels' => array_values($sdgs),
            'projectData' => $projectData,
            'researchData' => $researchData,
            'statusReportData' => $statusReportData,
            'terminalReportData' => $terminalReportData
        ]);
    } catch (\Exception $e) {
        // Log error details for debugging
        Log::error('Error fetching SDG comparison data: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch data'], 500). $e->getMessage();
    }
}
}
