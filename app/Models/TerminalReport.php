<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminalReport extends Model
{
    use HasFactory;
    public const Published = 1;
    public const Draft = 0;
    protected $fillable = [
        'related_title', 
        'related_type', 
        'related_id', 
        'researchers_id', 
        'cooperating_agency_id', 
        'funding_agency_id', 
        'date_started', 
        'date_ended', 
        'total_approved_budget', 
        'actual_released_budget', 
        'actual_expenditure', 
        'abstract', 
        'files_link',
        'review_status_id',
        'is_publish',
        'related_link',
        'user_id',
    ];
    protected $casts = [
        'date_started' => 'datetime',
        'date_ended' => 'datetime',
    ];

    /**
     * Relationship: Related model (Project or Research).
     */
    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Relationship: Researchers associated with the report.
     */
    public function researchers()
    {
        return $this->belongsToMany(Researcher::class, 'terminal_report_researcher', 'terminal_report_id', 'researcher_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Relationship: Cooperating agency.
     */
    public function cooperatingAgency()
    {
        return $this->belongsTo(CooperatingAgency::class, 'cooperating_agency_id');
    }

    /**
     * Relationship: Funding agency.
     */
    public function fundingAgency()
    {
        return $this->belongsTo(FundingAgency::class, 'funding_agency_id');
    }

    public function terminalReportFiles()
    {
        return $this->hasMany(TerminalReportFile::class);
    }

    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class, 'review_status_id');
    }
    public function getProjectResearchStatusAttribute()
    {
        if ($this->related_type === 'App\Models\Project') {
            return Project::find($this->related_id)->projectResearchStatus; // Assuming the Project model has a projectResearchStatus relationship
        } elseif ($this->related_type === 'App\Models\Research') {
            return Research::find($this->related_id)->projectResearchStatus; // Assuming the Research model has a projectResearchStatus relationship
        }
    
        return null; // Return null if no related type matches
    }
    public function feedbacks()
    {
        return $this->belongsToMany(Feedback::class, 'feedback_terminal_report','terminal_report_id', 'feedback_id');
    }
}
