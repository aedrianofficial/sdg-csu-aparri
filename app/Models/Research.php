<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use HasFactory;


    public const Published = 1;
    public const Draft = 0;

    protected $fillable = ['researchcategory_id',
                           'title', 
                           'description', 
                           'is_publish',
                           'research_status',
                           'user_id',
                           'review_status_id'];

    public function researchfiles()
    {
        return $this->hasMany(Researchfile::class, 'research_id');
    }
    
    public function roleActions()
    {
        return $this->morphMany(RoleAction::class, 'content');
    }


    public function sdg()
    {
        return $this->belongsToMany(Sdg::class);
    }

    public function researchcategory()
    {
        return $this->belongsTo(Researchcategory::class, 'researchcategory_id');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'related');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbacks()
    {
        return $this->belongsToMany(Feedback::class, 'feedback_research', 'research_id', 'feedback_id');
    }

    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class, 'review_status_id');
    }
}
