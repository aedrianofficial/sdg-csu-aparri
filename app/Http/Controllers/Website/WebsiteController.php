<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use App\Models\Researchcategory;
use App\Models\Researchfile;
use App\Models\Sdg;
use App\Models\StatusReport;
use App\Models\TerminalReport;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebsiteController extends Controller
{
    public function viewResearchFile($id)
    {
        $file = Researchfile::findOrFail($id);
        $fileContent = $file->file;
    
        return response($fileContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $file->original_filename . '"');
    }
    
    public function showSdg($id)
    {
        $sdgs = [
            1 => "End poverty in all its forms everywhere",
            2 => "End hunger, achieve food security and improved nutrition and promote sustainable agriculture",
            3 => "Ensure healthy lives and promote well-being for all at all ages",
            4 => "Ensure inclusive and equitable quality education and promote lifelong learning opportunities for all",
            5 => "Achieve gender equality and empower all women and girls",
            6 => "Ensure availability and sustainable management of water and sanitation for all",
            7 => "Ensure access to affordable, reliable, sustainable and modern energy for all",
            8 => "Promote sustained, inclusive and sustainable economic growth, full and productive employment and decent work for all",
            9 => "Build resilient infrastructure, promote inclusive and sustainable industrialization and foster innovation",
            10 => "Reduce inequality within and among countries",
            11 => "Make cities and human settlements inclusive, safe, resilient and sustainable",
            12 => "Ensure sustainable consumption and production patterns",
            13 => "Take urgent action to combat climate change and its impacts",
            14 => "Conserve and sustainably use the oceans, seas and marine resources for sustainable development",
            15 => "Protect, restore and promote sustainable use of terrestrial ecosystems, sustainably manage forests, combat desertification, and halt and reverse land degradation and halt biodiversity loss",
            16 => "Promote peaceful and inclusive societies for sustainable development, provide access to justice for all and build effective, accountable and inclusive institutions at all levels",
            17 => "Strengthen the means of implementation and revitalize the Global Partnership for Sustainable Development"
        ];

        $sdg = (object) [
            'id' => $id,
            'name' => 'SDG ' . $id,
            'description' => $sdgs[$id] ?? 'No description available'
        ];

        // Get published projects related to this SDG
        $projects = Project::whereHas('sdg', function($query) use ($id) {
                $query->where('sdgs.id', $id);
            })
            ->where('is_publish', Project::Published)
            ->with(['projectimg', 'sdg'])
            ->get();

        // Get published research related to this SDG
        $researches = Research::whereHas('sdg', function($query) use ($id) {
                $query->where('sdgs.id', $id);
            })
            ->where('is_publish', Research::Published)
            ->with(['researchCategory', 'sdg'])
            ->get();

            // Get all SDGs with research count
            $sdgPublished = Sdg::withCount([
                'research as research_count' => function ($query) {
                    $query->where('is_publish', Research::Published); // or simply: where('is_publish', 1)
                },
                'project as project_count' => function ($query) {
                    $query->where('is_publish', Project::Published); // or simply: where('is_publish', 1)
                },
            ])->get()->map(function ($sdg) {
                $sdg->project_research_count = $sdg->research_count + $sdg->project_count;
                return $sdg;
            });
        

        // Get all research categories with count
        $researchCategories = Researchcategory::withCount('research')
            ->orderBy('name')
            ->get();

        return view('website.sdg_content.sdgs.index', compact('sdg', 'projects', 'researches', 'sdgPublished', 'researchCategories'));
    }

    public function projectsByCoordinates($latitude, $longitude)
    {
    // Fetch published projects based on the given coordinates
    $projects = Project::where('is_publish', 1)
        ->where('latitude', $latitude)
        ->where('longitude', $longitude)
        ->orderBy('id', 'desc')
        ->paginate(6);

  // Get the address of the first project, if available
    $address = $projects->first()->location_address ?? 'Address not available';

    // Fetch SDGs with the count of published projects
    $sdgs = Sdg::withCount([
        'project' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

    return view('website.sdg_content.projects_programs.projects_by_coordinates', [
        'projects' => $projects,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'address' => $address,
        'sdgs' => $sdgs
    ]);
}

    //Yearly Overview
    public function yearlyOverview(Request $request)
    {
        $year = $request->get('year', date('Y'));
    
        // Total Published Counts for the year
        $totalPublishedStatusReports = StatusReport::whereYear('created_at', $year)
            ->where('is_publish', StatusReport::Published)
            ->count();
    
        $totalPublishedTerminalReports = TerminalReport::whereYear('created_at', $year)
            ->where('is_publish', TerminalReport::Published)
            ->count();
    
        $totalPublishedProjects = Project::whereYear('created_at', $year)
            ->where('is_publish', Project::Published)
            ->count();
    
        $totalPublishedResearch = Research::whereYear('created_at', $year)
            ->where('is_publish', Research::Published)
            ->count();
    
      // Fetch SDGs with the count of related projects, status reports, terminal reports, and research for the year
        $sdgs = Sdg::withCount([
            'project' => fn ($query) => $query->whereYear('projects.created_at', $year)->where('projects.is_publish', Project::Published),
            'research' => fn ($query) => $query->whereYear('research.created_at', $year)->where('research.is_publish', Research::Published),
        ])->get()->map(function ($sdg) use ($year) {
            // Count status reports related to the SDG for the specified year
            $statusReportCount = StatusReport::where('is_publish', 1)
                ->whereYear('created_at', $year) // Filter by created_at year
                ->where(function ($query) use ($sdg) {
                    $query->where('related_type', 'App\Models\Project')
                        ->whereHas('related', function ($query) use ($sdg) {
                            $query->whereHas('sdg', function ($query) use ($sdg) {
                                $query->where('sdgs.id', $sdg->id);
                            });
                        })
                        ->orWhere(function ($query) use ($sdg) {
                            $query->where('related_type', 'App\Models\Research')
                                ->whereHas('related', function ($query) use ($sdg) {
                                    $query->whereHas('sdg', function ($query) use ($sdg) {
                                        $query->where('sdgs.id', $sdg->id);
                                    });
                                });
                        });
                })->count();

    // Count terminal reports related to the SDG for the specified year
    $terminalReportCount = TerminalReport::where('is_publish', 1)
        ->whereYear('created_at', $year) // Filter by created_at year
        ->where(function ($query) use ($sdg) {
            $query->where('related_type', 'App\Models\Project')
                ->whereHas('related', function ($query) use ($sdg) {
                    $query->whereHas('sdg', function ($query) use ($sdg) {
                        $query->where('sdgs.id', $sdg->id);
                    });
                })
                ->orWhere(function ($query) use ($sdg) {
                    $query->where('related_type', 'App\Models\Research')
                        ->whereHas('related', function ($query) use ($sdg) {
                            $query->whereHas('sdg', function ($query) use ($sdg) {
                                $query->where('sdgs.id', $sdg->id);
                            });
                        });
                });
        })->count();

    // Calculate total count for the SDG
    $sdg->total_count = $sdg->project_count + $statusReportCount + $terminalReportCount + $sdg->research_count;
    return $sdg;
});
    
      
    
        // Most Popular SDG for Projects
        $popularProjectSdg = Sdg::withCount(['project' => fn ($query) => $query->whereYear('projects.created_at', $year)->where('projects.is_publish', Project::Published)])
            ->orderBy('project_count', 'desc')->first();
    
        // Most Popular SDG for Research
       
        $popularResearchSdg = Sdg::withCount(['research' => fn ($query) => $query->whereYear('research.created_at', $year)->where('research.is_publish', Research::Published)])
            ->orderBy('research_count', 'desc')->first();
    
       // Top Contributor for Projects
        $topContributorForProjects = User::withCount([
            'projects' => fn ($query) => $query->whereYear('created_at', $year)->where('is_publish', 1),
        ])
        ->having('projects_count', '>', 0) // Only get users with more than 0 projects
        ->orderByDesc('projects_count')
        ->first();

        // Top Contributor for Research
        $topContributorForResearch = User::withCount([
            'researches' => fn ($query) => $query->whereYear('created_at', $year)->where('is_publish', 1),
        ])
        ->having('researches_count', '>', 0) // Only get users with more than 0 researches
        ->orderByDesc('researches_count')
        ->first();

        // Top Contributor for Status Reports
        $topContributorForStatusReports = User::withCount([
            'statusReports' => fn ($query) => $query->whereYear('created_at', $year)->where('is_publish', 1),
        ])
        ->having('status_reports_count', '>', 0) // Only get users with more than 0 status reports
        ->orderByDesc('status_reports_count')
        ->first();
    
        // Top Contributor for Terminal Reports
        $topContributorForTerminalReports = User::withCount([
            'terminalReports' => fn ($query) => $query->whereYear('created_at', $year)->where('is_publish', 1),
        ])
        ->having('terminal_reports_count', '>', 0) // Only get users with more than 0 reports
        ->orderByDesc('terminal_reports_count')
        ->first();
    
        // Return response for AJAX
        if ($request->ajax()) {
            $html = view('website.sdg_content.yearly_overview.partial', compact(
                'totalPublishedStatusReports', 'totalPublishedTerminalReports', 'totalPublishedProjects', 'totalPublishedResearch',
                 'popularProjectSdg', 'popularResearchSdg',
                'topContributorForProjects', 'topContributorForResearch', 'topContributorForStatusReports', 'topContributorForTerminalReports', 'sdgs', 'year'
            ))->render();
    
            return response()->json(['html' => $html]);
        }
    
        // Default view for non-AJAX request
        return view('website.sdg_content.yearly_overview.index', compact(
            'totalPublishedStatusReports', 'totalPublishedTerminalReports', 'totalPublishedProjects', 'totalPublishedResearch',
             'popularProjectSdg', 'popularResearchSdg',
            'topContributorForProjects', 'topContributorForResearch', 'topContributorForStatusReports', 'topContributorForTerminalReports', 'sdgs', 'year'
        ));
    }

    public function display_sdg_content(Sdg $sdg, Request $request)
    {
        try {
            Log::info('Request Data:', $request->all());
            $year = $request->get('year', date('Y'));
        
            // Fetch SDGs with the count of related projects and research for the year
            $sdgs = Sdg::withCount([
                'project' => fn ($query) => $query->whereYear('projects.created_at', $year)->where('projects.is_publish', Project::Published),
                'research' => fn ($query) => $query->whereYear('research.created_at', $year)->where('research.is_publish', Research::Published),
            ])->get()->map(function ($sdg) {
                $sdg->total_count = $sdg->project_count + $sdg->research_count; // Removed report_count
                return $sdg;
            });
        
            // Fetch paginated published projects for the selected SDG and year
            $projects = $sdg->project()
                ->where('is_publish', 1)
                ->whereYear('projects.created_at', $year)
                ->orderBy('id', 'desc')
                ->paginate(4);
        
            // Fetch paginated published research for the selected SDG and year
            $research = $sdg->research()
                ->where('is_publish', 1)
                ->whereYear('research.created_at', $year)
                ->orderBy('id', 'desc')
                ->paginate(4);
        
            if ($request->ajax()) {
                // Return a partial view or JSON response for AJAX requests
                return response()->json([
                    'html' => view('website.sdg_content.project_research_report.partial', [
                        'projects' => $projects,
                        'research' => $research,
                        'sdg' => $sdg,
                        'selectedYear' => $year,
                        'sdgs' => $sdgs, // Add this line to pass the $sdgs variable
                    ])->render(),
                ]);
            }
        
            return view('website.sdg_content.project_research_report.index', [
                'sdgs' => $sdgs,
                'projects' => $projects,
                'research' => $research,
                'sdg' => $sdg,
                'selectedYear' => $year,
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching yearly data:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'An error occurred while fetching yearly data.'], 500);
        }
    }
    
    

    public function home()
    {
       
        $sdgs = Sdg::all();
    
        // Fetch projects with geolocation data for the map
        $mapProjects = Project::where('is_publish', Project::Published)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id','title', 'location_address', 'latitude', 'longitude']);
    
       
        return view('website.sdg_content.index', [
           
            'sdgs' => $sdgs,
            'mapProjects' => $mapProjects, // Pass projects with geolocation for map
          
        ]);
    }
    


    public function display_single_report($report_id) {
        $report = Report::findOrFail($report_id);
        $latestReports = Report::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::withCount([
            'report' => function ($query) {
                $query->where('is_publish', 1); // Fetch only published reports
            }
        ])->get();
    
        
        // Fetch related projects based on related_id
        $projects = Project::where('id', $report->related_id)->where('is_publish', true)->get();
    
        return view('website.sdg_content.sdg_reports.single', [
            'report' => $report,
            'latestReports' => $latestReports,
            'sdgs' => $sdgs,
            'projects' => $projects // Pass the projects to the view
        ]);
    }
    

    public function sdg_report_main(Sdg $sdg)
    {
        // Fetch SDGs with the count of related reports
        $sdgs = Sdg::withCount([
            'report' => function ($query) {
                $query->where('is_publish', 1); // Fetch only published reports
            }
        ])->get();
    
        
        // Fetch reports for the selected SDG and paginate
        $reports = Report::orderBy('id', 'desc')->where('is_publish', Report::Published)->paginate(4    );
    
        return view('website.sdg_content.sdg_reports.index', [
            'sdg' => $sdg,
            'reports' => $reports,
            'sdgs' => $sdgs // Pass the SDGs with the report count
        ]);
    }
    


    public function display_report_sdg(Sdg $sdg)
    {
        // Fetch SDGs with the count of published reports
        $sdgs = Sdg::withCount([
            'report' => function ($query) {
                $query->where('is_publish', 1); // Only count published reports
            }
        ])->get();
    
        // Fetch paginated published reports for the selected SDG
        $reports = $sdg->report()
            ->where('is_publish', 1) // Filter only published reports
            ->orderBy('id', 'desc')  // Optional: Order by newest first
            ->paginate(4);
    
        return view('website.sdg_content.sdg_reports.', [
            'reports' => $reports,
            'sdg' => $sdg,
            'sdgs' => $sdgs
        ]);
    }
    public function showStatusReportProjectPublished(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('loggedBy')->findOrFail($id);
        $sdgs = Sdg::withCount([
            'project' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();

        // Return the view for showing the status report details, including notification data
        return view('website.sdg_content.status_report.project.single', compact('statusReport','sdgs'));
    }

    public function showTerminalReportProjectPublished(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
            ->findOrFail($id);
    
        // Check if the terminal report is published
        if (!$terminalReport->is_publish) {
            // Handle the case where the report is not published (e.g., redirect or show a message)
            return redirect()->back()->with('error', 'This terminal report is not published.');
        }
    
        // Get the first terminal report file
        $terminalReportFile = $terminalReport->terminalReportFiles->first();
        $sdgs = Sdg::withCount([
            'project' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();
      
        // Return the view for showing the terminal report details, including notification data
        return view('website.sdg_content.terminal_report.project.single', compact('terminalReport', 'terminalReportFile','sdgs'));
    }
    public function showStatusReportResearchPublished(string $id, Request $request)
    {
        // Find the status report by its ID, including the user who logged it
        $statusReport = StatusReport::with('loggedBy')->findOrFail($id);
        $sdgs = Sdg::withCount([
            'project' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();

        // Return the view for showing the status report details, including notification data
        return view('website.sdg_content.status_report.research.single', compact('statusReport','sdgs'));
    }

    public function showTerminalReportResearchPublished(string $id, Request $request)
    {
        // Find the terminal report by its ID, including the user who logged it
        $terminalReport = TerminalReport::with(['cooperatingAgency', 'fundingAgency', 'researchers', 'terminalReportFiles'])
            ->findOrFail($id);
    
        // Check if the terminal report is published
        if (!$terminalReport->is_publish) {
            // Handle the case where the report is not published (e.g., redirect or show a message)
            return redirect()->back()->with('error', 'This terminal report is not published.');
        }
    
        // Get the first terminal report file
        $terminalReportFile = $terminalReport->terminalReportFiles->first();
        $sdgs = Sdg::withCount([
            'project' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();
      
        // Return the view for showing the terminal report details, including notification data
        return view('website.sdg_content.terminal_report.research.single', compact('terminalReport', 'terminalReportFile','sdgs'));
    }
  

    public function display_single_project($project_id)
{
    // Fetch the specific project
    $project = Project::where('id', $project_id)
        ->where('is_publish', 1) // Ensure the project is published
        ->firstOrFail();

    // Fetch the latest 5 published projects
    $latestProjects = Project::where('is_publish', 1)
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();

    // Fetch SDGs with the count of related published projects
    $sdgs = Sdg::withCount([
        'project' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

   

    // Fetch Status Reports based on the specified criteria
    $statusReports = StatusReport::where('related_id', $project_id)
        ->where('is_publish', 1)
        ->where('review_status_id', 3)
        ->where('related_type', 'App\Models\Project')
        ->whereIn('log_status', ['Proposed', 'On-Going', 'On-Hold', 'Rejected']) // Proposed, On-Going, On-Hold, Rejected
        ->get();

    // Fetch Terminal Reports based on the specified criteria
    $terminalReports = TerminalReport::where('related_id', $project_id)
        ->where('is_publish', 1)
        ->where('review_status_id', 3)
        ->where('related_type', 'App\Models\Project') // Ensure it's related to Project
        ->get();

    return view('website.sdg_content.projects_programs.single', [
        'project' => $project,
        'latestProjects' => $latestProjects,
        'sdgs' => $sdgs,
        'statusReports' => $statusReports,
        'terminalReports' => $terminalReports,
    ]);
}
    
    

    public function sdg_project_main()
    {
        // Fetch SDGs with the count of published projects
        $sdgs = Sdg::withCount([
            'project' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();
    
        // Fetch published projects and paginate
        $projects = Project::where('is_publish', 1)
            ->orderBy('id', 'desc')
            ->paginate(4);
    
        return view('website.sdg_content.projects_programs.index', [
            'sdgs' => $sdgs,
            'projects' => $projects,
        ]);
    }
    

    public function display_project_sdg(Sdg $sdg)
    {
        // Fetch SDGs with the count of related published projects
        $sdgs = Sdg::withCount([
            'project' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();

        // Fetch paginated published projects for the selected SDG
        $projects = $sdg->project()
            ->where('is_publish', 1) // Filter only published projects
            ->orderBy('id', 'desc')
            ->paginate(4);

        return view('website.sdg_content.projects_programs.projects_programs', [
            'projects' => $projects,
            'sdgs' => $sdgs,
            'sdg' => $sdg,
        ]);
    }

 
    public function display_single_research($research_id)
    {
        // Fetch SDGs with the count of related published research
        $sdgs = Sdg::withCount([
            'research' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();
    
        // Fetch the specific published research
        $research = Research::with('researchcategory')
            ->where('id', $research_id)
            ->where('is_publish', 1) // Ensure it's published
            ->firstOrFail();
    
        // Fetch the latest 5 published research items
        $latestResearch = Research::where('is_publish', 1)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
    
        // Fetch research categories with counts of published research
        $researchCategories = Researchcategory::withCount([
            'research' => function ($query) {
                $query->where('is_publish', 1);
            }
        ])->get();
    
        
    // Fetch Status Reports based on the specified criteria
            $statusReports = StatusReport::where('related_id', $research_id)
            ->where('is_publish', 1)
            ->where('review_status_id', 3)
            ->where('related_type', 'App\Models\Research') // Ensure it's related to Project
            ->whereIn('log_status', ['Proposed', 'On-Going', 'On-Hold', 'Rejected']) // Proposed, On-Going, On-Hold, Rejected
            ->get();

        // Fetch Terminal Reports based on the specified criteria
        $terminalReports = TerminalReport::where('related_id', $research_id)
            ->where('is_publish', 1)
            ->where('review_status_id', 3)
            ->where('related_type', 'App\Models\Research') // Ensure it's related to Project
            ->get();

        return view('website.sdg_content.research_extension.single', [
            'research' => $research,
            'researchCategories' => $researchCategories,
            'statusReports'=>$statusReports,
            'terminalReports'=>$terminalReports,
            'latestResearch' => $latestResearch,
            'sdgs' => $sdgs,
        ]);
    }
    
    
    public function sdg_research_main()
{
    // Fetch SDGs with the count of related published research
    $sdgs = Sdg::withCount([
        'research' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

    // Fetch research categories with counts of published research
    $researchCategories = Researchcategory::withCount([
        'research' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

    // Fetch published research items and paginate
    $research = Research::where('is_publish', 1)
        ->orderBy('id', 'desc')
        ->paginate(4);

    return view('website.sdg_content.research_extension.index', [
        'research' => $research,
        'sdgs' => $sdgs,
        'researchCategories' => $researchCategories,
    ]);
}

    

public function display_research_sdg(Sdg $sdg)
{
    // Fetch SDGs with the count of related published research
    $sdgs = Sdg::withCount([
        'research' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

    // Fetch research categories with counts of published research
    $researchCategories = Researchcategory::withCount([
        'research' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

    // Fetch published research items for the selected SDG and paginate
    $research = $sdg->research()
        ->where('is_publish', 1)
        ->orderBy('id', 'desc')
        ->paginate(4);

    return view('website.sdg_content.research_extension.research_extension_sdg', [
        'research' => $research,
        'sdgs' => $sdgs,
        'sdg' => $sdg,
        'researchCategories' => $researchCategories,
    ]);
}

public function display_research_category(Researchcategory $researchcategory)
{
    // Fetch SDGs with the count of related published research
    $sdgs = Sdg::withCount([
        'research' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

    // Fetch research categories with counts of published research
    $researchCategories = Researchcategory::withCount([
        'research' => function ($query) {
            $query->where('is_publish', 1);
        }
    ])->get();

    // Fetch published research items for the selected category and paginate
    $research = $researchcategory->research()
        ->where('is_publish', 1)
        ->orderBy('id', 'desc')
        ->paginate(4);

    return view('website.sdg_content.research_extension.research_extension_categories', [
        'research' => $research,
        'sdgs' => $sdgs,
        'researchcategory' => $researchcategory,
        'researchCategories' => $researchCategories,
    ]);
}

    public function contact_us(){
        return view("website.sdg_content.contact.index");
    }
    
}
