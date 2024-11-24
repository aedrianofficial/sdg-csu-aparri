<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Reportimg extends Model
{
    use HasFactory;


    protected $fillable=['image'];

    // Ensure the accessor correctly transforms the image path
    protected function image(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) {
                    // Detect the MIME type of the image for the data URI
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_buffer($finfo, $value);
                    finfo_close($finfo);

                    // Encode as base64 with the detected MIME type
                    return 'data:' . $mimeType . ';base64,' . base64_encode($value);
                }
                return null;
            },
        );
    }
}
