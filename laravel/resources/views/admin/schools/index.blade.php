@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2"
            role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-2"
            role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex flex-col lg:grid lg:grid-cols-3 gap-6 items-start">
        <div class="w-full order-1 lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                <i class="fas fa-school text-slate-400 text-sm"></i>
                <h3 class="font-bold text-gray-800 text-sm">Daftar Sekolah Mitra</h3>
            </div>

            <div class="p-5 overflow-x-auto scroller-smooth w-full block">
                <table class="w-full text-left border-collapse min-w-[550px]">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs font-bold text-gray-500 uppercase bg-gray-50/70">
                            <th class="py-3 px-4">Nama Instansi</th>
                            <th class="py-3 px-4">Alamat</th>
                            <th class="py-3 px-4 text-center w-36">Jumlah Pengajar</th>
                            <th class="py-3 px-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($schools as $school)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-4 font-semibold text-gray-900 min-w-[160px]">{{ $school->name }}</td>
                                <td class="py-4 px-4 text-gray-600 min-w-[200px]">{{ $school->address ?? '-' }}</td>
                                <td class="py-4 px-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-slate-100 text-slate-800 whitespace-nowrap">
                                        {{ $school->total_teachers }} Guru
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap space-x-1">
                                    <button type="button"
                                        onclick="openEditModal('{{ $school->id }}', '{{ addslashes($school->name) }}', '{{ addslashes($school->address) }}')"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition inline-flex items-center justify-center cursor-pointer">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah ini? Data guru yang terikat tidak boleh ada.')">
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
                            <tr>
                                <td colspan="4" class="py-8 text-center text-sm text-gray-400 font-medium">Belum ada data
                                    sekolah terdaftar. Silakan input di form.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="w-full order-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                <i class="fas fa-plus-circle text-slate-400 text-sm"></i>
                <h3 class="font-bold text-gray-800 text-sm">Tambah Sekolah Baru</h3>
            </div>

            <div class="p-5">
                <form action="{{ route('admin.schools.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Instansi Sekolah</label>
                        <input type="text" name="name"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('name') border-rose-500 @enderror"
                            placeholder="Contoh: SMPN 1 Karawang Barat" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Lengkap (Opsional)</label>
                        <textarea name="address" rows="3"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                            placeholder="Jl. Raya Barat No. 12..."></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-black shadow-lg shadow-blue-600/10 active:scale-98 transition cursor-pointer flex items-center justify-center gap-2">
                        <i class="fas fa-save text-xs"></i> Simpan Sekolah
                    </button>
                </form>
            </div>
        </div>

        <div class="w-full order-3 lg:col-span-2 space-y-6">
            <div class="space-y-4">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider tracking-widest pl-1">Analitik Instansi
                    Mitra</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div
                            class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <i class="fas fa-users-cog text-sm"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Rata-rata
                                Pengajar</span>
                            <span class="text-base font-black text-gray-900">
                                {{ count($schools) > 0 ? round($schools->sum('total_teachers') / count($schools), 1) : 0 }}
                                <span class="text-xs font-normal text-gray-400">Guru / Instansi</span>
                            </span>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div
                            class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <i class="fas fa-user-graduate text-sm"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Total
                                Akumulasi Siswa</span>
                            <span class="text-base font-black text-gray-900">
                                {{ $schools->sum('total_students') }} <span class="text-xs font-normal text-gray-400">Murid
                                    Aktif</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                        <i class="fas fa-trophy text-amber-500 text-sm"></i>
                        <h3 class="font-bold text-gray-800 text-sm">Instansi Dengan Siswa Terbanyak</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-gray-200 text-xs font-bold text-gray-500 uppercase bg-gray-50/70">
                                    <th class="py-3 px-4 w-16 text-center">Rank</th>
                                    <th class="py-3 px-4">Nama Sekolah</th>
                                    <th class="py-3 px-4 text-center w-36">Total Siswa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-600 font-medium">
                                @forelse($schools->sortByDesc('total_students')->take(3) as $topSchool)
                                    <tr class="hover:bg-gray-50/30 transition">
                                        <td class="py-3 px-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center w-6 h-6 rounded-xl font-bold text-xs
                                                {{ $loop->first ? 'bg-amber-50 text-amber-700 border border-amber-200 shadow-sm' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-900 font-bold">{{ $topSchool->name }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-800 whitespace-nowrap">
                                                {{ $topSchool->total_students }} Siswa
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-6 text-center text-gray-400 italic font-medium">Belum
                                            ada data peringkat instansi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full order-4 space-y-3">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider tracking-widest pl-1">Aktivitas Instansi
                Terbaru</h3>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                @forelse($recentActivities as $activity)
                    <div class="flex items-start gap-3 text-xs">
                        <div
                            class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $activity->role === 'teacher' ? 'bg-blue-500 animate-pulse' : 'bg-emerald-500' }}">
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-gray-900 font-bold leading-tight">
                                {{ $activity->name }}
                                <span class="font-normal text-gray-500">terdaftar sebagai
                                    {{ $activity->role === 'teacher' ? 'Guru/Pengajar' : 'Siswa/Murid' }}</span>
                            </p>
                            <span class="text-[10px] font-semibold text-indigo-600 block mt-1 truncate">
                                <i class="fas fa-school text-[9px] mr-0.5"></i>
                                {{ $activity->school->name ?? 'Instansi Umum' }}
                            </span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium flex-shrink-0 pl-1 whitespace-nowrap">
                            {{ $activity->created_at ? $activity->created_at->diffForHumans() : '-' }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-400 italic text-xs">Belum ada aktivitas guru atau siswa terikat
                        sekolah baru-baru ini.</div>
                @endforelse
            </div>
        </div>

    </div>

    <div id="editSchoolModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-all duration-300 hidden opacity-0">
        <div class="fixed inset-0 bg-transparent" onclick="closeEditModal()"></div>

        <div
            class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl border border-gray-100 p-6 transform transition-all duration-300 scale-95 opacity-0 z-10">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2 text-gray-800">
                    <i class="fas fa-edit text-amber-500 text-sm"></i>
                    <h3 class="font-bold text-sm">Ubah Data Instansi</h3>
                </div>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form id="editSchoolForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Instansi Sekolah</label>
                    <input type="text" name="name" id="edit_name"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                        required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Lengkap (Opsional)</label>
                    <textarea name="address" id="edit_address" rows="3"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()"
                        class="w-full py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold transition active:scale-98 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-black shadow-lg shadow-amber-600/10 active:scale-98 transition cursor-pointer flex items-center justify-center gap-2">
                        <i class="fas fa-check text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, address) {
            const modal = document.getElementById('editSchoolModal');
            const form = document.getElementById('editSchoolForm');
            const content = modal.querySelector('.transform');

            form.action = `/admin/schools/${id}`;

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_address').value = address === '-' ? '' : address;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 20);
        }

        function closeEditModal() {
            const modal = document.getElementById('editSchoolModal');
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
