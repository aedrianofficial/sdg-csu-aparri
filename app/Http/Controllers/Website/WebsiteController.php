<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use App\Models\Researchcategory;
use App\Models\Sdg;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebsiteController extends Controller
{
    //Yearly Overview
    public function yearlyOverview(Request $request)
{
    $year = $request->get('year', date('Y'));

    // Total Published Counts for the year
    $totalPublishedReports = Report::whereYear('created_at', $year)->where('is_publish', Report::Published)->count();
    $totalPublishedProjects = Project::whereYear('created_at', $year)->where('is_publish', Project::Published)->count();
    $totalPublishedResearch = Research::whereYear('created_at', $year)->where('is_publish', Research::Published)->count();

    // Fetch SDGs with the count of related projects, reports, and research for the year
    $sdgs = Sdg::withCount([
        'project' => fn ($query) => $query->whereYear('projects.created_at', $year)->where('projects.is_publish', Project::Published),
        'report' => fn ($query) => $query->whereYear('reports.created_at', $year)->where('reports.is_publish', Report::Published),
        'research' => fn ($query) => $query->whereYear('research.created_at', $year)->where('research.is_publish', Research::Published),
    ])->get()->map(function ($sdg) {
        $sdg->total_count = $sdg->project_count + $sdg->report_count + $sdg->research_count;
        return $sdg;
    });

    // Most Popular SDGs
    $popularReportSdg = Sdg::withCount(['report' => fn ($query) => $query->whereYear('reports.created_at', $year)->where('reports.is_publish', Report::Published)])
        ->orderBy('report_count', 'desc')->first();

    $popularProjectSdg = Sdg::withCount(['project' => fn ($query) => $query->whereYear('projects.created_at', $year)->where('projects.is_publish', Project::Published)])
        ->orderBy('project_count', 'desc')->first();

    $popularResearchSdg = Sdg::withCount(['research' => fn ($query) => $query->whereYear('research.created_at', $year)->where('research.is_publish', Research::Published)])
        ->orderBy('research_count', 'desc')->first();

    // Top Contributors (at least one contribution in projects, or non-zero contributions overall)
    $topContributors = User::withCount([
        'reports' => fn ($query) => $query->whereYear('created_at', $year)->where('is_publish', 1),
        'projects' => fn ($query) => $query->whereYear('created_at', $year)->where('is_publish', 1),
        'researches' => fn ($query) => $query->whereYear('created_at', $year)->where('is_publish', 1),
    ])
        ->havingRaw('projects_count > 0 OR (reports_count + researches_count) > 0') // Ensure at least one project OR contributions in other categories
        ->orderByDesc('reports_count')
        ->orderByDesc('projects_count')
        ->take(3)
        ->get();

    // Return response for AJAX
    if ($request->ajax()) {
        $html = view('website.sdg_content.yearly_overview.partial', compact(
            'totalPublishedReports', 'totalPublishedProjects', 'totalPublishedResearch',
            'popularReportSdg', 'popularProjectSdg', 'popularResearchSdg',
            'topContributors', 'sdgs','year'
        ))->render();

        return response()->json(['html' => $html]);
    }

    // Default view for non-AJAX request
    return view('website.sdg_content.yearly_overview.index', compact(
        'totalPublishedReports', 'totalPublishedProjects', 'totalPublishedResearch',
        'popularReportSdg', 'popularProjectSdg', 'popularResearchSdg',
        'topContributors', 'sdgs', 'year'
    ));
    }
    public function display_sdg_content(Sdg $sdg, Request $request)
    {
        try {
            Log::info('Request Data:', $request->all());
            $year = $request->get('year', date('Y'));
        
            // Fetch SDGs with the count of related projects, reports, and research for the year
            $sdgs = Sdg::withCount([
                'project' => fn ($query) => $query->whereYear('projects.created_at', $year)->where('projects.is_publish', Project::Published),
                'report' => fn ($query) => $query->whereYear('reports.created_at', $year)->where('reports.is_publish', Report::Published),
                'research' => fn ($query) => $query->whereYear('research.created_at', $year)->where('research.is_publish', Research::Published),
            ])->get()->map(function ($sdg) {
                $sdg->total_count = $sdg->project_count + $sdg->report_count + $sdg->research_count;
                return $sdg;
            });
        
            // Fetch paginated published reports for the selected SDG and year
            $reports = $sdg->report()
                ->where('is_publish', 1)
                ->whereYear('reports.created_at', $year)
                ->orderBy('id', 'desc')
                ->paginate(4);
        
            $projects = $sdg->project()
                ->where('project_status', 'published')
                ->whereYear('projects.created_at', $year)
                ->orderBy('id', 'desc')
                ->paginate(4);
        
            $research = $sdg->research()
                ->where('is_publish', 1)
                ->whereYear('research.created_at', $year)
                ->orderBy('id', 'desc')
                ->paginate(4);
        
            if ($request->ajax()) {
                // Return a partial view or JSON response for AJAX requests
                return response()->json([
                    'html' => view('website.sdg_content.project_research_report.partial', [
                        'reports' => $reports,
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
                'reports' => $reports,
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
    
    

    public function home2()
    {
       
        $sdgs = Sdg::all();
    
        // Fetch projects with geolocation data for the map
        $mapProjects = Project::where('is_publish', Project::Published)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id','title', 'location_address', 'latitude', 'longitude']);
    
        // Paginate projects and reports for the list
        $projects = Project::orderBy('id', 'desc')->where('is_publish', Project::Published)->take(3)->get();
        $reports = Report::orderBy('id', 'desc')->where('is_publish', Report::Published)->take(3)->get();
    
        // Fetch the latest research with their categories
        $research = Research::with(['researchcategory', 'researchfiles']) // Load research category and files
        ->orderBy('id', 'desc')
        ->where('is_publish', Research::Published) 
        ->take(3)
        ->get();
    
        return view('website.sdg_content.index2', [
            'reports' => $reports,
            'projects' => $projects,
            'sdgs' => $sdgs,
            'mapProjects' => $mapProjects, // Pass projects with geolocation for map
            'research' => $research, // Pass the latest research
        ]);
    }
    


    public function display_single_report2($report_id) {
        $report = Report::findOrFail($report_id);
        $latestReports = Report::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::withCount([
            'report' => function ($query) {
                $query->where('is_publish', 1); // Fetch only published reports
            }
        ])->get();
    
        
        // Fetch related projects based on related_id
        $projects = Project::where('id', $report->related_id)->where('is_publish', true)->get();
    
        return view('website.sdg_content.sdg_reports.single2', [
            'report' => $report,
            'latestReports' => $latestReports,
            'sdgs' => $sdgs,
            'projects' => $projects // Pass the projects to the view
        ]);
    }
    

  
    public function sdg_report_main2(Sdg $sdg)
    {
        // Fetch SDGs with the count of related reports
        $sdgs = Sdg::withCount([
            'report' => function ($query) {
                $query->where('is_publish', 1); // Fetch only published reports
            }
        ])->get();
    
        
        // Fetch reports for the selected SDG and paginate
        $reports = Report::orderBy('id', 'desc')->where('is_publish', Report::Published)->paginate(4    );
    
        return view('website.sdg_content.sdg_reports.index2', [
            'sdg' => $sdg,
            'reports' => $reports,
            'sdgs' => $sdgs // Pass the SDGs with the report count
        ]);
    }
    


    public function display_report_sdg2(Sdg $sdg)
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
    
        return view('website.sdg_content.sdg_reports.reports2', [
            'reports' => $reports,
            'sdg' => $sdg,
            'sdgs' => $sdgs
        ]);
    }
    
  

    public function display_single_project2($project_id)
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
    
        // Fetch related published reports
        $reports = Report::where('related_id', $project_id)
            ->where('is_publish', 1)
            ->paginate(6);
    
        return view('website.sdg_content.projects_programs.single2', [
            'project' => $project,
            'latestProjects' => $latestProjects,
            'sdgs' => $sdgs,
            'reports' => $reports,
        ]);
    }
    
    

    public function sdg_project_main2()
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
    
        return view('website.sdg_content.projects_programs.index2', [
            'sdgs' => $sdgs,
            'projects' => $projects,
        ]);
    }
    

    public function display_project_sdg2(Sdg $sdg)
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

        return view('website.sdg_content.projects_programs.projects_programs2', [
            'projects' => $projects,
            'sdgs' => $sdgs,
            'sdg' => $sdg,
        ]);
    }

 
    public function display_single_research2($research_id)
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
    
        return view('website.sdg_content.research_extension.single2', [
            'research' => $research,
            'researchCategories' => $researchCategories,
            'latestResearch' => $latestResearch,
            'sdgs' => $sdgs,
        ]);
    }
    
    
    public function sdg_research_main2()
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

    return view('website.sdg_content.research_extension.index2', [
        'research' => $research,
        'sdgs' => $sdgs,
        'researchCategories' => $researchCategories,
    ]);
}

    

public function display_research_sdg2(Sdg $sdg)
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
