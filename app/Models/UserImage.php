<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserImage extends Model
{
    use HasFactory;

    protected $table = 'user_images'; // Table name
    public $userUploads = '/images/users/'; // Base directory for user images

    // Define fillable fields for mass assignment
    protected $fillable = ['user_id', 'image_path'];

    /**
     * Accessor for image path.
     * This ensures that the correct URL is returned for the image_path field.
     */
    protected function imagePath(): Attribute
{
    return Attribute::make(
        get: fn($value) => $value ? asset('images/users/' . $value) : null
    );
}


    /**
     * Relationship: Each image belongs to a single user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function existsOnDisk(): bool
{
    $filePath = public_path('images/users/' . basename($this->image_path));
    return file_exists($filePath);
}
}
