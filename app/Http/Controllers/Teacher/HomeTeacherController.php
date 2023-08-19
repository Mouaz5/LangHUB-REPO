<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Course;
use App\Models\Academy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\RateController;

class HomeTeacherController extends Controller
{
        /*protected function uploadImages(Request $request) {

        foreach ($request->file('images') as $image) {
            $imageName = time().$image->getClientOriginalName();
            $image->move(public_path('images'), $image);
            $imageUrl = "teacher/images/$imageName";
            return $imageUrl;
            //$path = $image->store('public/images');
        }
        return response()->json([
            'status' => 200,
            'message' => 'success - Images uploaded successfully',
            'data' => [],
        ]);
    }*/

    public function test() {
        $academies = Course::with('rate');
        return $academies;
    }
    public function academySearching(Request $request) {
        $request->validate([
            'search_key' => 'required'
        ]);
        $academiesByName = Academy::where('name' , 'like' , "%$request->search_key%")
        ->get();
        $academiesByLocation = Academy::where('location' , 'like' , "%$request->search_key%")
        ->get();
        $mergedAcademies = $academiesByName->merge($academiesByLocation);
        $mergedAcademies = $mergedAcademies->map(function ($item) {
            $item['rate'] = RateController::getAcademyRate($item);
            return $item;
        });
        return response()->json([
            'status' => 200,
            'message' => 'done succefully',
            'search teacher' => $mergedAcademies
        ]);
    }
}
