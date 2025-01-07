<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingAgency extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency',
        'is_active',
    ];

    /**
     * Relationship: Funding Agency's associated terminal reports.
     */
    public function terminalReports()
    {
        return $this->hasMany(TerminalReport::class, 'funding_agency_id');
    }
}
