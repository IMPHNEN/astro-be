<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectApplication extends Model
{
    protected $table = 'project_applications';
    protected $primaryKey = 'application_id';
    
    protected $fillable = [
        'project_id',
        'freelancer_id',
        'status',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'status' => 'string',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id', 'user_id');
    }
}