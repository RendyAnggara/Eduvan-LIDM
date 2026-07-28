@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6 w-full">
        <div
            class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.courses.index') }}"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 hover:bg-indigo-50 text-gray-500 hover:text-indigo-600 rounded-lg font-bold text-[11px] transition border border-gray-100">
                        <i class="fas fa-arrow-left text-[10px]"></i> Kembali
                    </a>
                    <span class="text-xs font-bold text-gray-300">/</span>
                    <span
                        class="text-xs font-bold text-indigo-600 bg-indigo-50/60 px-2.5 py-0.5 rounded-lg border border-indigo-100/50">
                        Kelola Materi Kursus
                    </span>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight truncate">
                        {{ $course->title }}
                    </h1>
                    @if ($course->course_type === 'school')
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 shrink-0">
                            <i class="fas fa-school text-[9px] mr-1"></i> Sekolah
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shrink-0">
                            <i class="fas fa-crown text-[9px] mr-1"></i> Marketplace
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">Kelola materi dan urutan pembelajaran kursus ini.</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div
                    class="px-3.5 py-2 bg-indigo-50/70 text-indigo-700 border border-indigo-100/80 rounded-xl font-black text-xs flex items-center gap-2">
                    <i class="fas fa-play-circle text-indigo-500 text-sm"></i>
                    <span>{{ count($course->contents) }} Video Tersimpan</span>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div
                class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold space-y-1 shadow-sm">
                <div class="flex items-center gap-2 text-rose-700">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span>Terjadi kesalahan input materi:</span>
                </div>
                <ul class="list-disc pl-5 font-medium space-y-0.5 text-rose-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="w-full">
                @if ($course->course_type !== 'school')
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Tambah Materi Baru</h3>
                                <p class="text-[11px] text-gray-400">Input video pembelajaran baru.</p>
                            </div>
                        </div>

                        <form action="{{ route('admin.courses.storeContent', $course->id) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">Judul
                                    Materi <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" value="{{ old('title') }}" required
                                    class="w-full px-3.5 py-2.5 border border-gray-200 bg-white rounded-xl text-xs font-medium text-gray-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm placeholder:text-gray-400"
                                    placeholder="Contoh: Pengenalan Sintaks Dasar">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">URL
                                    Video (YouTube/Vimeo) <span class="text-rose-500">*</span></label>
                                <input type="text" name="video_url" value="{{ old('video_url') }}" required
                                    class="w-full px-3.5 py-2.5 border border-gray-200 bg-white rounded-xl text-xs font-medium text-gray-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm placeholder:text-gray-400"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">Urutan
                                    (Order)</label>
                                <input type="number" name="order"
                                    value="{{ old('order', count($course->contents) + 1) }}" min="1"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 bg-white rounded-xl text-xs font-bold text-gray-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm">
                            </div>

                            <button type="submit"
                                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-md shadow-indigo-100 transition active:scale-95 cursor-pointer flex items-center justify-center gap-2">
                                <i class="fas fa-save text-xs"></i> Simpan Materi
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-5 space-y-3 shadow-sm">
                        <div class="flex items-center gap-2 text-blue-900 font-bold text-xs uppercase tracking-wider">
                            <i class="fas fa-lock text-blue-600"></i> Mode Pantau (Read-Only)
                        </div>
                        <p class="text-xs text-blue-800 leading-relaxed font-medium">
                            Materi pembelajaran pada kursus instansi ini dikelola secara independen oleh <strong
                                class="font-bold text-blue-950">Guru Pengampu Sekolah</strong>.
                        </p>
                        <div
                            class="p-3 bg-white/80 rounded-xl border border-blue-100/80 text-[11px] text-gray-600 space-y-1">
                            <span class="font-bold text-gray-800 block">Hak Akses Admin:</span>
                            <ul class="list-disc pl-4 space-y-0.5">
                                <li>Memantau urutan & struktur materi video.</li>
                                <li>Dilarang menambah/mengedit materi internal.</li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2 w-full min-w-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-list-ul text-indigo-600 text-sm"></i>
                            <h3 class="font-black text-gray-900 text-base tracking-tight">Daftar Materi Pembelajaran</h3>
                        </div>
                        <span
                            class="text-xs font-bold px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl">
                            Total: {{ count($course->contents) }} Video
                        </span>
                    </div>

                    <!-- DESKTOP TABLE VIEW -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead
                                class="bg-gray-50/80 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="py-3.5 px-4 w-16 text-center">Order</th>
                                    <th class="py-3.5 px-4">Judul Materi</th>
                                    <th class="py-3.5 px-4 w-28 text-center">Tipe</th>
                                    @if ($course->course_type !== 'school')
                                        <th class="py-3.5 px-4 w-20 text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($course->contents->sortBy('order') as $content)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 px-4 text-center font-bold text-gray-400">
                                            <span
                                                class="w-6 h-6 rounded-lg bg-gray-100 text-gray-600 text-xs font-bold inline-flex items-center justify-center">
                                                {{ $content->order }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-gray-900 leading-snug">
                                            {{ $content->title }}
                                            @if ($content->content_url)
                                                <a href="{{ $content->content_url }}" target="_blank"
                                                    class="text-[11px] font-semibold text-indigo-600 hover:underline block mt-0.5 truncate max-w-md">
                                                    <i class="fas fa-external-link-alt text-[9px] mr-0.5"></i>
                                                    {{ $content->content_url }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                                VIDEO
                                            </span>
                                        </td>
                                        @if ($course->course_type !== 'school')
                                            <td class="py-3.5 px-4 text-center">
                                                <form action="{{ route('admin.courses.destroyContent', $content->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-8 h-8 inline-flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition cursor-pointer border border-rose-100/50 shadow-sm"
                                                        title="Hapus Materi">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $course->course_type !== 'school' ? 4 : 3 }}"
                                            class="p-12 text-center text-gray-400 italic">
                                            <i class="fas fa-inbox text-3xl mb-2 text-gray-300 block"></i>
                                            Belum ada materi pembelajaran yang diunggah untuk kursus ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="block md:hidden p-4 space-y-3 bg-gray-50/30">
                        @forelse($course->contents->sortBy('order') as $content)
                            <div class="bg-white rounded-2xl border border-gray-100 p-3.5 space-y-2 shadow-sm">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span
                                            class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $content->order }}
                                        </span>
                                        <h4 class="font-bold text-gray-900 text-xs leading-snug truncate">
                                            {{ $content->title }}
                                        </h4>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-1.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded text-[9px] font-black uppercase tracking-wider shrink-0">
                                        VIDEO
                                    </span>
                                </div>

                                @if ($content->content_url)
                                    <a href="{{ $content->content_url }}" target="_blank"
                                        class="text-[11px] font-medium text-indigo-600 hover:underline block truncate pl-8">
                                        <i class="fas fa-external-link-alt text-[9px] mr-1"></i> Tonton Link Video
                                    </a>
                                @endif

                                @if ($course->course_type !== 'school')
                                    <div class="pt-2 border-t border-gray-100 flex justify-end">
                                        <form action="{{ route('admin.courses.destroyContent', $content->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200/60 rounded-lg text-[10px] font-bold flex items-center gap-1 transition active:bg-rose-100 shadow-sm">
                                                <i class="fas fa-trash text-[9px]"></i> Hapus Materi
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div
                                class="bg-white p-8 rounded-2xl text-center text-gray-400 text-xs italic border border-gray-100">
                                <i class="fas fa-inbox text-2xl mb-1 text-gray-300 block"></i>
                                Belum ada materi pembelajaran yang diunggah untuk kursus ini.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
