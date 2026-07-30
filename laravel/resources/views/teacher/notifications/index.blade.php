@extends('layouts.teacher')

@section('title', 'Notifikasi Siswa')

@section('content')
    <div class="space-y-6">
        <div
            class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Broadcast Notifikasi Siswa</h1>
                <p class="text-slate-500 text-sm mt-1">Kirim pengumuman atau informasi penting langsung ke HP siswa sekolah
                    Anda.</p>
            </div>
            <div
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-teal-50 border border-teal-100 text-teal-600 text-xs font-bold shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.6 1.01 1.087 1.328.52.34 1.132.482 1.748.408 1.25-.152 2.19-1.22 2.19-2.483v-1.127c0-.508.188-.998.53-1.373l1.838-2.022a1.875 1.875 0 0 0 0-2.518l-1.838-2.022c-.342-.375-.53-.865-.53-1.373V6.265c0-1.264-.94-2.331-2.19-2.483-.616-.074-1.228.068-1.748.408-.487.318-.84.778-1.087 1.328-.401.891-.732 1.821-.985 2.783m0 9.18a38.1 38.1 0 0 0 0-9.18" />
                </svg>
                Fitur Pengumuman Guru
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl flex items-center gap-3 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 text-emerald-600 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl flex items-center gap-3 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 text-rose-600 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Formulir Notifikasi</h2>

                <form action="{{ route('teacher.notifications.send') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul
                            Notifikasi</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            placeholder="Contoh: Pengumuman Tugas Kuis Bab 3"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all placeholder:text-slate-400 @error('title') border-rose-500 @enderror"
                            required>
                        @error('title')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe
                            Notifikasi</label>
                        <select name="type"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all @error('type') border-rose-500 @enderror"
                            required>
                            <option value="info" selected>Info Umum</option>
                            <option value="pengumuman">Pengumuman Sekolah</option>
                            <option value="alert">Penting / Urgent</option>
                            <option value="success">Prestasi / Ucapan Selamat</option>
                        </select>
                        @error('type')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pesan
                            Notifikasi</label>
                        <textarea name="message" rows="4" placeholder="Tuliskan isi pesan singkat yang akan tampil di HP siswa..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all placeholder:text-slate-400 @error('message') border-rose-500 @enderror"
                            required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-2 flex justify-start">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-teal-600/20 text-sm transition-all duration-200 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                            Blast Notifikasi
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">Petunjuk Pengiriman</h3>

                    <ul class="space-y-3 text-xs text-slate-600 leading-relaxed">
                        <li class="flex items-start gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Notifikasi ini hanya dikirim ke siswa yang terdaftar di <strong>sekolah
                                    Anda</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Pesan akan langsung muncul sebagai <strong>Push Notification</strong> di HP siswa (jika
                                aplikasi diinstall).</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Siswa juga dapat melihat pesan ini di dalam menu <strong>Inbox Notifikasi
                                    EduVan</strong>.</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
@endsection
