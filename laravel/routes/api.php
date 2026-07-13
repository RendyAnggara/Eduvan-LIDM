<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CertificateApiController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\VoucherController;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'EduLearn RESTful API Server is Running Live'
    ]);
});

// Route Publik Akses Umum/Mobile Siswa Sebelum Login
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);
Route::post('/xendit/callback', [EnrollmentController::class, 'handleCallback']);
Route::get('/courses/{course_id}/contents', [ContentController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/update', [AuthController::class, 'updateProfile']); // Hanya untuk update password di Controller baru
    Route::get('/notifications', [NotificationApiController::class, 'getNotifUser']);
    Route::post('/notifications/read/{id}', [NotificationApiController::class, 'markAsRead']);

    Route::middleware('role:student')->group(function () {
        Route::post('/enrollments', [EnrollmentController::class, 'store']);
        Route::get('/enrollments', [EnrollmentController::class, 'index']);

        Route::post('/progress/mark-completed', [ProgressController::class, 'markAsCompleted']);
        Route::get('/courses/{course_id}/progress', [ProgressController::class, 'getProgress']);

        Route::get('/courses/{course_id}/quizzes', [CourseController::class, 'getQuizzesForStudent']);

        Route::post('/progress/submit-quiz', [ProgressController::class, 'submitQuiz']);


        Route::get('/courses/{course_id}/certificate', [EnrollmentController::class, 'getCertificate']);
        Route::get('/my-certificates', [CertificateApiController::class, 'index']);
        Route::get('/certificates/{id}/download', [CertificateApiController::class, 'downloadMobile']);
        Route::post('/courses/{id}/rate', [CourseController::class, 'rate']);

        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
        Route::post('/student/redeem-voucher', [VoucherController::class, 'redeemVoucher']);
    });

    Route::middleware('role:teacher,admin')->group(function () {
        Route::get('/instructor/dashboard', [CourseController::class, 'dashboard']);
        Route::post('/contents', [ContentController::class, 'store']);
        Route::post('/quizzes', [QuizController::class, 'store']);

        Route::post('/students/create', [AuthController::class, 'createStudentByTeacher']);

        Route::get('/courses/{course_id}/students', [EnrollmentController::class, 'getEnrolledStudents']);
        Route::get('/courses/{course_id}/progress/{user_id}', [ProgressController::class, 'getStudentProgress']);
    });
});
