@extends('layouts.admin')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Total Course</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ $stats['total_courses'] }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shadow-inner">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-50 flex items-center text-[11px] font-bold text-indigo-600">
                <span>Materi mapel berbayar aktif di EduLearn</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Total Instansi Sekolah</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ $stats['total_schools'] }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 shadow-inner">
                    <i class="fas fa-school text-lg"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-50 flex items-center text-[11px] font-bold text-emerald-600">
                <span>Sekolah terintegrasi saat ini</span>
            </div>
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-wider tracking-widest">Aktivitas & Data Instansi
            </h2>
        </div>
        <div class="flex items-center gap-3">
            <!-- Tanda Halaman Slide Dinamis -->
            <span id="slideCounter" class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg">
                1 / {{ count($schoolsData) ?: 1 }}
            </span>
            <div class="flex items-center gap-1">
                <button onclick="moveSlide('prev')"
                    class="w-8 h-8 rounded-lg border border-gray-200 bg-white shadow-sm flex items-center justify-center text-gray-500 hover:bg-gray-50 active:scale-95 transition-all cursor-pointer">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <button onclick="moveSlide('next')"
                    class="w-8 h-8 rounded-lg border border-gray-200 bg-white shadow-sm flex items-center justify-center text-gray-500 hover:bg-gray-50 active:scale-95 transition-all cursor-pointer">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden w-full mb-4 rounded-2xl select-none">
        <div id="schoolSlider"
            class="flex transition-transform duration-500 ease-out cursor-grab active:cursor-grabbing touch-pan-y"
            style="transform: translateX(0%);">
            @forelse($schoolsData as $index => $school)
                <div class="w-full flex-shrink-0 px-1">
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-50">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 flex-shrink-0 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-950 text-base leading-tight truncate">{{ $school->name }}
                                    </h4>
                                    <p class="text-xs text-gray-400 font-medium mt-0.5">ID Instansi: {{ $school->id }}</p>
                                </div>
                            </div>
                            <span
                                class="self-start sm:self-auto bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black tracking-wide uppercase">
                                Terverifikasi
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div
                                class="bg-gray-50/70 rounded-xl p-3 sm:p-4 border border-gray-50 text-center flex sm:flex-col items-center sm:justify-center justify-between gap-2">
                                <div class="flex items-center sm:block gap-2.5">
                                    <div
                                        class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center sm:mx-auto shadow-sm">
                                        <i class="fas fa-user-tie text-sm"></i>
                                    </div>
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-wider block text-left sm:text-center sm:mt-2">Total
                                        Guru</span>
                                </div>
                                <h5 class="text-lg sm:text-xl font-black text-gray-900">{{ $school->total_teachers }} <span
                                        class="text-xs font-normal text-gray-400">Pengajar</span></h5>
                            </div>

                            <div
                                class="bg-gray-50/70 rounded-xl p-3 sm:p-4 border border-gray-50 text-center flex sm:flex-col items-center sm:justify-center justify-between gap-2">
                                <div class="flex items-center sm:block gap-2.5">
                                    <div
                                        class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center sm:mx-auto shadow-sm">
                                        <i class="fas fa-user-graduate text-sm"></i>
                                    </div>
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-wider block text-left sm:text-center sm:mt-2">Total
                                        Murid</span>
                                </div>
                                <h5 class="text-lg sm:text-xl font-black text-gray-900">{{ $school->total_students }} <span
                                        class="text-xs font-normal text-gray-400">Siswa</span></h5>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="w-full bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                    Belum ada sekolah terdaftar di sistem.
                </div>
            @endforelse
        </div>
    </div>

    <div class="flex justify-center items-center gap-1.5 mb-8">
        @foreach ($schoolsData as $index => $school)
            <div
                class="slide-dot w-2 h-2 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-indigo-600 w-5' : 'bg-gray-200' }}">
            </div>
        @endforeach
    </div>

    <div class="mb-4">
        <h2 class="text-xs font-black text-gray-400 uppercase tracking-wider tracking-widest">Aktivitas Pembelian Kursus
            Terbaru</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto scroller-smooth">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 border-b">Student / Pembeli</th>
                        <th class="p-4 border-b">Kursus Yang Dibeli</th>
                        <th class="p-4 border-b">Tanggal Akses</th>
                        <th class="p-4 border-b text-center w-32 pr-6">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 pl-6">
                                <div class="font-bold text-gray-900">{{ $tx->user->name ?? 'Student Terhapus' }}</div>
                                <div class="text-xs text-gray-400">{{ $tx->user->email ?? '-' }}</div>
                            </td>
                            <td class="p-4 font-medium text-gray-800">
                                {{ $tx->course->title ?? 'Kursus Tidak Ditemukan' }}
                            </td>
                            <td class="p-4 text-xs text-gray-400 font-medium">
                                {{ $tx->created_at ? $tx->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </td>
                            <td class="p-4 text-center pr-6">
                                <span
                                    class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-bold inline-block">
                                    Sukses
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada aktivitas transaksi
                                pembelian kursus baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = {{ count($schoolsData) }};
        const slider = document.getElementById('schoolSlider');
        const counter = document.getElementById('slideCounter');
        const dots = document.querySelectorAll('.slide-dot');

        function updateSliderUI() {
            if (totalSlides <= 1) return;

            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
            counter.innerText = `${currentSlide + 1} / ${totalSlides}`;

            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.remove('bg-gray-200');
                    dot.classList.add('bg-indigo-600', 'w-5');
                } else {
                    dot.classList.remove('bg-indigo-600', 'w-5');
                    dot.classList.add('bg-gray-200');
                }
            });
        }

        function moveSlide(direction) {
            if (totalSlides <= 1) return;

            if (direction === 'next') {
                currentSlide = (currentSlide + 1) % totalSlides;
            } else {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            }

            updateSliderUI();
        }
        let startX = 0;
        let endX = 0;

        slider.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, {
            passive: true
        });

        slider.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            handleSwipe();
        }, {
            passive: true
        });

        function handleSwipe() {
            const threshold = 40;
            if (startX - endX > threshold) {
                moveSlide('next');
            } else if (endX - startX > threshold) {
                moveSlide('prev');
            }
        }
    </script>
@endsection
