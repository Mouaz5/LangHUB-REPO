<?php

namespace App\Http\Controllers\AcademyAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AcademyAdminstrator;
use App\Models\Academy;
use App\Models\AcademyTeacher;
use App\Models\Teacher;


use App\Models\AcademyPhoto;
use App\traits\imageTrait;




use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 

class NourAuthAcademyAdminController extends Controller
{
    // use imageTrait;
    public function register(Request $request) {
        // return response($request,200);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required',
            'phone' => 'required|string',
            'institute_name' => 'required|string',
            'place_institute' => 'required|string',
            'license_number' => 'required',
            'description' => 'required|string',
            'images' => 'required',
            'english' =>'required|boolean',
            'germany' => 'required|boolean',
            'spanish' => 'required|boolean',
            'french' => 'required|boolean',
            // 'type of courses' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'role_id' => 2,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if(!$user){
            return response("user register failed.",400);
        }
        $user_id= $user->id;
        // return response($request,200);
        $academyAdminstrator = AcademyAdminstrator::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone,
            'user_id' => $user_id,
        ]);

        if(!$academyAdminstrator){
            return response("academy Adminstrator failed.",400);
        }

        $adminstrator_id =   $academyAdminstrator ->id;
        $academy = Academy::create([
            'name' => $request->name_academy,
            'description' => $request->description,
            'location' => $request->place_institute,
            'license_number' => $request->license_number,
            'english' => $request->english,
            'germany' => $request->germany,
            'spanish' => $request->spanish,
            'french' => $request->french,
            // 'image' => $request->image,
            'academy_adminstrator_id' => $adminstrator_id,
        ]);
        $path = 'academy-images';
        $academy_id =  $academy ->id;
        $images_temp = ['arr.png','brr.png'];
        $i=0;
        foreach($images_temp as $image){
        
            // $image_name = $this->saveImage($image, $path);
            $images[]=[
            'image' => $image,
            'academy_pending_id' => 1,
            ];
            $i++;
     }

     $academyPhoto = AcademyPhoto::insert($images);
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $user['token_type'] = 'Bearer';
        $response = [
            'status' => true,
            'message' => 'Registration success',
            'token' => $token,
            'role' => $user->role['name'],
        ];
        return response($response,200);
    }

    // login
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [            
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->input();
        
        if(!Auth::attempt(['email' => $data['email'], 'password' => $data['password'],'role_id' => 2]))
            return response("verify your email or password or auth",400);
        
        else
        {
        // get User
        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $user['token_type'] = 'Bearer';

        }
        $response = [
            'status' => true,
            'message' => 'Registration success',
            'token' => $token,
            'role' => $user->role['name'],

        ];
        
        if ($user)
        return response($response,200);
        else
            return response("login failed.",400);

    }

    public function get_profile(Request $request){

        // get User
           $user = Auth::user();
           $academyAdmin = AcademyAdminstrator::where('user_id', $user->id)->first();
           $academy = Academy::where('adminstrator_id', $academyAdmin->id)->with('photos')->first();

        //    $academy =  $academyAdmin->academy();
       
           $response = [
            //    'user' => $user,
               'academyAdmin'=>$academyAdmin,
               'academy'=>$academy,
              
           ];
        
           if ($user)
           return response($response,200);
           else
               return response("not exist.",400);
       
    }


    public function show_joining_request_teachers(Request $request){

            // get User
           $user = Auth::user();
           $academyAdmin = AcademyAdminstrator::where('user_id', $user->id)->first();
           $academy = Academy::where('adminstrator_id', $academyAdmin->id)->with('photos')->first();
           $academyTeachers = AcademyTeacher::where('academy_id', $academy->id)->where('approved' , 0)->get();
           $teachers= [] ;
           foreach($academyTeachers as $teacher){
            $teachers[] = Teacher::where('id' , $teacher->teacher_id)->get();
            hasAcademy
            }



           $response = [
         
               'teachers'=>$teachers,
              
           ];
               
               if ($user)
               return response($response,200);
               else
                   return response("not exist.",400);
           
    }




}




