<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function addProfile(Request $request){
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized!'], 401);

         }

        $this->validate($request,[
            'first_name'=>'required',
            'last_name'=>'required',
            'address'=>'required',
            'profile_pic'=>'image|mimes:jpeg,png,svg|nullable',
        ]);

        $id = User::where('id',Auth::user()->id)->pluck('id')->first();

        $profile = new Profile;
        $profile->first_name = $request->input('first_name');
        $profile->user_id = $id;
        $profile->last_name = $request->input('last_name');
        $profile->address = $request->input('address');


            $profile->save();
            return response()->json([
                "status" => "success",
                "message" => "Profile Added Successfully!"
              ], 200);
    }
}
