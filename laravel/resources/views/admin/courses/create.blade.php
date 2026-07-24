@extends('layouts.admin')

@section('content')
    <div class="px-2 sm:px-4 md:px-0 space-y-6 w-full">

        <!-- HEADER UTAMA: RATA POJOK KIRI SEJAJAR DENAH LAYOUT -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}"
                class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center transition shadow-sm shrink-0 active:scale-95"
                title="Kembali ke Daftar Kursus">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Tambah Kursus Baru</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Lengkapi formulir di bawah untuk membuat kursus baru di
                    marketplace.</p>
            </div>
        </div>

        <!-- BANNER INFO (MEMANJANG PROPORSIONAL) -->
        <div class="p-4 bg-indigo-50/70 border border-indigo-100 rounded-2xl flex items-start gap-3 text-indigo-900 text-xs">
            <div
                class="w-7 h-7 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm shadow-indigo-200">
                <i class="fas fa-store text-xs"></i>
            </div>
            <div>
                <span class="font-bold block text-sm text-indigo-950">Publikasi Kursus Marketplace Admin</span>
                <p class="mt-0.5 leading-relaxed text-indigo-700">
                    Kursus yang ditambahkan di halaman ini akan terdaftar sebagai <strong
                        class="font-black text-indigo-900">Kursus Premium Komersial</strong>. Konten ini langsung dapat
                    diakses oleh siswa seluruh sekolah melalui katalog Eduvan Marketplace.
                </p>
            </div>
        </div>

        <!-- FORM TUNGGAL RESPONSIF -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-6 md:p-8">
            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <!-- JUDUL KURSUS -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Judul Kursus <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-3 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-gray-400 placeholder:font-normal"
                        placeholder="Contoh: Pemrograman Web Lanjutan dengan Laravel & Tailwind">
                </div>

                <!-- GRID KATEGORI & HARGA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- KATEGORI -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Kategori Kursus
                            <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select name="category" required
                                class="w-full px-4 py-3 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition appearance-none cursor-pointer">
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="Computer Science"
                                    {{ old('category') == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                <option value="Microsoft Office"
                                    {{ old('category') == 'Microsoft Office' ? 'selected' : '' }}>Microsoft Office</option>
                            </select>
                            <div class="absolute right-4 top-3.5 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- HARGA -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Harga Jual (Rp)
                            <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-xs font-black text-gray-400">Rp</span>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-bold text-gray-900 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-gray-400 placeholder:font-normal"
                                placeholder="150000">
                        </div>
                    </div>
                </div>

                <!-- DESKRIPSI KURSUS -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Deskripsi Lengkap
                        <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="4" required
                        class="w-full px-4 py-3 border border-gray-200 bg-gray-50/50 rounded-xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-gray-400 placeholder:font-normal leading-relaxed"
                        placeholder="Jelaskan secara ringkas materi apa saja yang akan dipelajari student dalam kursus ini...">{{ old('description') }}</textarea>
                </div>

                <!-- UPLOAD COVER KURSUS INTERAKTIF -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Cover Sampul Kursus
                        (Opsional)</label>
                    <div class="relative border-2 border-dashed border-gray-200 hover:border-indigo-400 rounded-2xl p-4 sm:p-6 bg-gray-50/50 hover:bg-indigo-50/20 transition-all text-center cursor-pointer group"
                        onclick="document.getElementById('coverInput').click()">

                        <input type="file" name="image" id="coverInput" accept="image/jpeg,image/png,image/jpg"
                            class="hidden" onchange="previewImage(event)">

                        <div id="uploadPlaceholder" class="space-y-2">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                                <i class="fas fa-cloud-upload-alt text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-700"><span class="text-indigo-600 underline">Klik
                                        untuk mengunggah</span> atau seret file ke sini</p>
                                <p class="text-[10px] text-gray-400 font-medium mt-0.5">Format file yang didukung: PNG, JPG,
                                    JPEG (Maks. 2MB)</p>
                            </div>
                        </div>

                        <!-- CONTAINER PREVIEW GAMBAR -->
                        <div id="imagePreviewContainer" class="hidden flex-col items-center justify-center gap-3">
                            <img id="imagePreview" src="#" alt="Preview Cover"
                                class="max-h-40 rounded-xl border border-gray-200 shadow-md object-cover">
                            <span class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                <i class="fas fa-sync-alt text-[10px]"></i> Ganti Gambar Cover
                            </span>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL AKSI RESPONSIF -->
                <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('admin.courses.index') }}"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold text-center transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-lg shadow-indigo-600/10 flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer">
                        <i class="fas fa-save text-xs"></i> Simpan & Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const placeholder = document.getElementById('uploadPlaceholder');
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    placeholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                    previewContainer.classList.add('flex');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
