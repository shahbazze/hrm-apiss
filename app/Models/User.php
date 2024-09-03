<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification; // Import the custom notification

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'pass',
        'status',
        'avatar', // Added avatar field
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pass',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'pass' => 'hashed',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Define a relationship to the TeamMember model.
     * 
     * This allows the user to be part of multiple teams through the TeamMember model.
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get all teams the user belongs to.
     *
     * This defines a many-to-many relationship through the team_members table.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')->withPivot('is_team_lead');
    }
}
