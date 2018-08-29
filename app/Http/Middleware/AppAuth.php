<?php

namespace App\Http\Middleware;

use Closure;
use URL, Auth, Cache, Gate;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\UsersModel;
class AppAuth
{

    public function handle($request, Closure $next)
    {

        $token = $request->header('token')? $request->header('token'): '';
        if(empty($token)){//header无token则request找
            $token = $request->token ? $request->token : '';
            if(empty($token)){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_errToken),'code'=>Msg::$err_errToken]);
            }
        }
        $model = new UsersModel;
        $user = $model->where(['token'=>$token])->first();
        if( empty($user) || $user->token != $token){//token失效，验证失败
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_errToken),'code'=>Msg::$err_errToken]);
        }

        if($user->token_overtime < time()){//token过期了
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failureToken),'code'=>Msg::$err_failureToken]);
        }

        //\Log::info($token.'----'.$user->token.'===='.$user->token_overtime."-----".time());
        //$request->user_id = $user->id;
        request()->offsetSet('user_id', $user->id); //request赋值
        //request()->offsetSet('token', $data['token']); //request赋值
        return $next($request);
    }


}
