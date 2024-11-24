<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public const Published = 1;
    public const Draft = 0;

    protected $fillable=['projectimg_id',
                         'title',
                         'review_status_id',
                         'description',
                         'is_publish',
                         'project_status',
                         'user_id',
                         'latitude',
                         'longitude',
                        'location_address'];

    public function projectimg(){
        return $this->belongsTo(Projectimg::class);
    }

    public function roleActions()
    {
        return $this->morphMany(RoleAction::class, 'content');
    }
    
    public function sdg(){
        return $this->belongsToMany(Sdg::class);
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
        return $this->belongsToMany(Feedback::class, 'feedback_project', 'project_id', 'feedback_id');
    }

    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class, 'review_status_id');
    }
}
