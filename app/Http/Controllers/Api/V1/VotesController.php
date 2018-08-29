<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\ActsModel;
use App\Models\Admin\ActsDetailModel;
use App\Models\Admin\ActsRecordsModel;
use App\Models\Admin\UsersModel;
use App\Models\Admin\FreezeModel;
use App\Models\Admin\FinanceModel;
use App\Models\Admin\CollectModel;
class VotesController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->actsModel = new ActsModel();
        $this->detailModel = new ActsDetailModel();
        $this->recordsModel = new ActsRecordsModel();
        $this->usersModel = new UsersModel();
        $this->freezeModel = new FreezeModel();
        $this->financeModel = new FinanceModel;
        $this->collectModel = new CollectModel;
    }

    //列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;//分页
        $size = $request->size ?  $request->size : $this->page_size;//分页取记录数
        $keywords = $request->keywords ? $request->keywords : '';//关键词
        $datetime = date('Y-m-d H:i:s');
        $where = " 1 = 1  and ('{$datetime}' between start_time and end_time or '{$datetime}' > end_time)";


        $data = $this->actsModel->whereRaw($where)->orderBy('id','desc')->paginate($size)->toArray();
        foreach($data['data'] as $k=>$v){

            $res[$k]['id'] = $v['id'];
            $res[$k]['header'] = $v['header'];
            $res[$k]['title'] = $v['title'];
            $res[$k]['index_img'] = $this->base_url.$v['index_img'];
            $lunbo_img = unserialize($v['lunbo_img']);
            foreach($lunbo_img as $key=>$val){
                $res[$k]['lunbo_img'][$key] = $this->base_url.$val;
            }
            // 状态
            switch ($v) {
                case $v['start_time'] > $datetime:
                    $res[$k]['status'] = 1;// 未开始
                    break;
                case $v['start_time'] < $datetime && $v['end_time'] > $datetime:
                    $res[$k]['status'] = 2;// 进行中
                    break;
                case $v['end_time'] < $datetime:
                    $res[$k]['status'] = 3;// 已结束
                    break;
            }
            // 投票进度
            $res[$k]['plan_rate'] = (string)((time() - strtotime($v['start_time'])) / (strtotime($v['end_time']) - strtotime($v['start_time'])) * 100);
            $res[$k]['reward_title'] = $v['reward_title'];
            $res[$k]['reward_desc'] = $v['reward_desc'];
            $res[$k]['voting_title'] = $v['voting_title'];
            $res[$k]['voting_desc'] = $v['voting_desc'];
            $res[$k]['rule_desc'] = $v['rule_desc'];
            $res[$k]['act_desc'] = $v['act_desc'];
            $res[$k]['count_user'] = $v['count_user'];
            $res[$k]['count_score'] = $v['count_score'];
            $res[$k]['start_time'] = strtotime($v['start_time']);
            $res[$k]['end_time'] = strtotime($v['end_time']);
            $res[$k]['create_time'] = strtotime($v['create_time']);
            $res[$k]['update_time'] = strtotime($v['update_time']);
        }
        $result =[
            'total'=>$data['total'],
            'count'=>count($data['data']),
            'page'=>$data['current_page'],
            'size'=>$data['per_page'],
            'last'=>$data['last_page'],
            'data'=>$res,
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //详情
    public function detail(Request $request){

        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $id = $request->id ? $request->id : 0;
        $user_id = $request->user_id ? $request->user_id : 0;
        $datetime = date('Y-m-d H:i:s');
        if(!$id){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>$err_noParameter]);
        }
        if($page == 1) {
            $result = $this->actsModel->where(['id' => $id])->orderBy('id', 'desc')->first()->toArray();
			$reslut['count_score'] = (int)$result['count_score'];
            $result['title'] = !empty($result['title'])?$result['title']:'';
            $result['header'] = !empty($result['header'])?$result['header']:'';
            $result['reward_title'] = !empty($result['reward_title'])?$result['reward_title']:'';
            $result['voting_title'] = !empty($result['voting_title'])?$result['voting_title']:'';
            if(!empty($result['reward_desc'])){
                $reward_desc =  explode(PHP_EOL, $result['reward_desc']);
                $reward_desc = array_filter($reward_desc);
                $result['reward_desc'] = array_values($reward_desc);
            }else{
                $result['reward_desc'] =  '';
            }
            if(!empty($result['voting_desc'])){
                $voting_desc = explode(PHP_EOL, $result['voting_desc']);
                $voting_desc =  array_filter($voting_desc);
                $result['voting_desc'] =  array_values($voting_desc);
            }else{
                $result['voting_desc'] =  '';
            }
            if(!empty($result['rule_desc'])){
                $rule_desc =  explode(PHP_EOL, $result['rule_desc']);
                $rule_desc =  array_filter($rule_desc);
                $result['rule_desc'] =  array_values($rule_desc);

            }else{
                $result['rule_desc'] =  '';
            }
            $lunbo_img = unserialize($result['lunbo_img']);
            $result['lunbo_img'] = [];
            foreach($lunbo_img as $key=>$val){
                $result['lunbo_img'][$key] = $this->base_url.$val;
            }
            // 状态
            if($result['start_time'] > $datetime){
                $result['status'] = 1;// 未开始
            }elseif($result['end_time'] < $datetime){
                $result['status'] = 3;// 已结束
            }else{
                $result['status'] = 2;// 进行中
            }

            // 投票进度
            $result['plan_rate'] = (string)((time() - strtotime($result['start_time'])) / (strtotime($result['end_time']) - strtotime($result['start_time'])) * 100);
            $result['index_img'] = $this->base_url.$result['index_img'];
            $result['create_time'] = strtotime($result['create_time']);
            $result['update_time'] = strtotime($result['update_time']);
            $collect = $this->collectModel->where(['x_type'=>2,'user_id'=>$user_id,'x_id'=>$id])->first();
            $result['is_collect'] = empty($collect) ? 0 :1 ;

        }

        $data = $this->detailModel->join("users","users.id","=","acts_detail.uid")->select('users.id as uid','users.username','users.mobile','users.avatar','acts_detail.*')->whereRaw("act_id={$id}")->paginate($size)->toArray();


        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                $detail[$k]['id'] = $v['id'];//id
                $detail[$k]['uid'] = $v['uid'];//用户id
                $detail[$k]['mobile'] = $v['mobile'];//手机号
                $detail[$k]['username'] = !empty($v['user_name']) ? $v['user_name'] :(!empty($v['username'])?$v['username']:substr_replace($v['mobile'],'****',3,4));//用户名 

                if(!empty($v['user_img'])){
                    if(substr($v['user_img'],0,4) == 'http'){
                        $detail[$k]['user_img'] = $v['user_img'];//头像
                    }else{
                        $detail[$k]['user_img'] = $this->base_url.$v['user_img'];//头像
                    }
                }else{
                    $detail[$k]['user_img'] = '';//无头像
                }
                $detail[$k]['user_desc'] = $v['user_desc']; //内容
                $detail[$k]['count_user'] = $v['count_user']; //投票总人数
                $detail[$k]['count_score'] = (int)$v['count_score']; //投票总红人圈
                $detail[$k]['create_time'] = $v['create_time'];
                $detail[$k]['update_time'] = $v['update_time'];

            }

        }else{
            $detail = [];
        }
        $result['total'] = $data['total'];
        $result['count'] = count($data['data']);
        $result['page'] = $data['current_page'];
        $result['size'] = $data['per_page'];
        $result['last'] = $data['last_page'];
        $result['data'] = $detail;

        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //投票支持
    public function pay(Request $request){

        $id = $request->id ? $request->id : 0;
        $act_id = $request->act_id ? $request->act_id : 0;
        $count = $request->count ? $request->count : 0;
        $user_id = $request->user_id ? $request->user_id :0;
        $act = $this->actsModel->where(['id'=>$act_id])->first();
        if(empty($act)){//投票活动不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noAct),'code'=>Msg::$err_noAct,'result'=>'投票活动不存在']);

        }
        $act = $act->toArray();
        $detail = $this->detailModel->where(['id'=>$id])->first();
        if(empty($detail)){//候选人不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noActDetail),'code'=>Msg::$err_noActDetail,'result'=>'候选人不存在']);

        }
        $detail = $detail->toArray();

        // 需要支付的红人圈
        $current_score = 1;
        // 用户红人圈查询
        $user = $this->usersModel->select('id','score')->where(['id'=>$user_id])->first();
        if(empty($user)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter,'result'=>'用户不存在！']);
        }
        $user = $user->toArray();
        if($user['score'] < $current_score){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noScore),'code'=>Msg::$err_noScore,'result'=>'您的剩余红人圈不足']);
        }

        // 投票状态
        switch ($act) {
            case strtotime($act['start_time']) > time(): // 未开始
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noStartAct),'code'=>Msg::$err_noStartAct,'result'=>'该投票项目未开始']);
                break;
            case strtotime($act['end_time']) < time(): // 已结束
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_overdueAct),'code'=>Msg::$err_overdueAct,'result'=>'该投票项目已结束']);
                break;
        }

        // 查询用户本项目投票总数
        $records = $this->recordsModel->where(['vote_id'=>$detail['id'],'user_id'=>$user_id])->count('id');
        // 查询用户本项目今日投票总数
        //$today = strtotime(date('Y-m-d'));
        //$tomorrow = strtotime('+1 days');

        //$count = $this->recordsModel->whereRaw(" user_id ={$user_id} and create_time between {$today} and {$tomorrow} ")->count('id');
        //if($count > 5){
            //return response()->json(['msg'=>Msg::getMsg(Msg::$err_alreadyVote),'code'=>Msg::$err_alreadyVote,'result'=>'对不起，您今日的投票次数已用完，请明日再投']);
       // }

        try{
            \DB::beginTransaction();
            $user_vote = $this->recordsModel->where(['user_id'=>$user_id,'vote_id'=>$id])->count('id');// 查询用户是否向本候选人投票
            // 投票记录
            $data['vote_id'] = $id;
            $data['user_id'] = $user_id;
            $data['voted_count'] = $current_score;
            $data['create_time'] = time();
            $this->recordsModel->insert($data);
            // 投票候选人信息更新
            $detail['count_score'] += $current_score;
            if(!$user_vote){
                $detail['count_user'] += 1;
            }
            $this->detailModel->where(['id'=>$id])->update($detail);
            // 投票项目更新
            $act['count_score'] += $current_score;
            if(!$records){
                $act['count_user'] += 1;
            }
            $this->actsModel->where(['id'=>$act_id])->update(['count_user'=>$act['count_user'],'count_score'=>$act['count_score']]);

            // 用户红圈更新
            $this->usersModel->where(['id'=>$user_id])->decrement('score',$current_score);


            // 用户支出记录
            $this->financeModel->insert([
                'user_id' => $user_id,
                'type' => 4,
                'number' => -$current_score,
                'action' => 1,
                'note' => '投票支持红人圈',
                'create_time'=> time()
            ]);

            // 候选人冻结红圈更新
            $this->usersModel->where('id', '=', $detail['uid'])->increment('freeze_score', $current_score);
            // 候选人冻结记录

            $datas['type'] = 3;
            $datas['user_id'] = $detail['uid'];
            $datas['number'] = $current_score;
            $datas['thaw_time'] = time() + (86400 * 7);//冻结7天 
            $datas['create_time'] = time();
            $this->freezeModel->insert($datas);
            // 候选人收入记录
            $user_score = $this->usersModel->where(['id'=> $detail['uid']])->increment('score',$current_score);
            $this->financeModel->insert([
                'user_id' => $detail['uid'],
                'type' => 4,
                'number' => $current_score,
                'action' => 0,
                'note' => '粉丝投票收入',
                'create_time'=> time()
            ]);
            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>'参加投票成功']);
        }catch (\Exception $e){
            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedVote),'code'=>Msg::$err_failedVote,'result'=>'参加投票失败']);
        }

    }


}
