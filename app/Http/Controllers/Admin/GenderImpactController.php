<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GenderImpact;
use App\Models\Project;
use App\Models\Research;
use App\Models\Sdg;
use Illuminate\Support\Facades\DB;

class GenderImpactController extends Controller
{
    /**
     * Display a dashboard of gender impact statistics
     */
    public function index()
    {
        // Get overall statistics
        $stats = $this->getGenderStatistics();
        
        // Get projects with gender impact, ordered by most recent
        $projects = Project::with(['genderImpact', 'sdg', 'user'])
            ->whereHas('genderImpact')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Get research with gender impact, ordered by most recent
        $research = Research::with(['genderImpact', 'sdg', 'user'])
            ->whereHas('genderImpact')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Get gender impact by SDG
        $sdgStats = $this->getGenderStatsBySDG();
        
        return view('admin.gender_impact.index', compact('stats', 'projects', 'research', 'sdgStats'));
    }
    
    /**
     * Show gender impact details for a specific project or research
     */
    public function show($type, $id)
    {
        if ($type === 'project') {
            $item = Project::with(['genderImpact', 'sdg', 'user'])->findOrFail($id);
            return view('admin.gender_impact.show_project', compact('item'));
        } elseif ($type === 'research') {
            $item = Research::with(['genderImpact', 'sdg', 'user'])->findOrFail($id);
            return view('admin.gender_impact.show_research', compact('item'));
        }
        
        return abort(404);
    }
    
    /**
     * Generate aggregate gender statistics
     */
    private function getGenderStatistics()
    {
        // Get counts for projects
        $projectStats = GenderImpact::whereNotNull('project_id')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN benefits_women = 1 THEN 1 ELSE 0 END) as benefits_women_count,
                SUM(CASE WHEN benefits_men = 1 THEN 1 ELSE 0 END) as benefits_men_count,
                SUM(CASE WHEN benefits_all = 1 THEN 1 ELSE 0 END) as benefits_all_count,
                SUM(CASE WHEN addresses_gender_inequality = 1 THEN 1 ELSE 0 END) as addresses_inequality_count
            ')
            ->first();
            
        // Get counts for research
        $researchStats = GenderImpact::whereNotNull('research_id')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN benefits_women = 1 THEN 1 ELSE 0 END) as benefits_women_count,
                SUM(CASE WHEN benefits_men = 1 THEN 1 ELSE 0 END) as benefits_men_count,
                SUM(CASE WHEN benefits_all = 1 THEN 1 ELSE 0 END) as benefits_all_count,
                SUM(CASE WHEN addresses_gender_inequality = 1 THEN 1 ELSE 0 END) as addresses_inequality_count
            ')
            ->first();
            
        // Calculate totals and percentages
        $total = ($projectStats->total ?? 0) + ($researchStats->total ?? 0);
        $stats = [
            'total' => $total,
            'projects_total' => $projectStats->total ?? 0,
            'research_total' => $researchStats->total ?? 0,
            'benefits_women' => [
                'count' => ($projectStats->benefits_women_count ?? 0) + ($researchStats->benefits_women_count ?? 0),
                'percentage' => $total > 0 ? 
                    round((($projectStats->benefits_women_count ?? 0) + ($researchStats->benefits_women_count ?? 0)) / $total * 100, 1) : 0
            ],
            'benefits_men' => [
                'count' => ($projectStats->benefits_men_count ?? 0) + ($researchStats->benefits_men_count ?? 0),
                'percentage' => $total > 0 ? 
                    round((($projectStats->benefits_men_count ?? 0) + ($researchStats->benefits_men_count ?? 0)) / $total * 100, 1) : 0
            ],
            'benefits_all' => [
                'count' => ($projectStats->benefits_all_count ?? 0) + ($researchStats->benefits_all_count ?? 0),
                'percentage' => $total > 0 ? 
                    round((($projectStats->benefits_all_count ?? 0) + ($researchStats->benefits_all_count ?? 0)) / $total * 100, 1) : 0
            ],
            'addresses_inequality' => [
                'count' => ($projectStats->addresses_inequality_count ?? 0) + ($researchStats->addresses_inequality_count ?? 0),
                'percentage' => $total > 0 ? 
                    round((($projectStats->addresses_inequality_count ?? 0) + ($researchStats->addresses_inequality_count ?? 0)) / $total * 100, 1) : 0
            ]
        ];
        
        return $stats;
    }
    
    /**
     * Generate gender statistics by SDG
     */
    private function getGenderStatsBySDG()
    {
        $sdgs = Sdg::all();
        $sdgStats = [];
        
        foreach ($sdgs as $sdg) {
            // Count projects with this SDG that have gender impact
            $projectCount = DB::table('gender_impacts')
                ->join('projects', 'gender_impacts.project_id', '=', 'projects.id')
                ->join('project_sdg', 'projects.id', '=', 'project_sdg.project_id')
                ->where('project_sdg.sdg_id', $sdg->id)
                ->count();
                
            // Count research with this SDG that have gender impact
            $researchCount = DB::table('gender_impacts')
                ->join('research', 'gender_impacts.research_id', '=', 'research.id')
                ->join('research_sdg', 'research.id', '=', 'research_sdg.research_id')
                ->where('research_sdg.sdg_id', $sdg->id)
                ->count();
                
            // Calculate if this SDG is gender-focused
            $genderImpactCount = $projectCount + $researchCount;
            
            if ($genderImpactCount > 0) {
                $sdgStats[] = [
                    'sdg' => $sdg,
                    'project_count' => $projectCount,
                    'research_count' => $researchCount,
                    'total_count' => $genderImpactCount
                ];
            }
        }
        
        // Sort by total count descending
        usort($sdgStats, function($a, $b) {
            return $b['total_count'] <=> $a['total_count'];
        });
        
        return $sdgStats;
    }
    
    /**
     * Export gender impact data to Excel/CSV
     */
    public function export(Request $request)
    {
        // Placeholder for export functionality
        // This would use a package like maatwebsite/excel to export data
        
        return redirect()->route('admin.gender-impact.index')
            ->with('success', 'Export functionality will be implemented in a future update.');
    }
}
