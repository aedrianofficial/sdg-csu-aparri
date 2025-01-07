<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusReport extends Model
{
    use HasFactory;

    public const Published = 1;
    public const Draft = 0;

    protected $table = 'status_reports';
    protected $fillable = [
        'related_type', 
        'related_id', 
        'related_title', 
        'log_status', 
        'remarks', 
        'logged_by_id',
        'review_status_id', // Add review_status_id to fillable
        'is_publish',
        'related_link',        // Add related_link to fillable
        'status_report_file'   
    ];

    /**
     * Relationship: Related model (Project or Research).
     */
    
    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Relationship: User who logged the status report.
     */
    public function loggedBy()
    {
        return $this->belongsTo(User::class, 'logged_by_id');
    
    }
    
    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class, 'review_status_id');
    }
    public function statusReportFiles()
    {
        return $this->hasMany(StatusReportFile::class);
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
        return $this->belongsToMany(Feedback::class, 'feedback_status_report', 'status_report_id', 'feedback_id');
    }
}
