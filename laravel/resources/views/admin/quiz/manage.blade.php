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
                    Kelola Quiz & Soal
                </h1>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div
                    class="px-3.5 py-2 bg-indigo-50/70 text-indigo-700 border border-indigo-100/80 rounded-xl font-black text-xs flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-indigo-500 text-sm"></i>
                    <span>{{ $questions->count() }} Soal Tersimpan</span>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm lg:sticky lg:top-6">
                <div class="flex items-center gap-2.5 mb-5 border-b border-gray-100 pb-3">
                    <div
                        class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-gray-900">Tambah Soal Baru</h2>
                        <p class="text-[11px] text-gray-400">Input pertanyaan & kunci jawaban.</p>
                    </div>
                </div>

                <form action="{{ route('admin.quiz.store', $course->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Pertanyaan / Soal</label>
                        <textarea name="question" rows="3"
                            class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-xs font-medium shadow-sm text-gray-900"
                            placeholder="Tuliskan pertanyaan soal di sini..." required></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Opsi A</label>
                            <input type="text" name="option_a" placeholder="Pilihan A"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-xs font-medium shadow-sm text-gray-900"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Opsi B</label>
                            <input type="text" name="option_b" placeholder="Pilihan B"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-xs font-medium shadow-sm text-gray-900"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Opsi C</label>
                            <input type="text" name="option_c" placeholder="Pilihan C"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-xs font-medium shadow-sm text-gray-900"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Opsi D</label>
                            <input type="text" name="option_d" placeholder="Pilihan D"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-xs font-medium shadow-sm text-gray-900"
                                required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kunci Jawaban Benar</label>
                        <div class="relative">
                            <select name="answer"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-xs font-bold text-gray-800 shadow-sm appearance-none cursor-pointer"
                                required>
                                <option value="a">Opsi A</option>
                                <option value="b">Opsi B</option>
                                <option value="c">Opsi C</option>
                                <option value="d">Opsi D</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-2.5 px-4 rounded-xl transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2 text-xs">
                        <i class="fas fa-save text-xs"></i> Simpan Soal Kuis
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between pb-1">
                    <h2 class="text-base font-black text-gray-900 tracking-tight">Daftar Soal Kursus Ini</h2>
                    <span class="text-xs text-gray-400 font-medium">Total: {{ $questions->count() }} Pertanyaan</span>
                </div>

                @if ($questions->isEmpty())
                    <div
                        class="bg-white border border-gray-200 border-dashed p-10 text-center rounded-2xl shadow-sm space-y-2">
                        <div
                            class="w-12 h-12 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto text-xl">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <p class="text-gray-500 text-xs italic font-medium">Belum ada soal kuis untuk course ini. Silakan
                            tambah soal lewat form di samping.</p>
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach ($questions as $index => $q)
                            @php
                                $ans = strtolower($q->correct_answer ?? 'a');
                            @endphp
                            <div
                                class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-3.5 relative hover:border-gray-200 transition">
                                <div class="flex justify-between items-center gap-2 border-b border-gray-50 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg font-black text-[10px] uppercase">
                                            Soal #{{ $index + 1 }}
                                        </span>
                                        <span
                                            class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg font-black text-[10px] uppercase">
                                            Kunci: Opsi {{ strtoupper($ans) }}
                                        </span>
                                    </div>

                                    <form action="{{ route('admin.quiz.destroy', $q->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin mau menghapus soal kuis ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-lg text-xs font-bold transition">
                                            <i class="fas fa-trash-alt text-[11px]"></i>
                                            <span class="text-[11px]">Hapus</span>
                                        </button>
                                    </form>
                                </div>

                                <p class="text-gray-900 font-extrabold text-sm leading-relaxed break-words">
                                    {{ $q->question_text }}
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <div
                                        class="flex items-center gap-2 p-2.5 rounded-xl transition-all border @if ($ans == 'a') bg-emerald-50 text-emerald-900 border-emerald-300 font-bold @else bg-gray-50 text-gray-800 border-gray-200 font-medium @endif">
                                        <span
                                            class="w-5 h-5 rounded-lg flex items-center justify-center text-[10px] font-black shrink-0 @if ($ans == 'a') bg-emerald-600 text-white @else bg-gray-200 text-gray-700 @endif">A</span>
                                        <span class="break-words">{{ $q->option_a }}</span>
                                    </div>

                                    <div
                                        class="flex items-center gap-2 p-2.5 rounded-xl transition-all border @if ($ans == 'b') bg-emerald-50 text-emerald-900 border-emerald-300 font-bold @else bg-gray-50 text-gray-800 border-gray-200 font-medium @endif">
                                        <span
                                            class="w-5 h-5 rounded-lg flex items-center justify-center text-[10px] font-black shrink-0 @if ($ans == 'b') bg-emerald-600 text-white @else bg-gray-200 text-gray-700 @endif">B</span>
                                        <span class="break-words">{{ $q->option_b }}</span>
                                    </div>

                                    <div
                                        class="flex items-center gap-2 p-2.5 rounded-xl transition-all border @if ($ans == 'c') bg-emerald-50 text-emerald-900 border-emerald-300 font-bold @else bg-gray-50 text-gray-800 border-gray-200 font-medium @endif">
                                        <span
                                            class="w-5 h-5 rounded-lg flex items-center justify-center text-[10px] font-black shrink-0 @if ($ans == 'c') bg-emerald-600 text-white @else bg-gray-200 text-gray-700 @endif">C</span>
                                        <span class="break-words">{{ $q->option_c }}</span>
                                    </div>

                                    <div
                                        class="flex items-center gap-2 p-2.5 rounded-xl transition-all border @if ($ans == 'd') bg-emerald-50 text-emerald-900 border-emerald-300 font-bold @else bg-gray-50 text-gray-800 border-gray-200 font-medium @endif">
                                        <span
                                            class="w-5 h-5 rounded-lg flex items-center justify-center text-[10px] font-black shrink-0 @if ($ans == 'd') bg-emerald-600 text-white @else bg-gray-200 text-gray-700 @endif">D</span>
                                        <span class="break-words">{{ $q->option_d }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
@endsection
