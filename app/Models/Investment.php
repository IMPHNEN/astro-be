<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $primaryKey = 'investment_id';
    public $timestamps = false;
    
    protected $fillable = [
        'investor_id',
        'project_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'invested_at' => 'datetime',
    ];

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}