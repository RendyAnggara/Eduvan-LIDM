<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Progress;

class Course extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
        'price',
        'image',
        'rating',
        'course_type',
        'grade_level'
    ];
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class)->orderBy('order', 'asc');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Enrollment::class, 'course_id', 'id', 'id', 'user_id');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function chapters()
    {
        return $this->hasMany(\App\Models\Chapter::class, 'course_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id');
    }
}
