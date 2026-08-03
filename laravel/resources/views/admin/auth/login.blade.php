<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - EduLearn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-800 antialiased min-h-screen flex">

    <div class="w-full min-h-screen grid grid-cols-1 lg:grid-cols-2">
        <div
            class="relative bg-slate-950 text-white p-8 sm:p-12 lg:p-16 flex flex-col justify-between overflow-hidden hidden lg:flex border-r border-slate-800/60">
            <div
                class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[140px] pointer-events-none">
            </div>
            <div
                class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[140px] pointer-events-none">
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center">
                        <span class="text-teal-400 font-black text-base tracking-tight">E</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-white">Edu<span
                                class="text-teal-400">Learn</span></h2>
                        <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider -mt-1">Administrator
                            Central Workspace</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 max-w-lg my-auto py-12">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-semibold mb-6">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    PORTAL ADMINISTRATOR
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Kontrol Penuh Platform & Pengelolaan Sistem.
                </h1>

                <p class="text-slate-400 text-sm sm:text-base mt-4 leading-relaxed">
                    Kelola data sekolah, verifikasi akun guru, manajerial kuis, transaksi sistem secara terpusat dalam satu dashboard.
                </p>

                <div class="mt-8 bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 backdrop-blur-md">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3">System Metrics
                        Status</div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/50">
                            <span class="block text-[10px] text-slate-500 font-bold uppercase">System</span>
                            <span class="text-xs font-bold text-teal-400">Active</span>
                        </div>
                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/50">
                            <span class="block text-[10px] text-slate-500 font-bold uppercase">Security</span>
                            <span class="text-xs font-bold text-indigo-400">SSL 256-bit</span>
                        </div>
                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/50">
                            <span class="block text-[10px] text-slate-500 font-bold uppercase">Database</span>
                            <span class="text-xs font-bold text-emerald-400">Connected</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10">
                <p class="text-slate-500 text-xs font-medium">
                    &copy; {{ date('Y') }} EduLearn Platform. All rights reserved.
                </p>
            </div>
        </div>
        <div class="bg-white p-6 sm:p-12 lg:p-16 flex flex-col justify-between min-h-screen">

            <div class="lg:hidden mb-8">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center">
                        <span class="text-teal-600 font-black text-sm tracking-tight">E</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-slate-900">Edu<span
                                class="text-teal-600">Learn</span></h2>
                        <p class="text-slate-500 text-[10px] uppercase font-bold tracking-wider -mt-1">Admin Portal</p>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-md mx-auto my-auto space-y-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Silakan masuk menggunakan kredensial akun administrator Anda.
                    </p>
                </div>

                @if (session('error'))
                    <div
                        class="bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm p-4 rounded-xl flex items-center gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-rose-500 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ url('admin/login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email"
                            class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                            Email Admin
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all @error('email') border-rose-500 @enderror"
                            placeholder="Contoh: admin@gmail.com">
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                            class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-4 pr-11 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all"
                                placeholder="Masukkan password">

                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 cursor-pointer transition-colors focus:outline-none">
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg id="eyeClose" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-5 h-5 hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-slate-950 hover:bg-slate-800 text-white font-bold rounded-xl py-3.5 transition-all duration-200 shadow-lg shadow-slate-950/20 active:scale-[0.99] cursor-pointer text-sm flex items-center justify-center gap-2">
                        <span>Masuk ke Portal Admin &rarr;</span>
                    </button>
                </form>
            </div>

            <div class="text-center pt-6 border-t border-slate-100">
                <a href="{{ route('landing.page') }}"
                    class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors inline-flex items-center gap-1.5">
                    <span>&larr; Kembali ke Beranda Utama</span>
                </a>
            </div>

        </div>

    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeOpen = document.querySelector('#eyeOpen');
        const eyeClose = document.querySelector('#eyeClose');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'password') {
                eyeOpen.classList.remove('hidden');
                eyeClose.classList.add('hidden');
            } else {
                eyeOpen.classList.add('hidden');
                eyeClose.classList.remove('hidden');
            }
        });
    </script>
</body>

</html>
