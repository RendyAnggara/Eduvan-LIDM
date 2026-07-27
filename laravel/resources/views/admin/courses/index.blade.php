@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6">

        <!-- HEADER UTAMA -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Daftar Kursus</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola materi, harga, kategori, dan seluruh konten pembelajaran
                    EduLearn.</p>
            </div>
            <div>
                <a href="{{ route('admin.courses.create') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-2 shadow-lg shadow-indigo-100 w-full sm:w-auto transition-all active:scale-95">
                    <i class="fas fa-plus text-[10px]"></i> Tambah Kursus Baru
                </a>
            </div>
        </div>

        <!-- FORM SEARCH & FILTER INTEGRASI (OPSI A: MINIMALIS & PROSISIONAL) -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('admin.courses.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 w-full">

                <div class="relative w-full lg:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul, deskripsi, atau nama sekolah..."
                        class="pl-10 pr-4 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition text-sm w-full font-medium placeholder:text-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                </div>

                <!-- 2. Dropdown Jenis Kursus -->
                <div class="w-full">
                    <select name="course_type" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-medium focus:border-indigo-500 focus:bg-white outline-none transition cursor-pointer">
                        <option value="all" {{ request('course_type') == 'all' ? 'selected' : '' }}>Semua Jenis Kursus
                        </option>
                        <option value="premium" {{ request('course_type') == 'premium' ? 'selected' : '' }}>Marketplace
                            (Admin)</option>
                        <option value="school" {{ request('course_type') == 'school' ? 'selected' : '' }}>Instansi (Guru)
                        </option>
                    </select>
                </div>

                <!-- 3. Dropdown Tingkat Kelas SMP (Kelas 7, 8, 9) -->
                <div class="w-full flex gap-2">
                    <select name="grade_level" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-medium focus:border-indigo-500 focus:bg-white outline-none transition cursor-pointer">
                        <option value="all">Semua Kelas SMP</option>
                        <option value="7" {{ request('grade_level') == '7' ? 'selected' : '' }}>Kelas 7 SMP</option>
                        <option value="8" {{ request('grade_level') == '8' ? 'selected' : '' }}>Kelas 8 SMP</option>
                        <option value="9" {{ request('grade_level') == '9' ? 'selected' : '' }}>Kelas 9 SMP</option>
                    </select>

                    @if (request('search') ||
                            (request('course_type') && request('course_type') !== 'all') ||
                            (request('grade_level') && request('grade_level') !== 'all'))
                        <a href="{{ route('admin.courses.index') }}"
                            class="px-3 py-2.5 border border-gray-200 text-center rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition shrink-0 flex items-center justify-center"
                            title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- 🖥️ DESKTOP VIEW: Tabel Tradisional -->
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 border-b w-12 text-center">No</th>
                        <th class="p-4 border-b w-24">Cover</th>
                        <th class="p-4 border-b">Judul Kursus & Detail Instansi</th>
                        <th class="p-4 border-b w-36">Kategori</th>
                        <th class="p-4 border-b w-32">Harga</th>
                        <th class="p-4 border-b w-24 text-center">Rating</th>
                        <th class="p-4 border-b text-right pr-6 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 pl-6 text-center font-bold text-gray-400">{{ $loop->iteration }}</td>
                            <td class="p-4">
                                @if ($course->image)
                                    @if (str_contains($course->image, 'data:image'))
                                        <img src="{{ $course->image }}"
                                            class="w-16 h-10 object-cover rounded-xl border border-gray-100 shadow-sm">
                                    @else
                                        <img src="{{ asset('storage/' . $course->image) }}"
                                            class="w-16 h-10 object-cover rounded-xl border border-gray-100 shadow-sm">
                                    @endif
                                @else
                                    <div
                                        class="w-16 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-black tracking-wide shadow-sm">
                                        EDV
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 min-w-[220px]">
                                <a href="{{ route('admin.courses.show', $course->id) }}"
                                    class="font-bold text-gray-900 hover:text-indigo-600 transition-colors text-base block leading-snug">
                                    {{ $course->title }}
                                </a>

                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    @if ($course->course_type === 'school')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 max-w-[220px] truncate"
                                            title="{{ $course->school->name ?? ($course->teachers->first()?->school?->name ?? 'Instansi Mitra') }}">
                                            <i class="fas fa-school text-[9px] mr-1"></i>
                                            {{ $course->school->name ?? ($course->teachers->first()?->school?->name ?? 'Instansi Mitra') }}
                                        </span>

                                        @if ($course->grade_level)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                                <i class="fas fa-graduation-cap text-[9px] mr-1"></i> Kelas
                                                {{ $course->grade_level }} SMP
                                            </span>
                                        @endif
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <i class="fas fa-crown text-[9px] mr-1"></i> Marketplace Admin
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    {{ $course->category }}
                                </span>
                            </td>
                            <td class="p-4 font-black text-emerald-600 text-base whitespace-nowrap">
                                @if ($course->price > 0)
                                    Rp {{ number_format($course->price) }}
                                @else
                                    <span class="text-emerald-500 font-bold">Gratis</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div
                                    class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-lg font-black text-xs">
                                    <i class="fas fa-star text-[10px]"></i>
                                    {{ number_format($course->rating, 1) ?? '0.0' }}
                                </div>
                            </td>
                            <td class="p-4 text-right pr-6 whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    @if ($course->course_type !== 'school')
                                        <a href="{{ route('admin.courses.edit', $course->id) }}"
                                            class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors"
                                            title="Edit Kursus">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @else
                                        <span
                                            class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-400 rounded-xl cursor-not-allowed"
                                            title="Kursus Sekolah Hanya Bisa Diubah Oleh Guru Pengampu">
                                            <i class="fas fa-lock text-xs"></i>
                                        </span>
                                    @endif

                                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition-colors cursor-pointer"
                                            title="Hapus Kursus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-gray-400 italic">
                                Data kursus tidak ditemukan atau belum didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 📱 MOBILE VIEW: List Card Responsif (EduVan) -->
        <div class="block md:hidden space-y-4">
            @forelse($courses as $course)
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex gap-3">
                        <div
                            class="w-24 h-16 shrink-0 overflow-hidden rounded-xl border border-gray-100 shadow-sm bg-gray-50">
                            @if ($course->image)
                                <img src="{{ $course->image }}" alt="Cover" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-black tracking-wide">
                                    EduLearn
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                <span
                                    class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md text-[9px] font-black uppercase tracking-wider truncate">
                                    {{ $course->category }}
                                </span>
                                <div class="inline-flex items-center gap-0.5 text-amber-600 font-black text-xs shrink-0">
                                    <i class="fas fa-star text-[10px]"></i>
                                    {{ number_format($course->rating, 1) ?? '0.0' }}
                                </div>
                            </div>

                            <a href="{{ route('admin.courses.show', $course->id) }}"
                                class="font-bold text-gray-900 text-sm leading-snug block hover:text-indigo-600 transition-colors line-clamp-2">
                                {{ $course->title }}
                            </a>

                            <div class="text-emerald-600 font-black text-sm mt-1">
                                @if ($course->price > 0)
                                    Rp {{ number_format($course->price) }}
                                @else
                                    <span class="text-emerald-500 font-bold">Gratis</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-2.5 bg-gray-50 rounded-xl text-xs flex flex-wrap items-center justify-between gap-2">
                        @if ($course->course_type === 'school')
                            <span class="font-bold text-gray-700 truncate max-w-[180px]">
                                <i class="fas fa-school text-blue-500 text-[10px] mr-1.5"></i>Sekolah:
                                <span
                                    class="font-medium text-gray-500">{{ $course->school->name ?? ($course->teachers->first()?->school?->name ?? 'Instansi Mitra') }}</span>
                            </span>
                            @if ($course->grade_level)
                                <span
                                    class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-100 text-[10px]">
                                    Kelas {{ $course->grade_level }} SMP
                                </span>
                            @endif
                        @else
                            <span class="font-bold text-gray-700">
                                <i class="fas fa-crown text-indigo-500 text-[10px] mr-1.5"></i>Tipe: <span
                                    class="font-medium text-gray-500">Marketplace Premium</span>
                            </span>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex gap-2">
                        <a href="{{ route('admin.courses.show', $course->id) }}"
                            class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-2.5 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-eye text-[10px]"></i>
                            {{ $course->course_type === 'school' ? 'Lihat Materi' : 'Kelola Materi' }}
                        </a>

                        @if ($course->course_type !== 'school')
                            <a href="{{ route('admin.courses.edit', $course->id) }}"
                                class="w-11 h-10 bg-blue-50 text-blue-600 flex items-center justify-center rounded-xl active:bg-blue-100 transition shrink-0">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                        @endif

                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                            class="inline-block shrink-0"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-11 h-10 bg-rose-50 text-rose-600 flex items-center justify-center rounded-xl active:bg-rose-100 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                    Data kursus tidak ditemukan atau belum didaftarkan.
                </div>
            @endforelse
        </div>

    </div>
@endsection
