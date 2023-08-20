<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Exam;
use App\Models\Offer;
use App\Models\Question;
use App\Models\Student;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use PhpParser\ErrorHandler\Collecting;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;

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
		$student = Student::where('user_id', auth()->id())->first();
		$courses = $student->courses()
			->with('teacher:id,first_name,last_name')
			->with('academy:id,name')
			->with('annualSchedule')
			->get();
		foreach($courses as $course){
			$course['is_offer'] = false ;
		}
		$offers = $student->offers()
		->wherePivot('approved' , 1)
		->with('teacher:id,first_name,last_name')
		->with('academy:id,name')
		->with('annualSchedules')
		->get();
		foreach($offers as $offer){
			$offer['is_offer'] = true ;
		}
		$combinedList = $courses->merge($offers);
		return response()->json([
			'status' => 200,
			'message' => 'success',
			'data' => $combinedList->all()
		]);
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
		$exam = $course->exams()->first();
		$questions = $exam->questions()->get();
		
		$quesionMark = 100 / sizeof($questions);		
		$i = 1;
		$mark = 0;
		foreach ($questions as $q) {
			if ($request["$i"] == $q['correct_choise'])
				$mark += $quesionMark;
			$i++;
		}
		
		$student->courses()->detach($course->id);
		$academy = $course->academy()->first();
		if ($mark >= 50)
		Certificate::create([
			'student_name' => "$student->firest_name . $student->last_name",
			'academy_name' => $academy->name ,
			'mark' => $mark ,
			'course_level' => $course['name'] ,
			'image' => $course->course_image ,
			'receive_date' => now()
		]);
		if ($mark<50)
		$message = " sorry you failed in this exams and your mark is $mark ";
		else $message = "good luck you passed this exam and your mark is $mark now you can show your certicficate in your profile" ;
		return response()->json([
			'status' => 200 ,
			'message' => $message 
		]);
	}

}
