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
use App\Http\Controllers\TestRecaptchaController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\LoginMonitorController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\MagicLinkController;

use App\Http\Controllers\ChatbotController;

// Chatbot Route
Route::post('/chatbot/message', [ChatbotController::class, 'handleMessage'])->name('chatbot.message');
Route::get('/download-apk', function () {
    $filePath = public_path('apk/android/students_ipes.apk');
    if (file_exists($filePath)) {
        return response()->download($filePath, 'students_ipes.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
    return abort(404, 'APK file not found.');
})->name('download.apk');

// Root route - redirect to login
Route::get('/', function () {
    return view('login');
});

// Test route for geolocation debugging




// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/admin/otp/verify', [LoginController::class, 'verifyAdminOtp'])->name('admin.otp.verify');
Route::post('/admin/otp/resend', [LoginController::class, 'resendAdminOtp'])->middleware('throttle.otp:3,10')->name('admin.otp.resend');
Route::post('/admin/otp/cancel', [LoginController::class, 'cancelAdminOtp'])->name('admin.otp.cancel');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/verify-student-id', [LoginController::class, 'verifyStudentId'])->name('verify.student.id');
Route::get('/clear-student-verification', [LoginController::class, 'clearStudentVerification'])->name('clear.student.verification');

//login Routes
Route::get('/pre-signup', [PreSignupController::class, 'showForm'])->name('pre_signup');
Route::post('/pre-signup/send-verification', [PreSignupController::class, 'sendVerification'])->name('pre_signup.send_verification');
Route::post('/pre-signup/verify-otp', [PreSignupController::class, 'verifyOtp'])->name('pre_signup.verify_otp');

// ID Check Routes
Route::get('/idcheck', [PreSignupController::class, 'showIdCheckForm'])->name('idcheck');
Route::post('/idcheck', [PreSignupController::class, 'checkId'])->name('idcheck.submit');
Route::post('/idcheck/verify', [PreSignupController::class, 'checkIdAjax'])->name('idcheck.verify');
Route::post('/idcheck/send-otp', [PreSignupController::class, 'sendIdCheckOtp'])->name('idcheck.send_otp');
Route::post('/idcheck/verify-otp', [PreSignupController::class, 'verifyIdCheckOtp'])->name('idcheck.verify_otp');
Route::post('/idcheck/store-session', [PreSignupController::class, 'storeVerifiedId'])->name('idcheck.store_session');

// Signup Routes
Route::get('/signup', [SignupController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [SignupController::class, 'signup'])->name('signup.submit');
Route::post('/check-duplicate-id', [SignupController::class, 'checkDuplicateId'])->name('check.duplicate.id');
Route::post('/check-user-id-availability', [SignupController::class, 'checkUserIdAvailability'])->name('check.user.id.availability');

// Password Reset Routes
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password/send-verification', [PasswordResetController::class, 'sendVerification'])->name('password.reset.send_verification');
Route::post('/reset-password/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.reset.verify_otp');
Route::post('/reset-password/update', [PasswordResetController::class, 'update'])->name('password.reset.update');
// Alias to support Laravel's default forgot-password link names used in Blade
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');

Route::get('/forgotpass', function () {
    return view('forgotpass');
})->name('forgotpass');

// Magic Link Routes
Route::post('/magic-link/send', [MagicLinkController::class, 'sendMagicLink'])->name('magic.link.send');
Route::get('/magic-link/reset/{token}', [MagicLinkController::class, 'showResetForm'])->name('magic.reset');
Route::post('/magic-link/update', [MagicLinkController::class, 'resetPassword'])->name('magic.reset.update');

// Super Admin Routes
Route::get('/superadmin/login', [SuperAdminController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/superadmin/login', [SuperAdminController::class, 'login'])->name('superadmin.login.submit');
Route::post('/superadmin/verify-accesscode', [SuperAdminController::class, 'verifyAccessCode'])->name('superadmin.verify-accesscode');
Route::post('/superadmin/otp/verify', [SuperAdminController::class, 'verifyOtp'])->name('superadmin.otp.verify');
Route::post('/superadmin/otp/resend', [SuperAdminController::class, 'resendOtp'])->name('superadmin.otp.resend');
Route::post('/superadmin/otp/cancel', [SuperAdminController::class, 'cancelOtp'])->name('superadmin.otp.cancel');
Route::get('/superadmin/home', [SuperAdminController::class, 'home'])->name('superadmin.home');
Route::get('/superadmin/users', [SuperAdminController::class, 'userManagement'])->name('superadmin.users');
Route::get('/superadmin/activity-log', [SuperAdminController::class, 'activityLog'])->name('superadmin.activity-log');
Route::get('/superadmin/filemanager', [SuperAdminController::class, 'fileManager'])->name('superadmin.filemanager');
Route::get('/superadmin/backup/download', [SuperAdminController::class, 'downloadBackup'])->name('superadmin.backup.download');
Route::delete('/superadmin/activity-log/{id}', [SuperAdminController::class, 'deleteActivityLog'])->name('superadmin.activity-log.delete');
Route::post('/superadmin/update-student', [SuperAdminController::class, 'updateStudent'])->name('superadmin.update-student');
Route::post('/superadmin/update-password', [SuperAdminController::class, 'updatePassword'])->name('superadmin.update-password');
Route::post('/superadmin/logout', [SuperAdminController::class, 'logout'])->name('superadmin.logout');
Route::post('/superadmin/add-id-user', [SuperAdminController::class, 'addIdUser'])->name('superadmin.add-id-user');
Route::post('/superadmin/check-id-number', [SuperAdminController::class, 'checkIdNumber'])->name('superadmin.check-id-number');
Route::get('/superadmin/admin-management', [SuperAdminController::class, 'adminManagement'])->name('superadmin.admin-management');
Route::post('/superadmin/admin-management/store', [SuperAdminController::class, 'storeAdmin'])->name('superadmin.admin-management.store');
Route::post('/superadmin/admin-management/update', [SuperAdminController::class, 'updateAdmin'])->name('superadmin.admin-management.update');
Route::post('/superadmin/admin-management/delete', [SuperAdminController::class, 'deleteAdmin'])->name('superadmin.admin-management.delete');

// Dashboard Routes (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Login monitor page (reuses dashboard layout with page=login-monitor)
    Route::get('/dashboard/login-monitor', function() {
        return redirect()->route('dashboard', ['page' => 'login-monitor']);
    })->name('dashboard.login-monitor');
    
    // Login monitor delete route
    Route::delete('/dashboard/login-monitor/{id}', [LoginMonitorController::class, 'deleteAttempt'])->name('login-monitor.delete');
    
    // Evaluation Routes
    Route::get('/evaluations', [EvaluationController::class, 'showForm'])->name('evaluations.form');
    Route::get('/evaluation-form', [EvaluationController::class, 'showEvaluationForm'])->name('evaluations.show');
    Route::post('/evaluations/submit', [EvaluationController::class, 'submit'])->name('evaluations.submit');
    Route::post('/evaluations/save-and-clear-all', [EvaluationController::class, 'saveAndClearAllResults'])->name('evaluations.saveAndClearAll');
    Route::get('/admin/check-evaluations-exist', [EvaluationController::class, 'checkEvaluationsExist'])->name('evaluations.checkExist');
    Route::get('/admin/check-questions-empty', [EvaluationController::class, 'checkQuestionsEmpty'])->name('evaluations.checkQuestionsEmpty');

    // Instructor Selection Persistence Routes
    Route::post('/evaluations/instructor-selection/save', [EvaluationController::class, 'saveInstructorSelection'])->name('instructor-selection.save');
    Route::post('/evaluations/instructor-selection/update-stage', [EvaluationController::class, 'updateSelectionStage'])->name('instructor-selection.update-stage');
    Route::post('/evaluations/instructor-selection/clear', [EvaluationController::class, 'clearInstructorSelections'])->name('instructor-selection.clear');
    Route::post('/evaluations/confirm-selection', [EvaluationController::class, 'confirmSelection'])->name('selection.confirm');
    Route::post('/evaluations/unlock-selection', [EvaluationController::class, 'unlockSelection'])->name('selection.unlock');

    // Academic Year Routes
    Route::get('/dashboard/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/dashboard/academic-years/store', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::post('/dashboard/academic-years/{id}/update', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::post('/dashboard/academic-years/{id}/toggle', [AcademicYearController::class, 'toggleActive'])->name('academic-years.toggle');
    Route::post('/dashboard/academic-years/{id}/delete', [AcademicYearController::class, 'destroy'])->name('academic-years.delete');
    Route::get('/academic-year/{token}/manage', [AcademicYearController::class, 'manage'])->name('academic-years.manage');

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

    // Admin management routes
    Route::post('/dashboard/add-admin', [DashboardController::class, 'addAdmin'])->name('admin.add');
    Route::post('/dashboard/update-admin', [DashboardController::class, 'updateAdmin'])->name('admin.update');
    Route::post('/dashboard/delete-admin', [DashboardController::class, 'deleteAdmin'])->name('admin.delete');

    // Profile Update Route
    Route::post('/dashboard/update-profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    // Sidebar Settings Routes
    Route::get('/dashboard/sidebar-settings', [DashboardController::class, 'getSidebarSettings'])->name('sidebar.settings.get');
    Route::post('/dashboard/sidebar-settings', [DashboardController::class, 'updateSidebarSettings'])->name('sidebar.settings.update');

    // Backup Routes
    Route::get('/admin/backup/download', [DashboardController::class, 'downloadBackup'])->name('backup.download');
    Route::delete('/admin/backup/{id}', [DashboardController::class, 'deleteBackup'])->name('backup.delete');

    // Test route for debugging
    Route::get('/dashboard/test-profile', function() {
        return response()->json(['message' => 'Profile route is working', 'user' => Auth::user()->full_name]);
    })->name('profile.test');

    // Student CRUD Routes
    Route::get('/dashboard/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/dashboard/update-students', [StudentController::class, 'update'])->name('students.update');
    Route::post('/dashboard/delete-students', [StudentController::class, 'destroy'])->name('students.delete');

    // Subject CRUD Routes
    Route::post('/dashboard/add-subject', [DashboardController::class, 'addSubject'])->name('subjects.store');
    Route::post('/dashboard/update-subject', [DashboardController::class, 'updateSubject'])->name('subjects.update');
    Route::post('/dashboard/delete-subject', [DashboardController::class, 'deleteSubject'])->name('subjects.delete');
    Route::get('/dashboard/search-staff', [DashboardController::class, 'searchStaff'])->name('staff.search');

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
    Route::post('/dashboard/pending-requests/approve-multiple', [RequestSigninController::class, 'approveMultiple'])->name('pending.requests.approveMultiple');
    Route::post('/dashboard/pending-requests/delete-multiple', [RequestSigninController::class, 'deleteMultiple'])->name('pending.requests.deleteMultiple');
});

 
// reCAPTCHA setup and test routes
Route::get('/recaptcha/setup', function () {
    return view('recaptcha-setup');
})->name('recaptcha.setup');

Route::get('/test/recaptcha/config', [TestRecaptchaController::class, 'testConfig'])->name('test.recaptcha.config');
Route::get('/test/recaptcha/type', [TestRecaptchaController::class, 'testCaptchaType'])->name('test.recaptcha.type');
Route::post('/test/recaptcha/verify', [TestRecaptchaController::class, 'testVerification'])->name('test.recaptcha.verify');


Route::any('public/{any?}', fn () => abort(404))->where('any', '.*');
Route::any('public_html/{any?}', fn () => abort(404))->where('any', '.*');
Route::any('index.php', fn () => abort(404));


