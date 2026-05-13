<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;


class CertificateApiController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil sertifikat milik user yang sedang login beserta data kursusnya
        $certificates = Certificate::with('course')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }
}
