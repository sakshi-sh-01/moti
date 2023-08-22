<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intrest;

class IntrestController extends Controller
{
    public function create(Request $request){
        $this->validate($request,[
            "intrest" => "string|required",
            "image" => "image|mimes:jpeg,png,jpg,gif",
            "parent_id" => "string"
        ]);

        $intrest_n = Intrest::where('name',$request->intrest)->first();
        if(isset($intrest_n->id) && ($intrest_n->parent_id === null)){
            return response()->json([
                "message" => "Intrest already exists"
            ],409);
        }else if(isset($intrest_n->id) && isset($intrest_n->parent_id)){
            return response()->json([
                "message" => "Sub-intrest already exists"
            ],409);
        }else{

            if($request->parent_id === null){

                $image = time(). '.' .$request->image->extension();
                $request->image->storeAs('public/images/intrests', $image);
                
                $intrests = new Intrest;
                $intrests->name = $request->intrest;
                $intrests->image = $image;
                $intrests->save();
    
                return response()->json([
                    "message" => "Intrest Saved",
                    "data" => $intrests
                ],200);
            }else{

                $image = time(). '.' .$request->image->extension();
                $request->image->storeAs('public/images', $image);

                $intrests = new Intrest;
                $intrests->name = $request->intrest;
                $intrests->image = $image;
                $intrests->parent_id = $request->parent_id;
                $intrests->save();
    
                return response()->json([
                    "message" => "Sub Intrest Saved",
                    "data" => $intrests
                ],200);
            }
        }
    }

    public function update(Request $request, $id){
        $this->validate($request,[
            "intrest" => "string|required",
            "image" => "string"
        ]);

        $intrest = Intrest::find($id);
        if(isset($intrest->id)){
            if($request->hasFile('image')){
                $file = $request->file('image');
                $custom_imagefile = time().'-'.$file->getClientOriginalName();
                $path_img = $file->storeAs('images', $custom_imagefile);
            }

            $intrest->name = $request->intrest ?? $intrest->name;
            $intrest->image = $path_img ?? $intrest->image;
            $intrest->save();

            return response()->json([
                "message" => "Updated and saved",
                "data" => $intrest
            ],200);
        }else{
            return response()->json([
                "message" => "No intrest exists for updation"
            ],404);
        }
    }

    public function detail($id){
        $intrest = Intrest::where('id',$id)->with('user')->first();
        if($intrest){
            return response()->json([
                "message" => "Found",
                "data" => $intrest
            ],200);
        }else{
            return response()->json([
                "message" => "No matching id exists"
            ],400);
        }
    }

    public function delete($id){
        $intrest = Intrest::find($id);
        if($intrest){
            $intrest->delete();
            return response()->json([
                "message" => "Deleted"
            ],200);
        }else{
            return response()->json([
                "message" => "No matching id exists"
            ],404);
        }
    }

    public function listing(){
        $intrests = Intrest::get();
        $count = $intrests->count();
        return response()->json([
            "message" => "List of intrests",
            "data" => array(
                "count" => $count,
                "data" => $intrests
            )
        ],200);
    }


}
