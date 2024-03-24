<?php

namespace App\Http\Controllers;

use App\Mail\SendOTPMail;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserAuthController extends Controller
{
    public function CreateUser(Request $request) {

        try {

            $request->validate([
                'name' => 'required | string | max:100',
                'email' => 'required  | max:200 | email | unique:users,email',
                'password' => 'required | min:6',
            ]);

            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            $token = User::where('email', '=', $request->input('email'))
                         ->first()->createToken('token')->plainTextToken;  
            $userId = User::latest()->first()->id;
            Profile::create([ 'user_id' =>  $userId]);
                         
            return response()->json(["status" => "success", "message" => "User creation successful", "token" => $token], 201);
        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function LoginUser(Request $request) {

        try {

            $request->validate([
                'email' => 'required  | max:200 | email',
                'password' => 'required | min:6',
            ]);

            $user = User::where('email', '=', $request->input('email'))->first();

            if($user && Hash::check($request->input('password'), $user->password)) {
                $token = $user->createToken('token')->plainTextToken;  
                return response()->json(["status" => "success", "message" => "User login successful", "token" => $token]);
            }

        } catch(Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function SendOTP(Request $request) {

        try {

            $request->validate([ 'email' => 'required  | max:200 | email']);

            $user = User::where('email', '=', $request->input('email'))->first();

            if (!$user) {
                return response()->json(["status" => "faild", "message" => "invalid email"]);
            }

            $otp = rand(1000, 9999);
            $user->update(['otp' => $otp]);
            Mail::to($request->email)->send(new SendOTPMail($otp));

            return response()->json(["status" => "success", "message" => "OTP send successfull", "email" => $request->email]);
        } catch(Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function VerifyOTP(Request $request) {

        try {

            $request->validate([
                'email' => 'required  | max:200 | email',
                'otp' => 'required | min:4 | max:4',
            ]);

            $user = User::where('email', '=', $request->input('email'))
                        ->where('otp', '=', $request->input('otp'))->first();
            
            if (!$user) {
                return response()->json(["status" => "faild", "message" => "invalid user"]);
            } else if (Carbon::now() > $user->updated_at->addMinutes(3)) {

                $user->update(['otp' => null]);
                return response()->json(["status" => "faild", "message" => "time expired"]);
            }

            $token = $user->createToken('token')->plainTextToken;
            $user->update(['otp' => null]);
            
            return response()->json(["status" => "success", "message" => "OTP verified successfull", "token" => $token]);
        } catch(Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }
    
    public function ResetPassword(Request $request) {

        try {

            $request->validate(['password' => 'required|string|min:6']);

            $id = Auth::id();
            User::where('id', '=', $id)->update(['password' => Hash::make($request->password)]);
            
            return response()->json(["status" => "success", "message" => "Password reset successful"]);

        } catch(Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function LogoutUser(Request $request) {

        try {

            Auth::user()->currentAccessToken()->delete();
            return response()->json(["status" => "success", "message" => "User logout successful"]);
        } catch(Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

}
