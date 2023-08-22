<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function create(Request $request){
        $this->validate($request,[
            "role" => "string|required"
        ]);

        $role = Role::where('name',$request->role)->first();
        if(isset($role->id)){
            return response()->json([
                "message" => "Already exists"
            ],409);
        }else{
            $roles = new Role;
            $roles->name = $request->role;
            $roles->save();
            return response()->json([
                "message" => "Role Created",
                "data" => $roles
            ],201);
        }
    }

    public function delete($id){
        $role = Role::where('id',$id)->first();
        if(isset($role)){
            $role->delete();
            return response()->json([
                "message" => "Role deleted"
            ],200);
        }

        else{
            return response()->json([
                "message" => "Id not found"
            ],400);
        }
    }

    public function list(){
        $role = Role::get();
        $role_count = $role->count();
        return response()->json([
            "message" => "Role listing",
            "data" => array(
                "Total count" => $role_count, 
                "Roles" => $role
            )
        ]);
    }

}
