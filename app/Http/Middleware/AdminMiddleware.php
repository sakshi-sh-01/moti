<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = Role::where('id',Auth::user()->role_id)->first();
        if(isset($role->name) && ($role->name === "Admin")){
            return $next($request);
        }else{
            return response()->json([
                "message" => "Access Denied"
            ]);
        }
    }
}
