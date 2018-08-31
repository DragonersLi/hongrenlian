<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\UsersModel as usersModel;
use Illuminate\Support\Facades\DB;
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


    /**
     * 获取添加红人圈用户信息
     * @param Request $request->id  用户ID
     * @return $data
     */
    public function scorelock(Request $request){
        $id = $request->id ? intval($request->id) : 0;
        if($id){
            $data = $this->model->where(['id'=>$id])->first()->toArray();
        }
        return view('admin.users.scorelock',array('data'=>$data));
    }

    /**
     * 添加冻结红人圈
     * @param Request $request->id 用户ID
     * @param Request $request->score 红人圈总数量
     * @param Request $request->title  红人圈描述
     * @return mixed
     */
    public function AddScorelock(Request $request){

        if($request->isMethod('post')) {
                $scorelock = array();//红人圈总类声明
                if (!intval($request->score)) {
                    return redirect('admin/users/scorelock/' . $request->id)->withErrors('红人圈不能为空!');
                }
                $scorelock['user_id'] = $request->id;
                $scorelock['total_score'] = $request->score ? intval($request->score) :  0;
                $scorelock['title'] = $request->title;
                $scorelock['status'] = 0;
                $scorelock['create_time'] = time();

                //分类的插入
                if (intval($request['attr']['keys'][0])) {
                    try {
                     //开启事务
                    \DB::beginTransaction();
                        //计算分配中的红人圈
                    $count_keys = array('count_keys' => null);
                    foreach ($request['attr']['keys'] as $k => $val) {
                                $count_keys['count_keys'] += $val;
                    }
                    //条件通过  有分类的先插入分类
                    if ($count_keys['count_keys'] == $request->score) {
                     $scorelock_id =  \DB::table('scorelock')->insertGetId($scorelock);
                     $attr = array();
                     $attr['pid'] = $scorelock_id;//获取父ID
                     $attr['create_time'] = time();
                      foreach ($request['attr']['keys'] as $k => $val)
                     {
                                $attr['score'] = $request['attr']['keys'][$k];
                                // 当前时间 + （当前天数*60秒*60分*24小时*天数）
                                $attr['thaw_time'] = $request['attr']['days'][$k] ? ($request['attr']['days'][$k] * 60 * 60 * 24) : 0;
                                $data[] = ['pid'=>$scorelock_id,'score'=>$attr['score'],'thaw_time'=>$attr['thaw_time'],'create_time'=>time()];
                     }
                     \DB::table('scorelock_detail')->insert($data);
                     \DB::commit();
                      return redirect('admin/users/index')->withSuccess('红人圈数量成功充值');
                    } else {
                      return redirect('admin/users/scorelock/' . $request->id)->withErrors('红人圈数量分配不合理!');
                    }

                    } catch (\Exception $e) {
                        \DB::rollback();
                        return redirect()->back()->withInput()->withErrors($e->getMessage());
                    }

                } else { //如果条件不成立，红人圈就变成直充
                    \DB::table('scorelock')->insertGetId($scorelock);
                    return redirect('admin/users/index')->withSuccess('红人圈数量成功充值');
                }
        }
    }

    //上传图片
    public function upload(Request $request)
    {
        return $message = $this->uploadFile('users');
    }




}