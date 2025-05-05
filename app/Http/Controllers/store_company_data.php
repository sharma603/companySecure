<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolDetails;

class store_school_data extends Controller
{
   public function SchoolData(Request $request)
   {
     $school_name = $request->school_name;
     $school_contact = $request->school_contact;
     $school_address = $request->school_address;
     $school_school_url = $request->school_url;

     $schoolDetails = new SchoolDetails;
     $schoolDetails->school_name=$school_name;
     $schoolDetails->contact_number=$school_contact;
     $schoolDetails->Address=$school_address;
     $schoolDetails->url=$school_school_url;
    
    if($schoolDetails->save() )
    {
        echo "save";

    }else{
      echo "not save";
    } 
     




   }

   public function SchoolGet(){
      $SchoolDetails = SchoolDetails::get();
      if($SchoolDetails){
        return response()->json([
          "schoolDetails"=>$SchoolDetails

        ]);
      }
   }
   public function SchoolDelete(Request $request){
      $school_id =  $request->id;
      if(SchoolDetails::where('id',$school_id)->delete()){
        echo 'delete';
      }else {
        echo 'not delete';
      }

   }
   // school update function
    public function SchoolUpdate(Request $request){
       
    
      echo $request->school_name;

      // $school_id = $request->id;
      // echo $school_id;
      // $school_id 

    //  if(SchoolDetails::where('id',$school_id)->update())
    //  {
    //    echo 'update';
    //  }else{
    //    echo 'not update';
    //  }
    }

    protected function userExists($userId)
    {
        return \App\Models\User::where('id', $userId)->exists();
    }




}
