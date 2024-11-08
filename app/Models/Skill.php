<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $primaryKey = 'skill_id';
    public $timestamps = false;
    
    protected $fillable = [
        'skill_name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills', 'skill_id', 'user_id');
    }
}