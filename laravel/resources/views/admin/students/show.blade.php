@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6">
        <div
            class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.students.index') }}"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 hover:bg-indigo-50 text-gray-500 hover:text-indigo-600 rounded-lg font-bold text-[11px] transition border border-gray-100">
                        <i class="fas fa-arrow-left text-[10px]"></i> Kembali
                    </a>
                    <span class="text-xs font-bold text-gray-300">/</span>
                    <span
                        class="text-xs font-bold text-indigo-600 bg-indigo-50/60 px-2.5 py-0.5 rounded-lg border border-indigo-100/50">
                        Detail Profil Student
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight pt-1">
                    Profil Student & Progres
                </h1>
            </div>

            <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto justify-end">
                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus student ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full sm:w-auto bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100/50 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition shadow-sm cursor-pointer">
                        <i class="fas fa-trash text-xs"></i> Hapus Student
                    </button>
                </form>
            </div>
        </div>

        @php
            $rawClass = $student->class ?? $student->enrollments->first()?->course?->grade_level;
            $studentClass = null;
            if ($rawClass) {
                if (str_contains((string) $rawClass, '7')) {
                    $studentClass = '7';
                } elseif (str_contains((string) $rawClass, '8')) {
                    $studentClass = '8';
                } elseif (str_contains((string) $rawClass, '9')) {
                    $studentClass = '9';
                }
            }

            $totalEnrollments = $student->enrollments->count();
            $avgProgress = 0;
            if ($totalEnrollments > 0) {
                $sumProgress = $student->enrollments->sum(
                    fn($e) => method_exists($e, 'calculateProgress') ? $e->calculateProgress() : 0,
                );
                $avgProgress = round($sumProgress / $totalEnrollments);
            }
        @endphp

        <div
            class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 rounded-2xl p-5 sm:p-7 text-white shadow-md relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

            <div
                class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-5 text-center sm:text-left">
                <div
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center justify-center text-2xl sm:text-3xl font-black shadow-inner shrink-0">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>

                <div class="flex-1 min-w-0 w-full">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight leading-tight">{{ $student->name }}</h2>
                        @if ($totalEnrollments > 0)
                            <span
                                class="px-2.5 py-0.5 bg-emerald-400/20 text-emerald-200 border border-emerald-400/30 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                Aktif Belajar
                            </span>
                        @else
                            <span
                                class="px-2.5 py-0.5 bg-white/10 text-gray-200 border border-white/20 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                Belum Ada Course
                            </span>
                        @endif
                    </div>

                    <p class="text-indigo-200 text-xs sm:text-sm font-medium mt-1 truncate max-w-full">
                        {{ $student->email }}
                    </p>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 sm:gap-2 mt-3">
                        @if ($student->school)
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-bold bg-white/10 border border-white/20 text-white backdrop-blur-sm max-w-full truncate">
                                <i class="fas fa-school text-[9px] mr-1.5 text-indigo-300"></i>
                                <span class="truncate">{{ $student->school->name }}</span>
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-medium bg-white/10 border border-white/15 text-indigo-200 backdrop-blur-sm">
                                <i class="fas fa-user text-[9px] mr-1.5"></i> Umum / Tanpa Sekolah
                            </span>
                        @endif

                        @if ($studentClass)
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-400/20 border border-amber-300/30 text-amber-200 backdrop-blur-sm shrink-0">
                                <i class="fas fa-graduation-cap text-[9px] mr-1.5 text-amber-300"></i> Kelas
                                {{ $studentClass }} SMP
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-medium bg-white/10 border border-white/15 text-indigo-200 backdrop-blur-sm shrink-0">
                                <i class="fas fa-graduation-cap text-[9px] mr-1.5"></i> Belum Set Kelas
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">Total
                        Course</p>
                    <h3 class="text-base sm:text-lg font-black text-gray-900 leading-none mt-1">
                        {{ $totalEnrollments }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">
                        Rata-Rata Progres</p>
                    <h3 class="text-base sm:text-lg font-black text-emerald-600 leading-none mt-1">
                        {{ $avgProgress }}%
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">NISN /
                        NIP</p>
                    <h3 class="text-xs sm:text-sm font-black text-gray-800 leading-none mt-1 truncate">
                        {{ $student->nisn_or_nip ?? '-' }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">
                        Terdaftar Pada</p>
                    <h3 class="text-xs sm:text-sm font-black text-gray-800 leading-none mt-1 truncate">
                        {{ $student->created_at->format('d M Y') }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="space-y-4 sm:space-y-6">
            @if ($totalEnrollments > 0)
                <div class="hidden md:block bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-base font-black text-gray-900 tracking-tight">Grafik Progres Belajar</h4>
                        <span class="text-xs text-gray-400 font-medium">Persentase Penyelesaian (%)</span>
                    </div>
                    <div id="chart-progres-desktop"></div>
                </div>

                <div class="block md:hidden bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="text-sm font-black text-gray-900 tracking-tight mb-3">Grafik Progres Belajar</h4>
                    <div class="w-full overflow-hidden">
                        <div id="chart-progres-mobile"></div>
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="text-base font-black text-gray-900 tracking-tight mb-4">Daftar Kursus Diikuti</h4>
                    <div class="space-y-3">
                        @foreach ($student->enrollments as $enroll)
                            @php
                                $prog = method_exists($enroll, 'calculateProgress') ? $enroll->calculateProgress() : 0;
                            @endphp
                            <div
                                class="p-3.5 sm:p-4 bg-gray-50/80 rounded-2xl border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-sm shadow-indigo-200">
                                        EDV
                                    </div>
                                    <div class="min-w-0">
                                        <h5 class="font-bold text-gray-900 text-sm truncate">{{ $enroll->course->title }}
                                        </h5>
                                        <p class="text-xs text-gray-400 mt-0.5">Kategori: <span
                                                class="font-semibold text-gray-600 uppercase">{{ $enroll->course->category }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <div class="flex-1 sm:w-32 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                            style="width: {{ $prog }}%"></div>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs rounded-xl font-bold shrink-0">
                                        {{ $prog }}% Selesai
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white p-8 sm:p-14 rounded-2xl shadow-sm text-center border border-gray-100 space-y-3">
                    <div
                        class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-50 text-gray-300 rounded-2xl mx-auto flex items-center justify-center text-xl sm:text-2xl border border-gray-100">
                        <i class="fas fa-inbox text-3xl text-gray-300"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-black text-gray-800 tracking-tight">Belum Ada Kursus yang Diikuti</h4>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
                            Siswa ini belum mendaftar atau membeli kursus apa pun di platform EduLearn.
                        </p>
                    </div>
                </div>
            @endif
        </div>

    </div>
    <div id="data-container" data-titles='@json($student->enrollments->map(fn($e) => $e->course->title))' data-progress='@json($student->enrollments->map(fn($e) => method_exists($e, 'calculateProgress') ? $e->calculateProgress() : 0))'
        style="display: none;">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('data-container');
            if (!container) return;

            const titles = JSON.parse(container.getAttribute('data-titles'));
            const dataProgress = JSON.parse(container.getAttribute('data-progress'));

            if (titles.length === 0) return;

            const optionsDesktop = {
                series: [{
                    name: 'Progres Belajar',
                    data: dataProgress
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        horizontal: true,
                        barHeight: '45%',
                        distributed: true
                    }
                },
                colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%"
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 'bold'
                    }
                },
                xaxis: {
                    categories: titles,
                    max: 100,
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9'
                },
                legend: {
                    show: false
                }
            };

            const optionsMobile = {
                series: [{
                    name: 'Progres Belajar',
                    data: dataProgress
                }],
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: false,
                        columnWidth: '45%',
                        distributed: true
                    }
                },
                colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%"
                    },
                    style: {
                        fontSize: '10px'
                    }
                },
                xaxis: {
                    categories: titles,
                    labels: {
                        rotate: -45,
                        style: {
                            colors: '#64748b',
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    max: 100,
                    labels: {
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9'
                },
                legend: {
                    show: false
                }
            };

            const chartDesktop = new ApexCharts(document.querySelector("#chart-progres-desktop"), optionsDesktop);
            chartDesktop.render();

            const chartMobile = new ApexCharts(document.querySelector("#chart-progres-mobile"), optionsMobile);
            chartMobile.render();
        });
    </script>
@endsection
