<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Support\Facades\Date;

class CourseStudentController extends Controller
{
	public function index()
	{
	}
	//book a specific course
	public function bookCourse(Course $course)
	{
		$student = Student::where('user_id', auth()->id())->first();
		// Check if the course is full
		if ($course->seats <= 0) {
			return response()->json([
				'error' => 'The course is already full'
			]);
		}
		// Check if the student has already booked the course
		if ($student->courses()->where('course_id', $course->id)->exists()) {
			return response()->json([
				'error' => 'You have already booked this course!'
			]);
		}
		// Register the student for the course
		$student->courses()->attach($course->id);
		$course->seats -= 1;
		$course->save();

		return response()->json([
			'success' => 'You have successfully registered for the course!'
		]);
	}

	//show student enrolled courses
	public function enrolledCourses()
	{
		//
		$student = Student::where('user_id', auth()->id())->first();
		$courses = $student->courses()
			->with('academy:id,name')
			->with('annualSchedule')
			->get();
		foreach($courses as $course){
			$course['is_offer'] = false ;
			$teacher = $course->teacher()->first();
			$user= $teacher->user()->first();
			$teacher['email'] = $user->email ;
			$course['teacher'] = $teacher ;
			$exam = $course->exam()->first();
			
			if ($exam == null) {
				$course['hasActivatExam'] = 0;
			}else {
				if ($exam->activated == 1)
					$course['hasActivatExam'] = 1;
			}
			//$course['hasNotification'] = $this->hasNotification($course);
		}
		$offers = $student->offers()
		->wherePivot('approved' , 1)
		->with('academy:id,name')
		->with('annualSchedules')
		->get();
		foreach($offers as $offer){
			$teacher = $offer->teacher()->first();
			$user= $teacher->user()->first();
			$teacher['email'] = $user->email ;
			$offer['teacher'] = $teacher ;
			$offer['is_offer'] = true ;
			$offer['hasActivatExam'] = 0;
			$course['hasNotification'] = 1;
		}
		$combinedList = $courses->merge($offers);
		return response()->json([
			'status' => 200,
			'message' => 'success',
			'data' => $combinedList->all()
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
	//Cancel a student's enrollment in a course
	public function cancelCourseEnrollment(Request $request, Course $course)
	{
		$student = Student::where('user_id', auth()->id())->first();
		$student->courses()->detach($course);
		return response()->json(['message' => 'Enrollment canceled']);
	}
	public function finishedCourses()
	{
		$student = Student::where('user_id', auth()->id())->first();
		$courses = $student->courses()
			->where('end_time', '<=', Date::now())
			->get();
		return response()->json([
			'status' => 200,
			'message' => 'done successfully',
			'data' => $courses
		]);
	}
	public function solveExam(Request $request, Course $course)
	{	
		$student = Student::where('user_id' , auth()->id())->first();
		if (!$student->courses()->wherePivot('course_id' , $course->id)->exists())
		return response()->json([
			'status' => 400 ,
			'message' => 'you did not enrolled in this course'
		]);
		$exam = $course->exam()->first();
		$questions = $exam->questions()->get();
		
		$quesionMark = 100 / sizeof($questions);		
		$i = 1;
		$mark = 0;
		foreach ($questions as $q) {
			if ($request["$i"] == $q['correct_choise'])
				$mark += $quesionMark;
			$i++;
		}
		
		
		$academy = $course->academy()->first();
		if ($mark >= 50)
		Certificate::create([
			'student_name' => "$student->firest_name . $student->last_name",
			'academy_name' => $academy->name ,
			'mark' => $mark ,
			'course_level' => $course['name'] ,
			'image' => $course->course_image ,
			'receive_date' => now(),
			'academy_id' => $academy->id,
			'student_id' => $student->id
		
		]);
		if ($mark<50)
		$message = " sorry you failed in this exams and your mark is $mark ";
		else $message = "good luck you passed this exam and your mark is $mark now you can show your certicficate in your profile" ;
		$student->courses()->detach($course->id);
		return response()->json([
			'status' => 200 ,
			'message' => $message 
		]);
	}
	public function getQuestions(Course $course) {
		$exam = $course->exam()->first();
		if ($exam == null)
			return response()->json([
				'status' => 200 ,
				'message' => 'this course dose not have an exam'
			]);
		if  ($exam->activated == true ){
			$questions = $exam->questions()->get();
			$questions = $questions->map(function ($item){
				return collect($item)->except('created_at', 'updated_at');
			});
			return response()->json([
				'status' => 200,
				'message' => 'done succefully',
				'data' => $questions
			]);
		}else {
			return response()->json([
				'status' => 205,
				'message' => 'the Exam not activated yet'
			]);
		}
	}
}

