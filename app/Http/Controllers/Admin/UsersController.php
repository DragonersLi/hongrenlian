<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\UsersModel as usersModel;
//前台用户管理
class UsersController extends BaseController
{


    public function __construct()
    {
        $this->model = new usersModel();
    }

    /**
     * 用户列表
     */
    public function index(Request $request)
    {
        $birthday = $request->birthday;
        $rangedate = $request->rangedate;
        $sex = $request->sex;
        $keywords = $request->keywords;
        $page = $request->page;
        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;
        if($sex>0){//性别
            $pages['sex'] = $sex;
            $where .= " and sex = {$sex}";
        }
        if(!empty($birthday)){//出生日期
            $pages['birthday'] = $birthday;
            $where .= " and birthday = '{$birthday}'";
        }
        if(!empty($rangedate)){//注册日期范围
            $pages['rangedate'] = $rangedate;
            $rangeday = explode('~',$rangedate);
            $where .= " and create_time >= '{$rangeday[0]}' and create_time <= '{$rangeday[1]}'";
        }

        if(!empty($keywords)){//用户名，真实姓名，手机号，邮箱，qq号
            $pages['keywords'] = $keywords;
            $where .= " and ( username like '%{$keywords}%' or mobile like '%{$keywords}%' ) ";
        }

        $links = $this->model->whereRaw($where)->orderBy('id','desc')->paginate($this->page_size)->appends($pages);
        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.users.index',$data);
    }



    //编辑粉丝
    public function edit(Request $request)
    {
        $id = $request->id;
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file');
                $result = $this->model->where(['id'=>$data['id']])->update($data);
                return redirect('admin/users/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        $data = $this->model->where(['id'=>$id])->first();
        return view('admin.users.edit',$data);
    }

    //冻结或解冻
    public function changeStatus(Request $request)
    {
        try{
            $id = $request->id;
            $status = $request->status ? 0 : 1;
            $this->model->where(['id'=>$id])->update(['status'=>$status]);
            return redirect()->back()->withSuccess('操作成功!');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }


    }
    /**
     * 收货地址列表
     */
    public function address($uid = 0)
    {
        $pages['page'] = isset($page) ? $page : 1;
        $links = \DB::table('users_address')->join("users","users.id","=","users_address.user_id")->whereRaw("users.id = {$uid}")->orderBy('is_default','desc')->paginate($this->page_size)->appends($pages);

        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.users.address',$data);
    }
    //上传图片
    public function upload(Request $request)
    {
        return $message = $this->uploadFile('users');
    }




}