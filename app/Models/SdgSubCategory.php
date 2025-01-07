<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SdgSubCategory extends Model
{
    use HasFactory;

    protected $table = "sdg_sub_categories";

    protected $fillable = [
        'sub_category_name', 
        'sub_category_description', 
        'sdg_id', 
        'is_active'
    ];

    /**
     * Relationship: Parent SDG category.
     */
    public function sdg()
    {
        return $this->belongsTo(Sdg::class, 'sdg_id');
    }

    /**
     * Relationship: Projects under this SDG subcategory.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_sdg_sub_category', 'sdg_sub_category_id', 'project_id');
    }


    /**
     * Relationship: Research under this SDG subcategory.
     */
    public function research()
    {
        return $this->belongsToMany(Research::class, 'research_sdg_sub_category', 'sdg_sub_category_id', 'research_id');
    }
    
}
