<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        return back();
    }

    public function contributor_notification()
    {
        return view('contributor.notification');
    }
    public function reviewer_notification()
    {
        return view('reviewer.notification');
    }
    public function approver_notification()
    {
        return view('approver.notification');
    }
    public function publisher_notification()
    {
        return view('publisher.notification');
    }
    public function admin_notification()
    {
        return view('auth.notification');
    }
    
}
