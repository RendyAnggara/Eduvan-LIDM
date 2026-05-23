<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRating extends Model
{
    // 🟢 Wajib dideklarasikan karena nama tabel lu jamak pake 's' (course_ratings)
    protected $table = 'course_ratings';

    // 🟢 Daftarkan kolom yang boleh diisi massal oleh Laravel
    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
    ];
    public function user()
    {
        // 🟢 PERBAIKAN: Ganti $table menjadi $this
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        // 🟢 PERBAIKAN: Ganti $table menjadi $this
        return $this->belongsTo(Course::class, 'course_id');
    }
}
