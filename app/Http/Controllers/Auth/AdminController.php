<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use App\Models\Sdg;
use Spatie\Activitylog\Models\Activity;
use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function my_activity_logs(Request $request)
    {
        $userId = auth()->user()->id;

        // Initialize query for activity logs
        $query = ActivityLog::where('causer_id', $userId);

        // Apply filters if present
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('type')) {
            // Assuming subject_type is stored as the full class name
            $query->where('subject_type', 'App\Models\\' . $request->type);
        }

        // Apply search filter for description
        if ($request->filled('title')) {
            $query->where('description', 'like', '%' . $request->title . '%');
        }

        // Retrieve activity logs for the authenticated user with pagination
        $activityLogs = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('auth.activity_logs.index', compact('activityLogs'));
    }
    public function index()
    {
        // Fetch projects from the database
        $projects = Project::where('is_publish', 1)
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get(['id','title', 'location_address', 'latitude', 'longitude']);
    
        return view('auth.dashboard', compact('projects'));
    }
    

    public function adminActivityLogs()
    {
        $logs = Activity::orderBy('id','desc')->get(); // Retrieve activity logs, you may need to adjust this query based on your requirements

        $reports = $logs->filter(function ($log) {
            return $log->subject_type == Report::class;
        });
    
        $projects = $logs->filter(function ($log) {
            return $log->subject_type == Project::class;
        });
    
        $researches = $logs->filter(function ($log) {
            return $log->subject_type == Research::class;
        });

        return view('auth.activity_logs.admin.activity_logs', compact('reports','projects','researches'));
    }

    public function displayAdminActivityLogs($id)
    {
        $log = Activity::findOrFail($id);
        $properties = json_decode($log->properties);

        return view('auth.activity_logs.admin.single', compact('log', 'properties'));
    }

    public function userActivityLogs()
    {
        $logs = Activity::orderBy('id', 'desc')->get();
        $feedbacks = $logs->filter(function ($log) {
            return $log->subject_type == Feedback::class;
        });
    
        return view('auth.activity_logs.users.activity_logs', compact('feedbacks'));
    }

    public function displayUserActivityLogs($id)
    {
        $log = Activity::findOrFail($id);
        $properties = json_decode($log->properties);

        return view('auth.activity_logs.users.feedbacks', compact('log', 'properties'));
    }

    // Admin Dashboard (Same as User)
    public function dashboard(){
        $latestReports = Report::orderBy('id', 'desc')->take(5)->get();
        $latestProjects = Project::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::all();

        $projects = Project::where('is_publish', Project::Published)->simplePaginate(3);
        $reports = Report::where('is_publish', Report::Published)->simplePaginate(3);

        return view('user.sdg_content.dashboard', [
            'reports' => $reports,
            'latestProjects' => $latestProjects,
            'projects' => $projects,
            'latestReports' => $latestReports,
            'sdgs' => $sdgs
        ]);
    }

    // SDG Reports - Main Page (Same as User)
    public function sdg_report_main(){
        $sdgs = Sdg::with('sdgimage')->get();
        return view('user.sdg_content.sdg_reports.index', ['sdgs' => $sdgs]);
    }

    // Display Reports for Specific SDG (Same as User)
    public function display_report_sdg(Sdg $sdg){
        $latestReports = Report::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::all();
        $reports = $sdg->report()->get();

        return view('user.sdg_content.sdg_reports.reports', [
            'reports' => $reports,
            'sdg' => $sdg,
            'latestReports' => $latestReports,
            'sdgs' => $sdgs
        ]);
    }

    // Display Single Report (Same as User)
    public function display_single_report($report_id){
        $report = Report::findOrFail($report_id);
        $latestReports = Report::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::all();

        return view('user.sdg_content.sdg_reports.single', [
            'report' => $report,
            'latestReports' => $latestReports,
            'sdgs' => $sdgs
        ]);
    }

    public function sdg_project_main(){
        $sdgs = Sdg::with('sdgimage')->get();
        return view('user.sdg_content.projects_programs.index', ['sdgs' => $sdgs]);
    }

    // Display Projects for Specific SDG (Same as User)
    public function display_project_sdg(Sdg $sdg){
        $latestProjects = Project::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::all();
        $projects = $sdg->project()->get();

        return view('user.sdg_content.projects_programs.projects_programs', [
            'projects' => $projects,
            'sdg' => $sdg,
            'latestProjects' => $latestProjects,
            'sdgs' => $sdgs
        ]);
    }

    // Display Single Project (Same as User)
    public function display_single_project($project_id){
        $project = Project::findOrFail($project_id);
        $latestProjects = Project::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::all();

        return view('user.sdg_content.projects_programs.single', [
            'project' => $project,
            'latestProjects' => $latestProjects,
            'sdgs' => $sdgs
        ]);
    }

    // Research Extension - Main Page (Same as User)
    public function sdg_research_main(){
        $sdgs = Sdg::with('sdgimage')->get();
        return view('user.sdg_content.research_extension.index', ['sdgs' => $sdgs]);
    }

    // Display Research for Specific SDG (Same as User)
    public function display_research_sdg(Sdg $sdg){
        $latestResearch = Research::orderBy('id', 'desc')->take(5)->get();
        $researchCategories = [];

        foreach ($sdg->research as $research) {
            $categoryName = $research->researchcategory->name;
            if (!isset($researchCategories[$categoryName])) {
                $researchCategories[$categoryName] = [];
            }
            $researchCategories[$categoryName][] = $research;
        }

        $sdgs = Sdg::all();
        return view('user.sdg_content.research_extension.research_extension', [
            'research' => $researchCategories,
            'sdg' => $sdg,
            'latestResearch' => $latestResearch,
            'sdgs' => $sdgs
        ]);
    }

    // Display Single Research (Same as User)
    public function display_single_research($research_id){
        $research = Research::with('researchcategory')->findOrFail($research_id);
        $latestResearch = Research::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::all();

        return view('user.sdg_content.research_extension.single', [
            'research' => $research,
            'latestResearch' => $latestResearch,
            'sdgs' => $sdgs
        ]);
    }
}