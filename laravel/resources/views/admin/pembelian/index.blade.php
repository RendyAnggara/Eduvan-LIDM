@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6">
        <div
            class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight pt-1">
                    Laporan Transaksi EduLearn
                </h1>
                <p class="text-xs text-gray-500">Monitoring pendapatan materi dan status pembayaran otomatis.</p>
            </div>

            <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
                <a href="{{ route('admin.pembelian.pdf') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md shadow-rose-100 gap-2">
                    <i class="fas fa-file-pdf text-sm"></i> Cetak Laporan PDF
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">Total
                        Pendapatan</p>
                    <h3 class="text-base sm:text-lg font-black text-emerald-600 leading-none mt-1 truncate">
                        Rp {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">
                        Transaksi Lunas</p>
                    <h3 class="text-base sm:text-lg font-black text-gray-900 leading-none mt-1">
                        {{ $totalSuccessCount }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">Menunggu
                        Bayar</p>
                    <h3 class="text-base sm:text-lg font-black text-amber-600 leading-none mt-1">
                        {{ $pendingCount }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">Sistem
                        Pembayaran</p>
                    <span
                        class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-bold rounded-lg">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> DompetX Automated
                    </span>
                </div>
            </div>
        </div>
        <div class="bg-white p-2 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap gap-2">
            <button onclick="switchTab('history')" id="tabBtn-history"
                class="tab-btn flex-1 min-w-[140px] py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 bg-indigo-600 text-white shadow-sm">
                <i class="fas fa-receipt text-xs"></i> Detail Transaksi Student
            </button>

            <button onclick="switchTab('summary')" id="tabBtn-summary"
                class="tab-btn flex-1 min-w-[140px] py-2.5 px-4 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                <i class="fas fa-chart-pie text-xs"></i> Omzet Per Kursus
            </button>
        </div>
        <div id="tabContent-history" class="tab-content space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div
                    class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h4 class="font-black text-gray-900 text-base tracking-tight">Detail Transaksi Student</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Seluruh riwayat pembayaran otomatis via QRIS / Virtual
                            Account.</p>
                    </div>

                    <div class="relative w-full sm:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" id="studentSearchInput" placeholder="Cari nama student atau materi..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm">
                    </div>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="dataTable">
                        <thead
                            class="bg-gray-50/80 text-gray-400 text-xs uppercase font-bold tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="p-4 pl-6">Tanggal & Waktu</th>
                                <th class="p-4">Nama Student</th>
                                <th class="p-4">Materi Kursus</th>
                                <th class="p-4 text-right">Harga Beli</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                            @forelse ($transactionDetails as $trans)
                                @php
                                    $isPaid = in_array(strtoupper($trans->status), ['SUCCESS', 'PAID', 'SETTLED']);
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 text-xs text-gray-500 whitespace-nowrap">
                                        {{ $trans->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ $trans->user->name }}
                                        <span
                                            class="block text-xs font-normal text-gray-400">{{ $trans->user->email }}</span>
                                    </td>
                                    <td class="p-4 text-gray-800 font-medium">{{ $trans->course->title }}</td>
                                    <td class="p-4 text-right font-black text-gray-900">
                                        Rp {{ number_format($trans->price_bought, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-center">
                                        @if ($isPaid)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl font-bold text-xs">
                                                <i class="fas fa-check-circle text-[10px]"></i> Lunas
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-xl font-bold text-xs">
                                                <i class="fas fa-clock text-[10px]"></i> {{ strtoupper($trans->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <a href="{{ route('admin.pembelian.download', $trans->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-100 text-xs font-bold rounded-xl transition gap-1.5 shadow-sm">
                                            <i class="fas fa-file-download text-[10px]"></i> Laporan PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-gray-400 italic">
                                        <i class="fas fa-inbox text-3xl mb-2 text-gray-300 block"></i>
                                        Belum ada transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="block md:hidden p-3.5 space-y-3 bg-gray-50/30" id="mobileCardsContainer">
                    @forelse ($transactionDetails as $trans)
                        @php
                            $isPaid = in_array(strtoupper($trans->status), ['SUCCESS', 'PAID', 'SETTLED']);
                        @endphp
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3 mobile-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-bold text-gray-900 text-sm leading-tight student-name">
                                        {{ $trans->user->name }}</h5>
                                    <span class="text-[10px] text-gray-400 block mt-0.5">
                                        {{ $trans->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>
                                @if ($isPaid)
                                    <span
                                        class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg font-bold text-[10px]">
                                        Lunas
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg font-bold text-[10px]">
                                        {{ strtoupper($trans->status) }}
                                    </span>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-100 flex justify-between items-end">
                                <div class="max-w-[55%]">
                                    <span class="text-[10px] text-gray-400 block">Materi:</span>
                                    <p class="text-xs text-gray-800 font-bold truncate course-title">
                                        {{ $trans->course->title }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-gray-400 block">Harga:</span>
                                    <p class="text-sm font-black text-gray-900">Rp
                                        {{ number_format($trans->price_bought, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-100">
                                <a href="{{ route('admin.pembelian.download', $trans->id) }}"
                                    class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-100 py-2 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 shadow-sm">
                                    <i class="fas fa-file-download text-[10px]"></i> Unduh Laporan PDF
                                </a>
                            </div>
                        </div>
                    @empty
                        <div
                            class="bg-white p-8 rounded-2xl text-center text-gray-400 text-xs italic border border-gray-100">
                            <i class="fas fa-inbox text-2xl mb-1 text-gray-300 block"></i>
                            Belum ada transaksi.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div id="tabContent-summary" class="tab-content hidden space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div
                    class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h4 class="font-black text-gray-900 text-base tracking-tight">Ringkasan Omzet Per Kursus</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Total materi terjual dan performa akumulasi pendapatan.</p>
                    </div>

                    <div class="relative w-full sm:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" id="courseSearchInput" placeholder="Cari materi / kursus..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm">
                    </div>
                </div>

                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="courseDataTable">
                        <thead
                            class="bg-gray-50/80 text-gray-400 text-xs uppercase font-bold tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="p-4 pl-6">Materi / Kursus</th>
                                <th class="p-4 text-right">Harga Kursus</th>
                                <th class="p-4 text-center">Total Terjual</th>
                                <th class="p-4 text-right">Total Pendapatan</th>
                                <th class="p-4 text-center pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                            @forelse ($courseReports as $report)
                                <tr class="hover:bg-gray-50/80 transition-colors course-row">
                                    <td class="p-4 pl-6 font-bold text-gray-900 course-title-text">{{ $report->title }}
                                    </td>
                                    <td class="p-4 text-right font-bold text-indigo-600">
                                        Rp {{ number_format($report->price ?? ($report->harga ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl font-bold text-xs">
                                            {{ $report->total_sold ?? 0 }} Terjual
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-black text-gray-900">
                                        Rp {{ number_format($report->total_revenue ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <a href="{{ route('admin.pembelian.course_pdf', $report->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 text-xs font-bold rounded-xl transition gap-1.5 shadow-sm">
                                            <i class="fas fa-file-pdf text-[10px]"></i> Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic">
                                        <i class="fas fa-inbox text-3xl mb-2 text-gray-300 block"></i>
                                        Belum ada data materi berbayar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tbody class="bg-gray-50 border-t border-gray-100 font-bold text-gray-800" id="courseTableFoot">
                            <tr>
                                <td colspan="3"
                                    class="p-4 pl-6 text-right uppercase tracking-wider text-xs text-gray-400 font-bold">
                                    Grand Total Pendapatan
                                </td>
                                <td class="p-4 text-right text-base font-black text-emerald-600">
                                    Rp {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="bg-gray-50"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="block md:hidden p-3.5 space-y-3 bg-gray-50/30" id="mobileCourseCardsContainer">
                    @forelse ($courseReports as $report)
                        <div
                            class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center mobile-course-card">
                            <div class="space-y-1 max-w-[60%]">
                                <h5 class="font-bold text-gray-900 text-sm leading-snug mobile-course-title">
                                    {{ $report->title }}</h5>
                                <p class="text-xs text-indigo-600 font-bold">
                                    Harga: Rp {{ number_format($report->price ?? ($report->harga ?? 0), 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400">Terjual: <span
                                        class="font-bold text-gray-700">{{ $report->total_sold ?? 0 }}</span></p>
                            </div>
                            <div class="text-right flex flex-col items-end gap-2">
                                <div>
                                    <span class="text-[10px] text-gray-400 block">Total Pendapatan</span>
                                    <span class="font-black text-gray-900 text-sm">Rp
                                        {{ number_format($report->total_revenue ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ route('admin.pembelian.course_pdf', $report->id) }}"
                                    class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-600 border border-rose-100 text-[10px] font-bold rounded-lg transition gap-1 shadow-sm">
                                    <i class="fas fa-file-pdf text-[9px]"></i> Cetak
                                </a>
                            </div>
                        </div>
                    @empty
                        <div
                            class="bg-white p-8 rounded-2xl text-center text-gray-400 text-xs italic border border-gray-100">
                            <i class="fas fa-inbox text-2xl mb-1 text-gray-300 block"></i>
                            Belum ada data materi berbayar.
                        </div>
                    @endforelse

                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex justify-between items-center"
                        id="mobileCourseGrandTotal">
                        <span class="text-xs font-bold text-emerald-800 tracking-wide uppercase">Grand Total</span>
                        <span class="text-base font-black text-emerald-700">Rp
                            {{ number_format($grandTotal ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        "use strict";

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className =
                    "tab-btn flex-1 min-w-[140px] py-2.5 px-4 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 transition flex items-center justify-center gap-2";
            });

            document.getElementById('tabContent-' + tabName).classList.remove('hidden');

            const activeBtn = document.getElementById('tabBtn-' + tabName);
            activeBtn.className =
                "tab-btn flex-1 min-w-[140px] py-2.5 px-4 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-sm transition flex items-center justify-center gap-2";
        }

        document.addEventListener('DOMContentLoaded', function() {
            const courseSearchInput = document.getElementById('courseSearchInput');
            if (courseSearchInput) {
                courseSearchInput.addEventListener('input', function() {
                    const filterValue = this.value.toLowerCase().trim();

                    document.querySelectorAll('#courseDataTable tbody .course-row').forEach(row => {
                        const title = row.querySelector('.course-title-text').textContent
                            .toLowerCase();
                        row.style.display = title.includes(filterValue) ? '' : 'none';
                    });

                    document.querySelectorAll('#mobileCourseCardsContainer .mobile-course-card').forEach(
                        card => {
                            const title = card.querySelector('.mobile-course-title').textContent
                                .toLowerCase();
                            card.style.display = title.includes(filterValue) ? 'flex' : 'none';
                        });
                });
            }

            const studentSearchInput = document.getElementById('studentSearchInput');
            if (studentSearchInput) {
                studentSearchInput.addEventListener('input', function() {
                    const filterValue = this.value.toLowerCase().trim();

                    document.querySelectorAll('#dataTable tbody tr').forEach(row => {
                        if (row.cells.length < 5) return;
                        const name = row.cells[1].textContent.toLowerCase();
                        const course = row.cells[2].textContent.toLowerCase();
                        row.style.display = (name.includes(filterValue) || course.includes(
                            filterValue)) ? '' : 'none';
                    });

                    document.querySelectorAll('#mobileCardsContainer .mobile-card').forEach(card => {
                        const name = card.querySelector('.student-name').textContent.toLowerCase();
                        const course = card.querySelector('.course-title').textContent
                            .toLowerCase();
                        card.style.display = (name.includes(filterValue) || course.includes(
                            filterValue)) ? 'block' : 'none';
                    });
                });
            }
        });
    </script>
@endsection
