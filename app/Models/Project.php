<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $primaryKey = 'project_id';
    
    protected $fillable = [
        'slug',
        'creator_id',
        'project_name',
        'budget',
        'deadline',
        'description',
        'proposal',
        'requirements',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status' => 'string',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'user_id');
    }

    public function applications()
    {
        return $this->hasMany(ProjectApplication::class, 'project_id', 'project_id');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class, 'project_id', 'project_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'project_id', 'project_id');
    }

    public function pitchDecks()
    {
        return $this->hasMany(PitchDeck::class, 'project_id', 'project_id');
    }
}