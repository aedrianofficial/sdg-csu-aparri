<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use HasFactory;

    public const Published = 1;
    public const Draft = 0;

    protected $fillable = [
        'sdg_sub_category_id',
        'researchcategory_id',
        'user_id',
        'review_status_id',
        'status_id',
        'title',
        'description',
        'is_publish',
        'file_link'
    ];

    // Relationship to Research Files
    public function researchfiles()
    {
        return $this->hasMany(Researchfile::class, 'research_id');
    }

    // Relationship to SDG Sub Category
    public function sdgSubCategories()
    {
        return $this->belongsToMany(SdgSubCategory::class, 'research_sdg_sub_category', 'research_id', 'sdg_sub_category_id');
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

    // Relationship to Research Category
    public function researchCategory()
    {
        return $this->belongsTo(Researchcategory::class, 'researchcategory_id');
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
        return $this->belongsToMany(Feedback::class, 'feedback_research', 'research_id', 'feedback_id');
    }

    // Relationship to SDG
    public function sdg()
    {
        return $this->belongsToMany(Sdg::class, 'research_sdg', 'research_id', 'sdg_id');
    }

    // Relationship to GenderImpact
    public function genderImpact()
    {
        return $this->hasOne(GenderImpact::class);
    }
}
