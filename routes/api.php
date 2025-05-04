<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\ReAllocateController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\ArrangingController;
use App\Http\Controllers\BulkAllocationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MeetingRequestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\TutorDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\TutorReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/user/register', [App\Http\Controllers\RegisterController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/allocations/{allocateId}', [ReAllocateController::class, 'reallocate']);
    // allocation start
    Route::get('allocations', [AllocationController::class, 'index']);
    Route::get('allocations/student', [AllocationController::class, 'ListStudents']);
    Route::get('/allocations/search', [AllocationController::class, 'search']);
    Route::get('allocations/{id}', [AllocationController::class, 'show']);
    Route::post('allocations', [AllocationController::class, 'store']);
    Route::post('bulk/allocations', [BulkAllocationController::class, 'allocate']);

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);

    Route::get('/meetinglist/student', [AllocationController::class, 'ListSearchStudent']);
    Route::get('/meetinglist/{id}', [AllocationController::class, 'ListSearch']);

    //allocation end

    // show non-allocated student
    Route::get('student/nonallocate', [BulkAllocationController::class, 'nonAllocatedStudentList']);
    // end non-allocated student

                                                                                      //Blog Routes start
    Route::get('/blogs/{id}', [App\Http\Controllers\BlogController::class, 'index']); //---Show single blog---//
    Route::get('/blogs/show/{id}', [App\Http\Controllers\BlogController::class, 'show']);
    Route::post('/blogs', [App\Http\Controllers\BlogController::class, 'store']);          ///--Blog Create---//
    Route::post('/blogs/{id}', [App\Http\Controllers\BlogController::class, 'update']);    //----Blog update---//
    Route::delete('/blogs/{id}', [App\Http\Controllers\BlogController::class, 'destroy']); ///----Blog delete---//
                                                                                           //////End of blog routes//////

    //------This is for student's search, views routes------//
    Route::get('/students/unallocationlists', [App\Http\Controllers\StudentController::class, 'index']);
    Route::get('/students/search', [App\Http\Controllers\StudentController::class, 'search']);
    Route::get('/students/namesort', [App\Http\Controllers\StudentController::class, 'sortStudents']);
    Route::get('/students/idsort', [App\Http\Controllers\StudentController::class, 'sortId']);

    Route::post('meetingrecord/store', [App\Http\Controllers\MeetingRecordController::class, 'store']);
    Route::get('meetingrecord/show/{id}', [App\Http\Controllers\MeetingRecordController::class, 'show']);
    Route::post('meetingrecord/update/{id}', [App\Http\Controllers\MeetingRecordController::class, 'update']);
    Route::delete('meetingrecord/delete/{id}', [App\Http\Controllers\MeetingRecordController::class, 'destroy']);
    //-----This is for student's dashboard----//
    Route::controller(StudentDashboardController::class)->group(function () {
        Route::get('/student/dashboard', 'getDashboardData');

    });

    ////-----This is for admin's dashboard----///
    Route::get('/admin/report', [App\Http\Controllers\AdminReportController::class, 'AdminReport']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/comments', [CommentController::class, 'index']);           // List all comments
    Route::post('/comments', [CommentController::class, 'store']);          // Add a comment
    Route::put('/comments/{id}', [CommentController::class, 'update']);     // Update a comment
    Route::get('/comments/{id}', [CommentController::class, 'show']);       // Get a single comment
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']); // Delete a comment
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get("/tutor/meetingrequest", [MeetingRequestController::class, 'meetingRequestList']);
    Route::get('/meetingrequest/{arrange_id}', [MeetingRequestController::class, 'index']);          // List all comments
    Route::post('/meetingrequest/{arrange_id}', [MeetingRequestController::class, 'create']);        // Add a comment
    Route::put('/meetingrequest/cancel/{id}', [MeetingRequestController::class, 'cancelRequest']);   // Update a comment
    Route::put('/meetingrequest/reject/{id}', [MeetingRequestController::class, 'rejectRequest']);   // Get a single comment
    Route::put('/meetingrequest/approve/{id}', [MeetingRequestController::class, 'approveRequest']); // Delete a comment
});

Route::middleware('auth:sanctum')->group(function () {
    // tutorlist
    Route::get('/tutors', [TutorController::class, 'tutorList']);
    Route::get('/students', [StudentController::class, 'getAllStudents']);
});

// Arranging Routes (Students Only)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/arranging', [ArrangingController::class, 'store']);          //  Only students can create arrangements
    Route::get('/arranging', [ArrangingController::class, 'index']);           //  Students & Tutors can view
    Route::get('/arranging/{id}', [ArrangingController::class, 'show']);       //  Fetch arrangement details
    Route::put('/arranging/{id}', [ArrangingController::class, 'update']);     //  Update arrangement status
    Route::delete('/arranging/{id}', [ArrangingController::class, 'destroy']); //  Allow cancellation
});

// tutor dashboard
Route::middleware('auth:sanctum')->get('/tutor/dashboard', [TutorDashboardController::class, 'index']);
Route::middleware('auth:sanctum')->get('/admin/tutor/dashboard/{id}', [TutorDashboardController::class, 'tutorDashboard']);
Route::middleware('auth:sanctum')->get('/admin/student/dashboard/{id}',[StudentDashboardController::class, 'getStudentDashboardData']);




// Single file download by type/id
Route::get('/download/{type}/{id}', [FileDownloadController::class, 'download']);

// Download all general documents in ZIP
Route::get('/download/blog/all/{blogId}', [FileDownloadController::class, 'downloadAllFilesForBlog']);


Route::middleware(['auth:sanctum'])->get('tutor/report', [TutorReportController::class, 'report']);

