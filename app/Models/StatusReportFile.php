<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusReportFile extends Model
{
    use HasFactory;
    protected $table = "status_report_files";
    protected $fillable = ['status_report_id', 'file', 'original_filename', 'extension'];

      // Accessor and mutator for the 'file' attribute
      protected function file(): Attribute
      {
          return Attribute::make(
              get: fn($file) => $file, // Retrieve binary data directly
              set: fn($file) => is_file($file) ? file_get_contents($file) : $file // Store binary data only if it's a file
          );
      }
    public function statusReport()
    {
        return $this->belongsTo(StatusReport::class);
    }
}
