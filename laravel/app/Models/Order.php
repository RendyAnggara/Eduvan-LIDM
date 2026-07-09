<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Menghubungkan Order kembali ke Guru/User yang membelinya (Belongs To)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Menghubungkan Order ke Course/Kelas Informatika yang dibeli (Belongs To)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Menghubungkan Order ke banyak Voucher yang lahir dari transaksi ini (Has Many)
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
