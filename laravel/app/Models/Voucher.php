<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    // Menghubungkan Voucher kembali ke data Order induknya (Belongs To)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Menghubungkan Voucher ke Course/Kelas Informatika yang diwakilinya (Belongs To)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
