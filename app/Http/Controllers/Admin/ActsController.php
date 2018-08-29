<?php
namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\UsersModel;
use App\Models\Admin\ActsModel;
use App\Models\Admin\ActsDetailModel;
use App\Models\Admin\ActsRecordsModel;
class ActsController extends BaseController
{

    public $status_type = [

        1=>'未开始',
        2=>'进行中',
        3=>'已结束',
        //4=>'已关闭'

    ];

    public function __construct()
    {
        $this->usersModel = new UsersModel;
        $this->actsModel = new ActsModel;
        $this->detailModel = new ActsDetailModel;
        $this->recordsModel = new ActsRecordsModel;

    }
    /**
     * 活动列表
     */
    public function index(Request $request)
    {
        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;
        $status_type = $request->status_type;
        $is_template = $request->is_template;
        $keywords = $request->keywords;
        $page = $request->page;

        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;
        $datetime = date('Y-m-d H:i:s');
        switch ($status_type) {
            case 1: // 未开始
                $pages['status_type'] = 1;
                $where .= " and '{$datetime}' < start_time";
                break;
            case 2: // 进行中
                $pages['status_type'] = 2;
                $where.= " and '{$datetime}' between start_time and end_time";
                break;
            case 3: // 已结束
                $pages['status_type'] = 3;
                $where.= " and '{$datetime}' > end_time";
                break;
//            case 4: // 已关闭
//                $pages['status_type'] = 4;
//                $where.=" and status = 9 ";
//                break;
        }

        if(isset($is_template) && in_array($is_template,[0,1])){//模板
            $pages['is_template'] = $is_template;
            $where .= " and is_template = {$is_template}";
        }


        if(!empty($keywords)){//活动标题，投票和被投票中奖标题
            $pages['keywords'] = $keywords;
            $where .= " and ( title like '%{$keywords}%' or reward_title like '%{$keywords}%' or voting_title like '%{$keywords}%' ) ";
        }

        $links = $this->actsModel->whereRaw($where)->orderBy('id','desc')->paginate($this->page_size)->appends($pages);
        $data['data'] = $links;
        $data['page'] = $pages;
        $data['status_type'] = $this->status_type;
        return view('admin.acts.index',$data);
    }

    //添加活动
    public function add(Request $request)
    {
        if($request->isMethod('post')){
            try{
                $time = date('Y-m-d H:i:s');
                $data = $request->except('_token','file','id');
                $range = $request->rangedatetime;
                if(!empty($range)){
                    $rangedatetime = explode('~',$range);
                    $data['start_time'] = $rangedatetime[0];
                    $data['end_time'] = $rangedatetime[1];
                }
                $lunbo_img = [];
                foreach($request->lunbo_img as $k=>$v){
                    !empty($v) && $lunbo_img[] = $v;
                }
                $data['lunbo_img'] = serialize($lunbo_img);

                $data['create_time'] = $time;
                $data['update_time'] = $time;
                unset($data['rangedatetime']);
                $result = $this->actsModel->insert($data);
                return redirect('admin/acts/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput($request->except('_token','file','id'))->withErrors($e->getMessage());
            }
        }
        $data = [];
        return view('admin.acts.add',$data);
    }
    //编辑活动
    public function edit(Request $request)
    {

        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file');
                $data['update_time'] = date('Y-m-d H:i:s');
                $range = $request->rangedatetime;
                if(!empty($range)){
                    $rangedatetime = explode('~',$range);
                    $data['start_time'] = $rangedatetime[0];
                    $data['end_time'] = $rangedatetime[1];
                }
                $lunbo_img = [];
                foreach($request->lunbo_img as $k=>$v){
                    !empty($v) && $lunbo_img[] = $v;
                }
                $data['lunbo_img'] = serialize($lunbo_img);
                unset($data['rangedatetime']);
                $result = $this->actsModel->where(['id'=>$data['id']])->update($data);
                return redirect('admin/acts/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }
        $act_id = $request->act_id;
        $data = $this->actsModel->where(['id'=>$act_id])->first();
        $data['act_id'] = $act_id;
        $data['rangedatetime'] = $data->start_time.' ~ '.$data->end_time;
        return view('admin.acts.edit',$data);
    }


    //冻结或解冻活动
    public function changeActStatus(Request $request)
    {
        try{
            $act_id = $request->act_id;
            $status =($request->status == 9)? 0 : 9;
            $this->actsModel->where(['id'=>$act_id])->update(['status'=>$status]);
            return redirect()->back()->withSuccess('操作成功!');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    //查看被投票的投票记录
    public function records(Request $request)
    {
        $act_id = $request->act_id;
        $vote_id = $request->vote_id;
        $keywords = $request->keywords;
        $page = $request->page;
        //$pre_url = $request->session()->get('_previous.url');//返回上页

        $where = " vote_id = {$vote_id} ";
        $pages['page'] = isset($page) ? $page : 1;


        if(!empty($keywords)){//粉丝表字段
            $pages['keywords'] = $keywords;
            $where .= " and concat(name,truename,mobile,email) like '%{$keywords}%' ";
        }

        $links = $this->recordsModel
            ->join('users','users.id','=','acts_records.user_id')
            ->select('acts_records.*','users.username','users.mobile')
            ->whereRaw($where)->orderBy('update_time','desc')
            ->paginate($this->page_size)->appends($pages);

        $data['data'] = $links;
        $data['page'] = $pages;
        $data['act_id'] = $act_id;
        $data['vote_id'] = $vote_id;
        return view('admin.acts.records',$data);
    }
    public function detail(Request $request){
        $act_id = $request->act_id;
        $page = $request->page;

        $where = " act_id = {$act_id} ";
        $pages['page'] = isset($page) ? $page : 1;

        if(isset($status) && in_array($status,[0,1])){//状态
            $pages['status'] = $status;
            $where .= " and status = {$status}";
        }


        if(!empty($keywords)){//活动标题，投票和被投票中奖标题
            $pages['keywords'] = $keywords;
            $where .= " and concat(name,truename,mobile,qq,vote_desc) like '%{$keywords}%' ";
        }

        $links= $this->detailModel->join("users","users.id",'=',"acts_detail.uid")->select("acts_detail.*","users.username","users.mobile","users.avatar")->whereRaw($where)->orderBy('id','desc')->paginate($this->page_size)->appends($pages);
       foreach($links as $k=>$v){
           $links[$k]['username'] = !empty($v['username']) ? $v['username'] : $v['mobile'];
       }
        $data['data'] = $links;
        $data['page'] = $pages;
        $data['act_id'] = $act_id;
        return view('admin.acts.detail',$data);
    }

    //添加候选人
    public function detailAdd(Request $request)
    {
        $act_id = $request->act_id;
        if($request->isMethod('post')){
            try{

                $data = $request->except('_token','file');
                $data['status'] = 1;
                $data['create_time'] = time();
                $res = $this->detailModel->where(['uid'=>$data['uid'],'act_id'=>$data['act_id']])->first();
                if(!empty($res)){
                    return redirect()->back()->withInput()->withErrors('候选人已存在！');
                }
                $result = $this->detailModel->insert($data);
                return redirect('admin/acts/detail/'.$act_id)->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }
        $users = $this->usersModel->select('id','username','mobile')->where(['status'=>1])->get()->toArray();

        foreach($users as $k=>$v){
            $users[$k]['username'] = !empty($v['username']) ? $v['username'] : $v['mobile'];
        }
        $data['act_id'] = $act_id;
        $data['users'] = $users;
        return view('admin.acts.detailAdd',$data);
    }
    //编辑候选人
    public function detailEdit(Request $request)
    {
        $act_id = $request->act_id;
        $detail_id= $request->detail_id;
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file');
                $data['update_time'] = time();

                $res = $this->detailModel->whereRaw( "uid={$data['uid']} and act_id = {$data['act_id']} and id !={$data['id']}")->first();
                if(!empty($res)){
                    return redirect()->back()->withInput()->withErrors('候选人已存在！');
                }
                $result = $this->detailModel->where(['id'=>$data['id']])->update($data);
                return redirect('admin/acts/detail/'.$act_id.'/'.$detail_id)->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        $data = $this->detailModel->where(['id'=>$detail_id])->first();
        $users = $this->usersModel->select('id','username','mobile')->where(['status'=>1])->get();
        foreach($users as $k=>$v){
            $users[$k]['username'] = !empty($v['username']) ? $v['username'] : $v['mobile'];
        }
        $data['users'] = $users;
        return view('admin.acts.detailEdit',$data);
    }

    //上传图片
    public function upload(Request $request)
    {
        return $message = $this->uploadFile('acts');
    }

}
