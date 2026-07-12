@extends('layouts.teacher')

@section('title', 'Riwayat Hasil Kuis')

@section('content')
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('teacher.students.index') }}"
                class="text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1 bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition">
                &larr; Kembali ke Manajemen Siswa
            </a>
            <span class="text-xs font-semibold text-slate-400">EduLearn Quiz</span>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col gap-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Riwayat Nilai & Pengecekan Batas
                Waktu</span>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight mt-0.5">{{ $student->name }}</h2>
            <p class="text-xs font-medium text-slate-500">
                NISN: {{ $student->nisn_or_nip }} | Kelas: <span
                    class="text-teal-600 font-semibold">{{ $student->class }}</span>
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-800 text-base mb-6 tracking-tight">Daftar Hasil Evaluasi Kuis Per Bab</h4>
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4 rounded-l-xl">NO</th>
                            <th class="py-3 px-4">MATA PELAJARAN</th>
                            <th class="py-3 px-4">JUDUL KUIS BAB</th>
                            <th class="py-3 px-4 text-center">DURASI KUIS</th>
                            <th class="py-3 px-4 text-center">LAMA MENGERJAKAN</th>
                            <th class="py-3 px-4 text-center">STATUS PENGERJAAN</th>
                            <th class="py-3 px-4 text-center">NILAI</th>
                            <th class="py-3 px-4 text-center rounded-r-xl">DETAIL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                        @forelse($allQuizzes as $index => $qRow)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-4 text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="py-4 px-4">
                                    <span
                                        class="px-2 py-1 rounded bg-slate-100 border border-slate-200 text-xs text-slate-700 font-bold">
                                        {{ $qRow['mapel_name'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-800">{{ $qRow['quiz_title'] }}</td>
                                <td class="py-4 px-4 text-center text-xs font-mono text-slate-500">{{ $qRow['time_limit'] }}
                                </td>
                                <td
                                    class="py-4 px-4 text-center text-xs font-mono {{ $qRow['duration'] != '-' ? 'text-slate-700' : 'text-slate-400' }}">
                                    {{ $qRow['duration'] }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if ($qRow['status'] === 'Sudah Mengerjakan')
                                        <span
                                            class="px-2.5 py-0.5 rounded text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">Selesai</span>
                                    @elseif($qRow['status'] === 'Belum Mengerjakan')
                                        <span
                                            class="px-2.5 py-0.5 rounded text-xs font-black bg-amber-50 text-amber-700 border border-amber-100">Belum
                                            Dikerjakan</span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded text-xs font-black bg-rose-50 text-rose-700 border border-rose-100">Tidak
                                            Mengerjakan</span>
                                    @endif
                                </td>
                                <td
                                    class="py-4 px-4 text-center font-extrabold text-sm font-mono {{ is_numeric($qRow['score']) && $qRow['score'] >= 75 ? 'text-teal-600' : ($qRow['score'] === '-' ? 'text-slate-300' : 'text-rose-500') }}">
                                    {{ $qRow['score'] }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if ($qRow['quiz_result_id'])
                                        <a href="{{ route('teacher.students.review_quiz', ['student_id' => $student->id, 'quiz_result_id' => $qRow['quiz_result_id']]) }}"
                                            class="inline-flex items-center text-[11px] bg-slate-800 hover:bg-slate-900 text-white font-bold px-3 py-1.5 rounded-xl transition shadow-xs">
                                            Lihat Jawaban
                                        </a>
                                    @else
                                        <span class="text-slate-300 font-bold font-mono">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 font-medium">Belum ada instrumen
                                    kuis yang terbit untuk tingkat kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="block md:hidden space-y-4">
                @forelse($allQuizzes as $index => $qRow)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col gap-3 shadow-xs">
                        <!-- Baris Atas: Badge Mapel & Status -->
                        <div class="flex items-center justify-between gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-slate-200/70 text-[10px] font-extrabold text-slate-700 max-w-[50%] truncate">
                                {{ $qRow['mapel_name'] }}
                            </span>

                            <div>
                                @if ($qRow['status'] === 'Sudah Mengerjakan')
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200/50">Selesai</span>
                                @elseif($qRow['status'] === 'Belum Mengerjakan')
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200/50">Belum</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200/50">Tidak
                                        Ikut</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Materi
                                Evaluasi</span>
                            <h5 class="font-black text-slate-800 text-sm tracking-tight mt-0.5">
                                {{ $qRow['quiz_title'] }}
                            </h5>
                        </div>
                        <div
                            class="grid grid-cols-3 gap-2 bg-white p-2.5 rounded-xl border border-slate-200/60 text-center items-center">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">Batas</span>
                                <span class="text-xs font-bold text-slate-700 font-mono">{{ $qRow['time_limit'] }}</span>
                            </div>
                            <div class="flex flex-col gap-0.5 border-x border-slate-100">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">Durasi</span>
                                <span class="text-xs font-bold text-slate-700 font-mono">{{ $qRow['duration'] }}</span>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">Nilai</span>
                                <span
                                    class="text-sm font-black font-mono {{ is_numeric($qRow['score']) && $qRow['score'] >= 75 ? 'text-teal-600' : ($qRow['score'] === '-' ? 'text-slate-300' : 'text-rose-500') }}">
                                    {{ $qRow['score'] }}
                                </span>
                            </div>
                        </div>
                        @if ($qRow['quiz_result_id'])
                            <a href="{{ route('teacher.students.review_quiz', ['student_id' => $student->id, 'quiz_result_id' => $qRow['quiz_result_id']]) }}"
                                class="w-full bg-slate-900 hover:bg-slate-950 active:bg-black text-white font-bold text-xs py-2.5 rounded-xl transition shadow-xs flex items-center justify-center mt-1 tracking-wide">
                                Lihat Lembar Jawaban
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-400 font-medium">Belum ada instrumen kuis yang terbit
                        untuk tingkat kelas ini.</div>
                @endforelse
            </div>

        </div>
    </div>
@endsection
