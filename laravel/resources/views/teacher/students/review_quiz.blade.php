@extends('layouts.teacher')

@section('title', 'Review Jawaban Kuis & Koreksi Essay')

@section('content')
    <div class="w-full">
        <div class="mb-5 flex items-center justify-between gap-2">
            <a href="{{ route('teacher.students.show_quizzes', $student->id) }}"
                class="text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1 bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition w-fit">
                &larr; Kembali ke Riwayat Kuis
            </a>
            <span class="text-xs font-semibold text-slate-400 hidden sm:inline">Lembar Koreksi Evaluasi</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 items-stretch">
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100 md:col-span-2 flex flex-col justify-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Evaluasi Hasil Belajar</span>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight mt-0.5">{{ $student->name }}</h2>
                <p class="text-xs font-medium text-slate-500 mt-1">
                    NISN: {{ $student->nisn_or_nip }} | Kelas: {{ $student->class }}
                </p>
                <div class="mt-3">
                    <span
                        class="text-xs text-slate-600 font-medium bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 inline-block">
                        Mata Pelajaran: <span
                            class="text-teal-600 font-bold">{{ $quizResult->course ? $quizResult->course->title : 'Umum' }}</span>
                    </span>
                </div>
            </div>

            <div
                class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Nilai Akhir Siswa</span>
                <span
                    class="text-4xl sm:text-5xl font-black {{ ($quizResult->score ?? 0) >= 75 ? 'text-teal-600' : 'text-slate-800' }} tracking-tight font-mono">
                    {{ $quizResult->score ?? '0' }}
                </span>
                <div class="mt-2">
                    @if (($quizResult->status ?? '') === 'Sudah Dinilai')
                        <span
                            class="text-[11px] font-bold px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                            &checkmark; Sudah Dinilai Guru
                        </span>
                    @else
                        <span
                            class="text-[11px] font-bold px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                            &bull; Perlu Koreksi Essay
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-xl shadow-2xs flex items-center gap-2">
                <span>&checkmark;</span> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-xl shadow-2xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('teacher.students.grade_essay', [$student->id, $quizResult->id]) }}" method="POST">
            @csrf

            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 w-full mb-6">
                <h4 class="font-bold text-slate-800 text-base mb-5 tracking-tight">Daftar Analisis Butir Soal & Koreksi
                    Jawaban</h4>

                <div class="space-y-5">
                    @forelse($studentAnswers as $index => $answer)
                        @php
                            $question = $answer->question;
                            $isEssay = ($question->type ?? 'multiple_choice') === 'essay';
                        @endphp

                        <div
                            class="p-4 rounded-xl border {{ $isEssay ? 'bg-purple-50/20 border-purple-200' : ($answer->is_correct ? 'bg-emerald-50/40 border-emerald-200' : 'bg-rose-50/40 border-rose-200') }} flex flex-col gap-3">
                            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-700">Soal Nomor {{ $index + 1 }}</span>
                                    @if ($isEssay)
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded uppercase bg-purple-100 text-purple-800">
                                            Essay / Uraian
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded uppercase bg-teal-100 text-teal-800">
                                            Pilihan Ganda
                                        </span>
                                    @endif
                                </div>

                                {{-- Status Penilaian Per Nomor --}}
                                @if (!$isEssay)
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $answer->is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $answer->is_correct ? 'Benar (+100)' : 'Salah (0)' }}
                                    </span>
                                @else
                                    @if (($quizResult->status ?? '') === 'Sudah Dinilai')
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            &checkmark; Skor Tersimpan: {{ $answer->score ?? 0 }} Poin
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                            &bull; Belum Diisi Nilai
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <!-- Teks Pertanyaan -->
                            <p class="text-sm font-semibold text-slate-800 mt-0.5 leading-relaxed">
                                {{ $question->question_text ?? 'Informasi pertanyaan tidak ditemukan.' }}
                            </p>

                            <!-- Tampilan Jawaban -->
                            @if (!$isEssay)
                                <!-- Tampilan PG -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1 text-xs">
                                    <div class="p-2.5 rounded-lg bg-white border border-slate-200">
                                        <span
                                            class="text-slate-400 block font-bold text-[10px] uppercase tracking-wide">Jawaban
                                            Siswa:</span>
                                        <span
                                            class="font-bold text-slate-800 mt-0.5 block">{{ $answer->answer_text ?? '-' }}</span>
                                    </div>
                                    <div class="p-2.5 rounded-lg bg-white border border-slate-200">
                                        <span
                                            class="text-slate-400 block font-bold text-[10px] uppercase tracking-wide">Kunci
                                            Jawaban Benar:</span>
                                        <span
                                            class="font-bold text-emerald-700 mt-0.5 block">{{ $question->correct_answer ?? '-' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3 mt-1">
                                    <div>
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                            Uraian Jawaban Siswa:
                                        </span>
                                        <div
                                            class="p-3.5 bg-white border border-purple-100 rounded-xl text-xs sm:text-sm text-slate-800 leading-relaxed font-normal min-h-[70px]">
                                            {!! nl2br(e($answer->answer_text ?? 'Siswa tidak mengisi jawaban.')) !!}
                                        </div>
                                    </div>

                                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Skor Essay (0 - 100) <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="number" name="essay_scores[{{ $answer->id }}]"
                                                    min="0" max="100" step="0.5"
                                                    value="{{ old('essay_scores.' . $answer->id, $answer->score ?? 0) }}"
                                                    required placeholder="Contoh: 85"
                                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-500">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Catatan / Feedback Guru (Opsional)
                                                </label>
                                                <input type="text" name="teacher_notes[{{ $answer->id }}]"
                                                    value="{{ old('teacher_notes.' . $answer->id, $answer->teacher_note) }}"
                                                    placeholder="Berikan masukan atau catatan untuk siswa..."
                                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @empty
                        <div
                            class="text-center py-12 text-slate-400 border border-dashed border-slate-200 rounded-xl bg-slate-50/50 w-full">
                            <p class="text-sm font-bold text-slate-500">Belum ada rincian lembar jawaban tersimpan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if (count($studentAnswers ?? []) > 0)
                <div
                    class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between gap-4 sticky bottom-4">
                    <p class="text-xs text-slate-500 font-medium hidden sm:block">
                        Klik tombol di samping untuk mengalkulasi ulang total skor kuis siswa.
                    </p>
                    <button type="submit"
                        class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-6 py-3 rounded-xl text-xs transition shadow-md w-full sm:w-auto text-center">
                        Simpan Koreksi & Update Nilai Akhir
                    </button>
                </div>
            @endif
        </form>
    </div>
@endsection
