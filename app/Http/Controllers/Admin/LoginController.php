<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests;
class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';
    protected $username;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => 'logout']);
    }
    /**
     * 重写登录视图页面
     * @author 晚黎
     * @date   2016-09-05T23:06:16+0800
     * @return [type]                   [description]
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }
    /**
     * 自定义认证驱动
     * @author 晚黎
     * @date   2016-09-05T23:53:07+0800
     * @return [type]                   [description]
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    //登陆
//    public function login(Request $request){
//
//        try{
//            $data = $request->except('_token');
//            $res = \DB::table('admin_users')->whereRaw('( username = ? or email = ? or mobile = ?)', ["{$data->username}","{$data->username}","{$data->username}"])->first();
//            print_R($data);print_R($res);die;
//            if(empty($res)){
//                return redirect()->back()->withInput()->withErrors('用户名不存在！');
//            }else{
//                if($res->password == md5($data->password)) {
//                    return redirect()->withSuccess('登陆成功!');
//                }else{
//                    return redirect()->back()->withInput()->withErrors('密码错误！');
//                }
//            }
//
//
//        }catch (\Exception $e){
//            return redirect()->back()->withInput()->withErrors($e->getMessage());
//        }
//    }
    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard('admin')->logout();

        request()->session()->flush();
        request()->session()->regenerate();

        return redirect('/admin/login');
    }

}
