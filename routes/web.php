<?php

use App\Http\Controllers\Analytics;
use App\Http\Controllers\Approver\ApproverController;
use App\Http\Controllers\Approver\FeedbackController as ApproverFeedbackController;
use App\Http\Controllers\Approver\ProfileController as ApproverProfileController;
use App\Http\Controllers\Approver\ProjectController as ApproverProjectController;
use App\Http\Controllers\Approver\ReportController as ApproverReportController;
use App\Http\Controllers\Approver\ResearchController as ApproverResearchController;
use App\Http\Controllers\Approver\StatusReportController as ApproverStatusReportController;
use App\Http\Controllers\Approver\TerminalReportController as ApproverTerminalReportController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\CooperatingAgencyController;
use App\Http\Controllers\Auth\FundingAgencyController;
use App\Http\Controllers\Auth\HomeController as AuthHomeController;
use App\Http\Controllers\Auth\ProfileController as AuthProfileController;
use App\Http\Controllers\Auth\ProjectController;
use App\Http\Controllers\Auth\ReportController;
use App\Http\Controllers\Auth\ResearchController;
use App\Http\Controllers\Auth\ResearcherController;
use App\Http\Controllers\Auth\StatusReportController;
use App\Http\Controllers\Auth\TerminalReportController;
use App\Http\Controllers\Contributor\ContributorController;
use App\Http\Controllers\Contributor\CooperatingAgencyController as ContributorCooperatingAgencyController;
use App\Http\Controllers\Contributor\FundingAgencyController as ContributorFundingAgencyController;
use App\Http\Controllers\Contributor\ProfileController as ContributorProfileController;
use App\Http\Controllers\Contributor\ProjectController as ContributorProjectController;
use App\Http\Controllers\Contributor\ReportController as ContributorReportController;
use App\Http\Controllers\Contributor\ResearchController as ContributorResearchController;
use App\Http\Controllers\Contributor\ResearcherController as ContributorResearcherController;
use App\Http\Controllers\Contributor\StatusReportController as ContributorStatusReportController;
use App\Http\Controllers\Contributor\TerminalReportController as ContributorTerminalReportController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Publisher\ProfileController as PublisherProfileController;
use App\Http\Controllers\Publisher\ProjectController as PublisherProjectController;
use App\Http\Controllers\Publisher\PublisherController;
use App\Http\Controllers\Publisher\ReportController as PublisherReportController;
use App\Http\Controllers\Publisher\ResearchController as PublisherResearchController;
use App\Http\Controllers\Publisher\StatusReportController as PublisherStatusReportController;
use App\Http\Controllers\Publisher\TerminalReportController as PublisherTerminalReportController;
use App\Http\Controllers\Reviewer\FeedbackController as ReviewerFeedbackController;
use App\Http\Controllers\Reviewer\ProfileController as ReviewerProfileController;
use App\Http\Controllers\Reviewer\ProjectController as ReviewerProjectController;
use App\Http\Controllers\Reviewer\ReportController as ReviewerReportController;
use App\Http\Controllers\Reviewer\ResearchController as ReviewerResearchController;
use App\Http\Controllers\Reviewer\ReviewerController;
use App\Http\Controllers\Reviewer\StatusReportController as ReviewerStatusReportController;
use App\Http\Controllers\Reviewer\TerminalReportController as ReviewerTerminalReportController;
use App\Http\Controllers\SdgController;
use App\Http\Controllers\Website\HomeController as WebsiteHomeController;
use App\Http\Controllers\Website\UserController;
use App\Http\Controllers\Website\WebsiteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\NoCache;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/reports/get-related-records', [ReportController::class, 'get_related_records'])->name('reports.get_related_records');

route::get('/analytics/review-status', [Analytics::class, 'reviewStatusAnalytics'])->name('analytics.reviewStatusAnalytics');
Route::get('/analytics/my-status', [Analytics::class, 'myStatusAnalytics'])->name('analytics.myStatusAnalytics');
Route::get('/analytics/sdg-comparison', [Analytics::class, 'getSdgComparisonData'])->name('analytics.sdgComparison');
route::get('/analytics/my-review-activity', [Analytics::class, 'myReviewActivity'])->name('analytics.myReviewActivity');
route::get('/analytics/my-approval-activity', [Analytics::class, 'myApprovalActivity'])->name('analytics.myApprovalActivity');
route::get('/analytics/my-publish-activity', [Analytics::class, 'myPublishActivity'])->name('analytics.myPublishActivity');
Route::get('/analytics/sdg-line-chart', [Analytics::class, 'getSdgLineChartData'])->name('analytics.sdgLineChart');


Route::get('/research/{id}/file/download', [ResearchController::class, 'downloadFile'])->name('research.file.download');


// downloading the status report file
Route::get('/status-report/file/download/{id}', [StatusReportController::class, 'downloadFile'])->name('status.report.file.download');
// /downloading the terminal report file
Route::get('/terminal-report/file/download/{id}', [TerminalReportController::class, 'downloadFile'])->name('terminal.report.file.download');

Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

Route::get('/sdg/subcategories', [SdgController::class, 'getSubCategories'])->name('sdg.subcategories');

Route::get('/test-timezone', function () {
    return now(); // Should return the current time in Asia/Manila
});


//admin middleware
Route::middleware(['auth', 'role:admin', NoCache::class])->group(function(){

    //dashboard
    Route::get('auth/dashboard',[AdminController::class, 'index'])->name('auth.dashboard');

    Route::get('auth/my-activity-logs',[AdminController::class, 'my_activity_logs'])->name('auth.activity_logs.my_activity_logs');
    Route::get('auth/all-activity-logs',[AdminController::class, 'all_activity_logs'])->name('auth.activity_logs.all_activity_logs');

    //user-profile
    Route::get('auth/profile/show/{id}', [AuthProfileController::class, 'show'])->name('auth.profile.show');
    Route::get('auth/profile/{id}/edit', [AuthProfileController::class, 'edit'])->name('auth.profile.edit');
    Route::put('auth/profile/{user}/update', [AuthProfileController::class, 'update'])->name('auth.profile.update');
    //notifications
    Route::get('auth/notifications',[NotificationController::class, 'admin_notification'])->name('admin.notifications');
    
    //user-management
    Route::get('/auth/users', [UserController::class, 'index'])->name('users.index');
    Route::get('auth/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('auth/user/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('auth/user/update/{id}', [UserController::class, 'update'])->name('users.update');
    
    //projects
    Route::resource('auth/projects', ProjectController::class);
    Route::get('auth/my_projects/', [ProjectController::class, 'my_projects'])->name('projects.my_projects');
    Route::get('auth/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('auth/projects/{project}/update', [ProjectController::class, 'update'])->name('auth.projects.update');
    Route::get('auth/projects/show/{id}',[ProjectController::class, 'show'])->name('projects.show');
    Route::get('auth/projects/show/need-changes/{id}',[ProjectController::class, 'need_changes'])->name('projects.need_changes');
    Route::get('auth/projects/show/rejected/{id}',[ProjectController::class, 'rejected'])->name('projects.rejected');

    // Routes for adding new agencies and researchers
    Route::post('auth/cooperating_agencies/store', [CooperatingAgencyController::class, 'store'])->name('auth.cooperating_agencies.store');
    Route::post('auth/funding_agencies/store', [FundingAgencyController::class, 'store'])->name('auth.funding_agencies.store');
    Route::post('auth/researchers/store', [ResearcherController::class, 'store'])->name('auth.researchers.store');

    //status-reports Project
    Route::get('auth/status-reports/create-project', [StatusReportController::class, 'createProject'])->name('auth.status_reports.create_project');
    Route::post('auth/status-reports/store-project', [StatusReportController::class, 'storeProject'])->name('auth.status_reports.store_project');
    Route::get('auth/status-reports-project-published/{id}', [StatusReportController::class, 'showProjectPublished'])->name('auth.status_reports.show_project_published');

    // Terminal Reports Project
    Route::get('auth/terminal-reports/create-projects', [TerminalReportController::class, 'createProject'])->name('auth.terminal_reports.create_project');
    Route::post('auth/terminal-reports/store-projects', [TerminalReportController::class, 'storeProject'])->name('auth.terminal_reports.store_project');
    Route::get('auth/terminal-reports-project-published/{id}', [TerminalReportController::class, 'showProjectPublished'])->name('auth.terminal_reports.show_project_published');
    Route::get('auth/terminal-reports-project/{id}', [TerminalReportController::class, 'showProject'])->name('auth.terminal_reports.show_project');

    //status-reports Research
    Route::get('auth/status-reports/create-research', [StatusReportController::class, 'createResearch'])->name('auth.status_reports.create_research');
    Route::post('auth/status-reports/store-research', [StatusReportController::class, 'storeResearch'])->name('auth.status_reports.store_research');
    Route::get('auth/status-reports-research-published/{id}', [StatusReportController::class, 'showResearchPublished'])->name('auth.status_reports.show_research_published');

    // Terminal Reports Research
    Route::get('auth/terminal-reports/create-research', [TerminalReportController::class, 'createResearch'])->name('auth.terminal_reports.create_research');
    Route::post('auth/terminal-reports/store-research', [TerminalReportController::class, 'storeResearch'])->name('auth.terminal_reports.store_research');
    Route::get('auth/terminal-reports-research-published/{id}', [TerminalReportController::class, 'showResearchPublished'])->name('auth.terminal_reports.show_research_published');
    Route::get('auth/terminal-reports-research/{id}', [TerminalReportController::class, 'showResearch'])->name('auth.terminal_reports.show_research');

    //terminal-reports
    Route::get('auth/terminal-reports', [TerminalReportController::class, 'index'])->name('auth.terminal_reports.index');
    Route::get('auth/my-terminal-reports', [TerminalReportController::class, 'my_reports'])->name('auth.terminal_reports.my_reports');
    Route::get('auth/terminal-reports/{id}/edit', [TerminalReportController::class, 'edit'])->name('auth.terminal_reports.edit');
    Route::put('auth/terminal-reports/{terminalReport}', [TerminalReportController::class, 'update'])->name('auth.terminal_reports.update');
    Route::get('auth/terminal-reports/projects/{id}/need-changes', [TerminalReportController::class, 'showProjectNeedChanges'])->name('auth.terminal_reports.projects.need_changes');
    Route::get('auth/terminal-reports/projects/{id}/rejected', [TerminalReportController::class, 'showProjectRejected'])->name('auth.terminal_reports.projects.rejected');
    Route::get('auth/terminal-reports/research/{id}/need-changes', [TerminalReportController::class, 'showResearchNeedChanges'])->name('auth.terminal_reports.research.need_changes');
    Route::get('auth/terminal-reports/research/{id}/rejected', [TerminalReportController::class, 'showResearchRejected'])->name('auth.terminal_reports.research.rejected');

    //status-reports
    Route::get('auth/status-reports', [StatusReportController::class, 'index'])->name('auth.status_reports.index');
    Route::get('auth/my-status-reports', [StatusReportController::class, 'my_reports'])->name('auth.status_reports.my_reports');
    Route::get('auth/status-reports/{id}/edit', [StatusReportController::class, 'edit'])->name('auth.status_reports.edit');
    Route::put('auth/status-reports/{statusReport}', [StatusReportController::class, 'update'])->name('auth.status_reports.update');
    Route::get('auth/status-reports/projects/{id}/need-changes', [StatusReportController::class, 'showProjectNeedChanges'])
    ->name('auth.status_reports.projects.need_changes');
    Route::get('auth/status-reports/projects/{id}/rejected', [StatusReportController::class, 'showProjectRejected'])
        ->name('auth.status_reports.projects.rejected');
    Route::get('auth/status-reports/research/{id}/need-changes', [StatusReportController::class, 'showResearchNeedChanges'])
        ->name('auth.status_reports.research.need_changes');
    Route::get('auth/status-reports/research/{id}/rejected', [StatusReportController::class, 'showResearchRejected'])
        ->name('auth.status_reports.research.rejected');

    //reports
    Route::resource('auth/reports', ReportController::class);
    Route::get('auth/my_reports/', [ReportController::class, 'my_reports'])->name('reports.my_reports');
    Route::get('auth/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('auth/reports/{report}/update', [ReportController::class, 'update'])->name('reports.update');
    Route::get('auth/reports/show/{id}',[ReportController::class, 'show'])->name('reports.show');
    Route::get('auth/reports/show/need-changes/{id}',[ReportController::class, 'need_changes'])->name('reports.need_changes');
    Route::get('auth/reports/show/rejected/{id}',[ReportController::class, 'rejected'])->name('reports.rejected');

    //research
    Route::resource('auth/research', ResearchController::class);
    Route::get('auth/my_research/', [ResearchController::class, 'my_research'])->name('research.my_research');
    Route::get('auth/research/{research}/edit', [ResearchController::class, 'edit'])->name('research.edit');
    Route::put('auth/research/{research}/update', [ResearchController::class, 'update'])->name('research.update');
    Route::get('auth/research/show/{id}',[ResearchController::class, 'show'])->name('research.show');
    Route::get('auth/research/show/need-changes/{id}',[ResearchController::class, 'need_changes'])->name('research.need_changes');
    Route::get('auth/research/show/rejected/{id}',[ResearchController::class, 'rejected'])->name('research.rejected');
});



//contributor
Route::middleware(['auth', 'role:contributor', NoCache::class])->group(function(){

    //contributor dashboard
    Route::get('contributor/dashboard',[ContributorController::class, 'index'])->name('contributor.dashboard');
     //contributor notifications
     Route::get('contributor/notifications',[NotificationController::class, 'contributor_notification'])->name('contributor.notifications');
     Route::get('contributor/activity-logs',[ContributorController::class, 'activity_logs'])->name('contributor.activity_logs');
    
    Route::get('contributor/profile/show/{id}', [ContributorProfileController::class, 'show'])->name('contributor.profile.show');
    Route::get('contributor/profile/{id}/edit', [ContributorProfileController::class, 'edit'])->name('contributor.profile.edit');
    Route::put('contributor/profile/{user}/update', [ContributorProfileController::class, 'update'])->name('contributor.profile.update');

    // Routes for adding new agencies and researchers
    Route::post('contributor/cooperating_agencies/store', [ContributorCooperatingAgencyController::class, 'store'])->name('contributor.cooperating_agencies.store');
    Route::post('contributor/funding_agencies/store', [ContributorFundingAgencyController::class, 'store'])->name('contributor.funding_agencies.store');
    Route::post('contributor/researchers/store', [ContributorResearcherController::class, 'store'])->name('contributor.researchers.store');

    //** status-reports Project
    Route::get('contributor/status-reports/create-project', [ContributorStatusReportController::class, 'createProject'])->name('contributor.status_reports.create_project');
    Route::post('contributor/status-reports/store-project', [ContributorStatusReportController::class, 'storeProject'])->name('contributor.status_reports.store_project');
    Route::get('contributor/status-reports-project-published/{id}', [ContributorStatusReportController::class, 'showProjectPublished'])->name('contributor.status_reports.show_project_published');

    //** Terminal Reports Project
    Route::get('contributor/terminal-reports/create-projects', [ContributorTerminalReportController::class, 'createProject'])->name('contributor.terminal_reports.create_project');
    Route::post('contributor/terminal-reports/store-projects', [ContributorTerminalReportController::class, 'storeProject'])->name('contributor.terminal_reports.store_project');
    Route::get('contributor/terminal-reports-project-published/{id}', [ContributorTerminalReportController::class, 'showProjectPublished'])->name('contributor.terminal_reports.show_project_published');
    Route::get('contributor/terminal-reports-project/{id}', [ContributorTerminalReportController::class, 'showProject'])->name('contributor.terminal_reports.show_project');

    //**status-reports Research
    Route::get('contributor/status-reports/create-research', [ContributorStatusReportController::class, 'createResearch'])->name('contributor.status_reports.create_research');
    Route::post('contributor/status-reports/store-research', [ContributorStatusReportController::class, 'storeResearch'])->name('contributor.status_reports.store_research');
    Route::get('contributor/status-reports-research-published/{id}', [ContributorStatusReportController::class, 'showResearchPublished'])->name('contributor.status_reports.show_research_published');

    //** Terminal Reports Research
    Route::get('contributor/terminal-reports/create-research', [ContributorTerminalReportController::class, 'createResearch'])->name('contributor.terminal_reports.create_research');
    Route::post('contributor/terminal-reports/store-research', [ContributorTerminalReportController::class, 'storeResearch'])->name('contributor.terminal_reports.store_research');
    Route::get('contributor/terminal-reports-research-published/{id}', [ContributorTerminalReportController::class, 'showResearchPublished'])->name('contributor.terminal_reports.show_research_published');
    Route::get('contributor/terminal-reports-research/{id}', [ContributorTerminalReportController::class, 'showResearch'])->name('contributor.terminal_reports.show_research');


    //terminal-reports
    Route::get('contributor/my-terminal-reports', [ContributorTerminalReportController::class, 'my_reports'])->name('contributor.terminal_reports.my_reports');
    Route::get('contributor/my-terminal-reports/need-changes', [ContributorTerminalReportController::class, 'need_changes'])->name('contributor.terminal_reports.need_changes');
    Route::get('contributor/my-terminal-reports/rejected', [ContributorTerminalReportController::class, 'rejected'])->name('contributor.terminal_reports.rejected');
    Route::get('contributor/terminal-reports/{id}/edit', [ContributorTerminalReportController::class, 'edit'])->name('contributor.terminal_reports.edit');
    Route::put('contributor/terminal-reports/{terminalReport}', [ContributorTerminalReportController::class, 'update'])->name('contributor.terminal_reports.update');
    Route::get('contributor/terminal-reports/projects/{id}/need-changes', [ContributorTerminalReportController::class, 'showProjectNeedChanges'])->name('contributor.terminal_reports.projects.need_changes');
    Route::get('contributor/terminal-reports/projects/{id}/rejected', [ContributorTerminalReportController::class, 'showProjectRejected'])->name('contributor.terminal_reports.projects.rejected');
    Route::get('contributor/terminal-reports/research/{id}/need-changes', [ContributorTerminalReportController::class, 'showResearchNeedChanges'])->name('contributor.terminal_reports.research.need_changes');
    Route::get('contributor/terminal-reports/research/{id}/rejected', [ContributorTerminalReportController::class, 'showResearchRejected'])->name('contributor.terminal_reports.research.rejected');

    //status-reports
    Route::get('contributor/my-status-reports', [ContributorStatusReportController::class, 'my_reports'])->name('contributor.status_reports.my_reports');

    Route::get('contributor/my-status-reports/need-changes', [ContributorStatusReportController::class, 'need_changes'])->name('contributor.status_reports.need_changes');
    Route::get('contributor/my-status-reports/rejected', [ContributorStatusReportController::class, 'rejected'])->name('contributor.status_reports.rejected');

    Route::get('contributor/status-reports/{id}/edit', [ContributorStatusReportController::class, 'edit'])->name('contributor.status_reports.edit');
    Route::put('contributor/status-reports/{statusReport}', [ContributorStatusReportController::class, 'update'])->name('contributor.status_reports.update');
    Route::get('contributor/status-reports/projects/{id}/need-changes', [ContributorStatusReportController::class, 'showProjectNeedChanges'])
    ->name('contributor.status_reports.projects.need_changes');
    Route::get('contributor/status-reports/projects/{id}/rejected', [ContributorStatusReportController::class, 'showProjectRejected'])
        ->name('contributor.status_reports.projects.rejected');
    Route::get('contributor/status-reports/research/{id}/need-changes', [ContributorStatusReportController::class, 'showResearchNeedChanges'])
        ->name('contributor.status_reports.research.need_changes');
    Route::get('contributor/status-reports/research/{id}/rejected', [ContributorStatusReportController::class, 'showResearchRejected'])
        ->name('contributor.status_reports.research.rejected');



    //projects
    Route::get('contributor/projects/index',[ContributorProjectController::class, 'index'])->name('contributor.projects.index');
    Route::get('contributor/projects/create',[ContributorProjectController::class, 'create'])->name('contributor.projects.create');
    Route::post('contributor/projects/store',[ContributorProjectController::class, 'store'])->name('contributor.projects.store');
    Route::get('contributor/projects/{project}/edit',[ContributorProjectController::class, 'edit'])->name('contributor.projects.edit');
    Route::put('auth/projects/{project}',[ContributorProjectController::class, 'update'])->name('contributor.projects.update');
    Route::get('contributor/projects/show/{id}',[ContributorProjectController::class, 'show'])->name('contributor.projects.show');
    Route::get('contributor/projects/destroy',[ContributorProjectController::class, 'destroy'])->name('contributor.projects.destroy');

    //request changes-project
    Route::get('contributor/projects/request_changes',[ContributorProjectController::class, 'request_changes'])->name('contributor.projects.request_changes');
    Route::get('contributor/projects/request_changes/show/{id}',[ContributorProjectController::class, 'show_request_changes'])->name('contributor.projects.request_changes.show');

    //rejected-project
    Route::get('contributor/projects/rejected',[ContributorProjectController::class, 'rejected'])->name('contributor.projects.rejected');
    Route::get('contributor/projects/rejected/show/{id}',[ContributorProjectController::class, 'show_rejected'])->name('contributor.projects.rejected.show');

    //reports
    Route::get('contributor/reports/index',[ContributorReportController::class, 'index'])->name('contributor.reports.index');
    Route::get('contributor/reports/create',[ContributorReportController::class, 'create'])->name('contributor.reports.create');
    Route::post('contributor/reports/store',[ContributorReportController::class, 'store'])->name('contributor.reports.store');
    Route::get('contributor/reports/{report}/edit',[ContributorReportController::class, 'edit'])->name('contributor.reports.edit');
    Route::put('auth/reports/{report}',[ContributorReportController::class, 'update'])->name('contributor.reports.update');
    Route::get('contributor/reports/show/{id}',[ContributorReportController::class, 'show'])->name('contributor.reports.show');
    Route::get('contributor/reports/destroy',[ContributorReportController::class, 'destroy'])->name('contributor.reports.destroy');
    //request changes
    Route::get('contributor/reports/request_changes',[ContributorReportController::class, 'request_changes'])->name('contributor.reports.request_changes');
    Route::get('contributor/reports/request_changes/show/{id}',[ContributorReportController::class, 'show_request_changes'])->name('contributor.reports.request_changes.show');
    //rejected
    Route::get('contributor/reports/rejected',[ContributorReportController::class, 'rejected'])->name('contributor.reports.rejected');
    Route::get('contributor/reports/rejected/show/{id}',[ContributorReportController::class, 'show_rejected'])->name('contributor.reports.rejected_show');


    //research
    Route::get('contributor/research/index',[ContributorResearchController::class, 'index'])->name('contributor.research.index');
    Route::get('contributor/research/create',[ContributorResearchController::class, 'create'])->name('contributor.research.create');
    Route::post('contributor/research/store',[ContributorResearchController::class, 'store'])->name('contributor.research.store');
    Route::get('contributor/research/{research}/edit',[ContributorResearchController::class, 'edit'])->name('contributor.research.edit');
    Route::put('auth/research/{research}',[ContributorResearchController::class, 'update'])->name('contributor.research.update');
    Route::get('contributor/research/show/{id}',[ContributorResearchController::class, 'show'])->name('contributor.research.show');
    Route::get('contributor/research/destroy',[ContributorResearchController::class, 'destroy'])->name('contributor.research.destroy');
    //request changes
    Route::get('contributor/research/request_changes',[ContributorResearchController::class, 'request_changes'])->name('contributor.research.request_changes');
    Route::get('contributor/research/request_changes/show/{id}',[ContributorResearchController::class, 'show_request_changes'])->name('contributor.research.request_changes.show');
    //rejected
    Route::get('contributor/research/rejected',[ContributorResearchController::class, 'rejected'])->name('contributor.research.rejected');
    Route::get('contributor/research/rejected/show/{id}',[ContributorResearchController::class, 'show_rejected'])->name('contributor.research.rejected.show');

    
});

    //reviewer
Route::middleware(['auth', 'role:reviewer', NoCache::class])->group(function(){

    Route::get('reviewer/profile/show/{id}', [ReviewerProfileController::class, 'show'])->name('reviewer.profile.show');
    Route::get('reviewer/profile/{id}/edit', [ReviewerProfileController::class, 'edit'])->name('reviewer.profile.edit');
    Route::put('reviewer/profile/{user}/update', [ReviewerProfileController::class, 'update'])->name('reviewer.profile.update');
    Route::get('reviewer/notifications',[NotificationController::class, 'reviewer_notification'])->name('reviewer.notifications');
    Route::get('reviewer/activity-logs',[ReviewerController::class, 'activity_logs'])->name('reviewer.activity_logs');

    //dashboard
    Route::get('reviewer/dashboard',[ReviewerController::class, 'index'])->name('reviewer.dashboard');

    //project/program
    //under review list
    Route::get('reviewer/projects/under_review',[ReviewerProjectController::class, 'under_review'])->name('reviewer.projects.under_review');
    Route::get('reviewer/project/show/{id}',[ReviewerProjectController::class, 'show'])->name('reviewer.projects.show');
    Route::post('reviewer/project/need-changes', [ReviewerProjectController::class, 'need_changes'])->name('reviewer.projects.needchanges');
    Route::post('reviewer/project/reject', [ReviewerProjectController::class, 'reject_project'])->name('reviewer.projects.reject');
    Route::put('reviewer/project/{project}/reviewed', [ReviewerProjectController::class, 'reviewed'])
    ->name('reviewer.projects.reviewed');
    Route::get('reviewer/project/reviewed-list', [ReviewerProjectController::class, 'reviewed_list'])
    ->name('reviewer.projects.reviewed_list');
    //show reviewed
    Route::get('reviewer/project/show/reviewed/{id}',[ReviewerProjectController::class, 'show_reviewed'])->name('reviewer.projects.show_reviewed');
    
     //need changes list
    Route::get('reviewer/projects/need-changes',[ReviewerProjectController::class, 'need_changes_list'])->name('reviewer.projects.needchanges_list');
    //show feedback for changes
    Route::get('reviewer/project/show/need-changes/feedback/{id}',[ReviewerProjectController::class, 'show_feedback_changes'])->name('reviewer.projects.feedback_changes');
    //show feedback for rejected
    Route::get('reviewer/project/show/rejected/feedback/{id}',[ReviewerProjectController::class, 'show_feedback_rejected'])->name('reviewer.projects.feedback_rejected');
    //rejected list
    Route::get('reviewer/project/rejected', [ReviewerProjectController::class, 'rejected_list'])->name('reviewer.projects.rejected');
    

    //status-reports
    //Under review status_reports list
    Route::get('reviewer/status-reports/under-review',[ReviewerStatusReportController::class, 'under_review'])->name('reviewer.status_reports.under_review');

    Route::get('reviewer/status-reports/show-project/{id}',[ReviewerStatusReportController::class, 'showProject'])->name('reviewer.status_reports.show_project');
    Route::get('reviewer/status-reports/show-research/{id}',[ReviewerStatusReportController::class, 'showResearch'])->name('reviewer.status_reports.show_research');

    Route::post('reviewer/status-reports/need-changes', [ReviewerStatusReportController::class, 'need_changes'])->name('reviewer.status_reports.needchanges');
    Route::post('reviewer/status-reports/reject', [ReviewerStatusReportController::class, 'reject_report'])->name('reviewer.status_reports.reject');
    Route::put('reviewer/status-reports/{statusReport}/reviewed', [ReviewerStatusReportController::class, 'reviewed'])
    ->name('reviewer.status_reports.reviewed');

    //show feedback for changes
    Route::get('reviewer/status-reports/show/project-need-changes/feedback/{id}',[ReviewerStatusReportController::class, 'showProjectNeedChanges'])->name('reviewer.status_reports.project_need_changes');
    Route::get('reviewer/status-reports/show/research-need-changes/feedback/{id}',[ReviewerStatusReportController::class, 'showResearchNeedChanges'])->name('reviewer.status_reports.research_need_changes');

    //need changes list
    Route::get('reviewer/status-reports/need-changes',[ReviewerStatusReportController::class, 'need_changes_list'])->name('reviewer.status_reports.needchanges_list');
    Route::get('reviewer/status-reports/reviewed-list', [ReviewerStatusReportController::class, 'reviewed_list'])
    ->name('reviewer.status_reports.reviewed_list');


    Route::get('reviewer/status-reports/show/reviewed-project/{id}',[ReviewerStatusReportController::class, 'showProjectReviewed'])->name('reviewer.status_reports.show_reviewed_project');
    Route::get('reviewer/status-reports/show/reviewed-research/{id}',[ReviewerStatusReportController::class, 'showResearchReviewed'])->name('reviewer.status_reports.show_reviewed_research');

    //show feedback for rejected status-report
    Route::get('reviewer/status-reports/show/project-rejected/feedback/{id}',[ReviewerStatusReportController::class, 'showProjectRejected'])->name('reviewer.status_reports.project_rejected');
    Route::get('reviewer/status-reports/show/research-rejected/feedback/{id}',[ReviewerStatusReportController::class, 'showResearchRejected'])->name('reviewer.status_reports.research_rejected');

    //rejected list
    Route::get('reviewer/status-reports/rejected', [ReviewerStatusReportController::class, 'rejected_list'])->name('reviewer.status_reports.rejected_list');



    //terminal-reports
    //Under review terminal_reports list
    Route::get('reviewer/terminal-reports/under-review',[ReviewerTerminalReportController::class, 'under_review'])->name('reviewer.terminal_reports.under_review');

    Route::get('reviewer/terminal-reports/show-project/{id}',[ReviewerTerminalReportController::class, 'showProject'])->name('reviewer.terminal_reports.show_project');
    Route::get('reviewer/terminal-reports/show-research/{id}',[ReviewerTerminalReportController::class, 'showResearch'])->name('reviewer.terminal_reports.show_research');

    Route::post('reviewer/terminal-reports/need-changes', [ReviewerTerminalReportController::class, 'need_changes'])->name('reviewer.terminal_reports.needchanges');
    Route::post('reviewer/terminal-reports/reject', [ReviewerTerminalReportController::class, 'reject_report'])->name('reviewer.terminal_reports.reject');
    Route::put('reviewer/terminal-reports/{terminalReport}/reviewed', [ReviewerTerminalReportController::class, 'reviewed'])
    ->name('reviewer.terminal_reports.reviewed');

    //show feedback for changes
    Route::get('reviewer/terminal-reports/show/project-need-changes/feedback/{id}',[ReviewerTerminalReportController::class, 'showProjectNeedChanges'])->name('reviewer.terminal_reports.project_need_changes');
    Route::get('reviewer/terminal-reports/show/research-need-changes/feedback/{id}',[ReviewerTerminalReportController::class, 'showResearchNeedChanges'])->name('reviewer.terminal_reports.research_need_changes');

    //need changes list
    Route::get('reviewer/terminal-reports/need-changes',[ReviewerTerminalReportController::class, 'need_changes_list'])->name('reviewer.terminal_reports.needchanges_list');
    Route::get('reviewer/terminal-reports/reviewed-list', [ReviewerTerminalReportController::class, 'reviewed_list'])
    ->name('reviewer.terminal_reports.reviewed_list');


    Route::get('reviewer/terminal-reports/show/reviewed-project/{id}',[ReviewerTerminalReportController::class, 'showProjectReviewed'])->name('reviewer.terminal_reports.show_reviewed_project');
    Route::get('reviewer/terminal-reports/show/reviewed-research/{id}',[ReviewerTerminalReportController::class, 'showResearchReviewed'])->name('reviewer.terminal_reports.show_reviewed_research');

    //show feedback for rejected terminal-report
    Route::get('reviewer/terminal-reports/show/project-rejected/feedback/{id}',[ReviewerTerminalReportController::class, 'showProjectRejected'])->name('reviewer.terminal_reports.project_rejected');
    Route::get('reviewer/terminal-reports/show/research-rejected/feedback/{id}',[ReviewerTerminalReportController::class, 'showResearchRejected'])->name('reviewer.terminal_reports.research_rejected');

    //rejected list
    Route::get('reviewer/terminal-reports/rejected', [ReviewerTerminalReportController::class, 'rejected_list'])->name('reviewer.terminal_reports.rejected_list');


    //reports
    //Under review report list
    Route::get('reviewer/reports/under_review',[ReviewerReportController::class, 'under_review'])->name('reviewer.reports.under_review');
    Route::get('reviewer/report/show/{id}',[ReviewerReportController::class, 'show'])->name('reviewer.reports.show');
    Route::post('reviewer/reports/need-changes', [ReviewerReportController::class, 'need_changes'])->name('reviewer.reports.needchanges');
    Route::post('reviewer/reports/reject', [ReviewerReportController::class, 'reject_report'])->name('reviewer.reports.reject');
    Route::put('reviewer/report/{report}/reviewed', [ReviewerReportController::class, 'reviewed'])
    ->name('reviewer.reports.reviewed');
    //show feedback for changes
    Route::get('reviewer/report/show/need-changes/feedback/{id}',[ReviewerReportController::class, 'show_feedback_changes'])->name('reviewer.reports.feedback_changes');
    //need changes list
    Route::get('reviewer/reports/need-changes',[ReviewerReportController::class, 'need_changes_list'])->name('reviewer.reports.needchanges_list');
    Route::get('reviewer/reports/reviewed-list', [ReviewerReportController::class, 'reviewed_list'])
    ->name('reviewer.reports.reviewed_list');
    Route::get('reviewer/report/show/reviewed/{id}',[ReviewerReportController::class, 'show_reviewed'])->name('reviewer.reports.show_reviewed');

    

    //show feedback for rejected report
    Route::get('reviewer/report/show/rejected/feedback/{id}',[ReviewerReportController::class, 'show_feedback_rejected'])->name('reviewer.reports.feedback_rejected');
    //rejected list
    Route::get('reviewer/report/rejected', [ReviewerReportController::class, 'rejected_list'])->name('reviewer.reports.rejected');

    //research
    //under_review research list
    Route::get('reviewer/research/under_review',[ReviewerResearchController::class, 'under_review'])->name('reviewer.research.under_review');
    Route::get('reviewer/research/show/{id}',[ReviewerResearchController::class, 'show'])->name('reviewer.research.show');
    Route::post('reviewer/research/need-changes', [ReviewerResearchController::class, 'need_changes'])->name('reviewer.research.needchanges');
    Route::post('reviewer/research/reject', [ReviewerResearchController::class, 'reject_research'])->name('reviewer.research.reject');
    route::post('reviewer/research/feedback/submit', [ReviewerFeedbackController::class, 'review_research'])->name('reviewer.research.feedback');
    Route::put('reviewer/research/{research}/reviewed', [ReviewerResearchController::class, 'reviewed'])
    ->name('reviewer.research.reviewed');
    Route::get('reviewer/research/reviewed-list', [ReviewerResearchController::class, 'reviewed_list'])
    ->name('reviewer.research.reviewed_list');
    
    //show feedback for changes
    Route::get('reviewer/research/show/need-changes/feedback/{id}',[ReviewerResearchController::class, 'show_feedback_changes'])->name('reviewer.research.feedback_changes');
     //need changes list
     Route::get('reviewer/research/need-changes',[ReviewerResearchController::class, 'need_changes_list'])->name('reviewer.research.needchanges_list');
    //show feedback for rejected
    Route::get('reviewer/research/show/rejected/feedback/{id}',[ReviewerResearchController::class, 'show_feedback_rejected'])->name('reviewer.research.feedback_rejected');
    //rejected list
    Route::get('reviewer/research/rejected', [ReviewerResearchController::class, 'rejected_list'])->name('reviewer.research.rejected');
    Route::get('reviewer/research/show/reviewed/{id}',[ReviewerResearchController::class, 'show_reviewed'])->name('reviewer.research.show_reviewed');
    
});

//approver
Route::middleware(['auth', 'role:approver', NoCache::class])->group(function(){
    
    //Dashboard
    Route::get('approver/dashboard',[ApproverController::class, 'index'])->name('approver.dashboard');

    Route::get('approver/profile/show/{id}', [ApproverProfileController::class, 'show'])->name('approver.profile.show');
    Route::get('approver/profile/{id}/edit', [ApproverProfileController::class, 'edit'])->name('approver.profile.edit');
    Route::put('approver/profile/{user}/update', [ApproverProfileController::class, 'update'])->name('approver.profile.update');
    Route::get('approver/notifications',[NotificationController::class, 'approver_notification'])->name('approver.notifications');
    Route::get('approver/activity-logs',[ApproverController::class, 'activity_logs'])->name('approver.activity_logs');

     //project/program
     Route::get('approver/projects/index',[ApproverProjectController::class, 'index'])->name('approver.projects.index');
     Route::get('approver/project/show/{id}',[ApproverProjectController::class, 'show'])->name('approver.projects.show');
    Route::post('approver/project/reject', [ApproverProjectController::class, 'reject_project'])->name('approver.projects.reject');
     Route::put('approver/project/{project}/approved', [ApproverProjectController::class, 'approved'])
     ->name('approver.projects.approved');
     //show feedback for rejected
     Route::get('approver/project/show/rejected/feedback/{id}',[ApproverProjectController::class, 'show_feedback_rejected'])->name('approver.projects.feedback_rejected');
     //rejected list
     Route::get('approver/project/rejected', [ApproverProjectController::class, 'rejected_list'])->name('approver.projects.rejected');
     Route::get('approver/project/approved-list', [ApproverProjectController::class, 'approved_list'])
    ->name('approver.projects.approved_list');
       //show approved
       Route::get('approver/project/show/approved/{id}',[ApproverProjectController::class, 'show_approved'])->name('approver.projects.show_approved');


    //status-reports
    //Pending Approval status_reports list
    Route::get('approver/status-reports/index',[ApproverStatusReportController::class, 'index'])->name('approver.status_reports.index');

    Route::get('approver/status-reports/show-project/{id}',[ApproverStatusReportController::class, 'showProject'])->name('approver.status_reports.show_project');
    Route::get('approver/status-reports/show-research/{id}',[ApproverStatusReportController::class, 'showResearch'])->name('approver.status_reports.show_research');

    Route::post('approver/status-reports/reject', [ApproverStatusReportController::class, 'reject_report'])->name('approver.status_reports.reject');
    Route::put('approver/status-reports/{statusReport}/approved', [ApproverStatusReportController::class, 'approved'])
    ->name('approver.status_reports.approved');

    Route::get('approver/status-reports/approved-list', [ApproverStatusReportController::class, 'approved_list'])
    ->name('approver.status_reports.approved_list');

    Route::get('approver/status-reports/show/approved-project/{id}',[ApproverStatusReportController::class, 'showProjectApproved'])->name('approver.status_reports.show_approved_project');
    Route::get('approver/status-reports/show/approved-research/{id}',[ApproverStatusReportController::class, 'showResearchApproved'])->name('approver.status_reports.show_approved_research');

    //show feedback for rejected status-report
    Route::get('approver/status-reports/show/project-rejected/feedback/{id}',[ApproverStatusReportController::class, 'showProjectRejected'])->name('approver.status_reports.project_rejected');
    Route::get('approver/status-reports/show/research-rejected/feedback/{id}',[ApproverStatusReportController::class, 'showResearchRejected'])->name('approver.status_reports.research_rejected');

    //rejected list
    Route::get('approver/status-reports/rejected', [ApproverStatusReportController::class, 'rejected_list'])->name('approver.status_reports.rejected_list');

 //terminal-reports
    //Pending Approval terminal_reports list
    Route::get('approver/terminal-reports/index',[ApproverTerminalReportController::class, 'index'])->name('approver.terminal_reports.index');

    Route::get('approver/terminal-reports/show-project/{id}',[ApproverTerminalReportController::class, 'showProject'])->name('approver.terminal_reports.show_project');
    Route::get('approver/terminal-reports/show-research/{id}',[ApproverTerminalReportController::class, 'showResearch'])->name('approver.terminal_reports.show_research');

    Route::post('approver/terminal-reports/reject', [ApproverTerminalReportController::class, 'reject_report'])->name('approver.terminal_reports.reject');
    Route::put('approver/terminal-reports/{terminalReport}/approved', [ApproverTerminalReportController::class, 'approved'])
    ->name('approver.terminal_reports.approved');

    Route::get('approver/terminal-reports/approved-list', [ApproverTerminalReportController::class, 'approved_list'])
    ->name('approver.terminal_reports.approved_list');

    Route::get('approver/terminal-reports/show/approved-project/{id}',[ApproverTerminalReportController::class, 'showProjectApproved'])->name('approver.terminal_reports.show_approved_project');
    Route::get('approver/terminal-reports/show/approved-research/{id}',[ApproverTerminalReportController::class, 'showResearchApproved'])->name('approver.terminal_reports.show_approved_research');

    //show feedback for rejected terminal-report
    Route::get('approver/terminal-reports/show/project-rejected/feedback/{id}',[ApproverTerminalReportController::class, 'showProjectRejected'])->name('approver.terminal_reports.project_rejected');
    Route::get('approver/terminal-reports/show/research-rejected/feedback/{id}',[ApproverTerminalReportController::class, 'showResearchRejected'])->name('approver.terminal_reports.research_rejected');

    //rejected list
    Route::get('approver/terminal-reports/rejected', [ApproverTerminalReportController::class, 'rejected_list'])->name('approver.terminal_reports.rejected_list');


    //reports
    Route::get('approver/reports/index',[ApproverReportController::class, 'index'])->name('approver.reports.index');
    Route::get('approver/report/show/{id}',[ApproverReportController::class, 'show'])->name('approver.reports.show');
    Route::post('approver/report/reject', [ApproverReportController::class, 'reject_report'])->name('approver.reports.reject');
    Route::put('approver/report/{report}/approved', [ApproverReportController::class, 'approved'])
    ->name('approver.reports.approved');
    Route::get('approver/report/approved-list', [ApproverReportController::class, 'approved_list'])
    ->name('approver.reports.approved_list');
    //show feedback for rejected report
    Route::get('approver/report/show/rejected/feedback/{id}',[ApproverReportController::class, 'show_feedback_rejected'])->name('approver.reports.feedback_rejected');
    //rejected list
    Route::get('approver/report/rejected', [ApproverReportController::class, 'rejected_list'])->name('approver.reports.rejected');
    Route::get('approver/report/show/approved/{id}',[ApproverReportController::class, 'show_approved'])->name('approver.reports.show_approved');

    

    //research
    Route::get('approver/research/index',[ApproverResearchController::class, 'index'])->name('approver.research.index');
    Route::get('approver/research/show/{id}',[ApproverResearchController::class, 'show'])->name('approver.research.show');
    route::post('approver/research/feedback/submit', [ApproverFeedbackController::class, 'approve_research'])->name('approver.research.feedback');
    Route::post('approver/research/reject', [ApproverResearchController::class, 'reject_research'])->name('approver.research.reject');
    Route::put('approver/research/{research}/approved', [ApproverResearchController::class, 'approved'])
    ->name('approver.research.approved');
    Route::get('approver/research/approved-list', [ApproverResearchController::class, 'approved_list'])
    ->name('approver.research.approved_list');
    //show feedback for rejected
    Route::get('approver/research/show/rejected/feedback/{id}',[ApproverResearchController::class, 'show_feedback_rejected'])->name('approver.research.feedback_rejected');
    //rejected list
    Route::get('approver/research/rejected', [ApproverResearchController::class, 'rejected_list'])->name('approver.research.rejected');
    Route::get('approver/research/show/approved/{id}',[ApproverResearchController::class, 'show_approved'])->name('approver.research.show_approved');
    

});

//Publisher
Route::middleware(['auth', 'role:publisher', NoCache::class])->group(function(){

    //Dashboard
    Route::get('publisher/dashboard',[PublisherController::class, 'index'])->name('publisher.dashboard');
    
    Route::get('publisher/profile/show/{id}', [PublisherProfileController::class, 'show'])->name('publisher.profile.show');
    Route::get('publisher/profile/{id}/edit', [PublisherProfileController::class, 'edit'])->name('publisher.profile.edit');
    Route::put('publisher/profile/{user}/update', [PublisherProfileController::class, 'update'])->name('publisher.profile.update');
    Route::get('publisher/notifications',[NotificationController::class, 'publisher_notification'])->name('publisher.notifications');
    Route::get('publisher/activity-logs',[PublisherController::class, 'activity_logs'])->name('publisher.activity_logs');
  

         //project/program
     Route::get('publisher/projects/index',[PublisherProjectController::class, 'index'])->name('publisher.projects.index');
     Route::get('publisher/project/show/{id}',[PublisherProjectController::class, 'show'])->name('publisher.projects.show');
     Route::put('publisher/project/{project}/published', [PublisherProjectController::class, 'published'])
     ->name('publisher.projects.published');
    //show published
    Route::get('publisher/project/show/published/{id}',[PublisherProjectController::class, 'show_published'])->name('publisher.projects.show_published');
    //published list
    Route::get('publisher/project/published', [PublisherProjectController::class, 'published_list'])->name('publisher.projects.published_list');


     //reports
    Route::get('publisher/reports/index',[PublisherReportController::class, 'index'])->name('publisher.reports.index');
    Route::get('publisher/report/show/{id}',[PublisherReportController::class, 'show'])->name('publisher.reports.show');
    Route::put('publisher/report/{report}/published', [PublisherReportController::class, 'published'])
    ->name('publisher.reports.published');
    //show published
    Route::get('publisher/report/show/published/{id}',[PublisherReportController::class, 'show_published'])->name('publisher.reports.show_published');
    //published list
    Route::get('publisher/report/published', [PublisherReportController::class, 'published_list'])->name('publisher.reports.published_list');


    //status-reports
    Route::get('publisher/status-reports/index',[PublisherStatusReportController::class, 'index'])->name('publisher.status_reports.index');

    Route::get('publisher/status-reports/show-project/{id}',[PublisherStatusReportController::class, 'showProject'])->name('publisher.status_reports.show_project');
    Route::get('publisher/status-reports/show-research/{id}',[PublisherStatusReportController::class, 'showResearch'])->name('publisher.status_reports.show_research');

    Route::put('publisher/status-reports/{statusReport}/published', [PublisherStatusReportController::class, 'published'])
    ->name('publisher.status_reports.published');

    Route::get('publisher/status-reports/published-list', [PublisherStatusReportController::class, 'published_list'])
    ->name('publisher.status_reports.published_list');

    Route::get('publisher/status-reports/show/published-project/{id}',[PublisherStatusReportController::class, 'showProjectPublished'])->name('publisher.status_reports.show_published_project');
    Route::get('publisher/status-reports/show/published-research/{id}',[PublisherStatusReportController::class, 'showResearchPublished'])->name('publisher.status_reports.show_published_research');


 //terminal-reports
    Route::get('publisher/terminal-reports/index',[PublisherTerminalReportController::class, 'index'])->name('publisher.terminal_reports.index');

    Route::get('publisher/terminal-reports/show-project/{id}',[PublisherTerminalReportController::class, 'showProject'])->name('publisher.terminal_reports.show_project');
    Route::get('publisher/terminal-reports/show-research/{id}',[PublisherTerminalReportController::class, 'showResearch'])->name('publisher.terminal_reports.show_research');

    Route::put('publisher/terminal-reports/{terminalReport}/published', [PublisherTerminalReportController::class, 'published'])
    ->name('publisher.terminal_reports.published');

    Route::get('publisher/terminal-reports/published-list', [PublisherTerminalReportController::class, 'published_list'])
    ->name('publisher.terminal_reports.published_list');

    Route::get('publisher/terminal-reports/show/published-project/{id}',[PublisherTerminalReportController::class, 'showProjectPublished'])->name('publisher.terminal_reports.show_published_project');
    Route::get('publisher/terminal-reports/show/published-research/{id}',[PublisherTerminalReportController::class, 'showResearchPublished'])->name('publisher.terminal_reports.show_published_research');



    //research
    Route::get('publisher/research/index',[PublisherResearchController::class, 'index'])->name('publisher.research.index');
    Route::get('publisher/research/show/{id}',[PublisherResearchController::class, 'show'])->name('publisher.research.show');
    Route::put('publisher/research/{research}/published', [PublisherResearchController::class, 'published'])
    ->name('publisher.research.published');
    //show published
    Route::get('publisher/research/show/published/{id}',[PublisherResearchController::class, 'show_published'])->name('publisher.research.show_published');
    //published list
    Route::get('publisher/research/published', [PublisherResearchController::class, 'published_list'])->name('publisher.research.published_list');
});


//User
Route::middleware(NoCache::class)->group(function(){
    //website dashboard
    Route::get('/',[WebsiteController::class, 'home2',NoCache::class])->name('website.home2');
    Route::get('/sdg/{id}', [WebsiteController::class, 'showSdg'])->name('website.sdg.show');

    //Yearly Overview of SDG
    Route::get('/yearly-overview', [WebsiteController::class, 'yearlyOverview'])->name('website.yearly_overview');
    Route::get('sdg/yearly-content/{sdg}', [WebsiteController::class, 'display_sdg_content'])->name('website.display_sdg_content');

    //user profile
    Route::get('user/profile/show/{id}', [\App\Http\Controllers\User\ProfileController::class, 'show'])->name('user.profile.show');
    Route::get('user/profile/{id}/edit', [\App\Http\Controllers\User\ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('user/profile/{user}/update', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('user.profile.update');


    //report
    Route::get('/sdg/all/reports/', [WebsiteController::class, 'sdg_report_main2'])->name('website.sdg_report_main2');
    Route::get('/sdg/reports/{sdg}', [WebsiteController::class, 'display_report_sdg2'])->name('website.display_report_sdg2');
    Route::get('sdg/report/{report_id}', [WebsiteController::class, 'display_single_report2'])->name('website.display_single_report2');

    //projects
    Route::get('/sdg/all/projects/', [WebsiteController::class, 'sdg_project_main2'])->name('website.sdg_project_main2');
    Route::get('/sdg/projects/{sdg}', [WebsiteController::class, 'display_project_sdg2'])->name('website.display_project_sdg2');
    Route::get('sdg/project/{project_id}', [WebsiteController::class, 'display_single_project2'])->name('website.display_single_project2');
    Route::get('/sdg/projects/coordinates/{latitude}/{longitude}', [WebsiteController::class, 'projectsByCoordinates'])->name('website.projects_by_coordinates');
    


    //research_extensions
    Route::get('/sdg/all/research', [WebsiteController::class, 'sdg_research_main2'])->name('website.sdg_research_main2');
    Route::get('/sdg/research/{sdg}', [WebsiteController::class, 'display_research_sdg2'])->name('website.display_research_sdg2');
    Route::get('/sdg/research/researchcategories/{researchcategory}', [WebsiteController::class, 'display_research_category'])->name('website.display_research_category');
    Route::get('/sdg/research/item/{research_id}', [WebsiteController::class, 'display_single_research2'])->name('website.display_single_research2');


    Route::get('/sdg/contact_us', [WebsiteController::class, 'contact_us'])->name('website.contact_us');

    Route::get('sdg/status-reports-project-published/{id}', [WebsiteController::class, 'showStatusReportProjectPublished'])->name('website.status_reports.show_project_published');
    Route::get('sdg/terminal-reports-project-published/{id}', [WebsiteController::class, 'showTerminalReportProjectPublished'])->name('website.terminal_reports.show_project_published');

    Route::get('sdg/status-reports-research-published/{id}', [WebsiteController::class, 'showStatusReportResearchPublished'])->name('website.status_reports.show_research_published');
    Route::get('sdg/terminal-reports-research-published/{id}', [WebsiteController::class, 'showTerminalReportResearchPublished'])->name('website.terminal_reports.show_research_published');

    Route::get('/terminal-report/file/view/{id}', [TerminalReportController::class, 'viewTerminalReportFile'])
    ->name('terminal.report.file.view');

    Route::get('/research/file/view/{id}', [WebsiteController::class, 'viewResearchFile'])->name('research.file.view');

// routes/web.php
});
