<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CheckRoleUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user;
        if($user){
            if($user->role == 'executive' || $user->role == 'executive'){
                return $next($request);
            }else{
                return response ("You don't have permission to do this action", 401);
            }
        }else{
            return response ('User not found', 401);
        }
    }
}
