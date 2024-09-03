<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'user_id', 'is_team_lead']; // Fields that are mass assignable

    /**
     * Define a relationship to the Team model.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Define a relationship to the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
