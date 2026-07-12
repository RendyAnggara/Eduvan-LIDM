@extends('layouts.teacher')

@section('title', 'Konfigurasi Konten Diferensiasi')

@section('content')
    <div class="w-full">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4 w-full">
                <a href="{{ route('teacher.material.manage', $lesson->chapter->course_id) }}"
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-white border border-slate-200/70 rounded-full text-[11px] font-bold text-teal-600 hover:bg-slate-50 hover:text-teal-700 shadow-sm transition duration-150">
                    <span class="text-xs">&larr;</span> Kembali ke Detail Bab
                </a>
                <span class="text-[10px] font-bold text-slate-300 tracking-wider hidden sm:inline font-mono">EduLearn
                    Kurikulum</span>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100 w-full">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                    {{ $lesson->chapter->course->title }} &nbsp;•&nbsp; {{ $lesson->chapter->title }}
                </span>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight mt-1">Konfigurasi Materi Diferensiasi
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Lengkapi bahan ajar untuk pertemuan: <strong
                        class="text-slate-700">{{ $lesson->title }}</strong></p>
            </div>
        </div>

        <form action="{{ route('teacher.material.update_content', $lesson->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div
                    class="lg:col-span-1 bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-base font-bold text-slate-800 tracking-tight">Jalur Visual & Auditori</h3>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Tujukan bagian ini untuk siswa dengan gaya belajar Visual/Auditori. Tempelkan tautan video
                            penjelasan materi pembelajaran yang relevan.
                        </p>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-600">URL Video Pembelajaran</label>
                            <input type="url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                            <span class="text-[10px] text-slate-400 leading-tight mt-0.5 block">Mendukung tautan sematan
                                dari YouTube atau Google Drive.</span>
                        </div>
                    </div>

                    @if ($lesson->video_url)
                        <div
                            class="p-3 bg-sky-50 border border-sky-100 text-sky-700 text-[11px] font-bold rounded-xl flex items-center gap-2 shadow-2xs">
                            <span
                                class="flex items-center justify-center w-4 h-4 rounded-full bg-sky-500 text-white text-[9px]">&checkmark;</span>
                            Konten video pembelajaran tersemat.
                        </div>
                    @endif
                </div>
                <div class="lg:col-span-2 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 tracking-tight">Jalur Membaca & Teks</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Modul rangkuman, teks bacaan, atau instruksi
                                tertulis untuk siswa tipe membaca.</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Isi Dokumen / Artikel Materi</label>
                        <textarea name="content_text"
                            placeholder="Tuliskan isi materi pembelajaran atau rangkuman bab di sini secara mendalam..."
                            class="w-full h-64 lg:h-96 px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium leading-relaxed resize-y">{{ old('content_text', $lesson->content_text) }}</textarea>
                    </div>
                </div>
            </div>
            <div
                class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 w-full">
                <button type="submit"
                    class="w-full sm:w-auto px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-xs text-center justify-center order-1 sm:order-2">
                    Simpan Materi Pembelajaran
                </button>
                <a href="{{ route('teacher.material.manage', $lesson->chapter->course_id) }}"
                    class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition text-center justify-center order-2 sm:order-1">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
