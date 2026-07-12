@extends('layouts.teacher')

@section('title', 'Review Jawaban Kuis')

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
                    class="text-4xl sm:text-5xl font-black {{ $quizResult->score >= 75 ? 'text-teal-600' : 'text-rose-500' }} tracking-tight">
                    {{ $quizResult->score }}
                </span>
                <div class="mt-2">
                    <span
                        class="text-[11px] font-bold px-2.5 py-0.5 rounded-md {{ $quizResult->score >= 75 ? 'bg-teal-50 text-teal-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ $quizResult->score >= 75 ? 'Bagus' : 'Perlu di tingkatkan' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 w-full">
            <h4 class="font-bold text-slate-800 text-base mb-5 tracking-tight">Daftar Analisis Butir Soal</h4>

            <div class="space-y-5">
                @forelse($studentAnswers ?? [] as $index => $answer)
                    <div
                        class="p-4 rounded-xl border {{ $answer->is_correct ? 'bg-emerald-50/40 border-emerald-200' : 'bg-rose-50/40 border-rose-200' }} flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-xs font-bold text-slate-700">Soal Nomor {{ $index + 1 }}</span>
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $answer->is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $answer->is_correct ? 'Benar' : 'Salah' }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 mt-1 leading-relaxed">{{ $answer->question_text }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 text-xs">
                            <div class="p-2.5 rounded-lg bg-white border border-slate-200/80">
                                <span class="text-slate-400 block font-bold text-[10px] uppercase tracking-wide">Jawaban
                                    Siswa:</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $answer->student_choice }}</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-white border border-slate-200/80">
                                <span class="text-slate-400 block font-bold text-[10px] uppercase tracking-wide">Kunci
                                    Jawaban Benar:</span>
                                <span class="font-bold text-emerald-700 mt-0.5 block">{{ $answer->correct_choice }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="text-center py-12 text-slate-400 border border-dashed border-slate-200 rounded-xl bg-slate-50/50 w-full">
                        <p class="text-sm font-bold text-slate-500">Rekap Jawaban Tersimpan</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto px-4 leading-relaxed">
                            Nilai akumulasi total {{ $quizResult->score }} sudah masuk ke sistem rapot digital.
                            Integrasikan tabel data jawaban siswa untuk melihat rincian pilihan per nomor soal secara
                            mendalam.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
