<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CooperatingAgency extends Model
{
    use HasFactory;
    protected $fillable = [
        'agency',
        'is_active',
    ];

    /**
     * Relationship: Cooperating Agency's associated terminal reports.
     */
    public function terminalReports()
    {
        return $this->hasMany(TerminalReport::class, 'cooperating_agency_id');
    }
}
