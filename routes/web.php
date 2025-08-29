<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\PreSignupController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RequestSigninController;
use App\Http\Controllers\StudentController;

// Root route - redirect to login
Route::get('/', function () {
    return view('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/verify-student-id', [LoginController::class, 'verifyStudentId'])->name('verify.student.id');
Route::get('/clear-student-verification', [LoginController::class, 'clearStudentVerification'])->name('clear.student.verification');

// Pre-signup Routes
Route::get('/pre-signup', [PreSignupController::class, 'showForm'])->name('pre_signup');
Route::post('/pre-signup/send-verification', [PreSignupController::class, 'sendVerification'])->name('pre_signup.send_verification');
Route::post('/pre-signup/verify-otp', [PreSignupController::class, 'verifyOtp'])->name('pre_signup.verify_otp');

// Signup Routes
Route::get('/signup', [SignupController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [SignupController::class, 'signup'])->name('signup.submit');

// Password Reset Routes
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password/send-verification', [PasswordResetController::class, 'sendVerification'])->name('password.reset.send_verification');
Route::post('/reset-password/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.reset.verify_otp');
Route::post('/reset-password/update', [PasswordResetController::class, 'update'])->name('password.reset.update');
// Alias to support Laravel's default forgot-password link names used in Blade
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');

// Dashboard Routes (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Evaluation Routes
    Route::get('/evaluations', [EvaluationController::class, 'showForm'])->name('evaluations.form');
    Route::get('/evaluation-form', [EvaluationController::class, 'showEvaluationForm'])->name('evaluations.show');
    Route::post('/evaluations/submit', [EvaluationController::class, 'submit'])->name('evaluations.submit');
    Route::post('/evaluations/save-and-clear-all', [EvaluationController::class, 'saveAndClearAllResults'])->name('evaluations.saveAndClearAll');

    // Academic Year Routes
    Route::get('/dashboard/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/dashboard/academic-years/store', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::post('/dashboard/academic-years/{id}/update', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::post('/dashboard/academic-years/{id}/toggle', [AcademicYearController::class, 'toggleActive'])->name('academic-years.toggle');
    Route::post('/dashboard/academic-years/{id}/delete', [AcademicYearController::class, 'destroy'])->name('academic-years.delete');
    Route::get('/academic-year/{id}/manage', [AcademicYearController::class, 'manage'])->name('academic-years.manage');

    // Academic Year AJAX for staff comments/evaluations
    Route::post('/academic-year/staff-comments', [AcademicYearController::class, 'getStaffCommentsForYear'])->name('staff.comments');
    Route::get('/academic-year/{staffId}/{academicYearId}/profile-ratings', [AcademicYearController::class, 'profileRatingsForYearAjax'])->name('staff.profileRatings');
    Route::get('/academic-year/{staffId}/{academicYearId}/detailed-evaluations', [AcademicYearController::class, 'detailedEvaluationsForYearAjax'])->name('staff.detailedEvaluations');
    
    // Staff AJAX routes for general staff ratings page (without academic year context)
    Route::post('/staff/comments', [StaffController::class, 'getStaffComments'])->name('staff.comments.general');
    Route::get('/staff/profile-ratings/{staffId}', [StaffController::class, 'getStaffProfileRatings'])->name('staff.profileRatings.general');
    Route::get('/staff/detailed-evaluations/{staffId}', [StaffController::class, 'getStaffDetailedEvaluations'])->name('staff.detailedEvaluations.general');

    // Staff CRUD Routes
    Route::post('/dashboard/add-staff', [StaffController::class, 'store'])->name('staff.store');
    Route::post('/dashboard/update-staff', [StaffController::class, 'update'])->name('staff.update');
    Route::post('/dashboard/delete-staff', [StaffController::class, 'destroy'])->name('staff.delete');

    // Student CRUD Routes
    Route::post('/dashboard/update-students', [StudentController::class, 'update'])->name('students.update');
    Route::post('/dashboard/delete-students', [StudentController::class, 'destroy'])->name('students.delete');

    // Subject CRUD Routes
    Route::post('/dashboard/add-subject', [DashboardController::class, 'addSubject'])->name('subjects.store');
    Route::post('/dashboard/update-subject', [DashboardController::class, 'updateSubject'])->name('subjects.update');
    Route::post('/dashboard/delete-subject', [DashboardController::class, 'deleteSubject'])->name('subjects.delete');

       // Question reuse routes
       Route::post('/questions/reuse-saved', [\App\Http\Controllers\QuestionController::class, 'reuseSavedQuestion'])->name('question.reuseSaved');
       Route::post('/questions/reuse-all-saved', [\App\Http\Controllers\QuestionController::class, 'reuseAllSavedQuestions'])->name('question.reuseAllSaved');

       // Questionnaires & Questions Routes
       Route::post('/dashboard/add-question', [\App\Http\Controllers\QuestionController::class, 'store'])->name('questions.store');
       Route::post('/dashboard/update-question', [\App\Http\Controllers\QuestionController::class, 'update'])->name('questions.update');
       Route::post('/dashboard/delete-question', [\App\Http\Controllers\QuestionController::class, 'destroy'])->name('questions.delete');
       Route::post('/dashboard/toggle-questionnaire-status', [\App\Http\Controllers\QuestionController::class, 'toggleStatus'])->name('questionnaires.toggle');
       Route::post('/dashboard/set-questionnaire-schedule', [\App\Http\Controllers\QuestionController::class, 'setSchedule'])->name('questionnaires.schedule.set');
       Route::get('/dashboard/clear-questionnaire-schedule', [\App\Http\Controllers\QuestionController::class, 'clearSchedule'])->name('questionnaires.schedule.clear');
       Route::post('/questions/save-all', [\App\Http\Controllers\QuestionController::class, 'saveAllQuestions'])->name('questions.saveAll');

    // Pending Requests Routes
    Route::get('/dashboard/pending-requests', [RequestSigninController::class, 'index'])->name('pending.requests.index');
    Route::post('/dashboard/pending-requests/{id}/approve', [RequestSigninController::class, 'approve'])->name('pending.requests.approve');
    Route::post('/dashboard/pending-requests/{id}/reject', [RequestSigninController::class, 'reject'])->name('pending.requests.reject');
    Route::post('/dashboard/pending-requests/{id}/delete', [RequestSigninController::class, 'delete'])->name('pending.requests.delete');
});

// Test email route
Route::get('/test-email', function () {
    try {
        Mail::raw('🎉 Gmail SMTP Test Successful!

Your MCC-IPES email configuration is working perfectly!

Time: ' . now() . '
From: MCC-IPES System

This is a test email to verify your Gmail SMTP setup.', function ($message) {
            $message->to('mccipesotp@gmail.com')
                    ->subject('MCC-IPES Email Test - ' . now());
        });
        
        return response()->json([
            'status' => 'success',
            'message' => '✅ Test email sent successfully!',
            'details' => [
                'to' => 'mccipesotp@gmail.com',
                'from' => config('mail.from.address'),
                'time' => now()
            ]
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => '❌ Email sending failed',
            'error' => $e->getMessage()
        ]);
    }
});