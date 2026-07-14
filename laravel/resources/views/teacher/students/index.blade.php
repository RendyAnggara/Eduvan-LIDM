@extends('layouts.teacher')

@section('title', 'Manajemen Siswa')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6 mb-6 flex flex-col gap-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-teal-600 tracking-tight flex items-center gap-2 mb-1">
                        Manajemen Siswa
                    </h1>
                    <p class="text-slate-500 font-medium text-xs sm:text-sm">
                        Kelola pendaftaran siswa, pembagian kelas, dan monitoring akses akun.
                    </p>
                </div>
                <div
                    class="flex items-center gap-3 bg-slate-50 border border-slate-200 px-4 py-3 rounded-xl shadow-sm text-slate-700 w-full md:w-auto md:min-w-[220px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-teal-600 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                    </svg>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wider">Instansi Sekolah</span>
                        <span class="text-xs font-bold text-slate-800 tracking-tight truncate">
                            {{ Auth::user()->school ? Auth::user()->school->name : 'SMP Negeri 2 Karawang Barat' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa Kelas 7</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-0.5">{{ $countClass7 ?? 0 }} <span
                            class="text-xs font-medium text-slate-400">Anak</span></h3>
                </div>
                <div
                    class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm font-mono">
                    07</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa Kelas 8</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-0.5">{{ $countClass8 ?? 0 }} <span
                            class="text-xs font-medium text-slate-400">Anak</span></h3>
                </div>
                <div
                    class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm font-mono">
                    08</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa Kelas 9</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-0.5">{{ $countClass9 ?? 0 }} <span
                            class="text-xs font-medium text-slate-400">Anak</span></h3>
                </div>
                <div
                    class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm font-mono">
                    09</div>
            </div>
        </div>
        @if (session('success'))
            <div
                class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl text-sm font-semibold mb-6 border border-emerald-200 shadow-sm flex items-center gap-2">
                <span
                    class="flex items-center justify-center w-4 h-4 rounded-full bg-emerald-500 text-white text-[10px]">&checkmark;</span>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="bg-rose-50 text-rose-600 p-4 rounded-2xl text-sm font-semibold mb-6 border border-rose-200 shadow-sm flex items-center gap-2">
                <span
                    class="flex items-center justify-center w-4 h-4 rounded-full bg-rose-500 text-white text-[10px]">&times;</span>
                {{ session('error') }}
            </div>
        @endif

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100 h-fit w-full">
                <h4 class="font-bold text-slate-800 text-lg mb-4 tracking-tight">Tambah Siswa Baru</h4>
                <form action="{{ route('teacher.students.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">NISN
                            Siswa</label>
                        <input type="text" name="nisn_or_nip" required placeholder="Contoh: 0081234567"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm bg-slate-50/50 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama
                            Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama lengkap siswa"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm bg-slate-50/50 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email
                            Siswa</label>
                        <input type="email" name="email" required placeholder="siswa@sekolah.sch.id"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm bg-slate-50/50 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas</label>
                        <select name="class" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm bg-slate-50/50 text-slate-700 font-medium">
                            <option value="" disabled selected>-- Pilih Kelas --</option>
                            <option value="Kelas 7">Kelas 7</option>
                            <option value="Kelas 8">Kelas 8</option>
                            <option value="Kelas 9">Kelas 9</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Password
                            Awal</label>
                        <div class="relative">
                            <input type="password" id="passwordInput" name="password" required
                                placeholder="Minimal 6 karakter"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm bg-slate-50/50 text-slate-800 pr-10">
                            <button type="button" onclick="togglePasswordVisibility('passwordInput', 'eyeIcon')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2.5 rounded-xl transition text-sm shadow-md mt-2">
                        Daftarkan & Kirim Akun
                    </button>
                </form>
            </div>

            <div
                class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 lg:col-span-2 flex flex-col w-full">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h4 class="font-bold text-slate-800 text-lg tracking-tight">Daftar Siswa Terdaftar</h4>
                    <button type="button" onclick="openExcelModal()"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold hover:bg-emerald-100 transition shadow-sm w-full sm:w-auto">
                        Import Excel (.Excel)
                    </button>
                </div>

                <form action="{{ route('teacher.students.index') }}" method="GET" class="flex gap-2 mb-6 w-full">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari berdasarkan nama, email, nisn..."
                            class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-500 font-medium text-xs text-slate-700">
                        @if (request('search'))
                            <a href="{{ route('teacher.students.index', request()->except('search')) }}"
                                class="absolute right-4 top-3 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                        @endif
                    </div>
                    <button type="submit"
                        class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 sm:px-5 py-2.5 rounded-xl text-xs transition shadow-sm shrink-0">
                        Cari
                    </button>
                </form>
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 rounded-l-xl text-center w-12">No</th>
                                <th class="py-3 px-4">NISN</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4 text-center">Kelas</th>
                                <th class="py-3 px-4 text-center">Status Akses</th>
                                <th class="py-3 px-4 text-center rounded-r-xl w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($students as $index => $student)
                                <tr class="hover:bg-slate-50/80 transition duration-150">
                                    <td class="py-4 px-4 text-center text-slate-400 font-bold text-xs">
                                        {{ $students->firstItem() + $index }}</td>
                                    <td class="py-4 px-4 text-slate-500 font-mono tracking-wide text-xs">
                                        {{ $student->nisn_or_nip }}</td>
                                    <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600 uppercase shrink-0">
                                            {{ substr($student->name, 0, 2) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span>{{ $student->name }}</span>
                                            <span
                                                class="text-[10px] font-medium text-slate-400 -mt-0.5">{{ $student->email }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span
                                            class="px-2 py-0.5 rounded-md text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200">
                                            {{ str_replace('Kelas ', 'K-', $student->class ?? '??') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if ($student->email_verified_at)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                onclick="openEditModal('{{ $student->id }}', '{{ $student->nisn_or_nip }}', '{{ $student->name }}', '{{ $student->email }}', '{{ $student->class }}')"
                                                class="text-amber-600 hover:text-amber-800 text-xs font-bold bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2 py-1 rounded-lg transition duration-150">
                                                Edit
                                            </button>
                                            <form action="{{ route('teacher.student.destroy', $student->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2 py-1 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-xs rounded-lg transition shadow-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 text-sm font-medium">Tidak
                                        ditemukan siswa terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="block md:hidden space-y-3">
                    @forelse($students as $index => $student)
                        <div class="p-4 bg-slate-50/70 border border-slate-200 rounded-xl flex flex-col gap-3">
                            <div class="flex items-center justify-between gap-2">
                                <span
                                    class="text-[10px] font-mono font-bold text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded">
                                    ID: {{ $student->nisn_or_nip }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ str_replace('Kelas ', 'K-', $student->class ?? '??') }}
                                    </span>
                                    @if ($student->email_verified_at)
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Aktif</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold uppercase shrink-0">
                                    {{ substr($student->name, 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-800 truncate">{{ $student->name }}</h4>
                                    <p class="text-slate-500 text-xs truncate">{{ $student->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200/60">
                                <button type="button"
                                    onclick="openEditModal('{{ $student->id }}', '{{ $student->nisn_or_nip }}', '{{ $student->name }}', '{{ $student->email }}', '{{ $student->class }}')"
                                    class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-lg transition">
                                    Edit
                                </button>
                                <form action="{{ route('teacher.student.destroy', $student->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-white border border-rose-200 text-rose-600 text-xs font-bold rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400 font-medium">Tidak ditemukan siswa terdaftar.
                        </div>
                    @endforelse
                </div>

                <div class="mt-5">
                    {{ $students->links() }}
                </div>
            </div>
        </section>

        <div class="mt-8 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 w-full">
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h4 class="font-bold text-slate-800 text-lg tracking-tight">Live Progress & Rapot Digital Siswa</h4>
                    <p class="text-xs text-slate-400">Pantau akumulasi penyelesaian materi video dan cetak nilai rapot
                        berkala siswa.</p>
                </div>

                <form action="{{ route('teacher.students.index') }}" method="GET"
                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if (request('students_page'))
                        <input type="hidden" name="students_page" value="{{ request('students_page') }}">
                    @endif

                    <input type="text" name="monitor_search" value="{{ request('monitor_search') }}"
                        placeholder="Cari nama siswa..."
                        class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium w-full sm:min-w-[150px]">

                    <select name="monitor_class"
                        class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-bold focus:outline-none focus:border-teal-500 w-full sm:w-auto">
                        <option value="">Semua Kelas</option>
                        <option value="Kelas 7" {{ request('monitor_class') == 'Kelas 7' ? 'selected' : '' }}>Kelas 7
                        </option>
                        <option value="Kelas 8" {{ request('monitor_class') == 'Kelas 8' ? 'selected' : '' }}>Kelas 8
                        </option>
                        <option value="Kelas 9" {{ request('monitor_class') == 'Kelas 9' ? 'selected' : '' }}>Kelas 9
                        </option>
                    </select>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit"
                            class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow-sm flex-1 sm:flex-initial text-center justify-center">
                            Filter
                        </button>
                        @if (request('monitor_search') || request('monitor_class'))
                            <a href="{{ route('teacher.students.index', request()->only(['search', 'students_page'])) }}"
                                class="text-xs text-rose-600 hover:underline font-bold px-1 shrink-0">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4 text-center">Tingkat Kelas</th>
                            <th class="py-3 px-4 text-center w-60">Progres Materi</th>
                            <th class="py-3 px-4 text-center">Rata-rata Nilai Kuis</th>
                            <th class="py-3 px-4 text-center w-36">Dokumen Rapot</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse($monitoringStudents as $index => $mStudent)
                            <tr class="hover:bg-slate-50/60 transition duration-150">
                                <td class="py-4 px-4 text-center text-slate-400 font-bold text-xs">
                                    {{ $monitoringStudents->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $mStudent->name }}</td>
                                <td class="py-4 px-4 text-center">
                                    <span
                                        class="px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600">{{ $mStudent->class }}</span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center gap-3 justify-center max-w-[200px] mx-auto">
                                        <!-- Bar Progres Visual -->
                                        <div
                                            class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50 shrink-0">
                                            <div class="bg-teal-500 h-2 rounded-full"
                                                style="width: {{ $mStudent->average_progress }}%"></div>
                                        </div>
                                        <!-- Teks Nilai & Link Akses Halaman Detail Progres -->
                                        <div
                                            class="flex items-center gap-0.5 shrink-0 text-xs font-bold text-slate-700 min-w-[70px] justify-end">
                                            <span>{{ $mStudent->average_progress }}%</span>
                                            <a href="{{ route('teacher.students.show_progress', $mStudent->id) }}"
                                                class="text-teal-600 hover:text-teal-700 underline text-[10px] font-bold ml-0.5">
                                                (Detail)
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('teacher.students.show_quizzes', $mStudent->id) }}"
                                        class="text-xs font-bold px-3 py-1.5 rounded-xl text-slate-700 bg-slate-50 hover:bg-teal-50 hover:text-teal-700 border border-slate-200 transition duration-150 inline-block">
                                        Cek Kuis ({{ $mStudent->average_quiz }}/100)
                                    </a>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('teacher.students.export_rapor', $mStudent->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white border border-slate-950 rounded-xl text-xs font-bold transition shadow-sm">
                                        Cetak Rapot
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs font-medium">Data
                                    monitoring tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="block md:hidden space-y-4">
                @forelse($monitoringStudents as $index => $mStudent)
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-xl flex flex-col gap-3">
                        <div class="flex justify-between items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-800 truncate">{{ $mStudent->name }}</h4>
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-200/60 text-slate-600 shrink-0">
                                {{ $mStudent->class }}
                            </span>
                        </div>
                        <a href="{{ route('teacher.students.show_progress', $mStudent->id) }}"
                            class="flex flex-col gap-2 bg-white border border-teal-100 p-3 rounded-xl hover:border-teal-400 active:bg-teal-50/50 transition shadow-xs group block w-full text-left">
                            <div class="flex justify-between items-center text-[11px] font-bold text-slate-500">
                                <span class="text-teal-700 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-teal-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    Cek Progres Materi
                                </span>
                                <span
                                    class="text-teal-600 bg-teal-50 px-1.5 py-0.5 rounded text-[10px] font-black group-hover:bg-teal-100 transition">
                                    {{ $mStudent->average_progress }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-slate-200/50">
                                <div class="bg-teal-500 h-2.5 rounded-full"
                                    style="width: {{ $mStudent->average_progress }}%"></div>
                            </div>
                        </a>

                        <div class="flex items-center justify-between gap-2 pt-1 text-xs">
                            <a href="{{ route('teacher.students.show_quizzes', $mStudent->id) }}"
                                class="font-bold text-slate-700 bg-white border border-slate-200 px-3 py-2 rounded-xl shadow-xs hover:bg-slate-100 transition flex-1 text-center justify-center">
                                Kuis: {{ $mStudent->average_quiz }}/100
                            </a>
                            <a href="{{ route('teacher.students.export_rapor', $mStudent->id) }}"
                                class="bg-slate-900 hover:bg-slate-950 text-white font-bold px-3 py-2 rounded-xl shadow-xs transition flex-1 text-center justify-center">
                                Cetak Rapot
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-400 font-medium">Data monitoring tidak ditemukan.</div>
                @endforelse
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100">
                {{ $monitoringStudents->links() }}
            </div>
        </div>

        <div id="editStudentModal"
            class="fixed inset-0 z-50 items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4">
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg tracking-tight">Edit Informasi Siswa</h3>
                        <p class="text-xs text-slate-400">Modifikasi profil dan otomatisasi notifikasi email.</p>
                    </div>
                    <button type="button" onclick="closeEditModal()"
                        class="text-slate-400 hover:text-slate-600 text-lg font-bold focus:outline-none">✕</button>
                </div>

                <form id="editProfileForm" method="POST" class="p-6 space-y-4 overflow-y-auto">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">NISN Siswa</label>
                            <input type="text" id="edit_nisn" name="nisn_or_nip" required
                                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs bg-slate-50 text-slate-800 focus:outline-none focus:border-teal-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kelas</label>
                            <select id="edit_class" name="class" required
                                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs bg-slate-50 text-slate-700 font-bold focus:outline-none focus:border-teal-500">
                                <option value="Kelas 7">Kelas 7</option>
                                <option value="Kelas 8">Kelas 8</option>
                                <option value="Kelas 9">Kelas 9</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" id="edit_name" name="name" required
                            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs bg-slate-50 text-slate-800 focus:outline-none focus:border-teal-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Email Aktif</label>
                        <input type="email" id="edit_email" name="email" required
                            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs bg-slate-50 text-slate-800 focus:outline-none focus:border-teal-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Password Baru / Konfirmasi
                            Akun</label>
                        <div class="relative">
                            <input type="password" id="editPasswordInput" name="password"
                                placeholder="Kosongkan jika tidak diubah"
                                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs bg-slate-50 text-slate-800 focus:outline-none focus:border-teal-500 pr-8 font-medium">
                            <button type="button" onclick="togglePasswordVisibility('editPasswordInput', 'eyeIconEdit')"
                                class="absolute right-2 bottom-2 text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg id="eyeIconEdit" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-amber-50/60 border border-amber-200/70 p-3 rounded-xl flex items-start gap-2.5 mt-2">
                        <input type="checkbox" id="send_to_email" name="send_to_email" value="1"
                            class="mt-0.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <div class="flex flex-col">
                            <label for="send_to_email"
                                class="text-xs font-bold text-amber-900 cursor-pointer select-none">Kirim langsung
                                perubahan ke email siswa</label>
                            <p class="text-[10px] text-amber-600 mt-0.5">*Centang ini agar kredensial otomatis meluncur ke
                                inbox siswa.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-4 py-2.5 rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-md">Simpan
                            & Kirim Akun</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="importExcelModal"
            class="fixed inset-0 z-50 items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4">
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm tracking-tight">Import Data Siswa Massal</h3>
                        <p class="text-[10px] text-slate-400">Unggah berkas spreadsheet data siswa terpadu.</p>
                    </div>
                    <button type="button" onclick="closeExcelModal()"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold focus:outline-none">✕</button>
                </div>

                <form action="{{ route('teacher.students.import_excel') }}" method="POST" enctype="multipart/form-data"
                    class="p-5 space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">Template Format Excel</span>
                            <span class="text-[9px] text-slate-400">Wajib gunakan kolom: nisn, nama, email, kelas,
                                password</span>
                        </div>
                        <a href="{{ route('teacher.students.download_template') }}"
                            class="text-[10px] font-bold bg-teal-600 hover:bg-teal-700 text-white px-2.5 py-1.5 rounded-lg transition shrink-0">
                            Unduh
                        </a>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pilih File Berkas (.xlsx,
                            .xls, .csv)</label>
                        <input type="file" name="excel_file" required
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-200 p-2 rounded-xl bg-slate-50/50">
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" onclick="closeExcelModal()"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] font-bold px-3 py-2 rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-4 py-2 rounded-xl transition shadow-md">Proses
                            Unggah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, nisn, name, email, classLevel) {
            const modal = document.getElementById('editStudentModal');
            document.getElementById('editProfileForm').action = `/teacher/students/${id}/update`;
            document.getElementById('edit_nisn').value = nisn;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_class').value = classLevel;
            document.getElementById('editPasswordInput').value = '';
            document.getElementById('send_to_email').checked = false;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditModal() {
            const modal = document.getElementById('editStudentModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        function openExcelModal() {
            const modal = document.getElementById('importExcelModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeExcelModal() {
            const modal = document.getElementById('importExcelModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
            }
        }
    </script>
@endsection
