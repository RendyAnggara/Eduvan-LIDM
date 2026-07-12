<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'title',
        'video_url',
        'content_text'
        ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }
}
