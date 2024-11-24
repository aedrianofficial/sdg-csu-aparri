<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use App\Models\Researchcategory;
use App\Models\Sdg;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
   

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
        $sdgs = Sdg::withCount('report')->get();
        
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
        $sdgs = Sdg::withCount('report')->get(); // Assuming there is a 'reports' relationship in Sdg model
        
        // Fetch reports for the selected SDG and paginate
        $reports = Report::orderBy('id', 'desc')->where('is_publish', Report::Published)->paginate(4    );
    
        return view('website.sdg_content.sdg_reports.index2', [
            'sdg' => $sdg,
            'reports' => $reports,
            'sdgs' => $sdgs // Pass the SDGs with the report count
        ]);
    }
    


    public function display_report_sdg2(Sdg $sdg){

        $sdgs = Sdg::withCount('report')->get();
        $reports = $sdg->report()->paginate(4);
        return view('website.sdg_content.sdg_reports.reports2', ['reports'=> $reports,
                                                                'sdg'=>$sdg,                                                    
                                                                'sdgs'=>$sdgs]);
    }
  

    public function display_single_project2($project_id) {
        $project = Project::findOrFail($project_id);
        $latestProjects = Project::orderBy('id', 'desc')->take(5)->get();
        $sdgs = Sdg::withCount('project')->get();
        
        // Fetch related reports
        $reports = Report::where('related_id', $project_id)->where('is_publish', true)->paginate(6);
        
        return view('website.sdg_content.projects_programs.single2', [
            'project' => $project,
            'latestProjects' => $latestProjects,
            'sdgs' => $sdgs,
            'reports' => $reports // Pass the reports to the view
        ]);
    }
    

    public function sdg_project_main2(){
        $sdgs = Sdg::withCount('project')->get(); // Assuming there is a 'projects' relationship in Sdg model
        
        // Fetch projects for the selected SDG and paginate
        $projects = Project::orderBy('id', 'desc')->where('is_publish', Project::Published)->paginate(4    );
       

        return view('website.sdg_content.projects_programs.index2', ['sdgs'=>$sdgs,
                                                                                'projects'=>$projects]);
    }

    public function display_project_sdg2(Sdg $sdg){
        $sdgs = Sdg::withCount('project')->get();
        $projects = $sdg->project()->paginate(4);

        return view('website.sdg_content.projects_programs.projects_programs2', ['projects'=> $projects,         
                                                                'sdgs'=>$sdgs,
                                                                'sdg'=>$sdg,]);
    }

 
    public function display_single_research2($research_id){

     
        $sdgs = Sdg::withCount('research')->get();
        $research = Research::with('researchcategory')->findOrFail($research_id);
        $latestResearch = Research::orderBy('id', 'desc')->take(5)->get();
        $researchCategories = Researchcategory::withCount('research')->get();
        return view('website.sdg_content.research_extension.single2', [
            'research' => $research,
            'researchCategories' =>$researchCategories,
            'latestResearch' => $latestResearch,
            'sdgs' => $sdgs
        ]);
    }
    


    public function sdg_research_main2() {
        // Fetch SDGs with project count
        $sdgs = Sdg::withCount('research')->get(); // Assuming there is a 'research' relationship in Sdg model
    
        // Fetch research categories with research count
        $researchCategories = Researchcategory::withCount('research')->get(); // Assuming there is a 'research' relationship in Researchcategory model
    
        // Fetch research for the selected SDG and paginate
        $research = Research::orderBy('id', 'desc')->where('is_publish', Research::Published)->paginate(4);
    
        return view('website.sdg_content.research_extension.index2', ['research'=>$research,
            'sdgs' => $sdgs,
            'researchCategories' => $researchCategories, // Pass the research categories with count to the view
        ]);
    }
    

    public function display_research_sdg2(Sdg $sdg){
         // Fetch SDGs with project count
         $sdgs = Sdg::withCount('research')->get(); // Assuming there is a 'research' relationship in Sdg model
    
         // Fetch research categories with research count
         $researchCategories = Researchcategory::withCount('research')->get(); // Assuming there is a 'research' relationship in Researchcategory model
     
         // Fetch research for the selected SDG and paginate
         $research = $sdg->research()->paginate(4);
         return view('website.sdg_content.research_extension.research_extension_sdg', ['research'=>$research,
             'sdgs' => $sdgs,
             'sdg' => $sdg,
             'researchCategories' => $researchCategories, // Pass the research categories with count to the view
         ]);
    }
    public function display_research_category(Researchcategory $researchcategory){
        // Fetch SDGs with project count
        $sdgs = Sdg::withCount('research')->get(); // Assuming there is a 'research' relationship in Sdg model
   
        // Fetch research categories with research count
        $researchCategories = Researchcategory::withCount('research')->get(); // Assuming there is a 'research' relationship in Researchcategory model
    
        // Fetch research for the selected SDG and paginate
        $research = $researchcategory->research()->paginate(4);
        return view('website.sdg_content.research_extension.research_extension_categories', ['research'=>$research,
            'sdgs' => $sdgs,
            'researchcategory' => $researchcategory,
            'researchCategories' => $researchCategories, // Pass the research categories with count to the view
        ]);
   }

    public function contact_us(){
        return view("website.sdg_content.contact.index");
    }
    
}
