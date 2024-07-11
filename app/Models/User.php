<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'role_id',
        'email',
        'username',
        'password',
        'role_id',
        'parent_id'
    ];

    /**
     * Get the user that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function marksAsStudent()
    {
        return $this->hasMany(Mark::class, 'student_id');
    }

    public function marksAsTeacher()
    {
        return $this->hasMany(Mark::class, 'teacher_id');
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'Student');
        });
    }

    public function scopeTeachers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'Teacher');
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id')->whereHas('role', function ($query) {
            $query->where('name', 'Teacher');
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role->name === 'Admin';
    }

    public function isTeacher()
    {
        return $this->role->name === 'Teacher';
    }

    public function isParent()
    {
        return $this->role->name === 'Parent';
    }

    public function isStudent()
    {
        return $this->role->name === 'Student';
    }
}
