<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $courseReports = Course::where('price', '>', 0)
            ->withCount(['enrollments as total_sold' => function ($query) {
                $query->whereIn('status', ['success', 'PAID', 'SETTLED']);
            }])
            ->withSum(['enrollments as total_revenue' => function ($query) {
                $query->whereIn('status', ['success', 'PAID', 'SETTLED']);
            }], 'price_bought')
            ->get();

        $transactionDetails = Enrollment::with(['user', 'course'])
            ->whereHas('user')
            ->whereHas('course')
            ->latest()
            ->get();

        $successfulTransactions = $transactionDetails->whereIn('status', ['success', 'PAID', 'SETTLED']);
        $grandTotal = $successfulTransactions->sum('price_bought');
        $totalSuccessCount = $successfulTransactions->count();
        $pendingCount = $transactionDetails->whereIn('status', ['pending', 'PENDING', 'Checking Admin'])->count();

        return view('admin.pembelian.index', compact(
            'courseReports',
            'transactionDetails',
            'grandTotal',
            'totalSuccessCount',
            'pendingCount'
        ));
    }

    public function exportPdf()
    {
        $transactions = Enrollment::with(['user', 'course'])
            ->whereIn('status', ['success', 'PAID', 'SETTLED'])
            ->latest()
            ->get();

        $totalRevenue = $transactions->sum('price_bought');

        $pdf = Pdf::loadView('admin.pembelian.pdf', compact('transactions', 'totalRevenue'));

        return $pdf->download('laporan-pembelian-dompetx-' . date('Y-m-d') . '.pdf');
    }

    public function downloadReport($id)
    {
        $trans = Enrollment::with(['user', 'course'])->findOrFail($id);

        $data = [
            'trans' => $trans,
            'downloaded_at' => Carbon::now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'
        ];

        $pdf = Pdf::loadView('admin.pembelian.report_item_pdf', $data)
            ->setOption('isRemoteEnabled', true)
            ->setPaper('a4', 'portrait');

        $filename = 'Laporan-Transaksi-' . Str::slug($trans->user->name ?? 'Student') . '-' . Str::slug($trans->course->title ?? 'Course') . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadCourseReport($id)
    {
        $course = Course::with(['enrollments' => function ($query) {
            $query->whereIn('status', ['success', 'PAID', 'SETTLED'])->with('user')->latest();
        }])->findOrFail($id);

        $totalSold = $course->enrollments->count();
        $totalRevenue = $course->enrollments->sum('price_bought');

        $data = [
            'course' => $course,
            'totalSold' => $totalSold,
            'totalRevenue' => $totalRevenue,
            'downloaded_at' => Carbon::now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'
        ];

        $pdf = Pdf::loadView('admin.pembelian.report_course_pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'Laporan-Materi-' . Str::slug($course->title) . '.pdf';

        return $pdf->download($filename);
    }
}
