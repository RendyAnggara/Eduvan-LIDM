@extends('layouts.teacher')

@section('title', 'Kelola Detail Materi')

@section('content')
    <div class="w-full">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4 w-full">
                <a href="{{ route('teacher.material.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-white border border-slate-200/70 rounded-full text-[11px] font-bold text-teal-600 hover:bg-slate-50 hover:text-teal-700 shadow-sm transition duration-150">
                    <span class="text-xs">&larr;</span> Kembali ke Daftar Mapel
                </a>
                <span class="text-[10px] font-bold text-slate-300 tracking-wider hidden sm:inline font-mono">EduLearn
                    Kurikulum</span>
            </div>
            <div
                class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
                <div>
                    <span
                        class="px-2.5 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold rounded uppercase tracking-wide">
                        Kelas {{ $course->grade_level }}
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight mt-1">{{ $course->title }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Struktur kurikulum Bab Pembelajaran dan ruang lingkup Pertemuan
                        Diferensiasi.</p>
                </div>

                <button onclick="toggleModal('modalLesson')"
                    class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-4 py-2.5 sm:py-2 rounded-xl text-xs transition shadow-sm shrink-0 w-full md:w-auto text-center justify-center">
                    Tambah Pertemuan Baru
                </button>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-xl shadow-xs">
                &checkmark; {{ session('success') }}
            </div>
        @endif
        <div class="space-y-4">
            @forelse($course->chapters as $index => $chapter)
                <div
                    class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden transition duration-150">

                    <div onclick="toggleAccordion('accordion-{{ $chapter->id }}')"
                        class="p-4 sm:p-5 bg-slate-50/50 hover:bg-slate-50 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between cursor-pointer select-none gap-3">

                        <div class="flex items-start gap-3 w-full md:flex-1 min-w-0">
                            <span
                                class="w-6 h-6 rounded-lg bg-slate-200 flex items-center justify-center text-xs font-mono text-slate-700 font-bold shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Materi
                                    Pokok</span>
                                <h4
                                    class="font-bold text-slate-800 text-sm sm:text-base tracking-tight leading-snug mt-0.5">
                                    {{ $chapter->title }}
                                </h4>
                            </div>
                        </div>
                        <div class="flex items-center justify-between md:justify-end gap-3 md:gap-4 shrink-0 w-full md:w-auto pt-3 md:pt-0 border-t border-slate-200/60 md:border-none"
                            onclick="event.stopPropagation();">
                            <div class="flex items-center gap-2">
                                <!-- Indikator Total Pertemuan -->
                                <span
                                    class="text-[10px] sm:text-xs font-bold text-slate-600 bg-white border border-slate-200 px-2.5 py-1 rounded-lg">
                                    {{ $chapter->lessons->count() }} Pertemuan
                                </span>
                                <form action="{{ route('teacher.material.destroy_chapter', $chapter->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus Bab ini beserta seluruh pertemuan di dalamnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-[10px] sm:text-xs font-bold text-rose-600 hover:text-rose-700 bg-white border border-rose-100 px-2.5 py-1 rounded-lg transition shadow-2xs">
                                        Hapus Bab
                                    </button>
                                </form>
                            </div>
                            <span id="icon-accordion-{{ $chapter->id }}"
                                class="text-slate-400 font-bold transition-transform duration-200 transform rotate-0 text-xs sm:text-sm pr-1">▼</span>
                        </div>
                    </div>
                    <div id="accordion-{{ $chapter->id }}"
                        class="hidden bg-slate-50/40 p-3 sm:p-5 space-y-3 border-b border-slate-100">
                        @forelse($chapter->lessons as $lesson)
                            <div
                                class="bg-white border border-slate-200/70 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                                <div class="flex flex-col gap-1 min-w-0">
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-700 tracking-tight leading-relaxed">
                                        {{ $lesson->title }}</h5>

                                    <!-- Badge Jalur Diferensiasi -->
                                    <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                        @if ($lesson->video_url)
                                            <span
                                                class="px-2 py-0.5 bg-sky-50 text-sky-700 border border-sky-100 text-[8px] sm:text-[9px] font-black rounded uppercase tracking-wide">
                                                Visual / Auditori
                                            </span>
                                        @endif
                                        @if ($lesson->content_text)
                                            <span
                                                class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 text-[8px] sm:text-[9px] font-black rounded uppercase tracking-wide">
                                                Teks Reading
                                            </span>
                                        @endif
                                        @if (!$lesson->video_url && !$lesson->content_text)
                                            <span
                                                class="px-2 py-0.5 bg-slate-100 text-slate-400 border border-slate-200 text-[8px] sm:text-[9px] font-bold rounded uppercase tracking-wide">
                                                Konten Kosong
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="flex items-center gap-2 justify-end pt-2.5 sm:pt-0 border-t border-slate-100 sm:border-none w-full sm:w-auto">
                                    <a href="{{ route('teacher.material.edit_content', $lesson->id) }}"
                                        class="px-3 py-1.5 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-[11px] rounded-lg transition shadow-2xs flex-1 sm:flex-none text-center">
                                        Edit Materi
                                    </a>

                                    <form action="{{ route('teacher.material.destroy_lesson', $lesson->id) }}"
                                        method="POST" class="flex-1 sm:flex-none"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertemuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full sm:w-auto px-3 py-1.5 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-[11px] rounded-lg transition shadow-2xs text-center">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div
                                class="py-6 text-center text-slate-400 text-xs font-medium border border-dashed border-slate-200 rounded-xl bg-white">
                                Belum ada agenda pertemuan atau sub-bab yang ditambahkan di dalam bab ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div
                    class="bg-white p-12 text-center rounded-2xl border border-slate-200 text-slate-400 text-xs font-medium">
                    Belum ada susunan Bab Pembelajaran yang dibuat untuk mata pelajaran ini. Silakan kembali ke halaman
                    utama untuk membuat Bab terlebih dahulu.
                </div>
            @endforelse
        </div>

        <div id="modalLesson"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center hidden z-50 p-4">
            <div
                class="bg-white rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-100 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-slate-800 text-base">Tambah Pertemuan Baru</h4>
                    <button onclick="toggleModal('modalLesson')"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form action="{{ route('teacher.material.store_lesson') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Pilih Bab Target</label>
                        <select name="chapter_id" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="">-- Pilih Bab --</option>
                            @foreach ($course->chapters as $cRow)
                                <option value="{{ $cRow->id }}">{{ $cRow->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Judul Pertemuan</label>
                        <input type="text" name="title" required
                            placeholder="Contoh: Pertemuan 1: Membedakan Fakta & Opini"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                    </div>

                    <div
                        class="pt-3 text-[11px] leading-relaxed font-medium text-slate-400 border-t border-dashed border-slate-200">
                        * Pengisian materi diferensiasi (Video & Teks Editor) akan diarahkan pada halaman formulir khusus
                        setelah nama pertemuan dibuat.
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100 pt-3">
                        <button type="button" onclick="toggleModal('modalLesson')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm">Lanjut
                            Buat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAccordion(id) {
            const container = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);

            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                container.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }
    </script>
@endsection
