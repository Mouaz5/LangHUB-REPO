<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherPost;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileTeacherController extends Controller
{
    protected function uploadImage($request) {
		$postImage = $request->file('image');
		$imageName = time().$postImage->getClientOriginalName();
		$postImage->move(public_path('teacher-posts'), $imageName);
		$imageUrl = asset('teacher-posts/'.$imageName);
		return $imageUrl;
	}

    //change Teacher Password
	public function changePassword(Request $request) {
		$teacher = User::where('id', auth()->id())->first();
	    $validatedData = $request->validate([
	        'current_password' => 'required|string',
	        'new_password' => 'required|string|min:8',
	    ]);
		
	    if (!Hash::check($validatedData['current_password'], $teacher->password)) {
	        return response()->json([
	        	'current_password' => 'The current password is incorrect'
	        ]);
	    }
	    $teacher->update(['password' => Hash::make($validatedData['new_password'])]);

	    return response()->json([
			'status' => 200,
			'message' => 'Password changed successfully'
	    ]);
	}
    //Show a student's profile
	public function show() {
		$teacher = Teacher::where('user_id', auth()->id())->first();
		$teacher['email'] = User::where('id' , auth()->id())->first()['email'];
		$posts = $teacher->posts()->get();
		$posts = $posts->map(function ($item) {
			return collect($item)->only(['id', 'title', 'image']);
		});
		$teacher = collect($teacher)->except(['created_at', 'updated_at', 'user_id']);
		$teacher['posts'] = $posts;
    	return response()->json([
			'status' => 200,
			'message' => 'teacher info',
    		'data' => $teacher
    	], 200);
	}
    //Update a student's profile
	public function update(Request $request) {
	    $validatedData = $request->validate([
	        'first_name' => 'required|string',
            'last_name' => 'required|string|unique:users,email',
            'phone_number' => 'required',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|min:5'
	    ]);
		$imageUrl = '';
		if ($request->hasFile('image')) {
			$imageUrl = $this->uploadImage($request);
		}
		
		$validatedData['photo'] = $imageUrl;
        $teacher = Teacher::where('user_id', auth()->id())->first();
    	$teacher->update($validatedData);

    	return response()->json([
			'status' => 200,
    		'success' => 'Profile updated successfully',
    		'teacher info' => $teacher
    	], 200);
	}

    public function uploadPost(Request $request) {
        $imageUrl = '';
		if ($request->hasFile('image')) {
			$imageUrl = $this->uploadImage($request);
		}
        $teacher = Teacher::where('user_id', auth()->id())->first();
        $post = $teacher->posts()->create([
            'title' => $request->title,
            'image' => $imageUrl
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'post added successfully',
            'data' => $post
        ]);
    }
    public function myPosts() {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        $posts = $teacher->posts()->get();
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $posts
        ]);
    }
	public function deletePost(TeacherPost $post) {
		$post->delete();
		return response()->json([
			'status' => 200,
			'message' => 'post deleted successfully'
		]);
	}
}
