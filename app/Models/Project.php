<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public const Published = 1;
    public const Draft = 0;

    protected $fillable = [
        'sdg_sub_category_id',
        'projectimg_id',
        'user_id',
        'review_status_id',
        'status_id',//project_research_status
        'title',
        'description',
        'is_publish',
        'latitude',
        'longitude',
        'location_address',
    ];

    // Relationship to Projectimg
    public function projectimg()
    {
        return $this->belongsTo(Projectimg::class);
    }
    public function sdg()
    {
        return $this->belongsToMany(Sdg::class, 'project_sdg', 'project_id', 'sdg_id');
    }

    // Relationship to SDG Sub Category
    public function sdgSubCategories()
    {
        return $this->belongsToMany(SdgSubCategory::class, 'project_sdg_sub_category', 'project_id', 'sdg_sub_category_id');
    }

    // Polymorphic relationship to RoleAction
    public function roleActions()
    {
        return $this->morphMany(RoleAction::class, 'content');
    }

    // Polymorphic relationship to Reports
    public function reports()
    {
        return $this->morphMany(Report::class, 'related');
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to ReviewStatus
    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class, 'review_status_id');
    }

    // Relationship to Status (status_id)
    public function status()
    {
        return $this->belongsTo(ProjectResearchStatus::class, 'status_id');
    }

    // Relationship to Feedbacks
    public function feedbacks()
    {
        return $this->belongsToMany(Feedback::class, 'feedback_project', 'project_id', 'feedback_id');
    }
}
