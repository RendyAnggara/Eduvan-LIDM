<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guru - EduLearn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-pattern {
            background-color: #0f172a;
            background-image: radial-gradient(at 0% 0%, hsla(172, 95%, 39%, 0.15) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(187, 92%, 42%, 0.1) 0, transparent 50%);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-stretch">
    <div
        class="hidden md:flex md:w-1/2 bg-pattern p-12 flex-col justify-between relative overflow-hidden border-r border-slate-800">
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="relative z-10">
            <h1 class="text-2xl font-black text-white tracking-tight">Edu<span class="text-teal-400">Learn</span></h1>
            <p class="text-xs text-slate-400 mt-0.5 font-mono">Teacher Central Workspace</p>
        </div>

        <div class="my-auto relative z-10 max-w-md space-y-6">
            <div class="space-y-2">
                <span
                    class="px-3 py-1 bg-teal-500/10 text-teal-400 border border-teal-500/20 text-[10px] font-bold uppercase tracking-widest rounded-full">Sistem
                    Inklusif</span>
                <h3 class="text-3xl font-extrabold text-white tracking-tight leading-tight">Manajemen Pembelajaran
                    Diferensiasi Jauh Lebih Mudah.</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Pantau perkembangan individu, kelola modul
                    audio-visual, teks bacaan, serta paket evaluasi siswa secara real-time dalam satu ruang kerja
                    terpadu.</p>
            </div>

            <div class="bg-slate-900/60 backdrop-blur-md border border-slate-800 p-4 rounded-2xl space-y-3 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 inline-block"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80 inline-block"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 inline-block"></span>
                    </div>
                    <span class="text-[9px] font-mono text-slate-500">edulearn_analytics.log</span>
                </div>
                <div class="space-y-2">
                    <div class="h-2 w-3/4 bg-slate-800 rounded-full animate-pulse"></div>
                    <div class="h-2 w-1/2 bg-slate-800 rounded-full animate-pulse"></div>
                    <div class="grid grid-cols-3 gap-2 pt-1">
                        <div
                            class="h-8 bg-teal-500/10 border border-teal-500/20 rounded-lg flex flex-col justify-center px-2">
                            <span class="text-[8px] text-slate-500 font-bold uppercase">Kelas 7</span>
                            <span class="text-xs text-teal-400 font-mono font-bold">Active</span>
                        </div>
                        <div
                            class="h-8 bg-cyan-500/10 border border-cyan-500/20 rounded-lg flex flex-col justify-center px-2">
                            <span class="text-[8px] text-slate-500 font-bold uppercase">Materi</span>
                            <span class="text-xs text-cyan-400 font-mono font-bold">+24 Bab</span>
                        </div>
                        <div
                            class="h-8 bg-indigo-500/10 border border-indigo-500/20 rounded-lg flex flex-col justify-center px-2">
                            <span class="text-[8px] text-slate-500 font-bold uppercase">Kuis</span>
                            <span class="text-xs text-indigo-400 font-mono font-bold">98% Done</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-[10px] text-slate-500 font-mono relative z-10">
            &copy; 2026 EduLearn Platform. All rights reserved.
        </div>
    </div>
    <div
        class="w-full md:w-1/2 bg-white flex flex-col justify-center items-center md:justify-between p-6 sm:p-12 lg:p-16 min-h-screen md:min-h-0">
        <div class="flex items-center justify-between md:hidden w-full border-b border-slate-100 pb-4 mb-8 shrink-0">
            <h2 class="text-xl font-black text-slate-800">Edu<span class="text-teal-600">Learn</span></h2>
            <span
                class="text-[10px] font-bold text-slate-400 font-mono bg-slate-50 border px-2 py-0.5 rounded-md">PORTAL
                GURU</span>
        </div>
        <div class="my-auto mx-auto w-full max-w-sm space-y-6">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Selamat Datang Kembali</h3>
                <p class="text-xs text-slate-400 mt-0.5">Silakan masuk menggunakan kredensial akun guru Anda.</p>
            </div>

            @if ($errors->any())
                <div
                    class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-xl shadow-2xs animate-in fade-in duration-200">
                    &times; {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('teacher.login') }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-600">Email Guru</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="Contoh: guru@gmail.com"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 font-medium transition duration-150">
                </div>

                <div class="flex flex-col gap-1.5 relative">
                    <label class="text-xs font-bold text-slate-600">Password</label>
                    <div class="relative w-full">
                        <input type="password" id="passwordInput" name="password" required
                            placeholder="Masukkan password"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 font-medium transition duration-150 pr-10">

                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 hover:bg-slate-800 border border-slate-950 text-white font-bold py-2.5 rounded-xl text-xs transition duration-200 shadow-sm mt-2 tracking-wide text-center justify-center flex items-center">
                    Masuk ke Portal Guru &rarr;
                </button>
            </form>
        </div>
        <div class="text-center pt-8 md:pt-0 shrink-0">
            <a href="{{ route('landing.page') }}"
                class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-teal-600 font-bold transition duration-150">
                &larr; Kembali ke Beranda Utama
            </a>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.824 7.824 3 3m-3-3-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><circle cx="12" cy="12" r="3" />`;
            }
        }
    </script>
</body>

</html>
