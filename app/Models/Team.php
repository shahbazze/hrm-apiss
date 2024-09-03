<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['team_name']; // Only `team_name` is mass assignable

    /**
     * Define a relationship to the TeamMember model.
     * 
     * This allows a team to have multiple members.
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get all users that belong to the team.
     *
     * This defines a many-to-many relationship through the team_members table.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_members')->withPivot('is_team_lead');
    }
}
