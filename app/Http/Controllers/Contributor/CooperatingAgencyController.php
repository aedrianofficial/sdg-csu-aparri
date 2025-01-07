<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\CooperatingAgency;
use Illuminate\Http\Request;

class CooperatingAgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'agency' => 'required|string|max:255',
        ]);
    
        $agency = CooperatingAgency::create([
            'agency' => $request->agency,
            'is_active' => 1, // Assuming new agencies are active by default
        ]);
    
        // Return JSON response with success message
        return response()->json([
            'id' => $agency->id,
            'agency' => $agency->agency,
            'message' => 'Cooperating Agency "' . $agency->agency . '" added successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
