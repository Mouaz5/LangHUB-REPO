<?php

namespace App\Http\Controllers\AcademyAdmin;

use App\Http\Controllers\Controller;
use App\Models\AcademyAdminstrator;
use App\Models\AcademyStudent;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use PDO;
use PHPUnit\Framework\Constraint\Count;

class AcademyAdminStudentController extends Controller
{
    public function student(Student $student){
        $student->load('user');
        return response()->json([
            'status' => 200,
            'message' => 'done successfully',
            'data' => $student 
        ]);
    }
    public function index(){
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();        
        $teachers = $admin
        ->academy()
        ->first()
        ->students()
        ->wherePivot('approved' , 1)
        ->get();
        return response()->json([
            'status' => 200 ,
            'message' => 'done successfully',
            'data' => $teachers
        ]);
    }
    public function showStudentRequests(){
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
        $students = $admin
        ->academy() 
        ->first()
        ->students()
        ->wherePivot('approved' , 0)
        ->get();

        $students = $students->map(function ($item) {
            $item['langauge'] = AcademyStudent::where('student_id', $item->id)
            ->where('academy_id', $item->pivot->academy_id)
            ->first()['language'];
            return $item;
        });
        return response()->json([
            'status' => 200 ,
            'message' => 'done successfully',
            'data' => $students
        ]);
    }
    public function acceptStudent(Student $student , Request $request){
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
        $academy = $admin->academy()->first();
        $academy->students()->updateExistingPivot($student->id , [
            'approved' => true 
        ]);
        
        return response()->json([
            'status' => 200 ,
            'message' => 'accepted successfully'
        ]);
    }
    public function rejectStudent(Student $student){
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
        $academy = $admin->academy()->first();
        $academy->students()->detach($student->id);
        return  response()->json([
            'status' => 200 ,
            'message' => 'deleted successfully'
        ]); 
    }
    public function addStudentToCourse(Course $course ,Student $student){
        if ($student->courses()->where('course_id', $course->id)->exists()){
            return response()->json([
                'status' => 201 ,
                'message' => 'this syudent already exist in this course'
            ]);
        }
        $student->courses()->attach($course->id);
        return response()->json([
            'statuse' => 200 ,
            'message' =>  'added successfully ' 
        ]);
    }
    public function removeStudentFromCourse(Course $course , Student $student){
        $student->courses()->detach($course->id);
        return response()->json([
            'status' => 200,
            'message' => 'deleted successfully'
        ]);
       
    }
    public function hasAcademy(){
        $user = User::where('id' , auth()->id())->first();
        if ($user->role()->first()['id'] != 2)return response()->json([
            'status' => 201 ,
            'message' => 'you are not admin'
        ]);
        if ($user->academyAdmin()->first()->academy()->first() != null){
            return response()->json([
                'status' => 200 ,
                'message' =>'have accademy' 
            ]);
        }
        if ($user->academyAdmin()->first()->academyPending()->first() != null ){
            return response()->json([
                'status' => 200 ,
                'message' =>'your request in progress' 
            ]);
        }
        return response()->json([
            'status' => 200 ,
            'message' => 'your academy has delete or your request not accepted'
        ]);
    }
}
