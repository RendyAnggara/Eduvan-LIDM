@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6">
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2"
                role="alert">
                <i class="fas fa-check-circle text-base"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-2"
                role="alert">
                <i class="fas fa-exclamation-circle text-base"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 space-y-1"
                role="alert">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <i class="fas fa-exclamation-circle text-base"></i>
                    <span>Gagal Menyimpan Data Akun:</span>
                </div>
                <ul class="list-disc list-inside pl-2 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- LAYOUT GRID RESPONSIVE -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- KOLOM KIRI (Daftar Pengajar & Analitik) -->
            <div class="order-1 lg:col-span-2 space-y-6 w-full min-w-0">

                <!-- CARD UTAMA: DAFTAR PENGAJAR -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div
                        class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-table text-slate-400 text-sm"></i>
                            <h3 class="font-bold text-gray-800 text-sm">Daftar Pengajar EduLearn</h3>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-60">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-search text-xs"></i>
                                </span>
                                <input type="text" id="teacherSearchInput" onkeyup="filterTeachers()"
                                    class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    placeholder="Cari nama, email, NIP...">
                            </div>
                            <select id="teacherSchoolFilter" onchange="filterTeachers()"
                                class="w-full sm:w-auto border border-gray-200 rounded-xl text-xs px-2.5 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 max-w-full sm:max-w-xs">
                                <option value="">Semua Sekolah</option>
                                @foreach ($schools as $school)
                                    <option value="{{ $school->name }}">{{ $school->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 w-full block" id="teacherContainer">

                        <!-- 🖥️ DESKTOP VIEW: Tabel Tradisional -->
                        <div class="hidden md:block overflow-x-auto scroller-smooth">
                            <table class="w-full text-left border-collapse min-w-[550px]" id="teacherTable">
                                <thead>
                                    <tr
                                        class="border-b border-gray-200 text-xs font-bold text-gray-500 uppercase bg-gray-50/70">
                                        <th class="py-3 px-4">Nama</th>
                                        <th class="py-3 px-4">Email</th>
                                        <th class="py-3 px-4">NIP</th>
                                        <th class="py-3 px-4">Instansi Sekolah</th>
                                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm" id="teacherTableBody">
                                    @forelse($teachers as $teacher)
                                        <!-- Desktop Row (Hidden on mobile) -->
                                        <tr class="teacher-row hover:bg-gray-50/50 transition hidden md:table-row"
                                            data-school="{{ $teacher->school->name ?? '' }}">
                                            <td class="search-name py-4 px-4 font-bold text-gray-900 min-w-[130px]">
                                                {{ $teacher->name }}</td>
                                            <td class="search-email py-4 px-4 text-gray-600">{{ $teacher->email }}</td>
                                            <td class="search-nip py-4 px-4 text-gray-500 font-medium">
                                                {{ $teacher->nisn_or_nip ?? '-' }}</td>
                                            <td class="py-4 px-4">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-100 max-w-[180px] truncate">
                                                    {{ $teacher->school->name ?? 'Belum Ditentukan' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center whitespace-nowrap space-x-1">
                                                <button type="button"
                                                    onclick="openEditModal('{{ $teacher->id }}', '{{ addslashes($teacher->name) }}', '{{ $teacher->email }}', '{{ $teacher->nisn_or_nip }}', '{{ $teacher->school_id }}')"
                                                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition inline-flex items-center justify-center cursor-pointer">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun guru ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition inline-flex items-center justify-center cursor-pointer">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="emptyRow" class="hidden md:table-row">
                                            <td colspan="5" class="py-8 text-center text-sm text-gray-400 font-medium">
                                                Belum ada data pengajar terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- 📱 MOBILE VIEW: List Card Responsif (Sinkron dengan JavaScript) -->
                        <div class="block md:hidden space-y-3" id="teacherMobileList">
                            @forelse($teachers as $teacher)
                                <div class="teacher-row bg-gray-50/60 rounded-xl border border-gray-100 p-4 space-y-3 shadow-sm hover:border-gray-200 transition"
                                    data-school="{{ $teacher->school->name ?? '' }}">
                                    <div class="flex items-start justify-between gap-2 border-b border-gray-200/60 pb-2">
                                        <div class="min-w-0">
                                            <h4 class="search-name font-bold text-gray-900 text-sm truncate">
                                                {{ $teacher->name }}</h4>
                                            <p class="search-email text-xs text-gray-500 truncate mt-0.5">
                                                {{ $teacher->email }}</p>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-100 shrink-0 max-w-[120px] truncate">
                                            {{ $teacher->school->name ?? 'Belum Ditentukan' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 text-xs">
                                        <div>
                                            <span class="text-[10px] text-gray-400 font-bold block uppercase">NIP</span>
                                            <span
                                                class="search-nip font-medium text-gray-700">{{ $teacher->nisn_or_nip ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button"
                                                onclick="openEditModal('{{ $teacher->id }}', '{{ addslashes($teacher->name) }}', '{{ $teacher->email }}', '{{ $teacher->nisn_or_nip }}', '{{ $teacher->school_id }}')"
                                                class="px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-lg text-[11px] font-bold flex items-center gap-1 transition active:bg-amber-100">
                                                <i class="fas fa-edit text-[10px]"></i> Ubah
                                            </button>
                                            <form action="{{ route('admin.teachers.destroy', $teacher->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun guru ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1.5 bg-rose-50 text-rose-700 border border-rose-200/60 rounded-lg text-[11px] font-bold flex items-center gap-1 transition active:bg-rose-100">
                                                    <i class="fas fa-trash text-[10px]"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div id="emptyMobileRow" class="py-8 text-center text-sm text-gray-400 font-medium">Belum
                                    ada data pengajar terdaftar.</div>
                            @endforelse
                        </div>

                    </div>

                    <!-- PAGINATION RESPONSIVE -->
                    <div class="p-4 px-5 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-gray-500"
                        id="paginationContainer">
                        <span id="paginationInfo" class="text-center sm:text-left">Showing 1 to 3 of 3 entries</span>
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-center">
                            <button type="button" onclick="prevSlide()" id="btnPrev"
                                class="flex-1 sm:flex-initial px-4 py-2 sm:py-1.5 border border-gray-200 bg-white rounded-xl hover:bg-gray-50 text-gray-700 disabled:opacity-40 disabled:hover:bg-white cursor-pointer transition flex items-center justify-center gap-1">
                                <i class="fas fa-chevron-left text-[10px]"></i> Prev
                            </button>
                            <button type="button" onclick="nextSlide()" id="btnNext"
                                class="flex-1 sm:flex-initial px-4 py-2 sm:py-1.5 border border-gray-200 bg-white rounded-xl hover:bg-gray-50 text-gray-700 disabled:opacity-40 disabled:hover:bg-white cursor-pointer transition flex items-center justify-center gap-1">
                                Next <i class="fas fa-chevron-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ANALITIK SECTION -->
                <div class="space-y-4">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Analitik Instansi Pengajar
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 shrink-0">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Total
                                    Akumulasi Akun</span>
                                <span class="text-base font-black text-gray-900 block truncate">{{ count($teachers) }}
                                    <span class="text-xs font-normal text-gray-400">Guru</span></span>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 shrink-0">
                                <i class="fas fa-university text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Total
                                    Distribusi Sekolah</span>
                                <span class="text-base font-black text-gray-900 block truncate">{{ count($schools) }}
                                    <span class="text-xs font-normal text-gray-400">Mitra</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                            <i class="fas fa-chart-line text-indigo-500 text-sm"></i>
                            <h3 class="font-bold text-gray-800 text-sm">Daftar Sebaran Pengajar per Instansi</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[300px]">
                                <thead>
                                    <tr
                                        class="border-b border-gray-200 text-xs font-bold text-gray-500 uppercase bg-gray-50/70">
                                        <th class="py-3 px-4 w-12 text-center">No</th>
                                        <th class="py-3 px-4">Nama Instansi Sekolah</th>
                                        <th class="py-3 px-4 text-center w-36">Total Guru</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-600 font-medium">
                                    @forelse($schools->take(3) as $sch)
                                        <tr class="hover:bg-gray-50/30 transition">
                                            <td class="py-3 px-4 text-center">
                                                <span
                                                    class="inline-flex items-center justify-center w-5 h-5 bg-gray-100 rounded-lg text-xs text-gray-700 font-bold">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-gray-900 font-bold truncate max-w-[180px]">
                                                {{ $sch->name }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-indigo-50 text-indigo-800">
                                                    {{ $teachers->where('school_id', $sch->id)->count() }} Guru
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-6 text-center text-gray-400 italic font-medium">
                                                Belum ada sebaran pengajar instansi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (Form Input & Import - Order di mobile di bawah) -->
            <div class="order-2 space-y-6 w-full">

                <!-- IMPORT MASSAL CARD -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                            <h3 class="font-bold text-gray-800 text-sm">Import Massal Akun Guru</h3>
                        </div>
                        <a href="{{ route('admin.teachers.download_template') }}"
                            class="text-[11px] text-blue-600 hover:text-blue-500 font-bold flex items-center gap-1 transition shrink-0">
                            <i class="fas fa-download text-[10px]"></i> Unduh Template
                        </a>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('admin.teachers.import_excel') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Instansi Sekolah
                                    Tujuan</label>
                                <select name="school_id"
                                    class="w-full px-3 py-2.5 border border-gray-200 bg-white rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    required>
                                    <option value="" disabled selected>-- Pilih Instansi Penempatan Massal --
                                    </option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Upload File Excel (.xlsx /
                                    .csv)</label>
                                <input type="file" name="excel_file" accept=".xlsx, .xls, .csv"
                                    class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer border border-gray-200 rounded-xl p-1 focus:outline-none"
                                    required>
                            </div>
                            <button type="submit"
                                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-black shadow-lg shadow-emerald-600/10 transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-upload text-xs"></i> Proses Import Guru
                            </button>
                        </form>
                    </div>
                </div>

                <!-- DAFTAR GURU BARU CARD -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                        <i class="fas fa-user-plus text-slate-400 text-sm"></i>
                        <h3 class="font-bold text-gray-800 text-sm">Daftarkan Guru Baru</h3>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('admin.teachers.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    placeholder="Bu Dewi Sartika, M.Pd" value="{{ old('name') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Email Aktif</label>
                                <input type="email" name="email"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    placeholder="nama_guru@sekolah.sch.id" value="{{ old('email') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">NIP (Opsional)</label>
                                <input type="text" name="nisn_or_nip"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    placeholder="1987XXXXXXXXXXXXXX" value="{{ old('nisn_or_nip') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Instansi Sekolah</label>
                                <select name="school_id"
                                    class="w-full px-3 py-2.5 border border-gray-200 bg-white rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    required>
                                    <option value="" disabled selected>-- Pilih Instansi Penempatan --</option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}"
                                            {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="p-3 bg-blue-50 border border-blue-100 text-blue-800 rounded-xl text-xs flex gap-2">
                                <i class="fas fa-info-circle mt-0.5 shrink-0"></i>
                                <span class="leading-normal">Password default dibuat otomatis oleh sistem dan langsung
                                    dikirim ke email guru.</span>
                            </div>
                            <button type="submit"
                                class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-black shadow-lg shadow-blue-600/10 transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-paper-plane text-xs"></i> Simpan & Kirim
                            </button>
                        </form>
                    </div>
                </div>

                <!-- AKTIVITAS TERBARU -->
                <div class="space-y-3">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Pendaftaran Guru Terbaru
                    </h3>
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                        @forelse($recentActivities as $activity)
                            <div class="flex items-start gap-3 text-xs">
                                <div class="w-2 h-2 rounded-full mt-1.5 shrink-0 bg-emerald-500"></div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-gray-900 font-bold leading-tight">
                                        {{ $activity->name }}
                                        <span class="font-normal text-gray-500">telah terdaftar sebagai pengajar</span>
                                    </p>
                                    <span class="text-[10px] font-semibold text-indigo-600 block mt-1 truncate">
                                        <i class="fas fa-school text-[9px] mr-0.5"></i>
                                        {{ $activity->school->name ?? 'Belum Ditentukan' }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium shrink-0 pl-1 whitespace-nowrap">
                                    {{ $activity->created_at ? $activity->created_at->diffForHumans() : '-' }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-400 italic text-xs">Belum ada aktivitas pendaftaran akun
                                guru baru-baru ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL EDIT RESPONSIF -->
    <div id="globalEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-all duration-300 hidden opacity-0">
        <div class="fixed inset-0 bg-transparent" onclick="closeEditModal()"></div>
        <div
            class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl border border-gray-100 p-5 sm:p-6 transform transition-all duration-300 scale-95 opacity-0 z-10">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2 text-gray-800">
                    <i class="fas fa-user-edit text-amber-500 text-sm"></i>
                    <h3 class="font-bold text-sm">Ubah Kredensial Pengajar</h3>
                </div>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form id="editTeacherForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email Aktif</label>
                    <input type="email" name="email" id="edit_email"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NIP (Opsional)</label>
                    <input type="text" name="nisn_or_nip" id="edit_nip"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Penempatan Sekolah</label>
                    <select name="school_id" id="edit_school_id"
                        class="w-full px-3 py-2 border border-gray-200 bg-white rounded-xl text-sm" required>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()"
                        class="w-full py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-600 text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="submit"
                        class="w-full py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-black shadow-lg shadow-amber-600/10 flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-check text-xs"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const rowsPerPage = 3;
        let filteredRows = [];

        // Fungsi bantu untuk mendeteksi apakah browser sedang di mode mobile
        function isMobileView() {
            return window.innerWidth < 768; // md breakpoint Tailwind adalah 768px
        }

        function initPagination() {
            // Ambil baris yang sesuai dengan view aktif saat ini saja
            const selector = isMobileView() ? 'div.teacher-row' : 'tr.teacher-row';
            const allRows = Array.from(document.querySelectorAll(selector));
            filteredRows = allRows;
            showPage(1);
        }

        function filterTeachers() {
            const searchQuery = document.getElementById('teacherSearchInput').value.toLowerCase().trim();
            const schoolQuery = document.getElementById('teacherSchoolFilter').value;

            // Filter target baris disesuaikan dengan status layar aktif
            const selector = isMobileView() ? 'div.teacher-row' : 'tr.teacher-row';
            const allRows = Array.from(document.querySelectorAll(selector));

            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.search-name').textContent.toLowerCase();
                const email = row.querySelector('.search-email').textContent.toLowerCase();
                const nip = row.querySelector('.search-nip').textContent.toLowerCase();
                const school = row.getAttribute('data-school');

                const matchesSearch = name.includes(searchQuery) || email.includes(searchQuery) || nip.includes(
                    searchQuery);
                const matchesSchool = schoolQuery === "" || school === schoolQuery;

                return matchesSearch && matchesSchool;
            });

            const emptyRow = document.getElementById('emptyRow');
            const emptyMobile = document.getElementById('emptyMobileRow');

            // Sembunyikan TOTAL semua baris (baik TR maupun DIV) sebelum memfilter ulang
            document.querySelectorAll('.teacher-row').forEach(row => row.style.display = 'none');

            if (filteredRows.length === 0) {
                if (emptyRow) emptyRow.style.display = isMobileView() ? 'none' : '';
                if (emptyMobile) emptyMobile.style.display = isMobileView() ? 'block' : 'none';
                document.getElementById('paginationContainer').style.display = 'none';
            } else {
                if (emptyRow) emptyRow.style.display = 'none';
                if (emptyMobile) emptyMobile.style.display = 'none';
                document.getElementById('paginationContainer').style.display = 'flex';
                showPage(1);
            }
        }

        function showPage(page) {
            currentPage = page;
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);

            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Pastikan semua disembunyikan kembali
            const selector = isMobileView() ? 'div.teacher-row' : 'tr.teacher-row';
            document.querySelectorAll(selector).forEach(row => row.style.display = 'none');

            // Tampilkan hanya yang masuk range pagination view aktif
            filteredRows.slice(start, end).forEach(row => {
                if (isMobileView()) {
                    row.style.display = 'block';
                } else {
                    row.style.display = 'table-row';
                }
            });

            const displayStart = totalRows === 0 ? 0 : start + 1;
            const displayEnd = end > totalRows ? totalRows : end;
            document.getElementById('paginationInfo').textContent =
                `Showing ${displayStart} to ${displayEnd} of ${totalRows} entries`;

            document.getElementById('btnPrev').disabled = (currentPage === 1 || totalRows === 0);
            document.getElementById('btnNext').disabled = (currentPage === totalPages || totalRows === 0);
        }

        function prevSlide() {
            if (currentPage > 1) showPage(currentPage - 1);
        }

        function nextSlide() {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (currentPage < totalPages) showPage(currentPage + 1);
        }

        // Jalankan ulang kalkulasi jika user mengubah ukuran browser/rotasi layar hp
        window.addEventListener('resize', () => {
            initPagination();
            filterTeachers();
        });

        document.addEventListener('DOMContentLoaded', initPagination);

        function openEditModal(id, name, email, nip, schoolId) {
            const modal = document.getElementById('globalEditModal');
            const form = document.getElementById('editTeacherForm');
            const content = modal.querySelector('.transform');

            form.action = `/admin/teachers/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_nip').value = nip === 'null' || nip === '' ? '' : nip;
            document.getElementById('edit_school_id').value = schoolId;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 20);
        }

        function closeEditModal() {
            const modal = document.getElementById('globalEditModal');
            const content = modal.querySelector('.transform');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
@endsection
