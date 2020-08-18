<?php

namespace App\Http\Controllers;

use App\User;
use App\Employee;
use App\Mail\signinMail;
use Illuminate\Http\Request;
use App\Mail\addEmployeeMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class EmployeeController extends Controller
{
    public function getEmployees() {
        $employees = Employee::all();
        return response()->json($employees, 200);
    }

    public function addEmployee(Request $request){
        if (!Auth::check()) {
           return response()->json(['message' => 'Unauthorized!'], 401);

        }

        $this->validate($request,[
            'first_name'=>'required',
            'other_names'=>'required',
            'gender'=>'required',
            'dob'=>'required',
            'address'=>'required',
            'city'=>'required',
            'age'=>'required',
            'qualification'=>'required',
            'profile_pic'=>'image|mimes:jpeg,png|nullable',
        ]);

        $id = User::where('id',Auth::user()->id)->pluck('id')->first();
        $employee = new Employee;
        $employee->first_name = $request->input('first_name');
        $employee->other_names = $request->input('other_names');
        $employee->user_id = $id;
        $employee->gender = $request->input('gender');
        $employee->dob = $request->input('dob');
        $employee->address = $request->input('address');
        $employee->city = $request->input('city');
        $employee->age = $request->input('age');
        $employee->qualification = $request->input('qualification');

        if($request->hasFile('profile_pic')){
            $file = $request->file('profile_pic')->store('pictures','public');
            $image = Image::make(public_path("storage/{$file}"));
            $image->save();
            $employee->profile_pic = $file;
        }else{
            $employee->profile_pic = null;
        }


            $employee->save();
            try{
                Mail::to($request->email)->send(new addEmployeeMail($request->all()));
               }catch(\Exception $error)
               {
                //  code here..
               }
            return response()->json([
                "status" => "success",
                "message" => "Employee Added Successfully!", $employee
              ], 200);
    }

    public function getEmployee($id){
        if (Employee::where('id', $id)->exists()) {
            $employee = Employee::where('id', $id)->get();
            return response()->json($employee, 200);
          } else {
            return response()->json([
              "message" => "Employee not found"
            ], 404);
          }
        }

    public function updateEmployee(Request $request, $id){
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized!'], 401);

         }
        $this->validate($request,[
            'first_name'=>'required',
            'other_names'=>'required',
            'gender'=>'required',
            'dob'=>'required',
            'address'=>'required',
            'city'=>'required',
            'age'=>'required',
            'qualification'=>'required',
            'profile_pic'=>'image|mimes:jpeg,png|nullable',
        ]);

        if (Employee::where('id', $id)->exists()) {
            $employee = Employee::find($id);

            $employee->first_name = $request->input('first_name');
        $employee->other_names = $request->input('other_names');
        $employee->gender = $request->input('gender');
        $employee->dob = $request->input('dob');
        $employee->address = $request->input('address');
        $employee->city = $request->input('city');
        $employee->age = $request->input('age');
        $employee->qualification = $request->input('qualification');

        if($request->hasFile('profile_pic')){
            $file = $request->file('profile_pic')->store('pictures','public');
            $image = Image::make(public_path("storage/{$file}"));
            $image->save();
            $employee->profile_pic = $file;
        }else{
            $employee->profile_pic = null;
        }

        $data = array(
            'first_name' => $employee->first_name,
            'other_names' => $employee->other_names,
            'gender' => $employee->gender,
            'dob' => $employee->dob,
            'address' => $employee->address,
            'city' => $employee->city,
            'age' => $employee->age,
            'qualification' => $employee->qualification,
        );

        }

        Employee::where('id', $id)->update($data);
        $employee->update();

            return response()->json([
                "status" => "success",
                "message" => "Employee Updated Successfully!", $employee
              ], 200);

    }


}
