<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Researcher extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'role',
    ];

    /**
     * Relationship: Researcher's associated terminal reports.
     */
    public function terminalReports()
    {
        return $this->belongsToMany(TerminalReport::class, 'terminal_report_researcher', 'researcher_id', 'terminal_report_id');
    }       
}
