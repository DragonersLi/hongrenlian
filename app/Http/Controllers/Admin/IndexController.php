<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\BaseController;
use Log;
use App\Models\Application;
use App\Models\AppAndroid as Android;
use App\Models\AppIos as Ios;
use QrCode;
use App\Http\Services\AdminUserService as AdminUserService;
class IndexController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('后台首页，当前用户名：'.auth('admin')->user()->name);
    }

    //重置密码
    public function resetPwd(Request $request)
    {

        if($request->isMethod('post')){
            try{
                $data = $request->except('_token');
                $service = new AdminUserService();
                $service->resetPwd($data);
                request()->session()->flush();
                request()->session()->regenerate();
                return redirect('/admin/login')->withSuccess('密码重置成功，请重新登陆!');
                //return redirect()->back()->withInput()->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }
        $data['pre_url'] = $request->session()->get('_previous.url');//返回上页
        return view('admin.index.resetPwd',$data);
    }

}
