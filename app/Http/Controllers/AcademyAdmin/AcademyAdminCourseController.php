<?php

namespace App\Http\Controllers\AcademyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcademyAdminstrator;
use App\Models\AcademyTeacher;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AcademyAdminCourseController extends Controller
{
    protected function uploadImage($request) {
		$image = $request->file('course_image');
		$imageName = time().$image->getClientOriginalName();
		$image->move(public_path('images'),$imageName);
        $imageUrl = asset('images/'.$imageName);
		return $imageUrl;
	}

    //AcademyAdmin can create courses
    public function addCourse(Request $request) {
        $validatedData = $request->validate([
			'name'=> 'required',
	        'description' => 'required|string',
            'hours' => 'required|integer',
			'language' => 'required|string'
			// other fields to validate
	    ]);
		
		$imageUrl = '';
		if ($request->hasFile('course_image')) {
			$imageUrl = $this->uploadImage($request);
		}
		if ($request->hours < 0) {
			return response()->json([
				'status' => 205,
				'message' => 'the selected value for hours invalid',
				'data' => []
			]);
		}
		$admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
		$academy = $admin->academy()->first();
		if ($request->language == 'english'&&$academy->english == false)
		return response()->json([
			'status' => 201 ,
			'message' => 'your academy dont have a english',
			'data' => []
		]);
		if ($request->language == 'french'&&$academy->french == false)
		return response()->json([
			'status' => 201 ,
			'message' => 'your academy dont have a french',
			'data' => []
		]);
		if ($request->language == 'spanish'&&$academy->spanish == false)
		return response()->json([
			'status' => 201 ,
			'message' => 'your academy dont have a spanish',
			'data' => []
		]);
		if ($request->language == 'germany'&&$academy->germany == false)
		return response()->json([
			'status' => 201 ,
			'message' => 'your academy dont have a germany',
			'data' => []
		]);
		$course = $academy->courses()->create($validatedData + [
			'course_image' => $imageUrl,
		]);
		// create a defualt group with this course
		$course->group()->create([
			'name' => $validatedData['name'],
		]);

    	return response()->json([
    		'status' => 200 ,
			'message' => 'Course created successfully',
    		'data' => $course
    	]);
	}
	public function activeCourse(Course $course ,Request $request){
	$data  = $request->validate([
			'seats' => 'required|integer',
			'start_date'=>'required|date',
			'end_date'=>'required|date',
			'price'=>'required|integer',
			'teacher_id' => 'required|integer',
			'saturday'=>'required', 
			'start_saturday'=>'required', 
			'end_saturday'=>'required',
			'sunday'=>'required', 
			'start_sunday'=>'required', 
			'end_sunday'=>'required',
			'monday'=>'required', 
			'start_monday'=>'required', 
			'end_monday'=>'required',
			'tuesday'=>'required', 
			'start_tuesday'=>'required', 
			'end_tuesday'=>'required',
			'wednsday'=>'required', 
			'start_wednsday'=>'required', 
			'end_wednsday'=>'required',
			'thursday'=>'required', 
			'start_thursday'=>'required', 
			'end_thursday'=>'required',
			'friday'=>'required', 
			'start_friday'=>'required', 
			'end_friday'=>'required',
			
		]);
		if ($request->price < 0 || $request->price > 100) {
			return response()->json([
				'status' => 205,
				'message' => 'the selected value of price is invalid',
				'data' => []
			]);
		}
		
		$course->seats = $request->seats;
		$course->start_date = $request->start_date;
		$course->end_date = $request->end_date;
		$course->price = $request->price;
		$course->active = true ;
		$course->teacher_id = $request->teacher_id;
		$course->save();
		$course->annualSchedule()->create($data); 
		return response()->json([
			'status' => 200 ,
			'message' => 'done successfully',
			'data' => $course
		]);
	}
	
    // update Course Information
	public function update(Request $request, Course $course) {
	    $validatedData = $request->validate([
	        'title' => 'required|string|max:255',
	        'description' => 'required|string',
            'price' => 'required|integer',
			'hours' => 'required|integer',
			'seats' => 'required|integer',
			'course_image' => 'required|image',
	        // other fields to validate
	    ]);
    	$course->update($validatedData);
		
    	return response()->json([
    		'success' => 'Course updated successfully',
    		'course' => $course
    	]);
	}

	public function addCourseSchedule(Request $request, Course $course) {
		$validatedData = $request->validate([
	        'day' => 'required|string',
			'start_time' => 'required|timezone',
			'end_time' => 'required|timezone',
			'date' => 'required|date'
	    ]);
		$schedule = $course->schedules()->create($validatedData);
		return response()->json([
			'message' => 'success',
			'status' => '200',
			'data' => $schedule,
		]);
	}
	///////////////
	public function inactiveCourses(Request $request){
		$request->validate([
			'language' => 'required',
		]);
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
        $academy = $admin->academy()->first();
		// return $academy ;
        $courses = $academy->courses()->where('active' , false)
		->where('language', '=' , $request->language)
		->get();
		
        return response()->json([
            'status' => 200 ,
            'message' => 'done successfully' ,
            'data' => $courses
        ]);
    }
	public function activeCourses(Request $request){
		$request->validate([
			'language' => 'required',
		]);
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
        $academy = $admin->academy()->first();
        $courses = $academy->courses()->where('active' , true)
		->where('language' , $request->language)
		->get();
        return response()->json([
            'status' => 200 ,
            'message' => 'done successfully' ,
            'data' => $courses
        ]);
    }
	public function teachersForCourse(Course $course , Request $request){
		$courseTime  = $request->validate([
			'saturday'=>'required', 
			'start_saturday'=>'required', 
			'end_saturday'=>'required',
			'sunday'=>'required', 
			'start_sunday'=>'required', 
			'end_sunday'=>'required',
			'monday'=>'required', 
			'start_monday'=>'required', 
			'end_monday'=>'required',
			'tuesday'=>'required', 
			'start_tuesday'=>'required', 
			'end_tuesday'=>'required',
			'wednsday'=>'required', 
			'start_wednsday'=>'required', 
			'end_wednsday'=>'required',
			'thursday'=>'required', 
			'start_thursday'=>'required', 
			'end_thursday'=>'required',
			'friday'=>'required', 
			'start_friday'=>'required', 
			'end_friday'=>'required',
		]);
		$academy = Academy::where('id' , $course->academy_id)->first();
		$teachers = $academy->teachers()->get() ;
		$data = [];
		
		// return $teachers ;
		if(count($teachers) == 0) return response()->json([
			'status' => 200 ,
			'message' => 'there is no teacher in this academy' ,
			'data' => $teachers
		]);
		foreach($teachers as $teacher){
			return "ppp";
			if ($course == null)echo " null " ;
			
			if($this->checkTime( $course,$courseTime , $teacher)){
				$data [] = $teacher ;
			}
		}
		if (count($data) == 0 ){
			$data = $teacher ;
			return response()->json([
				'status' => 200 ,
				'message' => 'there is no teachers have common scuduales so this is list of all teachers',
				'data' => $data 
			])	;
		}
		return response()->json([
			'status' => 200 ,
			'message' => 'those teachers can teach in this course',
			'data' => $data  
		]);
	} 
	
	protected function checkTime(Course $course, $courseTime , Teacher $teacher):bool
	{
		$teacherAcademy = AcademyTeacher::where('teacher_id' , $teacher->id)
		->where('academy_id',$course->academy_id)
		->first();
	
		$teacherTime = $teacherAcademy->schedules()->first();
		if ($teacherTime === null) {
			return false;	
		}
		if ($courseTime == null) {
			return 0 ;
		}
		//saturday
		if ($courseTime['saturday'] == 1 && $teacherTime->saturday == 0)
		return false ;
		//sunday
		if ($courseTime['sunday'] == 1 && $teacherTime->sunday == 0)
		return false ;
		//monday
		if ($courseTime['monday'] == 1 && $teacherTime->monday == 0)
		return false ;
		//tuesday
		if ($courseTime['tuesday'] == 1 && $teacherTime->tuesday == 0)
		return false ;
		//wednsday
		if ($courseTime['wednsday'] == 1 && $teacherTime->wednsday == 0)
		return false ;
		//thursday
		if ($courseTime['thursday'] == 1 && $teacherTime->thursday == 0)
		return false ;
		//friday
		if ($courseTime['friday'] == 1 && $teacherTime->friday == 0)
		return false ;
		//saturday
		if ($courseTime['saturday'] == 1 && $teacherTime->saturday == 1)
			if (($courseTime['start_saturday'] < $teacherTime->start_saturday||
				$courseTime['end_saturday'] > $teacherTime->end_saturday)
			){
				return false ;
			}
		//sunday
		if ($courseTime['sunday'] == 1 && $teacherTime->sunday == 1)
			if (($courseTime['start_sunday'] < $teacherTime->start_sunday||
				$courseTime['end_sunday'] > $teacherTime->end_sunday)
			)return false ;
		//monday	
		if ($courseTime['monday'] == 1 && $teacherTime->monday == 1)
			if (($courseTime['start_monday'] < $teacherTime->start_monday||
				$courseTime['end_monday'] > $teacherTime->end_monday)
			)return false ;
		//tuesday
		if ($courseTime['tuesday'] == 1 && $teacherTime->tuesday == 1)
			if (($courseTime['start_tuesday'] < $teacherTime->start_tuesday||
				$courseTime['end_tuesday'] > $teacherTime->end_tuesday)
			)return false ;
		//wednsday
		if ($courseTime['wednsday'] == 1 && $teacherTime->wednsday == 1)
			if (($courseTime['start_wednsday'] < $teacherTime->start_wednsday||
				$courseTime['end_wednsday'] > $teacherTime->end_wednsday)
			)return false ;
		//thursday
		if ($courseTime['thursday'] == 1 && $teacherTime->thursday == 1)
			if (($courseTime['start_thursday'] < $teacherTime->start_thursday||
				$courseTime['end_thursday'] > $teacherTime->end_thursday)
			)return false ;
		//friday
		if ($courseTime['friday'] == 1 && $teacherTime->friday == 1)
			if (($courseTime['start_friday']< $teacherTime->start_friday||
				$courseTime['end_friday'] > $teacherTime->end_friday)
			)return false ;
						
		return true ;
	}
	public function showCourse(Course $course){
		$data = $course->load(['students' , 'teacher' , 'annualSchedule']);
		return response() -> json([
			'message' => 'done successflly',
			'status' => 200 ,
			'data' => $data 
		]);
	}
	public function deleteCourse(Course $course){
		$admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
		$academy = $admin-> academy()->first() ;
		// return $academy ;
		if ($course->academy_id != $academy->id){
			return response()->json([
				'message' => 'you can not deleted this course ' ,
				'status' => 201 
			]);
		}
		$course->delete();
		return response()->json([
		'message' => 'deleted successfully',
		'status'=> 200
	]);
	}


}
