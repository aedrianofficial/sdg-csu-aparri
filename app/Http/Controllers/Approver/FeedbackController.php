<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function approve_project(Request $request)
{
    // Validate the form input
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'feedback' => 'nullable|string|max:2000', // Feedback is optional for approval
        'review_status' => 'required|in:Needs Changes,Rejected',
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Check if there's any feedback, create a feedback entry if provided
    if (!empty($validated['feedback'])) {
        $feedback = Feedback::create([
            'feedback' => $validated['feedback'],
            'users_id' => $user->id,  // Authenticated user's ID
        ]);

        // Find the project and attach feedback to it
        $project = Project::findOrFail($validated['project_id']);
        $project->feedbacks()->attach($feedback->id);
    }

    $project = Project::findOrFail($validated['project_id']);
    $project->update([
        'review_status' => $validated['review_status'],
        // 'is_publish' => true, // Optionally set project to 'Published'
    ]);

    // Redirect back with a success message
    return redirect()->back()->with('alert-success', 'Feedback submitted successfully.');
}

public function approve_report(Request $request)
{
    $validated = $request->validate([
        'report_id' => 'required|exists:reports,id',
        'feedback' => 'nullable|string|max:2000', 
        'review_status' => 'required|string|in:Needs Changes,Rejected',
    ]);

    $report = Report::findOrFail($validated['report_id']);

    // Create feedback entry in the database
    $feedback = Feedback::create([
        'feedback' => $validated['feedback'],
        'users_id' => auth()->id(), // Assuming this is the reviewer
    ]);

    // Attach the feedback to the report
    $report->feedbacks()->attach($feedback->id);

    // Update report status or any other related operations
    $report->update([
        'review_status' => $validated['review_status'],
    ]);

    return redirect()->route('approver.reports.show', $report->id)
                     ->with('alert-success', 'Feedback has been submitted.');
}
public function approve_research(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'research_id' => 'required|exists:research,id',
            'feedback' => 'nullable|string|max:2000',
            'review_status' => 'required|in:Needs Changes,Rejected',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Create the feedback entry
        $feedback = Feedback::create([
            'feedback' => $validated['feedback'],
            'users_id' => $user->id,  // Authenticated user's ID
        ]);

        // Find the research and attach feedback to it
        $research = Research::findOrFail($validated['research_id']);
        $research->feedbacks()->attach($feedback->id);

        // Update the research's review status
        $research->update([
            'review_status' => $validated['review_status'],
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('alert-success', 'Feedback submitted successfully.');
    }







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
