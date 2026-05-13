@extends('layouts.admin')

@section('content')
    <div class="container mx-auto py-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Monitoring Sertifikat</h2>
            <p class="text-sm text-gray-500">Daftar student yang telah menyelesaikan kursus dan siap menerima sertifikat.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-black">
                    <tr>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Kursus</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pendingCertificates as $progress)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-700">{{ $progress->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $progress->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $progress->course->title }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- Cek apakah sudah punya nomor sertifikat di tabel certificates --}}
                                @php
                                    $certificate = \App\Models\Certificate::where('user_id', $progress->user_id)
                                        ->where('course_id', $progress->course_id)
                                        ->first();
                                    $isIssued = !empty($certificate);
                                @endphp

                                @if ($isIssued)
                                    <div class="flex flex-col items-center space-y-2">
                                        <span class="text-green-600 font-bold text-[10px] uppercase">
                                            <i class="fas fa-check-double mr-1"></i> Terbit
                                        </span>
                                        <a href="{{ route('admin.certificates.preview', $certificate->id) }}"
                                            class="text-indigo-600 hover:text-indigo-800 font-bold text-[10px] uppercase flex items-center justify-center bg-indigo-50 px-3 py-1 rounded-full transition">
                                            <i class="fas fa-eye mr-1"></i> Preview Sertifikat
                                        </a>
                                    </div>
                                @else
                                    <form
                                        action="{{ route('admin.certificates.issue', [$progress->user_id, $progress->course_id]) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-blue-700 transition shadow-sm">
                                            Validasi Sertifikat
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">
                                Belum ada student yang menyelesaikan kursus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
