<?php

use App\Http\Controllers\AcademyAdmin\AcademyAdminCourseController;
use App\Http\Controllers\AcademyAdmin\AcademyAdminExamController;
use App\Http\Controllers\AcademyAdmin\AcademyAdminOfferController;
use App\Http\Controllers\AcademyAdmin\AcademyAdminProfilecontroller;
use App\Http\Controllers\AcademyAdmin\AcademyAdminStudentController;
use App\Http\Controllers\AcademyAdmin\AcademyAdminTeacherController;
use App\Http\Controllers\AcademyAdmin\AuthAcademyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\Student\AuthStudentController;
use App\Http\Controllers\Student\ProfileStudentController;
use App\Http\Controllers\Student\CourseStudentController;
use App\Http\Controllers\Student\AcademyStudentController;
use App\Http\Controllers\Student\LessonStudentController;
use App\Http\Controllers\Student\GroupStudentController;
use App\Http\Controllers\Teacher\AuthTeacherController;
use App\Http\Controllers\Teacher\ProfileTeacherController;
use App\Http\Controllers\Teacher\HomeTeacherController;
use App\Http\Controllers\Teacher\CourseTeacherController;
use App\Http\Controllers\Teacher\InstituesTeacherController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\OfferStudentController;
use App\Http\Controllers\Student\RateController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdmin\RequestMangeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('student/register',[AuthStudentController::class, 'register']);
Route::post('teacher/register',[AuthTeacherController::class, 'register']);
Route::post('academy-admin/register',[AuthAcademyController::class, 'register']);

Route::post('/login',[LoginController::class,'login']);
// Route::post('/login' , function(){
//     return "hello" ;
// });

Route::group(['prefix' => 'super-admin', 'middleware' => ['auth:sanctum','superAdmin']], function () {
    Route::get('accept-academy/{academyPending}' , [RequestMangeController::class , 'acceptAcademy'] );
    Route::get('reject-academy/{academyPending}' , [RequestMangeController::class , 'rejectrequest'] );
    Route::delete('delete-academy/{academy}' , [RequestMangeController::class , 'deleteAcademy'] );
    Route::get('/requests' ,[RequestMangeController::class , 'requests']);
    Route::get('academies' , [RequestMangeController::class ,'academies' ] );
    
});//dont 

Route::group(['prefix' => 'academy-admin', 'middleware' => ['auth:sanctum','academyAdmin']], function () {
    Route::get('/', [HomeTeacherController::class, 'index']);
    Route::group(['prefix' => 'profile'], function() {
        Route::post('update-requestTime' ,[AcademyAdminProfileController::class , 'uodateRequestTime']);
        Route::get('/feedback' ,[AcademyAdminProfilecontroller::class , 'feedBack']);
        Route::get('/', [AcademyAdminProfilecontroller::class, 'show']);
        Route::post('/update', [AcademyAdminProfilecontroller::class, 'update']);
        Route::post('change-password' , [AcademyAdminProfilecontroller::class , 'changePassword']);
        Route::get('accepted' , [AcademyAdminProfilecontroller::class , 'accepted']);
    });
    Route::group(['prefix' => 'courses'], function() {
        Route::get('/{course}/show' , [AcademyAdminCourseController::class , 'showCourse']);
        Route::post('/{course}/add-schedule', [AcademyAdminCourseController::class, 'addCourseSchedule']);
        Route::post('/{course}/update', [AcademyAdminCourseController::class, 'update']);
        Route::post('/{course}/active', [AcademyAdminCourseController::class, 'activeCourse']);
        Route::get('{course}/checkTeacher' ,[AcademyAdminCourseController::class , 'teachersForCourse']);
        Route::post('/', [AcademyAdminCourseController::class, 'addCourse']);
        Route::delete('{course}' , [AcademyAdminCourseController::class , 'deleteCourse']);
        Route::get('/inactive' , [AcademyAdminCourseController::class,'inactiveCourses'] );
        Route::get('/active' , [AcademyAdminCourseController::class,'activeCourses'] );
        Route::get('/addStudentToCourse/{course}/{student}' ,[AcademyAdminStudentController::class , 'addStudentToCourse']);
    });
    Route::group(['prefix' => 'teachers'], function() {
        Route::get('/' , [AcademyAdminTeacherController::class,'index'] );
        Route::get('/requests' , [AcademyAdminTeacherController::class,'showTeacherRequests'] );
        Route::get('/{teacher}' , [AcademyAdminTeacherController::class,'teacher'] );
        Route::get('/accept-teacher/{teacher}' , [AcademyAdminTeacherController::class , 'acceptTeacher']);
        Route::delete('/reject-teacher/{teacher}' , [AcademyAdminTeacherController::class , 'rejectTeacher']);
    });
    Route::group(['prefix' => 'students'], function() {
        Route::get('/' , [AcademyAdminStudentController::class,'index'] );
        Route::get('/requests' , [AcademyAdminStudentController::class,'showStudentRequests'] );
        Route::get('/{student}' , [AcademyAdminStudentController::class,'student'] );
        Route::get('/accept-student/{student}' , [AcademyAdminStudentController::class , 'acceptStudent']);
        Route::delete('/reject-student/{student}' , [AcademyAdminStudentController::class , 'rejectStudent']);
    });
    Route::group(['prefix' => 'offers'], function() {
        Route::get('/' , [AcademyAdminOfferController::class , 'index']);
        Route::post('/add' , [AcademyAdminOfferController::class , 'addOffer']);
    });
    Route::group(['prefix' => 'exams'], function() {
        Route::post('/addExam/{course}' , [AcademyAdminExamController::class , 'addExam']);
        Route::delete('deleteExam/{course}' , [AcademyAdminExamController::class , 'deleteExam']);
        Route::get('/{course}' ,[AcademyAdminExamController::class , 'show']);
        
    });
});

Route::group(['prefix' => 'teacher', 'middleware' => ['auth:sanctum','teacher']], function () {
    Route::group(['prefix' => 'profile'], function() {
        Route::get('/', [ProfileTeacherController::class, 'show']);
        Route::post('/', [ProfileTeacherController::class, 'update']);
        Route::post('/change-password', [ProfileTeacherController::class, 'changePassword']);
        Route::post('upload-post', [ProfileTeacherController::class, 'uploadPost']);
        Route::get('my-posts', [ProfileTeacherController::class, 'myPosts']);
        Route::delete('delete-post/{post}', [ProfileTeacherController::class, 'deletePost']);
    });
    Route::get('pending-requests', [InstituesTeacherController::class, 'pendingRequests']);
    Route::group(['prefix' => 'courses'], function() {
        Route::get('/', [CourseTeacherController::class, 'index']);
        Route::get('/{course}', [CourseTeacherController::class, 'show']);
        Route::get('/{course}/students', [CourseTeacherController::class, 'courseStudents']);
        Route::post('{course}/add-marks', [CourseTeacherController::class,'addMarks']);
        Route::post('/{course}/add-lesson', [InstituesTeacherController::class, 'addLesson']);
        Route::get('/{course}/show-lessons', [InstituesTeacherController::class, 'showLessons']);
        Route::delete('{lesson}/delete-lesson', [InstituesTeacherController::class, 'destroy']);
    });
    Route::group(['prefix' => 'institutes'], function() {
        Route::get('/', [InstituesTeacherController::class, 'index']);
        Route::get('/{academy}', [InstituesTeacherController::class, 'show']);
        Route::post('{id}/add-request', [InstituesTeacherController::class, 'store']);
        Route::post('search', [HomeTeacherController::class, 'academySearch']);
        Route::delete('{order}/cancel-request', [InstituesTeacherController::class, 'cancelRequest']);
        Route::get('students/{course}', [InstituesTeacherController::class, 'showStudents']);
        Route::get('courses-history', [InstituesTeacherController::class, 'coursesHistory']);
    });
});

Route::group(['prefix' => 'student', 'middleware' => ['auth:sanctum','student']], function () {

    Route::group(['prefix' => 'profile'], function() {
        Route::get('/', [ProfileStudentController::class, 'show']);
        Route::post('/', [ProfileStudentController::class, 'update']);
        Route::post('/change-password', [ProfileStudentController::class, 'changePassword']);
    });//done

    Route::group(['prefix' => 'courses'], function() {
        
        Route::get('/enrolled-courses', [CourseStudentController::class, 'enrolledCourses']);
        Route::get('certificate' , [ProfileStudentController::class , 'certificats']);
        Route::post('solve-exam/{course}' , [CourseStudentController::class , 'solveExam']);
        Route::group(['prefix' => '{course}/lessons'], function() {
            Route::get('/', [LessonStudentController::class, 'lessons']);
            Route::get('/{lesson}', [LessonStudentController::class, 'show']);
        });
    });
    

    Route::group(['prefix' => 'academies'], function() {
        Route::get('/' , [AcademyStudentController::class , 'index']) ;
        Route::post('join/{academy}' , [AcademyStudentController::class ,'joinToAcademy' ]) ;
        Route::get('show/{academy}' , [AcademyStudentController::class , 'academy']);
        Route::get('show-request' , [AcademyStudentController::class , 'showRequest'] ) ;
        Route::get('cancel-request/{academyStudent}', [AcademyStudentController::class , 'delete']) ;
        Route::post('feedback/{academy}' , [AcademyStudentController::class , 'addFeedBack']) ;
        Route::post('rate-academy/{academy}' , [AcademyStudentController::class ,'rateAcademy' ]) ;
        Route::post('rate-teacher/{teacher}' , [AcademyStudentController::class ,'rateTeacher' ]) ;
    });//done
    Route::group(['prefix' => 'rate'] , function (){
        Route::post('/teacher/{teacher}' ,[RateController::class ,'rateTeacher' ] );
        Route::post('/academy/{academy}' ,[RateController::class ,'rateAcademy' ] );
    });//done
    Route::group(['prefix' => 'notification'] , function(){
        Route::get('/course/{course}' , [NotificationController::class , 'showCourseNotifications']);
        Route::get('acceptAcademy' ,[NotificationController::class , 'showAcademyNotifications']);
        Route::get('offers' , [NotificationController::class , 'showOffersNotifications']);
        Route::get('lesson/{lessonNotification}' , [NotificationController::class , 'showLessonNotification']);
        Route::get('/academy/{academyNotification}' , [NotificationController::class , 'showAcademyNotification']);
        Route::get('offer/{offerNotification}' , [NotificationController::class , 'showOfferNotification']);
    });

    Route::group(['prefix' => 'offers'], function() {
        Route::get('/' , [OfferStudentController::class , 'index']);
        Route::get('/requests' , [OfferStudentController::class , 'showOfferRequests']);
        Route::get('/enroll/{offer}' , [OfferStudentController::class , 'enrollToOffer']);
        Route::delete('delete-request/{offer}' ,[OfferStudentController::class,'delete']);
        Route::get('ten' , [OfferStudentController::class , 'tenOffers']);
    });


    Route::group(['prefix' => 'groups'], function() {
        Route::get('/', [GroupStudentController::class, 'index']);
        Route::get('/{group}', [GroupStudentController::class, 'show']);
        Route::post('{group}/join', [GroupStudentController::class, 'joinGroup']);
        Route::post('{group}/leave', [GroupStudentController::class, 'leaveGroup']);
        Route::post('{group}/send-message', [GroupStudentController::class, 'sendMessage']);
        Route::get('{group}/all-messages', [GroupStudentController::class, 'showMessages']);
        Route::post('{group}/delete-message', [GroupStudentController::class, 'deleteMessage']);
    });

    Route::group(['prefix' => 'academy'], function() {
        Route::get('/all' , [AcademyStudentController::class , 'index']) ;//done Edit
        Route::get('{academy}/allcourses', [AcademyStudentController::class, 'allAcademyCourses']);//done
        Route::get('/{academy}', [AcademyStudentController::class, 'show']);//done Edit
        Route::post('rate/{academy}' , [AcademyStudentController::class , 'rate']) ;//done add
    });
});
//route that access to all
Route::get('academies' ,[GeneralController::class , 'academies']);
Route::get('courses' , [GeneralController::class , 'courses']);
Route::get('courses/{academy}' , [GeneralController::class , 'courseInAcademy']);
Route::get('offers' , [GeneralController::class , 'offers']);
Route::get('offer/{offer}' , [GeneralController::class , 'offer']);
Route::get('academy/{academy}' , [GeneralController::class , 'academy']);
Route::get('teacher/{teacher}' , [GeneralController::class , 'teacher']);
Route::post('academySearch' , [GeneralController::class , 'academySearch']);
Route::get('search-academies', [AcademyStudentController::class, 'academySearch']);
Route::post('mouaz', [HomeController::class, 'test']);