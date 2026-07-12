@extends('layouts.teacher')

@section('title', 'Materi Diferensiasi')

@section('content')
    <div class="w-full">
        <div
            class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Materi Pembelajaran Diferensiasi</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kelola ruang lingkup materi pembelajaran inklusif per tingkat kelas.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
                <form action="{{ route('teacher.material.index') }}" method="GET" class="w-full sm:w-auto">
                    <select name="class_level" onchange="this.form.submit()"
                        class="w-full sm:w-auto px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                        <option value="">Semua Tingkatan</option>
                        <option value="7" {{ $selectedClass == '7' ? 'selected' : '' }}>Kelas 7</option>
                        <option value="8" {{ $selectedClass == '8' ? 'selected' : '' }}>Kelas 8</option>
                        <option value="9" {{ $selectedClass == '9' ? 'selected' : '' }}>Kelas 9</option>
                    </select>
                </form>
                <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
                    <button onclick="toggleModal('modalCourse')"
                        class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 sm:px-4 sm:py-2 rounded-xl text-xs transition shadow-sm border border-slate-950 text-center justify-center">
                        Tambah Mapel
                    </button>

                    <button onclick="toggleModal('modalChapter')"
                        class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 sm:px-4 sm:py-2 rounded-xl text-xs transition shadow-sm text-center justify-center">
                        Buat Bab Baru
                    </button>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-xl shadow-xs">
                &checkmark; {{ session('success') }}
            </div>
        @endif
        <div class="space-y-6">
            @php
                $targetClasses = $selectedClass ? [$selectedClass] : ['7', '8', '9'];
            @endphp

            @foreach ($targetClasses as $class)
                @php
                    $courses = $coursesGrouped->get($class, collect());
                @endphp

                @if ($courses->isNotEmpty() || $selectedClass)
                    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 w-full space-y-5">
                        <div
                            class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500 inline-block shrink-0"></span>
                                    Kurikulum Pembelajaran - Kelas {{ $class }}
                                </h3>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Ruang lingkup materi inklusif
                                    khusus untuk tingkatan kelas {{ $class }}.</p>
                            </div>

                            <div class="shrink-0 w-full sm:w-auto">
                                <span
                                    class="px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 text-[10px] font-bold rounded-lg block text-center sm:inline-block">
                                    {{ $courses->count() }} Mapel Aktif
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4sm:gap-5">
                            @forelse($courses as $course)
                                <div
                                    class="bg-slate-50/50 border border-slate-200/60 rounded-xl p-4 sm:p-5 hover:bg-white hover:border-slate-200 hover:shadow-xs transition duration-200 flex flex-col justify-between relative group">
                                    <div>
                                        <div class="flex items-center justify-between mb-3 gap-2">
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <span
                                                    class="px-2 py-0.5 bg-white text-teal-700 border border-slate-200 text-[10px] font-black rounded-md uppercase shrink-0">
                                                    K-{{ $course->grade_level }}
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-bold truncate">Kurikulum
                                                    Inklusif</span>
                                            </div>

                                            <button type="button"
                                                onclick="openEditCourseModal('{{ $course->id }}', '{{ addslashes($course->title) }}', '{{ $course->grade_level }}', '{{ addslashes($course->description) }}')"
                                                class="text-[11px] font-bold text-teal-600 hover:text-teal-700 bg-white border border-slate-200 px-2 py-0.5 rounded shadow-2xs transition shrink-0">
                                                Edit
                                            </button>
                                        </div>

                                        <h3 class="font-bold text-slate-800 text-base leading-snug mb-1 tracking-tight">
                                            {{ $course->title }}</h3>
                                        <p class="text-xs text-slate-400 line-clamp-2 mb-4 font-medium">
                                            {{ $course->description ?? 'Belum ada deskripsi mata pelajaran.' }}
                                        </p>

                                        <!-- Indikator Total Bab -->
                                        <div
                                            class="bg-white border border-slate-200/60 p-3 rounded-xl flex items-center justify-between text-xs text-slate-600 font-bold mb-4">
                                            <span>Total Bab Utama:</span>
                                            <span class="text-slate-900 bg-slate-100 px-2 py-0.5 rounded font-mono">
                                                {{ $course->chapters_count }} Bab
                                            </span>
                                        </div>
                                    </div>

                                    <a href="{{ route('teacher.material.manage', $course->id) }}"
                                        class="w-full text-center py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition border border-slate-950 shadow-xs block tracking-wide">
                                        Kelola Detail Materi
                                    </a>
                                </div>
                            @empty
                                <div
                                    class="col-span-full py-6 text-center text-slate-400 text-xs font-medium bg-slate-50/30 border border-dashed border-slate-200 rounded-xl">
                                    Belum ada mata pelajaran yang terdaftar untuk Kelas {{ $class }}.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($coursesGrouped->isEmpty())
                <div
                    class="bg-white p-12 text-center rounded-2xl border border-slate-100 text-slate-400 text-xs font-medium">
                    Belum ada data mata pelajaran yang terdaftar pada kurikulum saat ini.
                </div>
            @endif
        </div>
        <div id="modalCourse"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center hidden z-50 p-4">
            <div
                class="bg-white rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-100 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-slate-800 text-base">Tambah Mata Pelajaran Baru</h4>
                    <button onclick="toggleModal('modalCourse')"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form action="{{ route('teacher.material.store_course') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Nama Mata Pelajaran</label>
                        <input type="text" name="title" required placeholder="Contoh: Bahasa Indonesia"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Tingkat Kelas</label>
                        <select name="grade_level" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="7">Kelas 7</option>
                            <option value="8">Kelas 8</option>
                            <option value="9">Kelas 9</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Deskripsi Ringkas</label>
                        <textarea name="description" rows="3" placeholder="Deskripsi mengenai mata pelajaran..."
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100 pt-3">
                        <button type="button" onclick="toggleModal('modalCourse')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm">Simpan
                            Mapel</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="modalEditCourse"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center hidden z-50 p-4">
            <div
                class="bg-white rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-100 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-slate-800 text-base">Ubah Data Mata Pelajaran</h4>
                    <button type="button" onclick="toggleModal('modalEditCourse')"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form id="formEditCourse" action="" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Nama Mata Pelajaran</label>
                        <input type="text" id="edit_course_title" name="title" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Tingkat Kelas</label>
                        <select id="edit_course_grade" name="grade_level" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="7">Kelas 7</option>
                            <option value="8">Kelas 8</option>
                            <option value="9">Kelas 9</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Deskripsi Ringkas</label>
                        <textarea id="edit_course_description" name="description" rows="3"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100 pt-3">
                        <button type="button" onclick="toggleModal('modalEditCourse')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="modalChapter"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center hidden z-50 p-4">
            <div
                class="bg-white rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-100 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-slate-800 text-base">Buat Bab Pembelajaran Baru</h4>
                    <button onclick="toggleModal('modalChapter')"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form action="{{ route('teacher.material.store_chapter') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Pilih Mata Pelajaran</label>
                        <select name="course_id" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($allSelectCourses as $cRow)
                                <option value="{{ $cRow->id }}">{{ $cRow->title }} (Kelas {{ $cRow->grade_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Judul Bab Baru</label>
                        <input type="text" name="title" required placeholder="Contoh: Bab 1: Teks Observasi"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100 pt-3">
                        <button type="button" onclick="toggleModal('modalChapter')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm">Simpan
                            Bab</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        function openEditCourseModal(id, title, grade, description) {
            const form = document.getElementById('formEditCourse');
            form.action = `/teacher/material/course/${id}`;
            document.getElementById('edit_course_title').value = title;
            document.getElementById('edit_course_grade').value = grade;
            document.getElementById('edit_course_description').value = description;
            toggleModal('modalEditCourse');
        }
    </script>
@endsection
