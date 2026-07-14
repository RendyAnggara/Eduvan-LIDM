<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduLearn - Wujudkan Pendidikan Inklusif & Merdeka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800 selection:bg-teal-500 selection:text-white">
    <nav
        class="bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/60 py-3.5 px-6 md:px-12 grid grid-cols-2 md:grid-cols-3 items-center w-full">
        <div class="flex justify-start">
            <a href="#" class="text-2xl font-black tracking-tight text-slate-900">
                Edu<span class="text-teal-600">Learn</span>
            </a>
        </div>
        <div class="hidden md:flex items-center justify-center gap-8 text-xs font-bold text-slate-500 tracking-wide">
            <a href="#fitur" class="hover:text-teal-600 transition">FITUR UTAMA</a>
            <a href="#alur" class="hover:text-teal-600 transition">ALUR SISTEM</a>
        </div>
    </nav>
    <header class="relative max-w-6xl mx-auto px-6 pt-20 pb-24 text-center flex flex-col items-center overflow-hidden">
        <div
            class="absolute -top-10 left-1/2 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-teal-400/10 to-cyan-400/10 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="relative z-10 flex flex-col items-center">
            <span
                class="bg-teal-50 text-teal-700 text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full border border-teal-200/60 mb-5 tracking-wide uppercase">
                Platform Pembelajaran Berdiferensiasi SMP
            </span>

            <h2
                class="text-3xl sm:text-5xl md:text-6xl font-black text-slate-900 tracking-tight max-w-4xl leading-[1.15] md:leading-[1.1]">
                Memfasilitasi <span
                    class="bg-gradient-to-r from-teal-600 to-cyan-600 bg-clip-text text-transparent">Keberagaman
                    Potensi</span> Siswa Indonesia
            </h2>

            <p class="text-slate-500 mt-6 text-sm sm:text-base md:text-lg max-w-2xl leading-relaxed font-medium">
                EduLearn hadir mendukung ekosistem pendidikan inklusif dan merdeka dengan metode belajar adaptif
                (Visual & Teks) yang dirancang khusus untuk jenjang SMP.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row gap-3 w-full sm:w-auto px-4 sm:px-0">
                <a href="{{ route('teacher.login') }}"
                    class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition text-xs sm:text-sm tracking-wide text-center">
                    Masuk Sebagai Guru
                </a>
                <a href="#fitur"
                    class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold px-8 py-3 rounded-xl shadow-2xs transition text-xs sm:text-sm tracking-wide text-center">
                    Pelajari Fitur
                </a>
            </div>
        </div>
    </header>

    <section id="fitur" class="bg-white border-y border-slate-200/60 py-20 px-6 scroll-mt-14">
        <div class="max-w-5xl mx-auto space-y-12">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Fitur Unggulan</h3>
                <p class="text-xs text-slate-400 font-medium">Teknologi modern yang dirancang presisi untuk mempermudah
                    pengajaran para guru.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="p-6 bg-slate-50/60 border border-slate-200/60 rounded-2xl hover:-translate-y-1.5 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div
                            class="w-10 w-10 h-10 bg-teal-50 border border-teal-100 rounded-xl flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.813 15.904 9 21l8.982-8.982M18 13.653V7.653c0-.426-.23-.819-.598-1.017L10.913 2.8c-.563-.304-1.243-.304-1.807 0L2.598 6.636C2.23 6.834 2 7.227 2 7.652v6c0 .426.23.819.598 1.017l6.508 3.51a1.871 1.871 0 0 0 1.807 0l1.243-.67" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[10px] text-slate-400 font-bold tracking-wider uppercase font-mono">Modul
                                Fleksibel</div>
                            <h4 class="font-bold text-slate-800 text-base">01. Berdiferensiasi</h4>
                        </div>
                        <p class="text-slate-500 text-xs leading-relaxed font-medium">Materi multi-format (Video, Teks)
                            memudahkan siswa memilih gaya belajar terbaik mereka secara inklusif.</p>
                    </div>
                </div>

                <div
                    class="p-6 bg-slate-50/60 border border-slate-200/60 rounded-2xl hover:-translate-y-1.5 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div
                            class="w-10 w-10 h-10 bg-teal-50 border border-teal-100 rounded-xl flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 3v16.5M21 19.5H3.75M21 5.75l-4.323 4.323a1.75 1.75 0 0 1-2.475 0L12 7.75l-4.33 4.33" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[10px] text-slate-400 font-bold tracking-wider uppercase font-mono">Live
                                Monitoring</div>
                            <h4 class="font-bold text-slate-800 text-base">02. Real-Time Tracking</h4>
                        </div>
                        <p class="text-slate-500 text-xs leading-relaxed font-medium">Guru dapat memantau progres
                            tontonan materi pembelajaran dan hasil skor kuis siswa secara instan lewat dashboard.</p>
                    </div>
                </div>

                <div
                    class="p-6 bg-slate-50/60 border border-slate-200/60 rounded-2xl hover:-translate-y-1.5 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div
                            class="w-10 w-10 h-10 bg-teal-50 border border-teal-100 rounded-xl flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5M5.25 4.5V2.25m9 2.25V2.25M16.5 13.5H21v5.25a2.25 2.25 0 0 1-2.25 2.25H4.5A2.25 2.25 0 0 1 2.25 18V4.5m18 9V9a2.25 2.25 0 0 0-2.25-2.25H4.5A2.25 2.25 0 0 0 2.25 9v4.5m18 0h-18" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[10px] text-slate-400 font-bold tracking-wider uppercase font-mono">
                                Automated Payment</div>
                            <h4 class="font-bold text-slate-800 text-base">03. Integrasi Dompet X</h4>
                        </div>
                        <p class="text-slate-500 text-xs leading-relaxed font-medium">Akses ke kelas premium terjaga
                            aman dan tervalidasi dengan sistem konfirmasi pembayaran virtual account otomatis.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="alur" class="bg-slate-50 py-20 px-6 scroll-mt-14">
        <div class="max-w-4xl mx-auto space-y-12">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Alur Kerja Platform</h3>
                <p class="text-xs text-slate-400 font-medium">Langkah mudah pengoperasian kurikulum digital terpadu
                    EduLearn.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                <div class="p-5 bg-white border border-slate-200/60 rounded-xl space-y-2 relative shadow-2xs">
                    <span
                        class="w-5 h-5 rounded-full bg-slate-900 text-white font-mono font-bold text-[10px] flex items-center justify-center absolute -top-2.5 -left-2">1</span>
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Penyusunan Materi</h5>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-medium">Guru mengunggah video ajar
                        Visual dan teks rangkuman di dashboard materi.</p>
                </div>

                <div class="p-5 bg-white border border-slate-200/60 rounded-xl space-y-2 relative shadow-2xs">
                    <span
                        class="w-5 h-5 rounded-full bg-slate-900 text-white font-mono font-bold text-[10px] flex items-center justify-center absolute -top-2.5 -left-2">2</span>
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Pembuatan Evaluasi</h5>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-medium">Guru membuat paket butir soal kuis
                        pilihan ganda terstruktur per tingkatan kelas.</p>
                </div>

                <div class="p-5 bg-white border border-slate-200/60 rounded-xl space-y-2 relative shadow-2xs">
                    <span
                        class="w-5 h-5 rounded-full bg-teal-600 text-white font-mono font-bold text-[10px] flex items-center justify-center absolute -top-2.5 -left-2">&checkmark;</span>
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Pantau Progres</h5>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-medium">Sistem otomatis merekam riwayat
                        tontonan video materi serta hasil nilai kuis siswa.</p>
                </div>
            </div>
        </div>
    </section>
    <footer class="bg-white border-t border-slate-200/60 py-10 text-center text-slate-400 text-xs font-medium">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 px-6">
            <div class="font-mono text-[11px] text-slate-400">
                &copy; 2026 <a href="{{ route('admin.login') }}"
                    class="text-slate-700 hover:text-teal-600 font-bold transition">EduLearn</a>. Mewujudkan Ekosistem
                Pendidikan Merdeka.
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <span class="text-slate-400">Jenjang SMP Terintegrasi</span>
            </div>
        </div>
    </footer>

</body>

</html>
