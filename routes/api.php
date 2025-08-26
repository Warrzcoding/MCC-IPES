use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\StaffController;

Route::get('/staff/fullname/{id}', [StaffController::class, 'fullName']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/student/profile', [StudentController::class, 'profile']);
    Route::put('/student/profile', [StudentController::class, 'updateProfile']);
    Route::get('/student/evaluations', [EvaluationController::class, 'index']);
    Route::post('/student/evaluations', [EvaluationController::class, 'submit']);
    Route::get('/student/evaluations/status', [EvaluationController::class, 'status']);
    Route::get('/student/notifications', [NotificationController::class, 'index']);
});
