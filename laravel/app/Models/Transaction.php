<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

class Transaction extends Model
{
    use HasFactory;

    // 🟢 1. DAFTARKAN KOLOM YANG BOLEH DIISI (MASS ASSIGNMENT)
    protected $fillable = [
        'user_id',
        'course_id',
        'reference_id',
        'amount',
        'status',
        'payment_url',
    ];

    // 🔗 2. RELASI: Menghubungkan Transaksi ke Data User/Siswa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 3. RELASI: Menghubungkan Transaksi ke Data Kursus
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
