<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    // Nama tabelnya (opsional, tapi bagus untuk jaga-jaga)
    protected $table = 'progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'content_id',
        'is_completed',
        'score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
