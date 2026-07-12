@extends('layouts.teacher')

@section('title', 'Bank Soal & Kuis')

@section('content')
    <div class="w-full">
        <div
            class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight font-sans">Soal & Kuis Mandiri</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kelola paket evaluasi pilihan ganda terintegrasi untuk siswa
                    EduLearn.</p>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                <button onclick="toggleModal('modalCreateQuiz')"
                    class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 sm:px-4 sm:py-2 rounded-xl text-xs transition shadow-sm w-full md:w-auto text-center justify-center flex items-center gap-1.5 tracking-wide">
                    Buat Paket Kuis Baru
                </button>
            </div>
        </div>
        @if (session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-xl shadow-2xs">
                &checkmark; {{ session('success') }}
            </div>
        @endif
        @if ($quizzes->isNotEmpty())
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6 w-full overflow-hidden">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lompat Cepat Ke Mata
                        Pelajaran</span>
                    <span class="text-[9px] font-medium text-slate-300 block sm:hidden">(Geser Layar &rarr;)</span>
                </div>
                <div
                    class="flex flex-nowrap items-center gap-2 overflow-x-auto pb-2 pt-0.5 scrollbar-none w-full scroll-smooth">
                    @php
                        $bookmarks = $quizzes->sortBy(['course.grade_level', 'course.title'])->unique('course_id');
                    @endphp
                    @foreach ($bookmarks as $bookmark)
                        @php
                            $cleanSlug =
                                'mapel-' .
                                ($bookmark->course->grade_level ?? '0') .
                                '-' .
                                ($bookmark->course_id ?? '0');
                        @endphp
                        <a href="#{{ $cleanSlug }}"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-50 hover:bg-teal-50 border border-slate-200/80 hover:border-teal-200 rounded-xl text-xs font-bold text-slate-600 hover:text-teal-700 transition duration-150 shadow-2xs whitespace-nowrap shrink-0 group">
                            <span
                                class="text-[8px] sm:text-[9px] px-1.5 py-0.5 bg-slate-200 group-hover:bg-teal-100/60 text-slate-700 group-hover:text-teal-800 font-mono rounded font-black transition">K-{{ $bookmark->course->grade_level }}</span>
                            <span>{{ $bookmark->course->title }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="space-y-8">
            @php
                $quizzesGroupedByClass = $quizzes->groupBy(function ($item) {
                    return $item->course->grade_level ?? 'Umum';
                });

                $availableClasses = ['7', '8', '9'];
            @endphp

            @foreach ($availableClasses as $class)
                @php
                    $classQuizzes = $quizzesGroupedByClass->get($class, collect());
                @endphp
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 w-full space-y-6">
                    <div
                        class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block shrink-0"></span>
                                Kurikulum Uji Mandiri - Kelas {{ $class }}
                            </h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Daftar paket evaluasi aktif khusus
                                untuk tingkatan kelas {{ $class }}.</p>
                        </div>

                        <div class="shrink-0 w-full sm:w-auto">
                            <span
                                class="px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 text-[10px] font-bold rounded-lg block text-center sm:inline-block">
                                {{ $classQuizzes->count() }} Kuis Aktif
                            </span>
                        </div>
                    </div>

                    @if ($classQuizzes->isNotEmpty())
                        @php
                            $quizzesGroupedByCourse = $classQuizzes->groupBy('course_id');
                        @endphp

                        <div class="space-y-6">
                            @foreach ($quizzesGroupedByCourse as $courseId => $courseQuizzes)
                                @php
                                    $firstQuiz = $courseQuizzes->first();
                                    $courseTitle = $firstQuiz->course->title ?? 'Mata Pelajaran Umum';
                                    $sortedCourseQuizzes = $courseQuizzes->sortBy('title');
                                    $courseSlug = 'mapel-' . $class . '-' . $courseId;
                                @endphp
                                <div id="{{ $courseSlug }}"
                                    class="space-y-3 bg-slate-50/40 p-3 sm:p-4 border border-slate-200/40 rounded-xl scroll-mt-6 target:ring-2 target:ring-teal-500/30 target:border-teal-300 transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2.5 py-0.5 bg-teal-50 text-teal-700 border border-teal-200/60 text-[10px] font-black rounded-md uppercase">
                                                {{ $courseTitle }}
                                            </span>
                                            <span
                                                class="text-[10px] text-slate-400 font-bold font-mono">({{ $sortedCourseQuizzes->count() }}
                                                Paket Bab)</span>
                                        </div>
                                        <a href="#"
                                            class="text-[10px] font-bold text-slate-400 hover:text-teal-600 transition">&uarr;
                                            Ke Atas</a>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach ($sortedCourseQuizzes as $quiz)
                                            <div
                                                class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col justify-between shadow-2xs relative group">

                                                <div class="space-y-1.5">
                                                    <div class="flex items-center justify-between">
                                                        <span
                                                            class="px-2 py-0.5 bg-slate-100 text-slate-500 border border-slate-200/60 text-[9px] font-bold rounded uppercase font-mono">
                                                            {{ $quiz->time_limit }} Mnt
                                                        </span>
                                                    </div>

                                                    <h4
                                                        class="text-sm font-bold text-slate-800 tracking-tight leading-snug pt-0.5">
                                                        {{ $quiz->title }}
                                                    </h4>

                                                    <p class="text-[11px] text-slate-400 font-medium pb-2">
                                                        Paket kuis ini berisi <strong
                                                            class="text-slate-600 font-bold">{{ $quiz->questions_count }}
                                                            pertanyaan</strong>.
                                                    </p>
                                                </div>
                                                <div
                                                    class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 w-full mt-auto">
                                                    <a href="{{ route('teacher.quiz.questions', $quiz->id) }}"
                                                        class="py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-[11px] rounded-lg transition border border-slate-950 text-center justify-center tracking-wide">
                                                        Kelola Soal
                                                    </a>

                                                    <form action="{{ route('teacher.quiz.destroy', $quiz->id) }}"
                                                        method="POST" class="w-full"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket kuis ini? Seluruh pertanyaan di dalamnya akan ikut terhapus permanen.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-full py-2 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-[11px] rounded-lg transition text-center justify-center">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="py-8 text-center text-slate-400 text-xs font-medium bg-slate-50/20 border border-dashed border-slate-200 rounded-xl">
                            Belum ada paket kuis terdaftar untuk Kelas {{ $class }}.
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
        <div id="modalCreateQuiz"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center hidden z-50 p-4 animate-in fade-in duration-150">
            <div
                class="bg-white rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-100 w-full max-w-md max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-slate-800 text-base">Buat Paket Kuis Baru</h4>
                    <button onclick="toggleModal('modalCreateQuiz')"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form action="{{ route('teacher.quiz.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Pilih Mata Pelajaran Induk</label>
                        <select name="course_id" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach ($courses as $cRow)
                                <option value="{{ $cRow->id }}">{{ $cRow->title }} (Kelas {{ $cRow->grade_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Nama / Judul Kuis</label>
                        <input type="text" name="title" required
                            placeholder="Contoh: Kuis Akhir Bab 1: Mengenal Teks Eksplanasi"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Batas Waktu Pengerjaan (Menit)</label>
                        <input type="number" name="time_limit" required min="1" value="30"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-mono font-bold">
                        <span class="text-[10px] text-slate-400 leading-tight mt-0.5 block">* Siswa otomatis dipaksa selesai
                            jika durasi menit berakhir.</span>
                    </div>

                    <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" onclick="toggleModal('modalCreateQuiz')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm">Simpan
                            & Lanjut</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <style>
        html {
            scroll-behavior: smooth;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <script>
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
