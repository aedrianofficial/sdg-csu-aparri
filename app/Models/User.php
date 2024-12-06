<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'email_verified_at',
        'password',
        'role',
        'phone_number',
        'address',
        'date_of_birth',
        'bio',
        'first_name',
        'last_name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
    ];

   public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'causer');
    }
    
    /**
     * Relationship: A user can have one profile image.
     */
    public function userImage()
    {
        return $this->hasOne(UserImage::class, 'user_id');
    }
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    
    public function researches()
    {
        return $this->hasMany(Research::class);
    }
    /**
     * Full address accessor to combine address fields into a readable format.
     *
     * @return string
     */
    
}
