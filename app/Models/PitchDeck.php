<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PitchDeck extends Model
{
    protected $table = 'pitch_decks';
    protected $primaryKey = 'pitch_id';
    public $timestamps = false;
    
    protected $fillable = [
        'project_id',
        'document_path',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}