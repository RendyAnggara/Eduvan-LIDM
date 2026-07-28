@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Daftar Student</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pantau aktivitas, progres belajar, dan detail instansi setiap student
                EduLearn.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Student</p>
                    <h3 class="text-lg font-black text-gray-900 leading-none mt-1">
                        {{ $stats['total_students'] ?? $students->total() }}</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-book-reader"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Siswa Aktif</p>
                    <h3 class="text-lg font-black text-emerald-600 leading-none mt-1">{{ $stats['active_students'] ?? 0 }}
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Belum Ada Course</p>
                    <h3 class="text-lg font-black text-amber-600 leading-none mt-1">{{ $stats['no_course'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('admin.students.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 w-full">

                <div class="relative w-full lg:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama student, email, atau sekolah..."
                        class="pl-10 pr-4 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition text-sm w-full font-medium placeholder:text-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                </div>
                <div class="w-full">
                    <select name="filter" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-medium focus:border-indigo-500 focus:bg-white outline-none transition cursor-pointer">
                        <option value="">Semua Status Belajar</option>
                        <option value="bought" {{ request('filter') == 'bought' ? 'selected' : '' }}>Sudah Beli Course
                        </option>
                        <option value="not_bought" {{ request('filter') == 'not_bought' ? 'selected' : '' }}>Belum Beli
                            Course</option>
                    </select>
                </div>

                <div class="w-full flex gap-2">
                    <select name="grade_level" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-medium focus:border-indigo-500 focus:bg-white outline-none transition cursor-pointer">
                        <option value="all">Semua Kelas SMP</option>
                        <option value="7" {{ request('grade_level') == '7' ? 'selected' : '' }}>Kelas 7 SMP</option>
                        <option value="8" {{ request('grade_level') == '8' ? 'selected' : '' }}>Kelas 8 SMP</option>
                        <option value="9" {{ request('grade_level') == '9' ? 'selected' : '' }}>Kelas 9 SMP</option>
                    </select>

                    @if (request('search') || request('filter') || (request('grade_level') && request('grade_level') !== 'all'))
                        <a href="{{ route('admin.students.index') }}"
                            class="px-3 py-2.5 border border-gray-200 text-center rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition shrink-0 flex items-center justify-center"
                            title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 border-b w-12 text-center">No</th>
                        <th class="p-4 border-b">Detail Student & Instansi</th>
                        <th class="p-4 border-b text-center w-40">Status Belajar</th>
                        <th class="p-4 border-b text-right pr-6 w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse($students as $student)
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
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 pl-6 text-center font-bold text-gray-400">
                                {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                            <td class="p-4">
                                <div class="font-bold text-gray-900 text-base leading-snug">
                                    {{ $student->name }}
                                </div>
                                <div class="text-xs text-gray-400 font-normal mt-0.5">
                                    {{ $student->email }}
                                </div>

                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    @if ($student->school)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            <i class="fas fa-school text-[9px] mr-1"></i> {{ $student->school->name }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                            <i class="fas fa-user text-[9px] mr-1"></i> Umum / Tanpa Sekolah
                                        </span>
                                    @endif

                                    @if ($studentClass)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                            <i class="fas fa-graduation-cap text-[9px] mr-1"></i> Kelas {{ $studentClass }}
                                            SMP
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-50 text-gray-400 border border-gray-100">
                                            <i class="fas fa-graduation-cap text-[9px] mr-1"></i> Belum Set Kelas
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="p-4 text-center">
                                @if ($student->enrollments->count() > 0)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs rounded-xl font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $student->enrollments->count() }} Course Diikuti
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-xl font-medium">
                                        Belum Ada Course
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-right pr-6 whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button onclick="openStudentModal('{{ $student->id }}')"
                                        class="w-8 h-8 flex items-center justify-center bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl transition-colors"
                                        title="Detail Cepat Modal">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    <a href="{{ route('admin.students.show', $student->id) }}"
                                        class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors"
                                        title="Halaman Detail Penuh">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data student ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition-colors cursor-pointer"
                                            title="Hapus Student">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400 italic">
                                Data student tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="block md:hidden space-y-3">
            @forelse($students as $student)
                @php
                    $rawClassMobile = $student->class ?? $student->enrollments->first()?->course?->grade_level;
                    $studentClassMobile = null;

                    if ($rawClassMobile) {
                        if (str_contains((string) $rawClassMobile, '7')) {
                            $studentClassMobile = '7';
                        } elseif (str_contains((string) $rawClassMobile, '8')) {
                            $studentClassMobile = '8';
                        } elseif (str_contains((string) $rawClassMobile, '9')) {
                            $studentClassMobile = '9';
                        }
                    }
                @endphp
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $student->name }}</h4>
                            <span class="text-xs text-gray-400 mt-0.5 block">{{ $student->email }}</span>
                        </div>
                        <div>
                            @if ($student->enrollments->count() > 0)
                                <span
                                    class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] rounded-lg font-bold">
                                    {{ $student->enrollments->count() }} Course
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-[10px] rounded-lg font-medium">
                                    Belum Beli
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-2.5 bg-gray-50 rounded-xl text-xs flex flex-wrap items-center justify-between gap-2">
                        <span class="font-bold text-gray-700 truncate max-w-[180px]">
                            <i class="fas fa-school text-blue-500 text-[10px] mr-1.5"></i>
                            <span
                                class="font-medium text-gray-500">{{ $student->school->name ?? 'Umum / Tanpa Sekolah' }}</span>
                        </span>
                        @if ($studentClassMobile)
                            <span
                                class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-100 text-[10px]">
                                Kelas {{ $studentClassMobile }} SMP
                            </span>
                        @else
                            <span class="font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded text-[10px]">
                                Belum Set Kelas
                            </span>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex gap-2">
                        <button onclick="openStudentModal('{{ $student->id }}')"
                            class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-2.5 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-eye text-[10px]"></i> Quick View
                        </button>

                        <a href="{{ route('admin.students.show', $student->id) }}"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-external-link-alt text-[10px]"></i> Detail Penuh
                        </a>

                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                            class="inline-block shrink-0"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus student ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-10 h-10 bg-rose-50 text-rose-600 flex items-center justify-center rounded-xl active:bg-rose-100 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                    Data student tidak ditemukan.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>

    </div>

    <div id="studentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

            <div
                class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden z-10 border border-gray-100 my-8 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Progres Belajar Student</h3>
                        <p class="text-xs text-gray-400">Detail persentase materi yang telah diselesaikan.</p>
                    </div>
                    <button onclick="closeModal()"
                        class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>

                <div id="loadingState" class="py-10 text-center">
                    <div
                        class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent">
                    </div>
                    <p class="mt-2 text-xs font-bold text-gray-400">Mengambil data progres...</p>
                </div>

                <div id="modalContent" class="hidden">
                    <div id="chart-container" class="mb-6 w-full">
                        <div id="modalChart"></div>
                    </div>
                    <div id="modalCourseList" class="space-y-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        let chart;

        function openStudentModal(id) {
            const modal = document.getElementById('studentModal');
            const loading = document.getElementById('loadingState');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');

            fetch(`/admin/api/students/${id}`)
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');
                    content.classList.remove('hidden');

                    updateChart(data.courses, data.progress);

                    let listHtml =
                        '<h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-2">Daftar Kursus Diikuti:</h4>';
                    if (data.courses.length === 0) {
                        listHtml +=
                            '<p class="text-xs italic text-gray-400 bg-gray-50 p-3 rounded-xl">Student ini belum memiliki / membeli kursus.</p>';
                    } else {
                        data.courses.forEach((course, index) => {
                            listHtml += `
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-800">${course}</span>
                                <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">${data.progress[index]}%</span>
                            </div>
                        `;
                        });
                    }
                    document.getElementById('modalCourseList').innerHTML = listHtml;
                });
        }

        function updateChart(categories, data) {
            if (chart) {
                chart.destroy();
            }

            const options = {
                series: [{
                    name: 'Progres',
                    data: data
                }],
                chart: {
                    type: 'bar',
                    height: 220,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6,
                        distributed: true,
                        barHeight: '55%'
                    }
                },
                colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
                xaxis: {
                    categories: categories,
                    max: 100
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    formatter: (val) => val + '%'
                }
            };

            chart = new ApexCharts(document.querySelector("#modalChart"), options);
            chart.render();
        }

        function closeModal() {
            document.getElementById('studentModal').classList.add('hidden');
        }
    </script>
@endsection
