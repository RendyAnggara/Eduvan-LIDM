@extends('layouts.teacher')

@section('title', 'Kelola Soal Kuis')

@section('content')
    <div class="w-full">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4 w-full">
                <a href="{{ route('teacher.quiz.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-white border border-slate-200/70 rounded-full text-[11px] font-bold text-teal-600 hover:bg-slate-50 hover:text-teal-700 shadow-sm transition duration-150">
                    <span class="text-xs">&larr;</span> Kembali ke Pembuatan Quiz
                </a>
                <span class="text-[10px] font-bold text-slate-300 tracking-wider hidden sm:inline font-mono">EduLearn
                    Evaluasi</span>
            </div>
            <div
                class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 w-full flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                        {{ $quiz->course->title }} &nbsp;•&nbsp; {{ $quiz->time_limit }} Menit pengerjaan
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight mt-1">{{ $quiz->title }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Kelola butir soal evaluasi (Pilihan Ganda & Essay) untuk paket
                        kuis ini.
                    </p>
                </div>
                <button onclick="toggleModal('modalAddQuestion')"
                    class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 sm:px-4 sm:py-2 rounded-xl text-xs transition shadow-sm shrink-0 w-full sm:w-auto text-center justify-center tracking-wide">
                    Tambah Soal
                </button>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-xl shadow-2xs">
                &checkmark; {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse($quiz->questions as $index => $question)
                <div
                    class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-4 sm:p-6 space-y-3.5 transition duration-150">

                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs font-mono text-white font-bold shrink-0">
                                {{ $index + 1 }}
                            </span>
                            @if (($question->type ?? 'multiple_choice') === 'essay')
                                <span
                                    class="px-2 py-0.5 bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold rounded-md">
                                    Soal Essay
                                </span>
                            @else
                                <span
                                    class="px-2 py-0.5 bg-teal-50 text-teal-700 border border-teal-200 text-[10px] font-bold rounded-md">
                                    Pilihan Ganda
                                </span>
                            @endif
                        </div>

                        <form action="{{ route('teacher.quiz.destroy_question', $question->id) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs font-bold text-rose-600 hover:text-rose-700 transition px-1 py-0.5">
                                Hapus Soal
                            </button>
                        </form>
                    </div>

                    <div
                        class="text-xs sm:text-sm font-bold text-slate-800 leading-relaxed bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                        {{ $question->question_text }}
                    </div>

                    @if (($question->type ?? 'multiple_choice') === 'multiple_choice')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                            <div
                                class="p-2.5 sm:p-3 border rounded-xl text-xs flex items-center gap-2 font-medium {{ $question->correct_answer === 'A' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-600' }}">
                                <span
                                    class="w-5 h-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold shrink-0 {{ $question->correct_answer === 'A' ? 'bg-emerald-600 text-white font-black' : '' }}">A</span>
                                <span class="leading-normal">{{ $question->option_a }}</span>
                            </div>
                            <div
                                class="p-2.5 sm:p-3 border rounded-xl text-xs flex items-center gap-2 font-medium {{ $question->correct_answer === 'B' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-600' }}">
                                <span
                                    class="w-5 h-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold shrink-0 {{ $question->correct_answer === 'B' ? 'bg-emerald-600 text-white font-black' : '' }}">B</span>
                                <span class="leading-normal">{{ $question->option_b }}</span>
                            </div>
                            <div
                                class="p-2.5 sm:p-3 border rounded-xl text-xs flex items-center gap-2 font-medium {{ $question->correct_answer === 'C' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-600' }}">
                                <span
                                    class="w-5 h-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold shrink-0 {{ $question->correct_answer === 'C' ? 'bg-emerald-600 text-white font-black' : '' }}">C</span>
                                <span class="leading-normal">{{ $question->option_c }}</span>
                            </div>
                            <div
                                class="p-2.5 sm:p-3 border rounded-xl text-xs flex items-center gap-2 font-medium {{ $question->correct_answer === 'D' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-600' }}">
                                <span
                                    class="w-5 h-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold shrink-0 {{ $question->correct_answer === 'D' ? 'bg-emerald-600 text-white font-black' : '' }}">D</span>
                                <span class="leading-normal">{{ $question->option_d }}</span>
                            </div>
                        </div>
                    @else
                        <div
                            class="p-3 bg-purple-50/40 border border-purple-100 rounded-xl text-xs text-purple-800 font-medium italic">
                             Jawaban akan ditulis langsung oleh siswa dalam bentuk uraian / deskripsi bebas.
                        </div>
                    @endif

                </div>
            @empty
                <div
                    class="bg-white p-12 text-center rounded-2xl border border-slate-200 text-slate-400 text-xs font-medium">
                    Belum ada soal yang diinput ke dalam kuis ini. Silakan klik tombol <strong class="text-teal-600">Tambah
                        Soal</strong> di atas.
                </div>
            @endforelse
        </div>

        <div id="modalAddQuestion"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center hidden z-50 p-4">
            <div
                class="bg-white rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-100 w-full max-w-xl max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-slate-800 text-base">Tambah Soal Baru</h4>
                    <button onclick="toggleModal('modalAddQuestion')"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form action="{{ route('teacher.quiz.store_question', $quiz->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Tipe Soal</label>
                        <select name="type" id="questionTypeSelect" onchange="toggleQuestionTypeFields()" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="multiple_choice" selected>Pilihan Ganda (A, B, C, D)</option>
                            <option value="essay">Essay / Uraian</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-600">Teks Pertanyaan</label>
                        <textarea name="question_text" rows="3" required placeholder="Tulis soal di sini..."
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium"></textarea>
                    </div>

                    <div id="multipleChoiceContainer" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-600">Pilihan A</label>
                                <input type="text" name="option_a" id="option_a" placeholder="Jawaban A"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-600">Pilihan B</label>
                                <input type="text" name="option_b" id="option_b" placeholder="Jawaban B"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-600">Pilihan C</label>
                                <input type="text" name="option_c" id="option_c" placeholder="Jawaban C"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-600">Pilihan D</label>
                                <input type="text" name="option_d" id="option_d" placeholder="Jawaban D"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500 font-medium">
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-600">Kunci Jawaban yang Benar</label>
                            <select name="correct_answer" id="correct_answer"
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                                <option value="">-- Pilih Kunci Jawaban --</option>
                                <option value="A">Opsi A</option>
                                <option value="B">Opsi B</option>
                                <option value="C">Opsi C</option>
                                <option value="D">Opsi D</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="toggleModal('modalAddQuestion')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm">Simpan
                            Soal</button>
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

        function toggleQuestionTypeFields() {
            const selectType = document.getElementById('questionTypeSelect').value;
            const container = document.getElementById('multipleChoiceContainer');
            const optionA = document.getElementById('option_a');
            const optionB = document.getElementById('option_b');
            const optionC = document.getElementById('option_c');
            const optionD = document.getElementById('option_d');
            const correctAnswer = document.getElementById('correct_answer');

            if (selectType === 'essay') {
                container.classList.add('hidden');
                optionA.removeAttribute('required');
                optionB.removeAttribute('required');
                optionC.removeAttribute('required');
                optionD.removeAttribute('required');
                correctAnswer.removeAttribute('required');
            } else {
                container.classList.remove('hidden');
                optionA.setAttribute('required', 'required');
                optionB.setAttribute('required', 'required');
                optionC.setAttribute('required', 'required');
                optionD.setAttribute('required', 'required');
                correctAnswer.setAttribute('required', 'required');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleQuestionTypeFields();
        });
    </script>
@endsection
