<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Project;
use App\Models\Report;
use App\Models\Research;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class FeedbackController extends Controller
{

    


    public function review_project(Request $request)
    {
    // Validate the form input
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'feedback' => 'required|string|max:2000',
        'review_status' => 'required|in:Needs Changes,Rejected',
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Create the feedback entry
    $feedback = Feedback::create([
        'feedback' => $validated['feedback'],
        'users_id' => $user->id,  // Authenticated user's ID
    ]);

    // Find the project and attach feedback to it
    $project = Project::findOrFail($validated['project_id']);
    $project->feedbacks()->attach($feedback->id);

    // Update the project's review status
    $project->update([
        'review_status' => $validated['review_status'],
    ]);

    // Redirect back with a success message
    return redirect()->back()->with('alert-success', 'Feedback submitted successfully   .');
    
}

public function review_report(Request $request)
{
    $validated = $request->validate([
        'report_id' => 'required|exists:reports,id',
        'feedback' => 'required|string',
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

    return redirect()->route('reviewer.reports.show', $report->id)
                     ->with('success', 'Feedback has been submitted.');
}

public function review_research(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'research_id' => 'required|exists:research,id',
            'feedback' => 'required|string|max:2000',
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

}
