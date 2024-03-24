<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkController extends Controller
{
    public function GetWork() {

        try {

            $works = Work::all();
            return response()->json(["status" => "success", "data" => $works], 200);
        } catch (Exception $ex) {

            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function StoreWork(Request $request) {

        try {

            $request->validate([
                'title' => 'required | string | max:100',
                'description' => 'string | max:500',
                'image' => 'required | image|mimes:jpg,jpeg,png,gif,svg|max:2048',
                'url' => 'required | string',
                'github' => 'required | string',
            ]);

            $newImageName = time().'-'.$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $newImageName);

            Work::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'image' => $newImageName,
                'url' => $request->input('url'),
                'github' => $request->input('github'),
            ]);

            return response()->json(["status" => "success", "message" => "Work created successfully"], 201);


        } catch (Exception $ex) {
            
            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }

    public function DeleteWork(Request $request) {

        try {

            $request->validate([
                'id' => 'required | integer',
            ]);

            $work = Work::findOrfail($request->input('id'));
            unlink(public_path('images').'/' .$work->image);
            $work->delete();

            return response()->json(["status" => "success", "message" => "Work deleted successfully"], 200);
        } catch (Exception $ex) {
            
            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }
    public function UpdateWork(Request $request) {

        try {

            $request->validate([
                'id' => 'required | integer',
                'title' => 'required | string | max:100',
                'description' => 'string | max:500',
                'image' => 'required | string |max:2048',
                'new_image' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048',
                'url' => 'required | string',
                'github' => 'required | string',
            ]);
            
            $work = Work::findOrfail($request->input('id'));


            $imageName = $request->input('image');
            $path = public_path('images').'/'.$imageName;

            if ($request->file('new_image') && file_exists($path)) {

                unlink($path);
                $newImageName = time().'-'.$request->file('new_image')->getClientOriginalName();
                $request->file('new_image')->move(public_path('images'), $newImageName);

                $work->update([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'image' => $newImageName,
                    'url' => $request->input('url'),
                    'github' => $request->input('github'),
                ]);

                return response()->json(["status" => "success", "message" => "Work updated successfully"], 200);
            }

            $work->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'image' => $imageName,
                'url' => $request->input('url'),
                'github' => $request->input('github'),
            ]);

            return response()->json(["status" => "success", "message" => "Work updated successfully"], 200);
        } catch (Exception $ex) {
            
            Log::alert("alert message", ["message" => $ex->getMessage(), "Date" => time()]);
            return response()->json(["status" => "faild", "message" => $ex->getMessage()]);
        }
    }
}
