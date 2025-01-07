<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = ['feedback', 'users_id'];

    /**
     * Get the user that owns the feedback.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Get the projects associated with the feedback.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'feedback_project', 'feedback_id', 'project_id');
    }

    /**
     * Get the reports associated with the feedback.
     */
    public function reports()
{
    return $this->belongsToMany(Report::class, 'feedback_report', 'feedback_id', 'report_id');
}


    /**
     * Get the research associated with the feedback.
     */
    public function research()
    {
        return $this->belongsToMany(Research::class, 'feedback_research', 'feedback_id', 'research_id');
    }

    public function statusReports()
    {
        return $this->belongsToMany(StatusReport::class, 'feedback_status_report', 'feedback_id', 'status_report_id');
    }
    public function terminalReports()
    {
        return $this->belongsToMany(TerminalReport::class, 'feedback_terminal_report','feedback_id', 'terminal_report_id');
    }
}
