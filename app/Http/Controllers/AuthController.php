<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Otp;
use App\Models\UserActivities;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request){
        $this->validate($request,[
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'dob' => 'date|required',
            'gender' => 'required|string',
            'email' => 'string|required',
            'password' => 'string|required|min:6',
            "role" => "string|required"
        ]);

        $email = User::where('email',$request->email)->exists();
        if($email){
            return response()->json([
                "message" => "Email already exists"
            ],409);

        }else{

            $role = Role::where('name',$request->role)->first();

            $user = new User;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->dob = $request->dob;
            $user->gender = $request->gender;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->status = $request->status ?? "Active";
            $user->role_id = $role->id;
            $user->save();
            $token = $user->createToken('authToken')->accessToken;
            return response()->json([
                "message" => "User added successfully",
                "token" => $token,
                "data" => $user
            ],201);
        }

    }

    public function login(Request $request){
        $this->validate($request,[
            'email' => 'string|required',
            'password' => 'string|required|min:6',
        ]);

        $user = User::where('email',$request->email)->first();
        if($user && isset($user->email)){
           if(Hash::check($request->password,$user->password)){
            $token = $user->createToken('authToken')->accessToken;
            return response()->json([
                "message" => "Logged in successfully",
                "token" => $token
            ],200);
           }else{
            return response()->json([
                "message" => "Invalid password"
            ],400);
           }
        }else{
            return response()->json([
                "message" => "Email doesn't exists"
            ],409);
        }
    }

    public function forget_password(Request $request){
        $this->validate($request,[
            "email" => "required|string"
        ]);

        $user = User::where('email',$request->email)->where('status','active')->first();
        if(isset($user->id)){
            $otp = rand(1000,9999);
            $rs= new Otp;
            $rs->otp = $otp;
            $rs->email = $request->email;
            $rs->save();
            return response()->json([
                "message" => "Otp sent",
                "otp" => $otp
            ],200);
        }else{
            return response()->json([
                "message" => "Email doesn't exists"
            ],400);
        }
    }

    public function reset_password(Request $request){
        $this->validate($request,[
            "password" => "required|string|min:6",
            "otp" => "required"
        ]);

        $otp = otp::where('otp',$request->otp)->first();
        if(isset($otp->email)){
            $user = User::where('email',$otp->email)->where('status','active')->first();
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                "message" => "Password reset successfully"
            ],200);
        }else{
            return response()->json([
                "message" => "Invalid otp"
            ],400);
        }
    }
}
