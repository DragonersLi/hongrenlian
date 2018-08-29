<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Http\Services\FansService as FansService;
use App\Models\Admin\FansModel;
//前台粉丝管理
class FansController extends BaseController
{


    public function __construct()
    {
        $this->model = new FansModel();
        $this->service = new FansService();
    }

    /**
     * 粉丝列表
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
            $where .= " and ( name like '%{$keywords}%' or truename like '%{$keywords}%' or mobile like '%{$keywords}%' or email like '%{$keywords}%' or qq like '%{$keywords}%' ) ";
        }

        $links = $this->model->whereRaw($where)->orderBy('id','desc')->paginate($this->page_size)->appends($pages);
        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.fans.index',$data);
    }

    //添加粉丝
    public function add(Request $request)
    {
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file','id');
                $result = $this->service->insertData($data);
                return redirect('admin/fans/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }
        $data = [];
        foreach ($this->service->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        return view('admin.fans.add',$data);
    }

    //编辑粉丝
    public function edit(Request $request)
    {
        $id = $request->id;
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file');
                $result = $this->service->updateData($data);
                return redirect('admin/fans/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        $data = $this->model->where(['id'=>$id])->first();
        foreach (array_keys($this->service->fields) as $field) {
            $data[$field] = old($field, $data->$field);
        }
        return view('admin.fans.edit',$data);
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

    //上传图片
    public function upload(Request $request)
    {
        return $message = $this->uploadFile('fans');
    }




}