<?php

namespace App\Http\Controllers;

use App\Mail\SendFromClintMail;
use App\Mail\ThankYouMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required | string',
                'email' => 'required | email | string',
                'message' => 'required | string',
            ]);

            // send mail to user
            Mail::to($request->input('email'))->send(new ThankYouMail($request->input('name')));

            // send mail to admin
            $user = User::all()->first()->email;
            Mail::to($user)->send(new SendFromClintMail($request->input('name'), $request->input('email'), $request->input('message')));


            return response()->json(["status" => "success", "message" => "message sent successfully"]);

        } catch (Exception $ex) {
            
            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }
}
