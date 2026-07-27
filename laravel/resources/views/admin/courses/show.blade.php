@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6 w-full">

        <!-- HEADER UTAMA DENGAN TOMBOL BACK RATA POJOK KIRI -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}"
                class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center transition shadow-sm shrink-0 active:scale-95"
                title="Kembali ke Daftar Kursus">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight truncate">{{ $course->title }}
                    </h1>
                    @if ($course->course_type === 'school')
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 shrink-0">
                            <i class="fas fa-school text-[9px] mr-1"></i> Sekolah
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shrink-0">
                            <i class="fas fa-crown text-[9px] mr-1"></i> Marketplace
                        </span>
                    @endif
                </div>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola materi dan urutan pembelajaran kursus.</p>
            </div>
        </div>

        <!-- ALERT ERROR VALIDASI -->
        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs space-y-1">
                <p class="font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan input materi:
                </p>
                <ul class="list-disc pl-5 space-y-0.5 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ALERT SUKSES -->
        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
                <i class="fas fa-check-circle text-sm text-emerald-600"></i> {{ session('success') }}
            </div>
        @endif

        <!-- GRID UTAMA (RESPONSIF 1 KOLOM DI MOBILE, 3 KOLOM DI DESKTOP) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- KOLOM KIRI: FORM TAMBAH MATERI / CARD READ-ONLY INFO -->
            <div class="w-full">
                @if ($course->course_type !== 'school')
                    <!-- FORM TAMBAH MATERI (KHUSUS KURSUS ADMIN) -->
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                            <i class="fas fa-plus-circle text-indigo-600 text-sm"></i>
                            <h3 class="font-bold text-gray-800 text-sm">Tambah Materi Baru</h3>
                        </div>
                        <form action="{{ route('admin.courses.storeContent', $course->id) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Judul
                                    Materi <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" value="{{ old('title') }}" required
                                    class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition"
                                    placeholder="Contoh: Pengenalan Sintaks Dasar">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">URL
                                    Video (YouTube/Vimeo) <span class="text-rose-500">*</span></label>
                                <input type="text" name="video_url" value="{{ old('video_url') }}" required
                                    class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Urutan
                                    (Order)</label>
                                <input type="number" name="order"
                                    value="{{ old('order', count($course->contents) + 1) }}" min="1"
                                    class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>
                            <button type="submit"
                                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-lg shadow-indigo-600/10 transition active:scale-95 cursor-pointer flex items-center justify-center gap-2">
                                <i class="fas fa-save text-xs"></i> Simpan Materi
                            </button>
                        </form>
                    </div>
                @else
                    <!-- CARD INFO READ-ONLY (KHUSUS KURSUS GURU/SEKOLAH) -->
                    <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-5 space-y-3">
                        <div class="flex items-center gap-2 text-blue-900 font-bold text-xs uppercase tracking-wider">
                            <i class="fas fa-lock text-blue-600"></i> Mode Pantau (Read-Only)
                        </div>
                        <p class="text-xs text-blue-800 leading-relaxed font-medium">
                            Materi pembelajaran pada kursus instansi ini dikelola secara independen oleh <strong
                                class="font-bold text-blue-950">Guru Pengampu Sekolah</strong>.
                        </p>
                        <div
                            class="p-3 bg-white/80 rounded-xl border border-blue-100/80 text-[11px] text-gray-600 space-y-1">
                            <span class="font-bold text-gray-800 block">Hak Akses Admin:</span>
                            <ul class="list-disc pl-4 space-y-0.5">
                                <li>Memantau urutan & struktur materi video.</li>
                                <li>Dilarang menambah/mengedit materi internal.</li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <!-- KOLOM KANAN: DAFTAR MATERI (TAMPILAN DUAL DESKTOP & MOBILE) -->
            <div class="lg:col-span-2 w-full min-w-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-list text-slate-400 text-sm"></i>
                            <h3 class="font-bold text-gray-800 text-sm">Daftar Materi Pembelajaran</h3>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-full">
                            Total: {{ count($course->contents) }} Video
                        </span>
                    </div>

                    <!-- 🖥️ DESKTOP VIEW: Tabel Tradisional -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead
                                class="bg-gray-50/70 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="py-3.5 px-4 w-16 text-center">Order</th>
                                    <th class="py-3.5 px-4">Judul Materi</th>
                                    <th class="py-3.5 px-4 w-28 text-center">Tipe</th>
                                    @if ($course->course_type !== 'school')
                                        <th class="py-3.5 px-4 w-20 text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($course->contents->sortBy('order') as $content)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3.5 px-4 text-center font-bold text-gray-400">{{ $content->order }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-gray-900 leading-snug">
                                            {{ $content->title }}
                                            @if ($content->content_url)
                                                <a href="{{ $content->content_url }}" target="_blank"
                                                    class="text-[11px] font-semibold text-indigo-600 hover:underline block mt-0.5 truncate max-w-md">
                                                    <i class="fas fa-external-link-alt text-[9px] mr-0.5"></i>
                                                    {{ $content->content_url }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded text-[10px] font-black uppercase tracking-wider">
                                                VIDEO
                                            </span>
                                        </td>
                                        @if ($course->course_type !== 'school')
                                            <td class="py-3.5 px-4 text-center">
                                                <form action="{{ route('admin.courses.destroyContent', $content->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                                        title="Hapus Materi">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $course->course_type !== 'school' ? 4 : 3 }}"
                                            class="py-10 text-center text-gray-400 italic font-medium">
                                            Belum ada materi pembelajaran yang diunggah untuk kursus ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- 📱 MOBILE VIEW: List Card Vertikal (EduVan Screen) -->
                    <div class="block md:hidden p-4 space-y-3">
                        @forelse($course->contents->sortBy('order') as $content)
                            <div class="bg-gray-50/60 rounded-xl border border-gray-100 p-3.5 space-y-2 shadow-sm">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span
                                            class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $content->order }}
                                        </span>
                                        <h4 class="font-bold text-gray-900 text-xs leading-snug truncate">
                                            {{ $content->title }}</h4>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-1.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded text-[9px] font-black uppercase tracking-wider shrink-0">
                                        VIDEO
                                    </span>
                                </div>

                                @if ($content->content_url)
                                    <a href="{{ $content->content_url }}" target="_blank"
                                        class="text-[11px] font-medium text-indigo-600 hover:underline block truncate pl-8">
                                        <i class="fas fa-external-link-alt text-[9px] mr-1"></i> Tonton Link Video
                                    </a>
                                @endif

                                @if ($course->course_type !== 'school')
                                    <div class="pt-2 border-t border-gray-200/60 flex justify-end">
                                        <form action="{{ route('admin.courses.destroyContent', $content->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200/60 rounded-lg text-[10px] font-bold flex items-center gap-1 transition active:bg-rose-100">
                                                <i class="fas fa-trash text-[9px]"></i> Hapus Materi
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-xs italic font-medium">
                                Belum ada materi pembelajaran yang diunggah untuk kursus ini.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
