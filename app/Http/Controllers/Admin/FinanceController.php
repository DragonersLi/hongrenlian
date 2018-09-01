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
        '13' => '所有奖励类型',
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
    //支出类型
    public $freeze = [
        '1' => '点赞',
        '2' => '推荐红人',
        '3' => '粉丝投票',
        '4' => '购买'
    ];

    /**
     * 财务列表
     */
    public function index(Request $request)
    {

            $rangedate = $request->rangedate;
            $type = $request->type;
            $action = $request->action;
            $keywords = $request->keywords;
            $page = $request->page;
            $where = " 1=1 ";
            $pages['page'] = isset($page) ? $page : 1;

            if(isset($type) && $type !== null ){//类型
                $pages['type'] = $type;
                $where .= " and finance.type = {$type}";
            }

            if(isset($action) && $action !== '-1' ){//收入 0   支出 1
                $pages['action'] = $action;
                $where .= " and finance.action = {$action}";
            }

            if(!empty($rangedate)){//创建日期范围
                $pages['rangedate'] = $rangedate;
                $rangeday = explode('~',$rangedate);
                foreach($rangeday as $key => $value){
                    $rangeday[$key] = strtotime($value);
                }
                $rangeday[1] = $rangeday[1]+((60*60*24) - 1);//精确到最后1秒
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

            //二级联动条件返回
            $data['income']  = $action && $action !== '-1' ?  $data['income'] = $this->expend : $data['income'] =$this->income;
            $data['incomes'] = $this->income;
            $data['expend'] = $this->expend;
            return view('admin.finance.index',$data);
    }

    /**
     * 红人圈列表
     */
    public function scorelockIndex(Request $request)
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
                $rangeday[1] = $rangeday[1]+((60*60*24) - 1);//精确到最后1秒
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
    public function scorelockEdit(Request $request){
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
    public function scorelockUpdate(Request $request){
            $data['status'] = $request->status;
            $data['update_time'] = time();
            $data['title'] = $request->title;
            $res = DB::table('scorelock')->where(['id'=>$request->id])->update($data);
            if($res){
                return redirect('admin/finance/scorelockIndex')->withSuccess('初审成功');
            }
    }


    /**
     * 终审
     */
    public function scorelockUp(Request $request){

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
                    $freeze['thaw_time'] = $request['attr']['days'][$k] ? $freeze['create_time'] + (((($request['attr']['days'][$k])*24)*60)*60) : 0;
                    $freeze['update_time'] = time();
                    $users = DB::table('users')->select('freeze_score','score')->where(['id' => $freeze['user_id'] ])->first();
                    //冻结
                    if($freeze['thaw_time'] > 0) {
                        //冻结记录表
                        DB::table('freeze')->insert($freeze);
                        $users->freeze_score = $users->freeze_score + $freeze['number'];
                        DB::table('users')->where(['id' => $freeze['user_id'] ])->update(['freeze_score'=>$users->freeze_score ]);
                    }else {
                        //直充值
                        $users->score = $users->score + $freeze['number'];
                        DB::table('users')->where(['id' => $freeze['user_id'] ])->update(['score'=>$users->score ]);
                        //进入财务表
                        $finance = array('type'=>14, 'number'=>$freeze['number'], 'action'=>0, 'user_id'=>$freeze['user_id'], 'note'=>$this->income[14], 'create_time'=>time(), 'update_time'=>time());
                        DB::table('finance')->insert($finance);
                    }
                }
            //直充值
            }else{
                $freeze['number'] = $request->total_score;
                $freeze['create_time'] = $request->create_time;
                $freeze['thaw_time'] = 0;
                $freeze['update_time'] = time();
                //$freezeData = DB::table('freeze')->insert($freeze);//进入冻结表
                $users = DB::table('users')->where(['id'=>$freeze['user_id'] ])->first();
                //直充值
                $users->score = $users->score + $freeze['number'];
                DB::table('users')->where(['id' => $freeze['user_id'] ])->update(['score'=>$users->score]);
                //进入财务表
                $finance = array('type'=>14, 'number'=>$freeze['number'], 'action'=>0, 'user_id'=>$freeze['user_id'], 'note'=>$this->income[14], 'create_time'=>time(), 'update_time'=>time());
                DB::table('finance')->insert($finance);
            }

            \DB::commit();
            } catch (\Exception $e) {
                \DB::rollback();
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
            if($res){
                return redirect('admin/finance/scorelockIndex')->withSuccess('终审成功');
            }

    }

    /**
     * 财务->冻结列表
     * @param Request $request
     * @return data
     */
    public function frozenIndex(Request $request){
            $rangedate = $request->rangedate;
            $type = $request->type;
            $status = $request->status;
            $keywords = $request->keywords;
            $page = $request->page;
            $where = " 1=1 ";
            $pages['page'] = isset($page) ? $page : 1;

            if(isset($type)){//冻结类型
                $pages['type'] = $type;
                $where .= " and freeze.type = {$type}";
            }

            if(isset($status)){//冻结状态
                $pages['status'] = $status;
                $where .= " and freeze.status = {$status}";
            }

            if(!empty($rangedate)){//创建日期范围
                $pages['rangedate'] = $rangedate;
                $rangeday = explode('~',$rangedate);
                foreach($rangeday as $key => $value){
                    $rangeday[$key] = strtotime($value);
                }
                $rangeday[1] = $rangeday[1]+((60*60*24) - 1);//精确到最后1秒
                $where .= " and freeze.create_time >= '{$rangeday[0]}' and freeze.create_time <= '{$rangeday[1]}'";
            }

            if(!empty($keywords)){//用户名，手机号
                $pages['keywords'] = $keywords;
                $where .= " and ( users.username like '%{$keywords}%' or users.mobile like '%{$keywords}%' ) ";
            }
            $links = DB::table('freeze')->select('users.username','users.mobile','users.avatar','freeze.type','freeze.number','freeze.status','freeze.thaw_time','freeze.create_time','freeze.id','freeze.user_id')
                ->join("users","users.id","=","freeze.user_id")->whereRaw($where)->orderBy('freeze.id','desc')->paginate($this->page_size)->appends($pages);
            $data['data'] = $links;
            $data['page'] = $pages;
            $data['type'] = $this->freeze;
            return view('admin.finance.frozen_index',$data);
    }

    /**
     * 去解冻
     * @param Request $request
     * @return int 成功
     */
    public function frozenUp(Request $request){
        try {
            //开启事务
            \DB::beginTransaction();
                $id =  intval($request->id);
                $user_id = intval($request->user_id);
                $data['status'] = 1;//0 是冻结；1是解冻
                $data['update_time'] = time();
                $freeze = \DB::table("freeze")->where(['id' => $id ])->update($data);

                //用户表红人圈、冻结红人圈修改
                $user = \DB::table("users")->select('score','freeze_score')->where(['id' => $user_id ])->first();
                $result['score'] = $request->number + $user->score;
                $result['freeze_score'] =  $user->freeze_score - $request->number;
                \DB::table("users")->where(['id'=> $request->user_id ])->update($result);

                //财务明细表
                $arr = $this->income;//获取收入分类
                $finnace['type'] = 14;
                $finnace['number'] = $request->number;
                $finnace['action'] = 0;
                $finnace['user_id'] = $user_id;
                $finnace['note'] = $arr[$finnace['type']];
                $finnace['create_time'] = time();
                $finnace['update_time'] = time();
                \DB::table("finance")->insert($finnace);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
        if($freeze){
                return redirect('admin/finance/frozenIndex')->withSuccess('手机号'.$request->mobile.'解冻成功');
        }
    }

    /**
     * ajax 二级联动
     * @param Request $request->type  类型
     * @return string 类型结果
     */
    public function incomeType(Request $request){
            if($request->type)
                {
                    if( $request->type == -1 ){
                        $data['code'] = 1;
                        return json_encode($data);
                    }else{
                        $data['code'] = 0;
                        $data['data'] = $this->expend;
                        $data['length'] = count($this->expend);
                        return json_encode($data);
                    }
             }else
            {
                        $data['code'] = 0;
                        $data['data'] = $this->income;
                        $data['length'] = count($this->income);
                        return json_encode($data);
            }
    }


}