<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\QuizProgressController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Web\Teacher\TeacherController;
use App\Http\Controllers\Web\Teacher\TeacherAuthController;
use App\Http\Controllers\Web\Teacher\MaterialController;
use App\Http\Controllers\Web\Teacher\QuizController;


Route::get('/', function () {
    return redirect()->route('landing.page');
});

Route::get('/landing', function () {
    return view('landing');
})->name('landing.page');

// Admin Auth
Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Teacher Auth
Route::get('teacher/login', [TeacherAuthController::class, 'showLoginForm'])->name('teacher.login');
Route::post('teacher/login', [TeacherAuthController::class, 'login']);
Route::post('teacher/logout', [TeacherAuthController::class, 'logout'])->name('teacher.logout');

Route::get('uploads/proofs/{filename}', function ($filename) {
    $cleanFilename = str_replace('uploads/proofs/', '', $filename);
    $path = public_path('uploads/proofs/' . $cleanFilename);

    if (!File::exists($path)) {
        abort(404);
    }
    if (ob_get_level()) {
        ob_end_clean();
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->where('filename', '.*');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Manajemen Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('admin.courses.show');
    Route::post('/courses/{id}/content', [CourseController::class, 'storeContent'])->name('admin.courses.storeContent');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('admin.courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');
    Route::delete('/courses/content/{content_id}', [CourseController::class, 'destroyContent'])->name('admin.courses.destroyContent');

    // Manajemen Students
    Route::get('/students', [StudentController::class, 'index'])->name('admin.students.index');
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('admin.students.show');
    Route::get('/api/students/{id}', [StudentController::class, 'apiShow'])->name('students.apiShow');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('admin.students.destroy');
    Route::post('/students', [StudentController::class, 'store'])->name('admin.students.store');

    // Manajemen Pembelian / Transaksi
    Route::get('/pembelian', [TransactionController::class, 'index'])->name('admin.pembelian.index');
    Route::get('/pembelian/download/{id}', [TransactionController::class, 'downloadReport'])->name('admin.pembelian.download');
    Route::get('/pembelian/course-pdf/{id}', [TransactionController::class, 'downloadCourseReport'])->name('admin.pembelian.course_pdf');
    Route::get('/pembelian/pdf', [TransactionController::class, 'exportPdf'])->name('admin.pembelian.pdf');
    Route::put('/pembelian/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('admin.pembelian.updateStatus');

    // Manajemen Quiz & Progress
    Route::get('/quiz-progress', [QuizProgressController::class, 'index'])->name('admin.quiz.index');
    Route::get('/quiz-progress/{id}', [QuizProgressController::class, 'show'])->name('admin.quiz.show');
    Route::get('/quiz-progress/{course}/manage', [QuizProgressController::class, 'manage'])->name('admin.quiz.manage');
    Route::post('/quiz-progress/{course}/storeQuiz', [QuizProgressController::class, 'storeQuiz'])->name('admin.quiz.store');
    Route::get('/quiz/{quiz}/edit', [QuizProgressController::class, 'editQuiz'])->name('admin.quiz.edit');
    Route::put('/quiz/{quiz}', [QuizProgressController::class, 'updateQuiz'])->name('admin.quiz.update');
    Route::delete('/quiz/{quiz}', [QuizProgressController::class, 'destroyQuiz'])->name('admin.quiz.destroy');

    // Manajemen Sertifikat dan Pembayaran
    Route::get('/certificates', [CertificateController::class, 'index'])->name('admin.certificates.index');
    Route::post('/certificates/issue/{userId}/{courseId}', [CertificateController::class, 'issue'])->name('admin.certificates.issue');
    Route::get('/certificates/preview/{id}', [CertificateController::class, 'preview'])->name('admin.certificates.preview');
    Route::get('/certificates/download/{id}', [CertificateController::class, 'download'])->name('admin.certificates.download');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/send', [NotificationController::class, 'store'])->name('admin.notifications.send');
});

Route::middleware(['auth', 'role:teacher,admin'])->prefix('teacher')->group(function () {
    // Dashboard Guru
    Route::get('/dashboard', [TeacherController::class, 'indexDashboard'])->name('teacher.dashboard');

    // Manajemen Siswa
    Route::get('/students', [TeacherController::class, 'indexStudents'])->name('teacher.students.index');
    Route::post('/students/store', [TeacherController::class, 'storeStudentByTeacher'])->name('teacher.students.store');
    Route::post('/student/store-massal', [TeacherController::class, 'storeStudent'])->name('teacher.student.store');
    Route::delete('/students/{id}', [TeacherController::class, 'destroyStudent'])->name('teacher.student.destroy');
    Route::put('/students/{id}/update', [TeacherController::class, 'updateStudentByTeacher'])->name('teacher.students.update');
    Route::post('/students/{id}/resend', [TeacherController::class, 'resendStudentAccount'])->name('teacher.students.resend');
    Route::get('/students/{id}/export-rapor', [TeacherController::class, 'exportRaporPdf'])->name('teacher.students.export_rapor');
    Route::get('/students/{id}/progress', [TeacherController::class, 'showStudentProgress'])->name('teacher.students.show_progress');
    Route::get('/students/{id}/quizzes', [TeacherController::class, 'showStudentQuizzes'])->name('teacher.students.show_quizzes');
    Route::get('/teacher/students/{student_id}/quizzes/{quiz_result_id}/review', [TeacherController::class,'reviewStudentAnswers'])->name('teacher.students.review_quiz');

    Route::get('/students/download-template', [TeacherController::class, 'downloadExcelTemplate'])->name('teacher.students.download_template');
    Route::post('/students/import-excel', [TeacherController::class, 'importStudentsExcel'])->name('teacher.students.import_excel');

    // Simulasi Pembelian
    Route::post('/checkout-simulated', [OrderController::class, 'checkoutSimulated'])->name('teacher.checkout.simulated');

    // materi
    Route::get('/material', [MaterialController::class, 'index'])->name('teacher.material.index');
    Route::post('/material/chapter', [MaterialController::class, 'storeChapter'])->name('teacher.material.store_chapter');
    Route::post('/material/course', [MaterialController::class, 'storeCourse'])->name('teacher.material.store_course');
    Route::get('/material/manage/{id}', [MaterialController::class, 'manage'])->name('teacher.material.manage');
    Route::delete('/material/chapter/{id}', [MaterialController::class, 'destroyChapter'])->name('teacher.material.destroy_chapter');
    Route::post('/material/lesson', [MaterialController::class, 'storeLesson'])->name('teacher.material.store_lesson');
    Route::get('/material/lesson/{id}/edit', [MaterialController::class, 'editContent'])->name('teacher.material.edit_content');
    Route::put('/material/lesson/{id}/update', [MaterialController::class, 'updateContent'])->name('teacher.material.update_content');
    Route::delete('/material/lesson/{id}', [MaterialController::class, 'destroyLesson'])->name('teacher.material.destroy_lesson');
    Route::put('/material/course/{id}', [MaterialController::class, 'updateCourse'])->name('teacher.material.update_course');

    //Quiz
    Route::get('/quiz', [QuizController::class, 'index'])->name('teacher.quiz.index');
    Route::post('/quiz', [QuizController::class, 'store'])->name('teacher.quiz.store');
    Route::delete('/quiz/{id}', [QuizController::class, 'destroy'])->name('teacher.quiz.destroy');
    Route::get('/quiz/{id}/questions', [QuizController::class, 'manageQuestions'])->name('teacher.quiz.questions');
    Route::post('/quiz/{id}/questions', [QuizController::class, 'storeQuestion'])->name('teacher.quiz.store_question');
    Route::delete('/quiz/question/{id}', [QuizController::class, 'destroyQuestion'])->name('teacher.quiz.destroy_question');
});
