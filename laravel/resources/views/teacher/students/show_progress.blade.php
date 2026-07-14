@extends('layouts.teacher')

@section('title', 'Detail Progres Siswa')

@section('content')
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('teacher.students.index') }}"
                class="text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1 bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition">
                &larr; Kembali ke Manajemen Siswa
            </a>
            <span class="text-xs font-semibold text-slate-400">EduLearn Progress</span>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col gap-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Monitoring Perkembangan Materi</span>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight mt-0.5">{{ $student->name }}</h2>
            <p class="text-xs font-medium text-slate-500">
                NISN: {{ $student->nisn_or_nip }} | Kelas: <span
                    class="text-teal-600 font-semibold">{{ $student->class }}</span>
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-800 text-base mb-6 tracking-tight">Akumulasi Progres per Mata Pelajaran</h4>

            <div class="space-y-6">
                @forelse($courses as $idx => $course)
                    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs">
                        <div
                            class="p-4 bg-slate-50/70 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-xs">
                                    {{ strtoupper(substr($course->title, 0, 2)) }}
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800 text-sm tracking-tight">{{ $course->title }}</h5>
                                    <p class="text-[11px] text-slate-400 font-bold">
                                        Total Ruang Lingkup: {{ $course->chapters->count() }} Bab Utama
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 sm:w-72 shrink-0 w-full justify-between sm:justify-end">
                                <div
                                    class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden border border-slate-300/40">
                                    <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ $course->average_progress }}%"></div>
                                </div>
                                <span
                                    class="text-xs font-black text-teal-700 w-12 text-right bg-teal-50 px-2 py-0.5 rounded border border-teal-100 shrink-0">
                                    {{ $course->average_progress }}%
                                </span>
                            </div>
                        </div>

                        <div class="p-4 bg-white">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-3">Detail
                                Capaian Pembelajaran Bab</span>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @forelse($course->chapters as $cIdx => $chapter)
                                    @php
                                        $isFinished =
                                            $course->average_progress >=
                                            (($cIdx + 1) / max($course->chapters->count(), 1)) * 100;
                                    @endphp
                                    <div
                                        class="p-3 rounded-xl border transition flex items-center justify-between gap-3 {{ $isFinished ? 'bg-emerald-50/30 border-emerald-200/80' : 'bg-slate-50/40 border-slate-200' }}">
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <span class="text-[10px] font-bold text-slate-400">Bab
                                                {{ $cIdx + 1 }}</span>
                                            <p class="text-xs font-bold text-slate-700 truncate tracking-tight">
                                                {{ $chapter->title }}</p>
                                        </div>

                                        <span
                                            class="shrink-0 text-[10px] font-black px-2 py-0.5 rounded-md border {{ $isFinished ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-200/60 text-slate-500 border-slate-300/30' }}">
                                            {{ $isFinished ? 'Selesai' : 'Belum' }}
                                        </span>
                                    </div>
                                @empty
                                    <div
                                        class="col-span-2 py-3 text-center text-xs text-slate-400 font-medium border border-dashed border-slate-200 rounded-xl">
                                        Belum ada rincian materi bab yang ditambahkan pada mata pelajaran ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                        <p class="text-sm font-bold text-slate-500">Mata Pelajaran Kosong</p>
                        <p class="text-xs text-slate-400 mt-1">Belum ada instrumen kurikulum yang aktif untuk tingkat kelas
                            siswa saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
