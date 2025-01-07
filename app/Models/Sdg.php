<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sdg extends Model
{
    use HasFactory;

        protected $fillable = [ 'name','sdgimg_id'];

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
