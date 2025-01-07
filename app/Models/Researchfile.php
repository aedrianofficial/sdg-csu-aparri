<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Researchfile extends Model
{
    use HasFactory;

    protected $table = 'researchfiles';
    protected $fillable = ['research_id', 'file','original_filename','extension'];

    protected function file(): Attribute
    {
        return Attribute::make(
            get: fn($file) => $file, // Retrieve binary data directly
            set: fn($file) => is_file($file) ? file_get_contents($file) : $file // Store binary data only if it's a file
        );
    }

    public function research()
    {
        return $this->belongsTo(Research::class);
    }
}
