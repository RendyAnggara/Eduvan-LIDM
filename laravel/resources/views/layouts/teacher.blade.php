<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Guru') - Edulearn</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 font-sans antialiased text-slate-800 flex flex-col md:flex-row min-h-screen">

    <header
        class="bg-slate-950 text-white p-4 flex items-center justify-between border-b border-slate-800 md:hidden sticky top-0 z-50 w-full shadow-md">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-teal-500/10 border border-teal-500/30 flex items-center justify-center">
                <span class="text-teal-400 font-black text-xs tracking-tight">E</span>
            </div>
            <h2 class="text-lg font-bold tracking-tight">Edu<span class="text-teal-400">Learn</span></h2>
        </div>

        <!-- Tombol Hamburger Trigger Open -->
        <button onclick="toggleMobileSidebar(true)" class="p-1 text-slate-400 hover:text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </header>

    <!-- ====================================================== -->
    <!-- 2. SIDEBAR NAVIGASI (Desktop & Full Height Mobile Drawer) -->
    <!-- ====================================================== -->
    <aside id="sidebarMenu"
        class="fixed inset-y-0 left-0 w-64 bg-slate-950 text-white flex flex-col justify-between shrink-0 border-r border-slate-800 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:h-screen md:sticky md:top-0 h-full">

        <!-- Bagian Atas Menu -->
        <div class="p-6 flex-1 flex flex-col">
            <!-- Brand Asset & Tombol Close Mobile -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center">
                        <span class="text-teal-400 font-black text-sm tracking-tight">E</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">Edu<span class="text-teal-400">Learn</span></h2>
                        <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider -mt-1">Portal Guru</p>
                    </div>
                </div>

                <!-- Tombol Close (Hanya di layar HP) -->
                <button onclick="toggleMobileSidebar(false)"
                    class="p-1 text-slate-400 hover:text-white md:hidden focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Menu Links -->
            <nav class="mt-4 space-y-1.5 flex-1">
                <a href="{{ route('teacher.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-200 {{ Route::is('teacher.dashboard') ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('teacher.students.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-200 {{ Route::is('teacher.students.*') ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Manajemen Siswa
                </a>

                <a href="{{ route('teacher.material.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition duration-150 {{ request()->routeIs('teacher.material.*') ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Materi Pembelajaran</span>
                </a>

                <a href="{{ route('teacher.quiz.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition duration-150 {{ request()->routeIs('teacher.quiz.*') ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Soal & Kuis</span>
                </a>
            </nav>
        </div>

        <!-- Tombol Logout (Tetap nempel di bawah tapi menyatu di area aside h-full) -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 mt-auto">
            <form action="{{ route('teacher.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white px-4 py-3 rounded-xl font-bold transition duration-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebarOverlay" onclick="toggleMobileSidebar(false)"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden z-40 md:hidden"></div>

    <main class="flex-1 p-4 sm:p-6 md:p-8 overflow-y-auto max-w-full">
        @yield('content')
    </main>

    <script>
        function toggleMobileSidebar(open) {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('sidebarOverlay');

            if (open) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>

</html>
