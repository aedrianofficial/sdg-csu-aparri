<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAction extends Model
{
    protected $table = 'role_actions';
    protected $fillable = [
        'user_id',
        'role',
        'action',
        'content_id',
        'content_type',
    ];

    // Polymorphic relationship to the content (project, report, or research)
    public function content()
    {
        return $this->morphTo();
    }
        // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
