<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewStatus extends Model
{
    use HasFactory;

    protected $table = 'review_statuses';

    // If the table doesn't have timestamps, disable it
    public $timestamps = true;

    // Define fillable fields if needed
    protected $fillable = ['status'];

    // Define relationships with other tables
    public function projects()
    {
        return $this->hasMany(Project::class, 'review_status_id');
    }

    public function research()
    {
        return $this->hasMany(Research::class, 'review_status_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'review_status_id');
    }
}
