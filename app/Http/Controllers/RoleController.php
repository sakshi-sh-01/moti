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

}
