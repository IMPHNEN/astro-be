<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $primaryKey = 'milestone_id';
    
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'color',
        'assigned_to',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'status' => 'string',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Scope a query to only include milestones of a given status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Move the milestone to the next status.
     *
     * @return bool
     */
    public function moveToNextStatus()
    {
        $statuses = ['backlog', 'in_progress', 'testing', 'awaiting_review',  'completed', 'dropped'];
        $currentIndex = array_search($this->status, $statuses);

        if ($currentIndex !== false && $currentIndex < count($statuses) - 1) {
            $this->status = $statuses[$currentIndex + 1];
            return $this->save();
        }

        return false;
    }

    /**
     * Move the milestone to the previous status.
     *
     * @return bool
     */
    public function moveToPreviousStatus()
    {
        $statuses = ['not_started', 'in_progress', 'completed'];
        $currentIndex = array_search($this->status, $statuses);

        if ($currentIndex !== false && $currentIndex > 0) {
            $this->status = $statuses[$currentIndex - 1];
            return $this->save();
        }

        return false;
    }
}