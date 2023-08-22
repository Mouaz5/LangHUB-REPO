<?php

namespace App\Http\Controllers\AcademyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcademyAdminstrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Psy\CodeCleaner\ReturnTypePass;

class AcademyAdminProfilecontroller extends Controller
{
    public function show(){
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())
        ->with('user')->first();
		$data['personal_info'] = $admin ;
		$data['academy_info'] = $admin->academy()->first(); 
        return response()->json([
			'status' => 200 ,
			'message' => 'get successfully',
			'data' => $data ,
		]) ; 
    }
    public function update(Request $request){
		
        $personalInfo = $request->validate([
			'personal_info.first_name'=>'nullable', 
			'personal_info.user_id'=>'nullable' ,
			'personal_info.last_name'=>'nullable' ,
			'personal_info.phone_number'=>'nullable' , 
			'personal_info.photo'=>'nullable' 
	        // other fields to validate
	    ]);
		$academyInfo =$request->validate([
        'academy_info.name'=>'nullable', 
		'academy_info.description'=>'nullable', 
		'academy_info.approved'=>'nullable', 
		'academy_info.location'=>'nullable', 
		'academy_info.license_number'=>'nullable', 
		'academy_info.delete_time' =>'nullable',
		'academy_info.english'=>'nullable', 
		'academy_info.french'=>'nullable', 
		'academy_info.spanish'=>'nullable', 
		'academy_info.germany'=>'nullable'
	    ]);
		$email = $request->validate([
			'email' => 'nullable|email'
		]);
		$user = User::where('id',auth()->id())->first();
		$user->update($email) ;
        $admin = $user->academyAdmin()->first();
    	$admin->update($personalInfo['personal_info']);
		$academ = $admin->academy()->first() ;
		$academ->update($academyInfo['academy_info']) ;
    	$data['personal_info'] =$admin ;
		$data['academy_info'] = $academ ; 
		$data['email'] = $user->email ;
		return response()->json([
			'status'=>200 ,
    		'message' => 'Profile updated successfully',
    		'data' => $data
    	]);
    }
    public function changePassword(Request $request) {
        $admin = User::where('id', auth()->id())->first();
        // return $student ;
	    $validatedData = $request->validate([
	        'current_password' => 'required',
	        'new_password' => 'required|string|min:8'
	    ]);
	    if (!Hash::check($request->current_password, $admin->password)) {
	        return response()->json([
				'status' => 205,
	        	'message' => 'The current password is incorrect',
	        ]);
	    }
	    $admin->update(['password' => Hash::make($validatedData['new_password'])]);
	    return response()->json([
			'status' => 200,
	    	'message' => 'Password changed successfully'
	    ]);
	}
	public function uodateRequestTime(Request $request){
		$request->validate([
			'request_time'=> 'required'
		]);
		$admin = AcademyAdminstrator::where('user_id' ,auth()->id())->first() ;
		$academy = $admin->academy()->first() ;
		$academy->delete_time = $request->request_time;
		$academy->save();
		return response()->json([
			'status' => 200 ,
			'message' => 'dene successfully',
			'data' => $academy->delete_time
		]);
	}

	public function feedBack (){
		$data = user::where('id' , auth() -> id())
		->first()
		->academyAdmin()
		->first()
		->academy()
		->first()
		->feedbacks()
		->get();
		return $data ;
	}
	public function accepted(){
		$admin =  AcademyAdminstrator::where('user_id' , auth()->id())->first();
		$academy = $admin->academy()->first();
		
		if ($academy == null )
			return response()->json(["message"=>"waiting"])  ;
		else return response()->json(["message"=>"approved"]);
	}
	
}
