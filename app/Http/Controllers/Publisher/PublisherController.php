<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Fetch projects from the database
    $projects = Project::where('is_publish', 1)
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get(['id','title', 'location_address', 'latitude', 'longitude']);

    // Return the publisher dashboard view with the projects data
    return view('publisher.dashboard', compact('projects'));
}

public function activity_logs(Request $request)
    {
        $userId = auth()->user()->id;

        // Initialize query for activity logs
        $query = ActivityLog::where('causer_id', $userId);

        // Apply filters if present
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('type')) {
            // Assuming subject_type is stored as the full class name
            $query->where('subject_type', 'App\Models\\' . $request->type);
        }

        // Apply search filter for description
        if ($request->filled('title')) {
            $query->where('description', 'like', '%' . $request->title . '%');
        }

        // Retrieve activity logs for the authenticated user with pagination
        $activityLogs = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('publisher.activity_logs.index', compact('activityLogs'));
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
        //
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
