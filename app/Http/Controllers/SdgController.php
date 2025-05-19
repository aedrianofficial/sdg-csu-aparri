<?php

namespace App\Http\Controllers;

use App\Models\Sdg;
use App\Models\SdgSubCategory;
use Illuminate\Http\Request;

class SdgController extends Controller
{
    /**
     * Get subcategories for selected SDGs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSubcategories(Request $request)
    {
        // Validate request
        $request->validate([
            'sdg_ids' => 'required|array',
            'sdg_ids.*' => 'exists:sdgs,id'
        ]);
        
        // Get the subcategories for the selected SDGs
        $subcategories = SdgSubCategory::whereIn('sdg_id', $request->sdg_ids)
            ->orderBy('sub_category_name')
            ->get();
            
        return response()->json($subcategories);
    }
}
