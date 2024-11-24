<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Researchcategory extends Model
{
    use HasFactory;

    protected $fillable=['name'];
    public function research() {
        return $this->hasMany(Research::class, 'researchcategory_id');
    }
    
}
