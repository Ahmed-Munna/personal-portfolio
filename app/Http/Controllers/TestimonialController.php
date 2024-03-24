<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    public function GetTestimonial() {
        
        try {

            $testimonials = Testimonial::all();
            return response()->json(["status" => "success", "data" => $testimonials], 200);
        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }
    public function StoreTestimonial(Request $request) {

        try {
            $request->validate([
                'name' => 'required | string | max:100',
                'comment' => 'required | string | max:500',
                'company' => 'required | string | max:100',
                'url' => 'required | string',
                'rating' => 'required | integer | min:1 | max:5',
            ]);

            Testimonial::create([
                'name' => $request->input('name'),
                'comment' => $request->input('comment'),
                'company' => $request->input('company'),
                'url' => $request->input('url'),
                'rating' => $request->input('rating'),
            ]);

            return response()->json(["status" => "success", "message" => "Testimonial added successfully"], 201);
        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function DeleteTestimonial(Request $request) {

        try {
            $request->validate([
                'id' => 'required | integer',
            ]);
            Testimonial::findOrfail($request->input('id'))->delete();
            return response()->json(["status" => "success", "message" => "Testimonial deleted successfully"], 200);
        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function UpdateTestimonial(Request $request) {

        try {

            $request->validate([
                'id' => 'required | integer',
                'name' => 'required | string | max:100',
                'comment' => 'required | string | max:500',
                'company' => 'required | string | max:100',
                'url' => 'required | string',
                'rating' => 'required | integer | min:1 | max:5',
            ]);

            Testimonial::findOrfail($request->input('id'))->update([
                'name' => $request->input('name'),
                'comment' => $request->input('comment'),
                'company' => $request->input('company'),
                'url' => $request->input('url'),
                'rating' => $request->input('rating'),
            ]);

            return response()->json(["status" => "success", "message" => "Testimonial updated successfully"], 200);
        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }
}
