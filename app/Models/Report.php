<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Research; 
use App\Models\Project;  
use App\Models\Sdg;

class Report extends Model
{
    use HasFactory;


    public const Published = 1;
    public const Draft = 0;

    protected $fillable = [
        'reportimg_id',
        'title',
        'description',
        'is_publish',
        'related_type',
        'related_id',
        'related_title',
        'review_status',
        'user_id',
        'feedback_id',
        'review_status_id' // Added feedback_id field
    ];

    public function reportimg()
    {
        return $this->belongsTo(Reportimg::class);
    }
    
    public function roleActions()
    {
        return $this->morphMany(RoleAction::class, 'content');
    }


    public function sdg()
    {
        return $this->belongsToMany(Sdg::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbacks()
    {
        return $this->belongsToMany(Feedback::class, 'feedback_report', 'report_id', 'feedback_id');
    }

    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class, 'review_status_id');
    }


}
