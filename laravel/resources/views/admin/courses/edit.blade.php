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
                        Edit Kursus
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight pt-1">
                    Edit Kursus: {{ $course->title }}
                </h1>
                <p class="text-xs text-gray-500">Perbarui data materi, kategori, harga, atau gambar cover kursus ini.</p>
            </div>
        </div>

        @if ($errors->any())
            <div
                class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold space-y-1 shadow-sm">
                <div class="flex items-center gap-2 text-rose-700">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span>Terjadi kesalahan input:</span>
                </div>
                <ul class="list-disc pl-5 font-medium space-y-0.5 text-rose-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-6 md:p-8">
            <form id="editCourseForm" action="{{ route('admin.courses.update', $course->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- JUDUL KURSUS -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Judul Kursus <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $course->title) }}" required
                        class="w-full px-4 py-3 border border-gray-200 bg-white rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition placeholder:text-gray-400 shadow-sm"
                        placeholder="Contoh: Pemrograman Web Lanjutan dengan Laravel & Tailwind">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- KATEGORI -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Kategori Kursus
                            <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select name="category" required
                                class="w-full px-4 py-3 border border-gray-200 bg-white rounded-xl text-sm font-bold text-gray-700 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition appearance-none cursor-pointer shadow-sm">
                                <option value="Computer Science"
                                    {{ old('category', $course->category) == 'Computer Science' ? 'selected' : '' }}>
                                    Computer Science</option>
                                <option value="Microsoft Office"
                                    {{ old('category', $course->category) == 'Microsoft Office' ? 'selected' : '' }}>
                                    Microsoft Office</option>
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
                            <span class="absolute left-4 top-3.5 text-xs font-black text-gray-400">Rp</span>
                            <input type="number" name="price" value="{{ old('price', $course->price) }}" required
                                min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 bg-white rounded-xl text-sm font-bold text-gray-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition placeholder:text-gray-400 shadow-sm"
                                placeholder="150000">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Deskripsi Lengkap
                        <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="4" required
                        class="w-full px-4 py-3 border border-gray-200 bg-white rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition placeholder:text-gray-400 leading-relaxed shadow-sm"
                        placeholder="Jelaskan secara ringkas materi apa saja yang akan dipelajari student dalam kursus ini...">{{ old('description', $course->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Ganti Cover Sampul
                        (Opsional)</label>
                    <div class="relative border-2 border-dashed border-gray-200 hover:border-indigo-400 rounded-2xl p-4 sm:p-6 bg-gray-50/50 hover:bg-indigo-50/20 transition-all text-center cursor-pointer group"
                        onclick="document.getElementById('coverInput').click()">

                        <input type="file" name="image" id="coverInput" accept="image/jpeg,image/png,image/jpg"
                            class="hidden" onchange="previewImage(event)">

                        <div id="uploadPlaceholder" class="{{ $course->image ? 'hidden' : '' }} space-y-2">
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

                        <div id="imagePreviewContainer"
                            class="{{ $course->image ? 'flex' : 'hidden' }} flex-col items-center justify-center gap-3">
                            <img id="imagePreview"
                                src="{{ $course->image ? (str_contains($course->image, 'data:image') ? $course->image : asset('storage/' . $course->image)) : '#' }}"
                                alt="Preview Cover"
                                class="max-h-40 rounded-xl border border-gray-200 shadow-md object-cover">
                            <span class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                <i class="fas fa-sync-alt text-[10px]"></i> Klik untuk Ganti Gambar Cover
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('admin.courses.index') }}"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold text-center transition shadow-sm">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-md shadow-indigo-100 flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer">
                        <i class="fas fa-save text-xs"></i> Update Kursus
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
