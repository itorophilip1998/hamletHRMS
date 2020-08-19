<?php

namespace App\Http\Controllers;

use App\User;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{


    public function addCompany(Request $request){
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized!'], 401);

         }
        $this->validate($request,[
            'company_name'=>'required',
            'company_address'=>'required',
            'company_email'=>'required',
            'company_phone'=>'required',
            'no_of_employees'=>'required',
            'city'=>'required',
            'state'=>'required',
            'zip_code'=>'required',
            'company_website'=>'required',
            'company_logo'=>'image|size:1024|mimes:jpeg,png,svg,jpg|nullable',
            'services'=>'required',
        ]);

        $id = User::where('id',Auth::user()->id)->pluck('id')->first();
        $company = new Company;
        $company->company_name = $request->input('company_name');
        $company->user_id = $id;
        $company->company_address = $request->input('company_address');
        $company->company_email = $request->input('company_email');
        $company->company_phone = $request->input('company_phone');
        $company->no_of_employees = $request->input('no_of_employees');
        $company->city = $request->input('city');
        $company->state = $request->input('state');
        $company->zip_code = $request->input('zip_code');
        $company->company_website = $request->input('company_website');
        $company->company_logo = $request->input('company_logo');
        $company->services = $request->input('services');

        if($request->hasFile('company_logo')){
            $file = $request->file('company_logo')->store('pictures','public');
            $image = Image::make(public_path("storage/{$file}"));
            $image->save();
            $company->company_logo = $file;
        }else{
            $company->company_logo = null;
        }

            $company->save();
            return response()->json([
                "status" => "success",
                "message" => "Company Details Added Successfully!"
              ], 200);
    }

}
