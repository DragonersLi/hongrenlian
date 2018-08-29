<?php

namespace App\Http\Middleware;

use Closure;
use URL, Auth, Cache, Gate;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\UsersModel;
class AppVistAuth
{

    public function handle($request, Closure $next)
    {
        $token = $request->header('token')? $request->header('token'): '';
        if(empty($token)){//header无token则request找
            $token = $request->token ? $request->token : '';
			empty($token) && $user_id = 0;
        
        }else{//有token
			$model = new UsersModel;
			$user = $model->where(['token'=>$token])->first();	
		    $user_id = (empty($user) || ($user->token != $token) || ($user->token_overtime < time())) ? 0 : $user->id; 
		}
 
        request()->offsetSet('user_id', $user_id); //request赋值
        return $next($request);
    }


}
