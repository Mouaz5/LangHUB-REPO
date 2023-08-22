<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseStudent ;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Rate ;

class LessonStudentController extends Controller
{
    //retrieve all the lessons that are available to a particular student
    public function lessons(Course $course) {
          
            $student_id = Student::where('user_id' , auth()->id())->first()['id'];
            
            $test = CourseStudent::where('student_id' , $student_id)
            ->where('course_id' , $course->id)
            ->first() ;
       
            if ($test == null )
            return response()->json([
                'status' =>  201 ,
                'message' => 'your are not enrolled in this course' 
            ]);
            $lessons =  $course->lessons()
            ->get()    ;
            return response()->json([
                'status' => 200 ,
                'message' => 'done successfully' ,
                'data' => $lessons
            ]);
        }

    

    public function show(Lesson $lesson) {
        return response()->json([
			'lesson' => $lesson
		]);
    }


    
}
