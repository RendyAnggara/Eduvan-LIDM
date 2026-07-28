@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6">
        <div
            class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.quiz.index') }}"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 hover:bg-indigo-50 text-gray-500 hover:text-indigo-600 rounded-lg font-bold text-[11px] transition border border-gray-100">
                        <i class="fas fa-arrow-left text-[10px]"></i> Kembali
                    </a>
                    <span class="text-xs font-bold text-gray-300">/</span>
                    <span
                        class="text-xs font-bold text-indigo-600 bg-indigo-50/60 px-2.5 py-0.5 rounded-lg border border-indigo-100/50">
                        {{ $course->title }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight pt-1">
                    Progres Student & Nilai Kuis
                </h1>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 min-h-[90px]">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fas fa-users"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider truncate">Total Student Terdaftar</p>
                    <h3 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight mt-1">
                        {{ $course->users->count() }} <span class="text-xs text-gray-400 font-normal">Siswa</span>
                    </h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 min-h-[90px]">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider truncate">Student Tuntas Kuis</p>
                    <h3 class="text-xl sm:text-2xl font-black text-emerald-600 leading-tight mt-1">
                        {{ $course->users->filter(fn($u) => isset($u->nilai_quiz_asli) && $u->nilai_quiz_asli !== '-')->count() }}
                        <span class="text-xs text-gray-400 font-normal">Siswa</span>
                    </h3>
                </div>
            </div>
        </div>

        <div
            class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" id="studentSearchInput" placeholder="Cari nama atau email student..."
                    class="w-full pl-10 pr-4 py-2 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label for="scoreFilter" class="text-xs font-bold text-gray-500 whitespace-nowrap">Filter Nilai:</label>
                <select id="scoreFilter"
                    class="w-full sm:w-auto px-3 py-2 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:outline-none focus:border-indigo-500 transition cursor-pointer">
                    <option value="all">Semua Student</option>
                    <option value="done">Sudah Kuis</option>
                    <option value="undone">Belum Kuis</option>
                    <option value="above_75">Nilai > 75</option>
                    <option value="below_75">Nilai ≤ 75</option>
                </select>
            </div>
        </div>

        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse" id="studentDataTable">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 border-b">Nama Student</th>
                        <th class="p-4 border-b w-32 text-center">Status</th>
                        <th class="p-4 border-b w-44 text-center">Nilai Quiz Terakhir</th>
                        <th class="p-4 border-b w-64 pr-6">Progres Belajar</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse ($course->users as $student)
                        @php
                            $score = $student->nilai_quiz_asli ?? '-';
                            $numericScore = is_numeric($score) ? (float) $score : -1;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors student-row" data-score="{{ $score }}"
                            data-numeric="{{ $numericScore }}">
                            <td class="p-4 pl-6">
                                <div class="font-bold text-gray-900 text-sm leading-snug student-name">{{ $student->name }}
                                </div>
                                <div class="text-xs text-gray-400 font-medium mt-0.5 student-email">{{ $student->email }}
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span
                                    class="inline-block px-3 py-1 rounded-xl text-[10px] font-black tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">
                                    Aktif
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if (is_numeric($score))
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl font-black text-sm">
                                        <i class="fas fa-check-circle text-[10px]"></i> {{ $score }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-400 rounded-xl font-bold text-xs">
                                        Belum Kuis
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 pr-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 max-w-[140px] overflow-hidden">
                                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500"
                                            style="width: {{ $student->persentase_asli ?? 0 }}%"></div>
                                    </div>
                                    <span
                                        class="text-xs font-black text-gray-700 w-12">{{ $student->persentase_asli ?? 0 }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400 italic">Belum ada student yang
                                bergabung di kursus ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="block md:hidden space-y-3" id="mobileStudentCardsContainer">
            @forelse ($course->users as $student)
                @php
                    $score = $student->nilai_quiz_asli ?? '-';
                    $numericScore = is_numeric($score) ? (float) $score : -1;
                @endphp
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4 mobile-student-card"
                    data-score="{{ $score }}" data-numeric="{{ $numericScore }}">
                    <div class="flex justify-between items-start gap-2">
                        <div class="max-w-[70%]">
                            <h4 class="font-bold text-gray-900 text-sm leading-tight student-name">{{ $student->name }}
                            </h4>
                            <span
                                class="text-[10px] text-gray-400 block mt-0.5 break-all student-email">{{ $student->email }}</span>
                        </div>
                        <span
                            class="inline-block px-2.5 py-0.5 rounded-lg text-[9px] font-black tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">
                            Aktif
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100 items-center">
                        <div class="p-2 rounded-xl text-center border bg-gray-50/50 border-gray-100">
                            <span class="text-[9px] text-gray-400 font-bold block uppercase tracking-wider mb-0.5">Skor
                                Kuis</span>
                            @if (is_numeric($score))
                                <span class="font-black text-sm text-indigo-700">
                                    {{ $score }}
                                </span>
                            @else
                                <span class="font-bold text-xs text-gray-400">Belum Kuis</span>
                            @endif
                        </div>

                        <div class="space-y-1.5 pl-1">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-gray-400 font-medium">Progres</span>
                                <span class="text-xs font-black text-gray-800">{{ $student->persentase_asli ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full"
                                    style="width: {{ $student->persentase_asli ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-xs italic border border-gray-100">
                    Belum ada student yang bergabung di kursus ini.
                </div>
            @endforelse
        </div>

    </div>
    <script>
        "use strict";

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('studentSearchInput');
            const scoreFilter = document.getElementById('scoreFilter');

            function filterStudents() {
                const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const filterValue = scoreFilter ? scoreFilter.value : 'all';

                function checkMatch(element) {
                    const name = element.querySelector('.student-name').textContent.toLowerCase();
                    const email = element.querySelector('.student-email').textContent.toLowerCase();
                    const score = element.getAttribute('data-score');
                    const numeric = parseFloat(element.getAttribute('data-numeric'));

                    const matchesSearch = name.includes(searchValue) || email.includes(searchValue);

                    let matchesFilter = true;
                    if (filterValue === 'done') {
                        matchesFilter = score !== '-';
                    } else if (filterValue === 'undone') {
                        matchesFilter = score === '-';
                    } else if (filterValue === 'above_75') {
                        matchesFilter = numeric > 75;
                    } else if (filterValue === 'below_75') {
                        matchesFilter = numeric >= 0 && numeric <= 75;
                    }

                    return matchesSearch && matchesFilter;
                }

                document.querySelectorAll('#studentDataTable tbody .student-row').forEach(row => {
                    row.style.display = checkMatch(row) ? '' : 'none';
                });

                document.querySelectorAll('#mobileStudentCardsContainer .mobile-student-card').forEach(card => {
                    card.style.display = checkMatch(card) ? 'block' : 'none';
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterStudents);
            if (scoreFilter) scoreFilter.addEventListener('change', filterStudents);
        });
    </script>
@endsection
