<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\DatabaseNotification;
class Notification extends Model
{
    use HasFactory;

    // Specify fillable attributes
    protected $fillable = [
        'user_id',
        'notifiable_type', // Add notifiable_type
        'notifiable_id',   // Add notifiable_id
        'type',
        'related_type',
        'related_id',
        'read_at',
        'data',
    ];
    
    // Specify the casts to ensure JSON data is handled properly
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Relationship to the User who received the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relationship to the related content (project, report, research).
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

        public function notifiable()
    {
        return $this->morphTo();
    }
}
