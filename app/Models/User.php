<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'slug',
        'username',
        'backgroud',
        'avatar',
        'bio',
        'website',
        'socials',
        'location',
        'timezone',
        'language',
        'currency',
        'phone',
        'cv',
        'role',
        'email',
        'backup_email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'socials' => 'array',
        ];
    }

    /**
     * Get the projects created by the user.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'creator_id');
    }

    /**
     * Get the applications made by the user.
     */
    public function applications()
    {
        return $this->hasMany(ProjectApplication::class, 'freelancer_id');
    }

    /**
     * Get the investments made by the user.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class, 'investor_id');
    }

    /**
     * Get the skills associated with the user.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }
}
