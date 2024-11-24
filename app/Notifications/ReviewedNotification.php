<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewedNotification extends Notification
{
    use Queueable;

    private $contentTitle;
    private $contentType;

    public function __construct($contentTitle, $contentType)
    {
        $this->contentTitle = $contentTitle;
        $this->contentType = $contentType;
    }

    /**
     * Specify the notification delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Define the data stored in the notifications table.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => "Your {$this->contentType} titled '{$this->contentTitle}' has been reviewed.",
            'content_type' => $this->contentType,
            'content_title' => $this->contentTitle,
        ];
    }
}
