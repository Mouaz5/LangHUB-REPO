<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AuthTeacherController extends Controller
{
    protected function uploadTeacherImage($request) {
		$image = $request->file('photo');
		$imageName = time().$image->getClientOriginalName();
		$image->move(public_path('teacher-images'),$imageName);
        $imageUrl = asset('teacher-images/'.$imageName);
		return $imageUrl;
	}

    public function register(Request $request) {
        $credentials = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string|unique:users,email',
            'phone_number' => 'required',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|min:5'
        ]);

        $imageUrl = '';
        if ($request->hasFile('photo')) {
            $imageUrl = $this->uploadTeacherImage($request);
        }
        $credentials['photo'] = $imageUrl;
        $user = User::query()->create([
            'role_id' => 3,
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
            'email_verified_at' => now()
        ]);

        $teacher = $user->teachers()->create($credentials);

        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $user['token_type'] = 'Bearer';
        return response()->json([
            'status' => 200,
            'message' => 'registeration done succefully',
            'teacher info' => $teacher,
            'token' => $token,
            'role' => 'teacher'
        ]);
    }
}
