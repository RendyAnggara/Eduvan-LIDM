@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6">
        <div
            class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight pt-1">
                    Quiz & Student Progress
                </h1>
                <p class="text-xs text-gray-500">Pantau efektivitas materi, kelengkapan kuis, dan progres student secara
                    real-time.</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div
                    class="px-3.5 py-2 bg-indigo-50/70 text-indigo-700 border border-indigo-100/80 rounded-xl font-black text-xs flex items-center gap-2">
                    <i class="fas fa-layer-group text-indigo-500 text-sm"></i>
                    <span>{{ $courses->count() }} Kursus Aktif</span>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 min-h-[90px]">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider truncate">Total Kursus</p>
                    <h3 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight mt-1">
                        {{ $courses->count() }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 min-h-[90px]">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shrink-0">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider truncate">Student Tuntas</p>
                    <h3 class="text-xl sm:text-2xl font-black text-emerald-600 leading-tight mt-1">
                        {{ $totalCompleted ?? 0 }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 min-h-[90px]">
                <div
                    class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base font-bold shrink-0">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider truncate">Kuis Selesai Dikerjakan</p>
                    <h3 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight mt-1">
                        {{ $totalQuizDoneCount ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-list-ul text-indigo-600 text-sm"></i>
                    <h4 class="font-black text-gray-900 text-base tracking-tight">Progres Kuis Per Kursus</h4>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <form action="{{ route('admin.quiz.index') }}" method="GET" class="relative w-full md:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" id="courseSearchInput" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama kursus..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm">
                    </form>

                    @if (request('search'))
                        <a href="{{ route('admin.quiz.index') }}"
                            class="text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 border border-rose-100">
                            <i class="fas fa-times-circle"></i> Reset
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse" id="courseDataTable">
                    <thead
                        class="bg-gray-50/80 text-gray-400 text-xs uppercase font-bold tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="p-4 pl-6">Nama Kursus</th>
                            <th class="p-4 text-center">Soal Kuis</th>
                            <th class="p-4 text-center">Total Student</th>
                            <th class="p-4 text-center">Student Tuntas</th>
                            <th class="p-4 text-right pr-6 w-72">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                        @forelse ($courses as $course)
                            <tr class="hover:bg-gray-50/80 transition-colors course-row">
                                <td class="p-4 pl-6 font-bold text-gray-900 course-title-text">
                                    {{ $course->title }}
                                </td>
                                <td class="p-4 text-center">
                                    @if (($course->quizzes_count ?? 0) > 0)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl font-bold text-xs">
                                            <i class="fas fa-check-circle text-[10px]"></i> {{ $course->quizzes_count }}
                                            Soal
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-500 rounded-xl font-medium text-xs">
                                            Belum Ada Soal
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl font-bold text-xs">
                                        {{ $course->users_count }} Student
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl font-bold text-xs">
                                        {{ $course->completed_count ?? 0 }} Selesai
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6 space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.quiz.show', $course->id) }}"
                                        class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-100 px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                                        <i class="fas fa-chart-line text-[10px]"></i> Detail Progres
                                    </a>
                                    <a href="{{ route('admin.quiz.manage', $course->id) }}"
                                        class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                                        <i class="fas fa-tasks text-[10px]"></i> Kelola Soal
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center text-gray-400 italic">
                                    <i class="fas fa-inbox text-3xl mb-2 text-gray-300 block"></i>
                                    Belum ada data kursus berbayar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="block md:hidden p-3.5 space-y-3 bg-gray-50/30" id="mobileCardsContainer">
                @forelse ($courses as $course)
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3 mobile-card">
                        <div class="flex justify-between items-start gap-2">
                            <div class="space-y-1 max-w-[65%]">
                                <h4 class="font-bold text-gray-900 text-sm leading-tight course-title">
                                    {{ $course->title }}
                                </h4>
                                @if (($course->quizzes_count ?? 0) > 0)
                                    <span class="text-[10px] font-bold text-emerald-600 block flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> {{ $course->quizzes_count }} Soal Tersedia
                                    </span>
                                @else
                                    <span class="text-[10px] text-gray-400 block">Belum ada soal kuis</span>
                                @endif
                            </div>
                            <span
                                class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg font-bold text-xs whitespace-nowrap">
                                {{ $course->users_count }} Student
                            </span>
                        </div>

                        <div class="pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                            <span class="text-gray-400">Student Tuntas:</span>
                            <span class="font-bold text-blue-600">{{ $course->completed_count ?? 0 }} Student</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('admin.quiz.show', $course->id) }}"
                                class="bg-indigo-600 text-white py-2 rounded-xl text-xs font-bold text-center active:bg-indigo-700 transition flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fas fa-chart-line text-[10px]"></i> Progres
                            </a>
                            <a href="{{ route('admin.quiz.manage', $course->id) }}"
                                class="bg-amber-50 text-amber-700 border border-amber-200 py-2 rounded-xl text-xs font-bold text-center active:bg-amber-100 transition flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fas fa-tasks text-[10px]"></i> Kelola Soal
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-xs italic border border-gray-100">
                        <i class="fas fa-inbox text-2xl mb-1 text-gray-300 block"></i>
                        Belum ada data kursus berbayar.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <script>
        "use strict";

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

                    document.querySelectorAll('#mobileCardsContainer .mobile-card').forEach(card => {
                        const title = card.querySelector('.course-title').textContent.toLowerCase();
                        card.style.display = title.includes(filterValue) ? 'block' : 'none';
                    });
                });
            }
        });
    </script>
@endsection
