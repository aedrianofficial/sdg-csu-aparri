<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectResearchStatus extends Model
{
    use HasFactory;
    protected $table = "project_research_statuses";
    
    protected $fillable = ['status', 'is_active'];

    /**
     * Relationship: Projects with this status.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'status_id');
    }

    /**
     * Relationship: Research with this status.
     */
    public function research()
    {
        return $this->hasMany(Research::class, 'status_id');
    }

}
