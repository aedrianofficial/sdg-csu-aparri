<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sdgimg extends Model
{
    use HasFactory;
    public $sdgUploads ='/images/sdg/landing/';

    protected $fillable=['image'];

    protected function image(): Attribute{
        return Attribute::make(
            get:fn($image)=> $this->sdgUploads.$image, 
        );
    }
}
