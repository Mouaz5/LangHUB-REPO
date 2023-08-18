<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\RateController;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherPost;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileTeacherController extends Controller
{
    protected function uploadImage($request) {
		$courseImage = $request->file('image');
		$imageName = time().$courseImage->getClientOriginalName();
		$courseImage->move(public_path('course-images'), $courseImage);
		$imageUrl = asset("teacher/post-images", $imageName);
		return $imageUrl;
	}

    //change Teacher Password
	public function changePassword(Request $request) {
		$teacher = Teacher::where('user_id', auth()->id())->first();
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
	        'name' => 'required|string|max:255',
	        'email' => 'required|string|email|max:255|unique:students,email,'
	        // other fields to validate
	    ]);
        $teacher = Teacher::where('user_id', auth()->id())->first();
    	$teacher->update($validatedData);

    	return response()->json([
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
