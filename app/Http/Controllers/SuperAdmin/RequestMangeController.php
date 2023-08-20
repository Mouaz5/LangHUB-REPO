<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcademyAdminstrator;
use App\Models\AcademyPending;
use App\Models\AcademyPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use PDO;

class RequestMangeController extends Controller
{
    public function acceptAcademy(AcademyPending $academyPending){
        AcademyPhoto::where('academy_pending_id' , $academyPending->id)->delete();
        // return $academyPending;
        $academy = Academy::create([
            'name' => $academyPending->name ,
            'description' => $academyPending->description,
            'location' => $academyPending->location,
            'image' => $academyPending->photo,
            'english' => $academyPending->english,
            'french'=>$academyPending->french,
            'spanish' => $academyPending->spanish,
            'germany' => $academyPending->germany ,
            'license_number' => $academyPending->license_number,
            'academy_adminstrator_id' => $academyPending->academy_adminstrator_id
        ]);
        
        AcademyPending::where('id' , $academyPending->id)->delete() ;
        return response()->json([
            'message' => 'academy added successfully',
            'status' => 200 ,
            'data' => $academy 
        ]);
    }
    public function RejectRequest(AcademyPending $academyPending){
        $admin = AcademyAdminstrator::where('id' , $academyPending->academy_adminstrator_id)->first();
        AcademyPhoto::where('academy_pending_id' , $academyPending->id)->delete();
        AcademyPending::where('id' , $academyPending->id)->delete() ;
    
        
        $admin->delete(); 
        return response()->json([
            'message'=> 'rejected successfully',
            'status' => 200 ,
        ]);
        

    }
    public function requests(){
            $academyPendings = AcademyPending::all();
            foreach ($academyPendings as $academyPending){
                $academyPending->load('photos');
                $academyPending['admin'] = AcademyAdminstrator::find( $academyPending->academy_adminstrator_id) ;
            }
            return response()->json([
                'status' =>200 ,
                'message' => 'done successflly',
                'data' => $academyPendings 
            ]);
    }
    public function academies(){
        $academies  = Academy::all();
        return response()->json([
            'status' => 200,
            'message' => 'done successfully',
            'data' => $academies
        ]);
    }
    public function deleteAcademy(Academy $academy){
        $academy1 = Academy::where('id' , $academy->id)->first();
        // return $academy1 ;
        $admin = $academy1->admin()->first();
        // return $academy ;
        // return $admin ;
        $user = $admin->user()->first() ;
       
        return response()->json([
            'status' => 200 ,
            'message' => 'deleted successfully',
        ]);
    }
}
