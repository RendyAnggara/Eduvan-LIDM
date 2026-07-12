<?php

namespace App\Http\Controllers\Web\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Mail\StudentRegisteredMail;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class TeacherController extends Controller
{
    public function storeStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'students' => 'required|array|min:1',
            'students.*.name' => 'required|string|max:255',
            'students.*.email' => 'required|email|unique:users,email',
            'students.*.nisn_or_nip' => 'required|string|unique:users,nisn_or_nip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, ada email atau NISN yang sudah terdaftar atau format salah.',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdCount = 0;
        $studentData = $request->input('students');
        $schoolId = Auth::user()->school_id;

        try {
            foreach ($studentData as $data) {
                $newUser = new User();
                $newUser->name = $data['name'];
                $newUser->email = $data['email'];
                $newUser->role = 'student';
                $newUser->nisn_or_nip = $data['nisn_or_nip'];
                $newUser->school_id = $schoolId;
                $newUser->password = Hash::make('edulearn2026');
                $newUser->email_verified_at = now();
                $newUser->save();

                $createdCount++;
            }

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil mendaftarkan sebanyak {$createdCount} siswa SMP ke Edulearn!",
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data massal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexDashboard(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $search = $request->input('search');
        $classFilter = $request->input('class_filter');

        $studentsQuery = User::where('role', 'student');

        if (is_null($schoolId)) {
            $studentsQuery->whereNull('school_id');
        } else {
            $studentsQuery->where('school_id', $schoolId);
        }

        if (!empty($search)) {
            $studentsQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('nisn_or_nip', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($classFilter)) {
            $studentsQuery->where('class', $classFilter);
        }

        $students = $studentsQuery->latest()->paginate(10, ['*'], 'students_page')->withQueryString();

        $totalStudents = is_null($schoolId)
            ? User::where('role', 'student')->whereNull('school_id')->count()
            : User::where('role', 'student')->where('school_id', $schoolId)->count();
        $baseLeaderboard = User::where('role', 'student')
            ->where('school_id', $schoolId)
            ->withMax('quizResults as highest_score', 'score');

        $leaderboard7 = (clone $baseLeaderboard)->where('class', 'Kelas 7')->orderBy('highest_score', 'desc')->take(5)->get();
        $leaderboard8 = (clone $baseLeaderboard)->where('class', 'Kelas 8')->orderBy('highest_score', 'desc')->take(5)->get();
        $leaderboard9 = (clone $baseLeaderboard)->where('class', 'Kelas 9')->orderBy('highest_score', 'desc')->take(5)->get();

        $videoActivities = \App\Models\Progress::whereHas('user', function($q) use ($schoolId) {
                                $q->where('role', 'student')->where('school_id', $schoolId);
                            })->with('user')->latest()->take(3)->get()->map(function($act) {
                                return [
                                    'name' => $act->user->name,
                                    'type' => 'video',
                                    'message' => "sedang mengakses materi dengan progres {$act->progress_percentage}%",
                                    'time' => $act->updated_at
                                ];
                            });

        $quizActivities = \App\Models\QuizResult::whereHas('user', function($q) use ($schoolId) {
                                $q->where('role', 'student')->where('school_id', $schoolId);
                            })->with('user')->latest()->take(3)->get()->map(function($act) {
                                return [
                                    'name' => $act->user->name,
                                    'type' => 'quiz',
                                    'message' => "menyelesaikan kuis dengan skor {$act->score} (Status: {$act->status})",
                                    'time' => $act->created_at
                                ];
                            });

        $recentActivities = collect()->merge($videoActivities)->merge($quizActivities)->sortByDesc('time')->take(5);

        return view('teacher.dashboard', compact(
            'students', 'totalStudents', 'recentActivities',
            'leaderboard7', 'leaderboard8', 'leaderboard9'
        ));
    }

    public function indexStudents(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $search = $request->input('search');
        $monitorSearch = $request->input('monitor_search');
        $monitorClass = $request->input('monitor_class');

        $baseCount = User::where('role', 'student')->where('school_id', $schoolId);
        $countClass7 = (clone $baseCount)->where('class', 'Kelas 7')->count();
        $countClass8 = (clone $baseCount)->where('class', 'Kelas 8')->count();
        $countClass9 = (clone $baseCount)->where('class', 'Kelas 9')->count();

        $studentsQuery = User::where('role', 'student')->where('school_id', $schoolId);
        if (!empty($search)) {
            $studentsQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        $students = $studentsQuery->latest()->paginate(5, ['*'], 'students_page')->withQueryString();

        $monitoringQuery = User::where('role', 'student')->where('school_id', $schoolId)->with(['quizResults']);

        if (!empty($monitorSearch)) {
            $monitoringQuery->where(function($q) use ($monitorSearch) {
                $q->where('name', 'LIKE', "%{$monitorSearch}%")->orWhere('email', 'LIKE', "%{$monitorSearch}%");
            });
        }

        if (!empty($monitorClass)) {
            $monitoringQuery->where('class', $monitorClass);
        }

        $monitoringStudents = $monitoringQuery->latest()->paginate(5, ['*'], 'monitor_page')->withQueryString();

        $monitoringStudents->getCollection()->transform(function($user) {
            $avgProgress = \App\Models\Progress::where('user_id', $user->id)->avg('progress_percentage') ?? 0;
            $avgQuizScore = $user->quizResults->avg('score') ?? 0;
            $user->average_progress = round($avgProgress, 1);
            $user->average_quiz = round($avgQuizScore, 1);
            return $user;
        });

        return view('teacher.students.index', compact(
            'students', 'countClass7', 'countClass8', 'countClass9', 'monitoringStudents'
        ));
    }

    public function storeStudentByTeacher(Request $request)
    {
        $request->validate([
            'nisn_or_nip' => ['required', 'string', 'max:50', 'unique:users,nisn_or_nip'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $school = Auth::user()->school;
        $schoolName = $school ? $school->name : 'Sekolah Terdaftar';

        User::create([
            'nisn_or_nip' => $request->nisn_or_nip,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school_id' => Auth::user()->school_id,
            'class' => $request->class,
            'email_verified_at' => now(),
        ]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = env('MAIL_PORT', 465);
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'EduLearn'));

            $mail->addAddress($request->email, $request->name);
            $mail->isHTML(true);
            $mail->Subject = 'Akun Belajar Edulearn Anda Telah Aktif!';

            $mail->Body    = "
                <div style='font-family: sans-serif; background-color: #f1f5f9; padding: 20px; color: #334155;'>
                    <div style='max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 16px; border: 1px solid #e2e8f0;'>
                        <h2 style='color: #0d9488; margin-bottom: 5px;'>" . env('MAIL_FROM_NAME', 'EduLearn') . "</h2>
                        <p style='font-size: 12px; color: #94a3b8; margin-top: 0;'>Pendidikan Inklusif Berdiferensiasi</p>
                        <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>

                        <p>Halo <strong>{$request->name}</strong>,</p>
                        <p>Selamat! Akun Anda telah didaftarkan untuk sekolah <strong>{$schoolName}</strong>. Gunakan detail akun di bawah ini untuk masuk ke aplikasi Edulearn:</p>

                        <div style='background-color: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #cbd5e1; margin: 20px 0;'>
                            <p style='margin: 5px 0;'><strong>Email:</strong> {$request->email}</p>
                            <p style='margin: 5px 0;'><strong>Password:</strong> {$request->password}</p>
                        </div>
                        <p style='margin-top: 30px;'>Selamat Belajar,<br><strong>Tim Informasi Edulearn</strong></p>
                    </div>
                </div>
            ";

            $mail->send();

        } catch (\Exception $e) {
            \Log::error("PHPMailer Error: " . $mail->ErrorInfo);
        }

        return redirect()->route('teacher.students.index')->with('success', 'Siswa baru berhasil didaftarkan and detail akun telah dikirim ke email siswa!');
    }

    public function updateStudentByTeacher(Request $request, $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        $request->validate([
            'nisn_or_nip' => ['required', 'string', 'max:50', 'unique:users,nisn_or_nip,' . $id],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'class' => ['required', 'string', 'in:Kelas 7,Kelas 8,Kelas 9'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $plainPassword = $request->password;

        $data = [
            'nisn_or_nip' => $request->nisn_or_nip,
            'name' => $request->name,
            'email' => $request->email,
            'class' => $request->class,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($plainPassword);
        }

        $student->update($data);

        if ($request->has('send_to_email') && $request->send_to_email == '1') {

            if (!$request->filled('password')) {
                return redirect()->route('teacher.students.index')->with('error', 'Gagal kirim email! Isi kolom password terlebih dahulu sebagai kredensial masuk siswa.');
            }

            $school = Auth::user()->school;
            $schoolName = $school ? $school->name : 'Sekolah Terdaftar';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
                $mail->SMTPAuth   = true;
                $mail->Username   = env('MAIL_USERNAME');
                $mail->Password   = env('MAIL_PASSWORD');
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = env('MAIL_PORT', 465);
                $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'EduLearn'));
                $mail->addAddress($request->email, $request->name);
                $mail->isHTML(true);
                $mail->Subject = '[PERBARUAN AKUN] Detail Informasi Akun Belajar Edulearn Anda';

                $mail->Body    = "
                    <div style='font-family: sans-serif; background-color: #f1f5f9; padding: 20px; color: #334155;'>
                        <div style='max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 16px; border: 1px solid #e2e8f0;'>
                            <h2 style='color: #0d9488; margin-bottom: 5px;'>" . env('MAIL_FROM_NAME', 'EduLearn') . "</h2>
                            <p style='font-size: 12px; color: #94a3b8; margin-top: 0;'>Pendidikan Inklusif Berdiferensiasi</p>
                            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>

                            <p>Halo <strong>{$request->name}</strong>,</p>
                            <p>Guru Anda baru saja memperbarui detail informasi akun belajar Edulearn Anda untuk <strong>{$request->class}</strong> di <strong>{$schoolName}</strong>:</p>

                            <div style='background-color: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #cbd5e1; margin: 20px 0;'>
                                <p style='margin: 5px 0;'><strong>Email Baru:</strong> {$request->email}</p>
                                <p style='margin: 5px 0;'><strong>Password Masuk:</strong> {$plainPassword}</p>
                            </div>

                            <p style='margin-top: 30px;'>Selamat Belajar,<br><strong>Tim Informasi Edulearn</strong></p>
                        </div>
                    </div>
                ";

                $mail->send();
                return redirect()->route('teacher.students.index')->with('success', 'Data berhasil diperbarui dan kredensial akun langsung terkirim ke email baru siswa!');

            } catch (\Exception $e) {
                \Log::error("PHPMailer Update & Resend Error: " . $mail->ErrorInfo);
                return redirect()->route('teacher.students.index')->with('success', 'Data profil diubah di DB, namun email notifikasi gagal terkirim: ' . $mail->ErrorInfo);
            }
        }

        return redirect()->route('teacher.students.index')->with('success', 'Perubahan informasi siswa berhasil disimpan!');
    }

    public function downloadExcelTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_siswa_edulearn.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['nisn', 'nama', 'email', 'kelas', 'password']);

            fputcsv($file, ['0081234567', 'Ahmad Subarjo', 'ahmad.subarjo@sekolah.sch.id', 'Kelas 7', 'Ahmad123']);
            fputcsv($file, ['0098765432', 'Siti Aminah', 'siti.aminah@sekolah.sch.id', 'Kelas 8', 'Siti999']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importStudentsExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StudentsImport, $request->file('excel_file'));

            return redirect()->route('teacher.students.index')->with('success', 'Import massal siswa selesai! Seluruh data masuk ke DB dan notifikasi email kredensial berhasil diproses.');
        } catch (\Exception $e) {
            \Log::error("Import Excel Error: " . $e->getMessage());
            return redirect()->route('teacher.students.index')->with('error', 'Gagal memproses file Excel. Pastikan format kolom sesuai dengan template.');
        }
    }

    public function exportRaporPdf($id)
    {
        $student = User::where('role', 'student')->with(['quizResults.course'])->findOrFail($id);
        $schoolName = Auth::user()->school ? Auth::user()->school->name : 'SMP Negeri 2 Karawang Barat';
        $teacherName = Auth::user()->name;
        $raporData = $student->quizResults->groupBy('course_id')->map(function($results) {
            $firstResult = $results->first();
            $totalScore = $results->sum('score');
            $avgScore = $results->avg('score') ?? 0;
            $countBab = $results->count();

            if($avgScore >= 85) { $grade = 'A'; }
            elseif($avgScore >= 75) { $grade = 'B'; }
            elseif($avgScore >= 60) { $grade = 'C'; }
            else { $grade = 'E'; }

            return [
                'mapel_name' => $firstResult->course ? $firstResult->course->title : 'Mata Pelajaran Umum',
                'total_score' => $totalScore,
                'avg_score' => round($avgScore, 2),
                'count_bab' => $countBab,
                'grade' => $grade
            ];
        });

        $totalBabSelesai = $student->quizResults->count();
        $rataRataKelulusan = $student->quizResults->avg('score') ?? 0;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('teacher.students.rapor_pdf', compact(
            'student', 'schoolName', 'teacherName', 'raporData', 'totalBabSelesai', 'rataRataKelulusan'
        ))->setPaper('a4', 'portrait');


        $fileName = 'Rapor_' . \Illuminate\Support\Str::slug($student->name) . '_' . $student->nisn_or_nip . '.pdf';

        return $pdf->download($fileName);
    }

    public function showStudentProgress($id)
    {
        $student = \App\Models\User::where('role', 'student')->findOrFail($id);
        $gradeLevel = filter_var($student->class, FILTER_SANITIZE_NUMBER_INT);
        $courses = \App\Models\Course::where('course_type', 'school')
            ->where('grade_level', $gradeLevel)
            ->with(['chapters'])
            ->get()
            ->map(function($course) use ($student) {

                $courseProgress = \App\Models\Progress::where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->avg('progress_percentage') ?? 0;

                $course->average_progress = round($courseProgress, 1);
                return $course;
            });

        return view('teacher.students.show_progress', compact('student', 'courses'));
    }

    public function showStudentQuizzes($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        $gradeLevel = filter_var($student->class, FILTER_SANITIZE_NUMBER_INT);

        $allQuizzes = \App\Models\Quiz::whereHas('course', function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel);
            })
            ->with('course')
            ->get()
            ->map(function($quiz) use ($student) {
                $result = \App\Models\QuizResult::where('user_id', $student->id)
                    ->where('course_id', $quiz->course_id)
                    ->first();

                $now = \Carbon\Carbon::now();
                $workDuration = '-';
                $quizResultId = null;

                if ($result) {
                    $status = 'Sudah Mengerjakan';
                    $score = $result->score;
                    $quizResultId = $result->id;

                    if (isset($result->created_at) && isset($result->updated_at)) {
                        $start = \Carbon\Carbon::parse($result->created_at);
                        $end = \Carbon\Carbon::parse($result->updated_at);
                        $diff = $start->diffInMinutes($end);
                        $workDuration = $diff > 0 ? $diff . ' Menit' : '1 Menit';
                    }
                } else {
                    if ($quiz->end_time && $now->greaterThan($quiz->end_time)) {
                        $status = 'Siswa ini Tidak mengerjakan';
                    } else {
                        $status = 'Belum Mengerjakan';
                    }
                    $score = '-';
                }

                return [
                    'id'            => $quiz->id,
                    'quiz_result_id'=> $quizResultId,
                    'quiz_title'    => $quiz->title,
                    'mapel_name'    => $quiz->course ? $quiz->course->title : 'Umum',
                    'time_limit'    => $quiz->time_limit ? $quiz->time_limit . ' Menit' : 'Tidak Ada Batas',
                    'deadline'      => $quiz->end_time ? $quiz->end_time->translatedFormat('d F Y, H:i') . ' WIB' : 'Tidak Ada Batas',
                    'status'        => $status,
                    'score'         => $score,
                    'duration'      => $workDuration
                ];
            });

        return view('teacher.students.show_quizzes', compact('student', 'allQuizzes'));
    }

    public function reviewStudentAnswers($student_id, $quiz_result_id)
    {
        $student = User::where('role', 'student')->findOrFail($student_id);
        $quizResult = \App\Models\QuizResult::with(['course'])->findOrFail($quiz_result_id);
        $studentAnswers = collect([]);

        return view('teacher.students.review_quiz', compact('student', 'quizResult', 'studentAnswers'));
    }

    public function destroyStudent($id)
    {
        $student = \App\Models\User::findOrFail($id);

        $student->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus secara permanen!');
    }
}
