<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sdg extends Model
{
    use HasFactory;

    protected $table = 'sdgs';
    
    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color'
    ];

    /**
     * Get the subcategories related to this SDG
     */
    public function subcategories()
    {
        return $this->hasMany(SdgSubCategory::class, 'sdg_id');
    }

    public function report()
    {
        return $this->belongsToMany(Report::class);
    }
    

    public function project()
    {
        return $this->belongsToMany(Project::class);
    }

    
    public function research()
    {
        return $this->belongsToMany(Research::class);
    }

    public function sdgimage()
    {
        return $this->belongsTo(Sdgimg::class,'sdgimg_id');
    }
    

}
