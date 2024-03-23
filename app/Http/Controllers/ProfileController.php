<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function UserProfile(Request $request) {

        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)
                   ->with('user:id,name,email')
                   ->first();
        if (!$profile) {

            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json([$profile], 200);
    }

    
}
