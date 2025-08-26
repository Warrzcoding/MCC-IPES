<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\RequestSigninController;
use App\Http\Controllers\PreSignupController;
use App\Http\Controllers\PasswordResetController;

Route::get('/', function () {
    return view('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/verify-student-id', [LoginController::class, 'verifyStudentId'])->name('verify.student.id');
Route::get('/clear-student-verification', [LoginController::class, 'clearStudentVerification'])->name('clear.student.verification');
Route::post('/login', [LoginController::class, 'login']) ->name('login.submit');
    //->middleware('account.lockout')
   
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Signup Routes
Route::get('/signup', [SignupController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [SignupController::class, 'signup'])->name('signup.submit');
Route::post('/check-duplicate-id', [SignupController::class, 'checkDuplicateId'])->name('check.duplicate.id');

// Pre-Signup (Microsoft 365 Verification) Routes
Route::get('/pre-signup', function() {
    return view('pre_signup');
})->name('pre_signup');
Route::post('/pre-signup/send', [PreSignupController::class, 'sendVerification'])->name('pre_signup.send_verification');
Route::post('/pre-signup/verify', [PreSignupController::class, 'verifyOtp'])->name('pre_signup.verify_otp');

// Password Reset Routes
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');
Route::post('/password/reset/send-verification', [PasswordResetController::class, 'sendVerification'])->name('password.reset.send_verification');
Route::post('/password/reset/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.reset.verify_otp');
Route::post('/password/reset/update', [PasswordResetController::class, 'update'])->name('password.reset.update');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/update-profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Admin Management Routes (Admin only)
    Route::post('/dashboard/add-admin', [DashboardController::class, 'addAdmin'])->name('admin.add');
    Route::post('/dashboard/update-admin', [DashboardController::class, 'updateAdmin'])->name('admin.update');
    Route::post('/dashboard/delete-admin', [DashboardController::class, 'deleteAdmin'])->name('admin.delete');
    
    // Staff Management Routes (Admin only)
    Route::post('/dashboard/add-staff', [StaffController::class, 'store'])->name('staff.store');
    Route::post('/dashboard/update-staff', [StaffController::class, 'update'])->name('staff.update');
    Route::post('/dashboard/delete-staff', [StaffController::class, 'destroy'])->name('staff.destroy');
    
    // Subject Management Routes (Admin only)
    Route::post('/dashboard/add-subject', [DashboardController::class, 'addSubject'])->name('subject.store');
    Route::post('/dashboard/update-subject', [DashboardController::class, 'updateSubject'])->name('subject.update');
    Route::post('/dashboard/delete-subject', [DashboardController::class, 'deleteSubject'])->name('subject.destroy');
    
    // Student Management Routes (Admin only)
    Route::post('/dashboard/update-students', [StudentController::class, 'update'])->name('students.update');
    Route::post('/dashboard/delete-students', [StudentController::class, 'destroy'])->name('students.destroy');
    
    // Questionnaire Routes (Admin only)
    Route::post('/dashboard/add-question', [QuestionController::class, 'store'])->name('question.store');
    Route::post('/dashboard/update-question', [QuestionController::class, 'update'])->name('question.update');
    Route::post('/dashboard/delete-question', [QuestionController::class, 'destroy'])->name('question.destroy');
    Route::post('/dashboard/toggle-questionnaire-status', [QuestionController::class, 'toggleStatus'])->name('questionnaire.toggle');
    Route::post('/dashboard/save-questions-to-academic', [QuestionController::class, 'saveToAcademic'])->name('questionnaire.save');
    Route::post('/dashboard/reuse-saved-question', [QuestionController::class, 'reuseSavedQuestion'])->name('question.reuseSaved');
    Route::post('/dashboard/reuse-all-saved-questions', [QuestionController::class, 'reuseAllSavedQuestions'])->name('question.reuseAllSaved');
    Route::post('/dashboard/create-academic-year', [QuestionController::class, 'createAcademicYear'])->name('academic.year.create');
    Route::get('/evaluates', [EvaluationController::class, 'showForm'])->name('evaluates');
    Route::post('/evaluations/submit', [EvaluationController::class, 'submit'])->name('evaluations.submit');
    Route::post('/staff-comments', [EvaluationController::class, 'getStaffComments'])->name('staff.comments');

    Route::post('/dashboard/set-questionnaire-schedule', [QuestionController::class, 'setSchedule'])->name('questionnaire.setSchedule');
    Route::get('/dashboard/clear-questionnaire-schedule', [QuestionController::class, 'clearSchedule'])->name('questionnaire.clearSchedule');

    Route::get('/staff/profile-ratings/{id}', [App\Http\Controllers\DashboardController::class, 'profileRatingsAjax']);
    Route::get('/staff/detailed-evaluations/{id}', [App\Http\Controllers\DashboardController::class, 'detailedEvaluationsAjax']);

    Route::prefix('dashboard')->group(function () {
        Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
        Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::post('/academic-years/{id}/update', [AcademicYearController::class, 'update'])->name('academic-years.update');
        Route::post('/academic-years/{id}/delete', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
        Route::post('/academic-years/{id}/toggle', [AcademicYearController::class, 'toggleActive'])->name('academic-years.toggle');
    });

    // Academic Year Management Page
    Route::get('/academic-year/{id}/manage', [App\Http\Controllers\AcademicYearController::class, 'manage'])->name('academic-years.manage');

    // Admin Request Signin Management
    Route::post('/dashboard/pending-requests/{id}/approve', [RequestSigninController::class, 'approve'])->name('pending.requests.approve');
    Route::post('/dashboard/pending-requests/{id}/reject', [RequestSigninController::class, 'reject'])->name('pending.requests.reject');
    Route::post('/dashboard/pending-requests/{id}/delete', [RequestSigninController::class, 'delete'])->name('pending.requests.delete');
});

Route::post('/dashboard/save-all-questions', [QuestionController::class, 'saveAllQuestions'])->name('questions.saveAll');
Route::post('/evaluations/save-and-clear-all', [App\Http\Controllers\EvaluationController::class, 'saveAndClearAllResults'])->name('evaluations.saveAndClearAll');

// Test route for debugging print functionality
Route::get('/test-staff-profile/{id}', [App\Http\Controllers\DashboardController::class, 'profileRatingsAjax'])->name('test.staff.profile');

Route::post('/academic-year/staff-comments', [AcademicYearController::class, 'getStaffCommentsForYear'])->name('academic_year.staff_comments');
Route::get('/academic-year/profile-ratings/{staffId}/{academicYearId}', [AcademicYearController::class, 'profileRatingsForYearAjax'])->name('academic_year.profile_ratings');
Route::get('/academic-year/detailed-evaluations/{staffId}/{academicYearId}', [AcademicYearController::class, 'detailedEvaluationsForYearAjax'])->name('academic_year.detailed_evaluations');

// Add this route for AJAX check
Route::get('/admin/check-questions-empty', [App\Http\Controllers\QuestionController::class, 'checkQuestionsEmpty'])->name('admin.checkQuestionsEmpty');
Route::get('/admin/check-evaluations-exist', [App\Http\Controllers\EvaluationController::class, 'checkEvaluationsExist'])->name('admin.checkEvaluationsExist')->middleware('auth');
