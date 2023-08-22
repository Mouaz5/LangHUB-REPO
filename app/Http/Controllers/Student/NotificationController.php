<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use App\Models\Academy;
use App\Models\AcademyNotification;
use App\Models\Course;
use App\Models\LessonNotification;
use App\Models\Offer;
use App\Models\OfferNotification;
use App\Models\Student;
use Generator;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\Builder\Stub;
use Psy\CodeCleaner\ReturnTypePass;

class NotificationController extends Controller
{
    public function showCourseNotifications(Course $course)
	{
		$student =  Student::where('user_id' , auth()->id())->first() ;
		if(!$course->students()->where('student_id' ,$student->id)->exists()){
			return response()->json([
				'status'=>201 ,
				'message' => 'you are not member in this course'
			]) ;
		}
		$c = $course->lessons()->with('notification')->get();
		$notification = array();
		$k = 0;
		for ($i = 0; $i < sizeof($c); $i++) {
			if ($c[$i]['notification'] != null) {
				$notification[$k] =  $c[$i]['notification'];
				$k++;
			}
		}
		return response()->json([
			'status' => 200 ,
			'message' => ' done successfully',
			'data' => $notification
		]);
	}
	public function hasNotification(Course $course){
		$lessons = $course->lessons()->get();
		foreach ($lessons as $lesson){
			$notifications = $lesson->notification()->get();
			foreach($notifications as $notification){
				if ($notification->read == false)
				return 1 ;
			}
		}
		return 0 ;
	}
    public function showLessonNotification(LessonNotification $lessonNotification){
        $lesson = $lessonNotification->lesson()->get();
		$lessonNotification->read = true ;
		$lessonNotification->save();
		return response()->json([
			'status'=> 200 ,
			'message' => 'done successfully' ,
			'data' => $lesson
		]);
    }
	public function showAcademyNotifications(){
		$student = Student::where('user_id' , auth()->id())->first();
		$notifications = $student->academyNotifications()->latest()->get();
		return response()->json([
			'status'=>200,
			'message' => 'done successfully' ,
			'data' => $notifications 
		]);
	}
	public function showAcademyNotification(AcademyNotification $academyNotification){
		$student = Student::where('user_id' , auth()->id())->first()['id'] ;
		if ($academyNotification->student_id != $student)
		return response()->json([
			'status' =>201 ,
			'message' => 'you cant show this notification because you are awomen'
		]);
		$academy = Academy::where('id' , $academyNotification->academy_id)->first();
		$G = new GeneralController ;
		$academy2 = $G->academy($academy) ;	
		$academyNotification->read = true ;
		$academyNotification->save() ;
		return $academy2 ;
	}
	public function showOffersNotifications(){
		$student = Student::where('user_id' , auth()->id())->first();
		$notifications = $student->offerNotifications()->latest()->get() ;
		return response()->json([
			'status' => 200,
			'message' => 'done successfully',
			'data' => $notifications
			]);
	}
	public function showOfferNotification(OfferNotification $offerNotification){
		$student = Student::where('user_id' , auth()->id())->first()['id'] ;
		if ($offerNotification->student_id != $student)
		return response()->json([
			'status' => 201 ,
			'message' => 'HaHaHaHaHaHa you can not show this notification maaaan'
		]);
		$offer = Offer::where('id' , $offerNotification->offer_id)->first();
		$G = new GeneralController ;
		$offer2 = $G->offer($offer) ;
		$offerNotification->read = true ;
		$offerNotification->save() ;
		return $offer2 ;
	}

}
