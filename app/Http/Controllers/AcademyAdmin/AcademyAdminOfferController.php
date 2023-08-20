<?php

namespace App\Http\Controllers\AcademyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcademyAdminstrator;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use PDO;

use function PHPSTORM_META\map;

class AcademyAdminOfferController extends Controller
{
    protected function uploadImage($request) {
		$image = $request->file('image');
		$imageName = time().$image->getClientOriginalName();
		$image->move(public_path('images'),$imageName);
        $imageUrl = asset('images/'.$imageName);
		return $imageUrl;
	}
    public function index(){
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
        $academy = $admin->academy()->first();
        $offers = $academy->offers()->get();
        foreach($offers as $offer){
            $offer->load('annualSchedules');
        }
        return response()->json([
            'status' => 200 ,
            'message' => 'done successfully',
            'data' => $offers
        ]);
    }
    public function addOffer(Request $request){
       $data =  $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'hours' => 'required|integer',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'description'=>'required|string',
            'image' => 'required',
            'seats' => 'required',
            'teacher_id'=>'required|integer',
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
        $imageUrl = '';
		if ($request->hasFile('image')) {
			$imageUrl = $this->uploadImage($request);
		}
        $data['image'] = $imageUrl ;
        $admin = AcademyAdminstrator::where('user_id' , auth()->id())->first();
        $academy = $admin->academy()->first() ;
        $offer = $academy->offers()->create($data , ['image' => $data['image']]);
        $offer->image = $data['image'] ;
        $offer->save();
        $offer->annualSchedules()->create($data);
        return response()->json([
            'status' => 200 ,
            'message' => 'done successfully',
            'data' => $data
        ]);
    }
    public function deleteOffer(Offer $offer){
        $admin =  AcademyAdminstrator::where('user_id' ,auth()->id())->first();
        $academy = $admin->academy()->first();
        if ($academy->id != $offer->academy_id){
            return response()->json([
                'status' => 201 ,
                'message' => 'you can not delet this offer , is not yours'
            ]);
        }
        $offer->delete();

        return response()->json([
            'status' => 200,
            'message' => 'deleted successfully'
        ]);
    }
}
