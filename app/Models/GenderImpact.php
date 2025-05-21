<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenderImpact extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_id',
        'project_id',
        'benefits_men',
        'benefits_women',
        'benefits_all',
        'addresses_gender_inequality',
        'men_count',
        'women_count',
        'gender_notes',
    ];

    protected $casts = [
        'benefits_men' => 'boolean',
        'benefits_women' => 'boolean',
        'benefits_all' => 'boolean',
        'addresses_gender_inequality' => 'boolean',
        'men_count' => 'integer',
        'women_count' => 'integer',
    ];

    /**
     * Get the research that owns the gender impact assessment.
     */
    public function research()
    {
        return $this->belongsTo(Research::class);
    }
    
    /**
     * Get the project that owns the gender impact assessment.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
