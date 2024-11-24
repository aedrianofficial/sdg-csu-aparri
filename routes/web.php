<?php

use App\Http\Controllers\Analytics;
use App\Http\Controllers\Approver\ApproverController;
use App\Http\Controllers\Approver\FeedbackController as ApproverFeedbackController;
use App\Http\Controllers\Approver\ProfileController as ApproverProfileController;
use App\Http\Controllers\Approver\ProjectController as ApproverProjectController;
use App\Http\Controllers\Approver\ReportController as ApproverReportController;
use App\Http\Controllers\Approver\ResearchController as ApproverResearchController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\HomeController as AuthHomeController;
use App\Http\Controllers\Auth\ProfileController as AuthProfileController;
use App\Http\Controllers\Auth\ProjectController;
use App\Http\Controllers\Auth\ReportController;
use App\Http\Controllers\Auth\ResearchController;
use App\Http\Controllers\Contributor\ContributorController;
use App\Http\Controllers\Contributor\ProfileController as ContributorProfileController;
use App\Http\Controllers\Contributor\ProjectController as ContributorProjectController;
use App\Http\Controllers\Contributor\ReportController as ContributorReportController;
use App\Http\Controllers\Contributor\ResearchController as ContributorResearchController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Publisher\ProfileController as PublisherProfileController;
use App\Http\Controllers\Publisher\ProjectController as PublisherProjectController;
use App\Http\Controllers\Publisher\PublisherController;
use App\Http\Controllers\Publisher\ReportController as PublisherReportController;
use App\Http\Controllers\Publisher\ResearchController as PublisherResearchController;
use App\Http\Controllers\Reviewer\FeedbackController as ReviewerFeedbackController;
use App\Http\Controllers\Reviewer\ProfileController as ReviewerProfileController;
use App\Http\Controllers\Reviewer\ProjectController as ReviewerProjectController;
use App\Http\Controllers\Reviewer\ReportController as ReviewerReportController;
use App\Http\Controllers\Reviewer\ResearchController as ReviewerResearchController;
use App\Http\Controllers\Reviewer\ReviewerController;
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

Route::get('/research/{id}/file/download', [ResearchController::class, 'downloadFile'])->name('research.file.download');

Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

Route::get('/test-timezone', function () {
    return now(); // Should return the current time in Asia/Manila
});


//admin middleware
Route::middleware(['auth', 'role:admin', NoCache::class])->group(function(){

    //dashboard
    Route::get('auth/dashboard',[AdminController::class, 'index'])->name('auth.dashboard');

    Route::get('auth/my-activity-logs',[AdminController::class, 'my_activity_logs'])->name('auth.my_activity_logs');
    
    //user-profile
    Route::get('auth/profile/show/{id}', [AuthProfileController::class, 'show'])->name('auth.profile.show');
    Route::get('auth/profile/{id}/edit', [AuthProfileController::class, 'edit'])->name('auth.profile.edit');
    Route::put('auth/profile/{user}/update', [AuthProfileController::class, 'update'])->name('auth.profile.update');
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

    //Activity Logs
    Route::get('auth/activity-logs/admin', [AdminController::class, 'adminActivityLogs'])->name('admin_activity_logs');
    Route::get('auth/activity-logs/{id}', [AdminController::class, 'displayAdminActivityLogs'])->name('admin_activity_logs.show');
    Route::get('auth/user-activity-logs', [AdminController::class, 'userActivityLogs'])->name('user_activity_logs');
    Route::get('auth/user-activity-logs/feedback/{id}', [AdminController::class, 'displayUserActivityLogs'])->name('feedback_activity_logs.show');

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



Route::middleware(NoCache::class)->group(function(){
    //website dashboard
    Route::get('/',[WebsiteController::class, 'home2',NoCache::class])->name('website.home2');

    //report
    Route::get('/sdg/reports2', [WebsiteController::class, 'sdg_report_main2'])->name('website.sdg_report_main2');
    Route::get('/sdg/reports2/{sdg}', [WebsiteController::class, 'display_report_sdg2'])->name('website.display_report_sdg2');
    Route::get('sdg/report2/{report_id}', [WebsiteController::class, 'display_single_report2'])->name('website.display_single_report2');

    //programs
    Route::get('/sdg/projects2', [WebsiteController::class, 'sdg_project_main2'])->name('website.sdg_project_main2');
    Route::get('/sdg/projects2/{sdg}', [WebsiteController::class, 'display_project_sdg2'])->name('website.display_project_sdg2');
    Route::get('sdg/project2/{project_id}', [WebsiteController::class, 'display_single_project2'])->name('website.display_single_project2');

    //research_extensions
    Route::get('/sdg/research2', [WebsiteController::class, 'sdg_research_main2'])->name('website.sdg_research_main2');
    Route::get('/sdg/research2/{sdg}', [WebsiteController::class, 'display_research_sdg2'])->name('website.display_research_sdg2');
    Route::get('/sdg/research2/researchcategories/{researchcategory}', [WebsiteController::class, 'display_research_category'])->name('website.display_research_category');
    Route::get('/sdg/research2/item/{research_id}', [WebsiteController::class, 'display_single_research2'])->name('website.display_single_research2');


    Route::get('/sdg/contact_us', [WebsiteController::class, 'contact_us'])->name('website.contact_us');


// routes/web.php
});
