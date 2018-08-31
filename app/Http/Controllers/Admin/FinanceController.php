<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\FinanceModel;
use App\Models\Admin\MessageModel as MessageModel;
use Illuminate\Support\Facades\DB;

//财务管理
class FinanceController extends BaseController
{
    public function __construct()
    {
        $this->model = new FinanceModel;
    }

    //收入类型
    public $income = [
        '1' => '日常领取',
        '2' => '转账收入',
        '3' => '点赞收入',
        '4' => '粉丝投票收入',
        /*奖励收入*/
        '5' => '注册奖励',
        '6' => '邀友奖励',
        '7' => '护驾有功奖励',
        '8' => '红人星探推荐奖励',
        /*退回类型*/
        '9' => '众筹失败退回',
        '10' => '星探冻结退回',
        /*追加类型*/
        '11' => '签到收入',
        '12' => '商家核销收入',
        '13' => '所有奖励类型(5,6,7,8)',
        '14' => '购买收入',
    ];
    //支出类型
    public $expend = [
        '1' => '转账支出',
        '2' => '点赞支出',
        '3' => '众筹支出',
        '4' => '投票支出',
        '5' => '兑换支出'
    ];

    /**
     * 财务列表
     */
    public function index(Request $request)
    {
        $rangedate = $request->rangedate;
        $status = $request->status;
        $keywords = $request->keywords;
        $page = $request->page;
        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;

        if(isset($status)){//审核状态
            $pages['type'] = $status;
            $where .= " and finance.type = {$status}";
        }

        if(isset($status)){//审核状态
            $pages['action'] = $status;
            $where .= " and finance.action = {$status}";
        }

        if(!empty($rangedate)){//创建日期范围
            $pages['rangedate'] = $rangedate;
            $rangeday = explode('~',$rangedate);
            foreach($rangeday as $key => $value){
                $rangeday[$key] = strtotime($value);
            }
            $rangeday[1] = ($rangeday[1]*60*60*23) + 59*60;//精确到最后59分
            $where .= " and finance.create_time >= '{$rangeday[0]}' and finance.create_time <= '{$rangeday[1]}'";
        }

        if(!empty($keywords)){//用户名，手机号
            $pages['keywords'] = $keywords;
            $where .= " and ( users.username like '%{$keywords}%' or users.mobile like '%{$keywords}%' ) ";
        }
        $links = DB::table('finance')->select('users.username','users.mobile','users.avatar','finance.type','finance.number','finance.action','finance.note','finance.create_time','finance.id')
            ->join("users","users.id","=","finance.user_id")->whereRaw($where)->orderBy('finance.id','desc')->paginate($this->page_size)->appends($pages);
        $data['data'] = $links;
        $data['page'] = $pages;
        //dd($data);
        return view('admin.finance.index',$data);
    }

    /**
     * 财务列表
     */
    public function ScorelockIndex(Request $request)
    {
        $rangedate = $request->rangedate;
        $status = $request->status;
        $keywords = $request->keywords;
        $page = $request->page;
        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;

        if(isset($status)){//审核状态
            $pages['status'] = $status;
            $where .= " and scorelock.status = {$status}";
        }
        if(!empty($rangedate)){//创建日期范围
            $pages['rangedate'] = $rangedate;
            $rangeday = explode('~',$rangedate);
            foreach($rangeday as $key => $value){
                $rangeday[$key] = strtotime($value);
            }
            $rangeday[1] = ($rangeday[1]*60*60*23) + 59*60;//精确到最后59分
            $where .= " and scorelock.create_time >= '{$rangeday[0]}' and scorelock.create_time <= '{$rangeday[1]}'";
        }

        if(!empty($keywords)){//用户名，手机号
            $pages['keywords'] = $keywords;
            $where .= " and ( users.username like '%{$keywords}%' or users.mobile like '%{$keywords}%' ) ";
        }
        $links = DB::table('scorelock')->select('users.username','users.mobile','users.avatar','scorelock.total_score','scorelock.title','scorelock.id','scorelock.status','scorelock.create_time')
            ->join("users","users.id","=","scorelock.user_id")->whereRaw($where)->orderBy('scorelock.id','desc')->paginate($this->page_size)->appends($pages);
        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.finance.scorelock_index',$data);
    }


    /**
     * 审核编辑页面
     * @param Request $request
     */
    public function ScorelockEdit(Request $request){
        $data = DB::table('scorelock')->select('users.username','users.mobile','users.avatar','scorelock.total_score','scorelock.title','scorelock.id','scorelock.status','scorelock.create_time','scorelock.user_id')
            ->join("users","users.id","=","scorelock.user_id")->where(['scorelock.id'=>$request->id])->get();
        $data = $data[0];

        $attr = DB::table('scorelock_detail')->where(['pid'=>$request->id])->get();
        //时间戳换天数
        foreach($attr as $key => $val){
            $val->days =  $val->thaw_time ? (($val->thaw_time/24)/60)/60 : 0;
        }
        return view('admin.finance.scorelock_edit',array('data'=>$data, 'attr'=>$attr));
    }


    /**
     * 初审
     */
    public function ScorelockUpdate(Request $request){
        $data['status'] = $request->status;
        $data['update_time'] = time();
        $data['title'] = $request->title;
        $res = DB::table('scorelock')->where(['id'=>$request->id])->update($data);
        if($res){
            return redirect('admin/finance/ScorelockIndex')->withSuccess('初审成功');
        }
    }


    /**
     * 终审
     */
    public function ScorelockUp(Request $request){

        try {
        //开启事务
        \DB::beginTransaction();

        $data['status'] = $request->status;
        $data['shenhe_time'] = time();
        $data['update_time'] = time();
        $data['title'] = $request->title;
        $res = DB::table('scorelock')->where(['id'=>$request->id])->update($data);

        $freeze['type'] = 4;
        $freeze['user_id'] = $request->user_id;
        $freeze['status'] = 0;
        //如果存在分配就先执行分配
        if($request['attr']['keys'][0]){
            foreach($request['attr']['keys'] as $k => $value){
                $freeze['number'] = $value;
                $freeze['create_time'] = $request['attr']['create_time'][$k];
                $freeze['thaw_time'] = $request['attr']['days'][$k] ? ((($request['attr']['days'][$k])*24)*60)*60 : 0;
                $freeze['update_time'] = time();
                $users = DB::table('users')->select('freeze_score','score')->where(['id' => $freeze['user_id'] ])->first();
                //冻结
                if($freeze['thaw_time'] > 0) {
                    DB::table('freeze')->insert($freeze);
                    $users->freeze_score = $users->freeze_score + $freeze['number'];
                    DB::table('users')->where(['id' => $freeze['user_id'] ])->update(['freeze_score'=>$users->freeze_score ]);
                }else {
                    //直充值
                    $users->score = $users->score + $freeze['number'];
                    DB::table('users')->where(['id' => $freeze['user_id'] ])->update(['score'=>$users->score ]);
                    $finance = array('type'=>14, 'number'=>$users->score, 'action'=>0, 'user_id'=>$freeze['user_id'], 'note'=>$this->income[14], 'create_time'=>time(), 'update_time'=>time());
                    DB::table('finance')->insert($finance);
                }
            }
        //直充值
        }else{
            $freeze['number'] = $request->total_score;
            $freeze['create_time'] = $request->create_time;
            $freeze['thaw_time'] = 0;
            $freeze['update_time'] = time();
            //$freezeData = DB::table('freeze')->insert($freeze);
            $users = DB::table('users')->where(['id'=>$freeze['user_id'] ])->first();
            $users->score = $users->score + $freeze['number'];
            DB::table('users')->where(['id' => $freeze['user_id'] ])->update(['score'=>$users->score]);
            $finance = array('type'=>14, 'number'=>$users->score, 'action'=>0, 'user_id'=>$freeze['user_id'], 'note'=>$this->income[14], 'create_time'=>time(), 'update_time'=>time());
            DB::table('finance')->insert($finance);
        }

        \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
        if($res){
            return redirect('admin/finance/ScorelockIndex')->withSuccess('终审成功');
        }

    }

    public function incomeType(Request $request){
            $type = $request->type;
    }


}