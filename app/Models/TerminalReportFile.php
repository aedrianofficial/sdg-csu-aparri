<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminalReportFile extends Model
{
    use HasFactory;

    protected $table = "terminal_report_files"; // Specify the table name
    protected $fillable = ['terminal_report_id', 'file', 'original_filename', 'extension'];

    // Accessor and mutator for the 'file' attribute
    protected function file(): Attribute
    {
        return Attribute::make(
            get: fn($file) => $file, // Retrieve binary data directly
            set: fn($file) => is_file($file) ? file_get_contents($file) : $file // Store binary data only if it's a file
        );
    }

    // Define the relationship to the TerminalReport model
    public function terminalReport()
    {
        return $this->belongsTo(TerminalReport::class);
    }
}
