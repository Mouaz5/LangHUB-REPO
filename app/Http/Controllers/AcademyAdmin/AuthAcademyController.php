<?php

namespace App\Http\Controllers\AcademyAdmin;

use App\Http\Controllers\Controller;
use App\Models\AcademyPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthAcademyController extends Controller
{
    protected function uploadImages($request) {
		$images = $request->file('photos');
        $urls = [] ;$i=0;
        foreach($images as $image){
            $imageName = time().$image->getClientOriginalName();
            $image->move(public_path('images'),$imageName);
            $urls [] = asset('images/'.$imageName);
            
        }
		return $urls;
	}
    protected function uploadImage($request) {
		$image = $request->file('photo');
		$imageName = time().$image->getClientOriginalName();
		$image->move(public_path('images'),$imageName);
        $imageUrl = asset('images/'.$imageName);
		return $imageUrl;
	}
    public function register(Request $request) {
   
        $credentials = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string',
            'name' => 'required|string',
            'location' => 'required|string',
            'license_number' => 'required',
            'description' =>  'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required',
            'photo' => 'required|image'
        ]);
        

        $user = User::query()->create([
            'role_id' => 2,
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
            'email_verified_at' => now()
        ]);
        $admin = $user->academyAdmin()->create($credentials);
        $en = false;
        $fr = false; 
        $sp = false;
        $ge = false;
        if ($request->english == true) $en =true; 
        if ($request->french == true) $fr = true;
        if ($request->spanish == true) $sp =true;
        if ($request->germany == true) $ge = true;
        $basePhoto = $this->uploadImage($request);
        $pendigAcademy = $admin->AcademyPending()->create([
            'name' => $request->name,
            'location' => $request->location,
            'license_number' => $request->license_number,
            'description' =>  $request->description,
            'photo' => $basePhoto,
            'english' => $en, 
            'french' => $fr,
            'spanish' => $sp,
            'germany' => $ge,
        ]);
        
        $photos = [] ;
        if ($request->hasFile('photos')) {
            $photos = $this->uploadImages($request) ;
            
        }
        foreach ($photos as $photo){
            $pendigAcademy->photos()->create([
                'image'=>$photo
            ]);
        }
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $user['token_type'] = 'Bearer';
        $response = response()->json([
            'status'=>true,
            'massage' => 'registeration done seccesfully',
            'data' => $admin,
            'token' => $token,
        ]);
        return $response;
    }
    //
}

