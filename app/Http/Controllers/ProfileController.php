<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function UserProfile(Request $request) {

        try {

            $user = Auth::user();
            $profile = Profile::where('user_id', $user->id)
                   ->with('user:id,name,email')
                   ->first();
            if (!$profile) {

                return response()->json(['error' => 'Profile not found'], 404);
            }

            return response()->json([$profile], 200);

        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function UpdateProfile(Request $request) {

        try {

            $request->validate([
                'name' => 'required | string | max:100',
                'email' => 'required  | max:200 | email',
                'number' => 'string | max:20',
                'about_me' => 'string | max:500',
                'photo' => 'required | string |max:2048',
                'new_photo' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048',
                'cv_link' => 'string',
                'github_link' => 'string',
                'linkedin_link' => 'string',
                'twitter_link' => 'string'
            ]);

            $imageName = $request->input('photo');
            $path = public_path('images').'/'.$imageName;

            if ($request->input('photo') && file_exists($path)) {
                unlink($path);
            }

            $newImageName = time().'-'.$request->file('new_photo')->getClientOriginalName();
            $request->file('new_photo')->move(public_path('images'), $newImageName);

            Profile::where('user_id', Auth::user()->id)->update([
                'number' => $request->input('number'),
                'photo' => $newImageName,
                'about_me' => $request->input('about_me'),
                'cv_link' => $request->input('cv_link'),
                'github_link' => $request->input('github_link'),
                'linkedin_link' => $request->input('linkedin_link'),
                'twitter_link' => $request->input('twitter_link')
            ]);

            User::where('id', Auth::user()->id)->update([
                'name' => $request->input('name'),
                'email' => $request->input('email')
            ]);

            return response()->json(["status" => "success", "message" => "Profile updated successfully"], 200);
        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }
}
