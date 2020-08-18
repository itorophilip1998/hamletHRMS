<?php

namespace App\Http\Controllers;

use App\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactInfoController extends Controller
{
    public function addContactInfo(Request $request){
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized!'], 401);

         }
        $this->validate($request,[
            'phone'=>'required',
            'email'=>'required',
            'emergency_contact'=>'required',
        ]);

        $contactInfo = new ContactInfo;
        $contactInfo->phone = $request->input('phone');
        $contactInfo->employee_id = $request->input('employee_id');
        $contactInfo->email = $request->input('email');
        $contactInfo->emergency_contact = $request->input('emergency_contact');

            $contactInfo->save();
            return response()->json([
                "status" => "success",
                "message" => "Contact Info Added Successfully!"
              ], 200);
    }
}
