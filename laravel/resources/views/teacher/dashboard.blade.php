@extends('layouts.teacher')

@section('title', 'Dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6 mb-6 flex flex-col gap-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-teal-600 tracking-tight flex items-center gap-2 mb-1">
                        Halo, {{ Auth::user()->name }}!
                    </h1>
                    <p class="text-slate-500 font-medium text-xs sm:text-sm">Selamat Datang di Dasbor Manajemen Siswa
                        EduLearn</p>
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
            <div
                class="pt-4 border-t border-slate-100 flex items-start sm:items-center gap-2 text-xs sm:text-sm text-slate-600 font-medium">
                <div
                    class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs shrink-0">
                    &checkmark;</div>
                <span class="leading-tight">Akun Anda Terverifikasi Sebagai <span
                        class="text-teal-600 font-semibold">Guru</span> di
                    {{ Auth::user()->school ? Auth::user()->school->name : 'SMP Negeri 2 Karawang Barat' }}</span>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Siswa
                        Terdaftar</span>
                    <span class="text-3xl font-extrabold text-slate-800 tracking-tight">
                        {{ $totalStudents ?? 0 }} <span class="text-base font-medium text-slate-400">Siswa</span>
                    </span>
                </div>
                <div class="bg-teal-50 text-teal-600 p-3 rounded-xl shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status
                        Aktivitas</span>
                    <span class="text-base sm:text-lg font-bold text-emerald-600 mt-2 flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span> Kelas Aktif
                    </span>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center justify-between sm:col-span-2 md:col-span-1">
                <div class="flex flex-col">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Model
                        Sistem</span>
                    <span class="text-sm sm:text-base font-bold text-slate-700 mt-2">Pembelajaran Diferensiasi</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6">
                <div class="mb-4">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-800 tracking-tight">Daftar Pemantauan Siswa</h2>
                </div>

                <form action="{{ route('teacher.dashboard') }}" method="GET"
                    class="flex flex-col sm:flex-row gap-3 mb-6 w-full">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, email, atau nisn..."
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-500 font-medium text-xs text-slate-700">
                        @if (request('search') || request('class_filter'))
                            <a href="{{ route('teacher.dashboard') }}"
                                class="absolute right-4 top-3 text-slate-400 hover:text-slate-600 text-xs font-bold">✕
                                Clear</a>
                        @endif
                    </div>

                    <div class="sm:w-48 shrink-0">
                        <select name="class_filter" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-500 font-bold text-xs text-slate-600">
                            <option value="">Semua Kelas</option>
                            <option value="Kelas 7" {{ request('class_filter') == 'Kelas 7' ? 'selected' : '' }}>Kelas 7
                            </option>
                            <option value="Kelas 8" {{ request('class_filter') == 'Kelas 8' ? 'selected' : '' }}>Kelas 8
                            </option>
                            <option value="Kelas 9" {{ request('class_filter') == 'Kelas 9' ? 'selected' : '' }}>Kelas 9
                            </option>
                        </select>
                    </div>
                </form>
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="py-3 px-4 rounded-l-xl">NISN</th>
                                <th class="py-3 px-4">Nama</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">Kelas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-4 text-slate-500 font-mono text-xs tracking-wide">
                                        {{ $student->nisn_or_nip }}</td>
                                    <td class="py-4 px-4 font-bold text-slate-900">{{ $student->name }}</td>
                                    <td class="py-4 px-4 text-slate-600 text-xs">{{ $student->email }}</td>
                                    <td class="py-4 px-4 text-center">
                                        <span
                                            class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700 border border-teal-100">
                                            {{ $student->class ?? 'Belum Diatur' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 font-medium">Tidak ada data
                                        siswa terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="block md:hidden space-y-3">
                    @forelse($students as $student)
                        <div class="p-4 bg-slate-50/70 border border-slate-200/60 rounded-xl flex flex-col gap-2">
                            <div class="flex items-center justify-between gap-2">
                                <span
                                    class="text-[10px] font-mono font-bold text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded">
                                    NISN: {{ $student->nisn_or_nip }}
                                </span>
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-black bg-teal-50 text-teal-700 border border-teal-100">
                                    {{ $student->class ?? 'Belum Diatur' }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">{{ $student->name }}</h4>
                                <p class="text-slate-500 text-xs mt-0.5">{{ $student->email }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400 font-medium">Tidak ada data siswa terdaftar.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $students->links() }}
                </div>
            </div>

            <div class="flex flex-col gap-6 w-full">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-slate-800 tracking-tight">Leaderboard Siswa</h2>
                        <p class="text-xs text-slate-400 font-bold mt-0.5">(Top 5 Skor Quiz Per Kelas)</p>
                    </div>

                    <div class="flex bg-slate-100 p-1 rounded-xl mb-4 text-xs font-bold">
                        <button onclick="switchTab('class7')" id="btn-class7"
                            class="flex-1 py-2 rounded-lg bg-white text-teal-600 shadow-sm transition-all focus:outline-none">Kelas
                            7</button>
                        <button onclick="switchTab('class8')" id="btn-class8"
                            class="flex-1 py-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all focus:outline-none">Kelas
                            8</button>
                        <button onclick="switchTab('class9')" id="btn-class9"
                            class="flex-1 py-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all focus:outline-none">Kelas
                            9</button>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div id="panel-class7" class="tab-panel flex flex-col gap-2">
                            @forelse($leaderboard7 as $index => $rank)
                                <div
                                    class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-xl min-w-0 gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <span
                                            class="w-5 h-5 flex items-center justify-center bg-teal-50 text-teal-600 font-black text-[10px] rounded-md shrink-0 border border-teal-100/50">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="font-bold text-xs text-slate-800 truncate">{{ $rank->name }}</span>
                                    </div>
                                    <span
                                        class="font-bold text-xs text-teal-700 bg-white border border-teal-100 px-2 py-0.5 rounded-md shadow-2xs shrink-0 font-mono">
                                        {{ $rank->highest_score ?? 0 }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-slate-400 font-medium">Belum ada nilai untuk
                                    Kelas 7.</div>
                            @endforelse
                        </div>

                        <div id="panel-class8" class="tab-panel flex flex-col gap-2 hidden">
                            @forelse($leaderboard8 as $index => $rank)
                                <div
                                    class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-xl min-w-0 gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <span
                                            class="w-5 h-5 flex items-center justify-center bg-teal-50 text-teal-600 font-black text-[10px] rounded-md shrink-0 border border-teal-100/50">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="font-bold text-xs text-slate-800 truncate">{{ $rank->name }}</span>
                                    </div>
                                    <span
                                        class="font-bold text-xs text-teal-700 bg-white border border-teal-100 px-2 py-0.5 rounded-md shadow-2xs shrink-0 font-mono">
                                        {{ $rank->highest_score ?? 0 }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-slate-400 font-medium">Belum ada nilai untuk
                                    Kelas 8.</div>
                            @endforelse
                        </div>
                        <div id="panel-class9" class="tab-panel flex flex-col gap-2 hidden">
                            @forelse($leaderboard9 as $index => $rank)
                                <div
                                    class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-xl min-w-0 gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <span
                                            class="w-5 h-5 flex items-center justify-center bg-teal-50 text-teal-600 font-black text-[10px] rounded-md shrink-0 border border-teal-100/50">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="font-bold text-xs text-slate-800 truncate">{{ $rank->name }}</span>
                                    </div>
                                    <span
                                        class="font-bold text-xs text-teal-700 bg-white border border-teal-100 px-2 py-0.5 rounded-md shadow-2xs shrink-0 font-mono">
                                        {{ $rank->highest_score ?? 0 }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-slate-400 font-medium">Belum ada nilai untuk
                                    Kelas 9.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-5 tracking-tight">Aktivitas Terkini</h2>
                    <div class="flex flex-col gap-5 pl-4 border-l-2 border-slate-100 relative">
                        @forelse($recentActivities ?? [] as $activity)
                            @php
                                $isQuiz = isset($activity['type']) && $activity['type'] === 'quiz';
                            @endphp
                            <div class="flex flex-col text-xs relative">
                                <span
                                    class="absolute -left-[21px] top-1 w-2 h-2 rounded-full border-2 bg-white {{ $isQuiz ? 'border-teal-500' : 'border-amber-500' }}"></span>

                                <p class="text-slate-600 leading-relaxed">
                                    <span class="font-bold text-slate-900">{{ $activity['name'] }}</span>
                                    {{ $activity['message'] }}
                                </p>
                                <span class="text-[10px] text-slate-400 font-bold mt-1 font-mono">
                                    {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-xs text-slate-400 font-medium">Belum ada riwayat aktivitas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(targetId) {
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.add('hidden'));
            document.getElementById('panel-' + targetId).classList.remove('hidden');
            const buttons = ['class7', 'class8', 'class9'];
            buttons.forEach(id => {
                const btn = document.getElementById('btn-' + id);
                if (id === targetId) {
                    btn.className =
                        "flex-1 py-2 rounded-lg bg-white text-teal-600 shadow-sm transition-all focus:outline-none";
                } else {
                    btn.className =
                        "flex-1 py-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all focus:outline-none";
                }
            });
        }
    </script>
@endsection
