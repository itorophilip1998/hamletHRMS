<?php

namespace App\Http\Controllers;

use App\User;
use App\Mail\signinMail;
use App\Mail\signupMail;
use App\Mail\signoutMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register','getAuthUser']]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|confirm|min:8|max:255',

        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()
            ], 200);
        }

        $user = new User;
        $user->fill($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        $token = auth()->login($user);
           try{
            Mail::to($request->email)->send(new signupMail($request->all()));
           }catch(\Exception $error)
           {
            //  code here..
           }
          return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()
            ], 200);
        }

      $credentials = $request->only(['email', 'password']);

      if (!$token = auth()->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
      }

      try{
        Mail::to($request->email)->send(new signinMail($request->all()));
       }catch(\Exception $error)
       {
        //  code here..
       }
      return $this->respondWithToken($token);
    }

    public function getAuthUser()
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized!'], 401);

         }
        $user = User::where('id',Auth::user()->id)
        ->with('company')
        ->with('profile')
        ->with('employees')
        ->with('employees.jobDetails')
        ->with('employees.contactInfo')
        ->first();
        return response()->json($user, 200);
    }

    public function logout() { 
        auth()->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'User successfully signed out'
        ], 200);
    }

    protected function respondWithToken($token)
    {
      return response()->json([
        'token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60,
        'user' => auth()->user()
      ]);
    }
}
