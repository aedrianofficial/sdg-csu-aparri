<?php

namespace App\Http\Controllers;

use App\Models\SdgSubCategory;
use Illuminate\Http\Request;

class SdgController extends Controller
{
    //
    public function getSubCategories(Request $request)
    {
        $sdgIds = $request->sdg_ids;

        $subCategories = SdgSubCategory::whereIn('sdg_id', $sdgIds)->get();

        return response()->json($subCategories);
    }
}
