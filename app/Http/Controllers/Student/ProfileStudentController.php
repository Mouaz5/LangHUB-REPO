<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;

class ProfileStudentController extends Controller
{
    //change Student Password
	public function changePassword(Request $request) {
        $student = User::where('id', auth()->id())->first();
	    $validatedData = $request->validate([
	        'current_password' => 'required|string',
	        'new_password' => 'required|string|min:8'
	    ]);
	    if (!Hash::check($validatedData['current_password'], $student->password)) {
	        return response()->json([
				'status' => 205,
	        	'message' => 'The current password is incorrect',
	        ]);
	    }
	    $student->update(['password' => Hash::make($validatedData['new_password'])]);
	    return response()->json([
			'status' => 200,
	    	'message' => 'Password changed successfully'
	    ]);
	}
    //Show a student's profile
	public function show() {
        $student = Student::where('user_id', auth()->id())->first();
		$student['email'] = User::where('id' , auth()->id())->first()['email'] ;
		return response()->json([
			'status'=>200 ,
			'message'=>'login done seccusfully',
    		'student' => $student 
    	]);
	}
    //Update a student's profile
	public function update(Request $request) {
	    $validatedData = $request->validate([
	        'first_name' => 'nullable|string|max:255',
			'last_name'=>'nullable|max:20' ,
			'phone_number'=>'nullable',
			'photo'=> 'nullable|image' ,
	        // other fields to validate
	    ]);
        $student = Student::where('user_id', auth()->id())->first();
    	$student->update($validatedData);
    	return response()->json([
			'status'=>200 ,
    		'message' => 'Profile updated successfully',
    		'student' => $student
    	]);
	}
	public function certificats(){
		
		$student = Student::where('user_id' ,auth()->id())->first();
		$cerificates = $student->certificates()->get();
		return response()->json([
			'statuse'=>200 ,
			'message' => 'done successfully',
			'data' => $cerificates
		]);
	}
}
