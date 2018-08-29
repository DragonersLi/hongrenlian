<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Admin\ActsModel as actsModel;
use App\Models\Admin\ActsDetailModel as detailModel;
use App\Models\Admin\ActsRecordsModel as recordsModel;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
class ActsController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->actsModel = new actsModel();
        $this->detailModel = new detailModel();
        $this->recordsModel = new recordsModel();
    }

    //活动主图列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
		$time = date('Y-m-d H:i:s');
        $where = "status = 1 and '{$time}' between start_time and end_time ";
        $data = $this->actsModel->whereRaw($where)->select('id','index_img')->orderBy('id','desc')->paginate($size)->toArray();
          if(!empty($data['data'])){
             foreach($data['data'] as $k=>$v){
                 $res[$k]['id'] = $v['id'];
                 $res[$k]['index_img'] = $this->base_url.$v['index_img'];
             }
         }else{
             $res = [];
         }


        $result =[
            'total'=>$data['total'],
            'count'=>count($res),
            'page'=>$data['current_page'],
            'size'=>$data['per_page'],
            'last'=>$data['last_page'],
            'data'=>$res
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //活动参与投票列表
    public function votes(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $act_id = $request->act_id;

        if(!$act_id){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>$err_noParameter]);
        }
        if($page == 1){
            $result = $this->actsModel->where(['id'=>$act_id])->orderBy('id','desc')->first()->toArray();
            $title = $result['header'];
            $header = $result['title'];
            $result['title'] = $title;
            $result['header'] = $header;
            $result['index_img'] = $this->base_url.$result['index_img'];
            $result['lunbo_img'] = unserialize($result['lunbo_img']);
            $result['voting_title'] = empty($result['voting_title'])?'':$result['voting_title'];
            $result['voting_desc'] = empty($result['voting_desc'])?'':$result['voting_desc'];
            if(!empty($result['reward_desc'])){
                $result['reward_desc'] =  explode(PHP_EOL, $result['reward_desc']);
            }else{
                $result['reward_desc'] =  '';
            }
            if(!empty($result['voting_desc'])){
                $result['voting_desc'] =  explode(PHP_EOL, $result['voting_desc']);
            }else{
                $result['voting_desc'] =  '';
            }
            if(!empty($result['rule_desc'])){
                $result['rule_desc'] =  explode(PHP_EOL, $result['rule_desc']);
            }else{
                $result['rule_desc'] =  '';
            }
            foreach($result['lunbo_img'] as $k=>$v){
                $result['lunbo_img'][$k] = $this->base_url.$v;
            }
        }

         $data = $this->detailModel->join("users","users.id",'=',"acts_detail.uid")
            ->select("acts_detail.*","users.username","users.mobile","users.avatar")
            ->where(['act_id'=>$act_id,'acts_detail.status'=>1])
            ->orderBy('id','desc')
            ->paginate($size)
            ->toArray();

        $result['total'] = $data['total'];
        $result['count'] =count($data['data']);
        $result['page'] = $data['current_page'];
        $result['size'] = $data['per_page'];
        $result['last'] = $data['last_page'];
        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                $result['data'][$k]['id'] = $v['id'];
                $result['data'][$k]['name'] = $v['username'];
                $result['data'][$k]['mobile'] = $v['mobile'];
                $result['data'][$k]['user_count'] = $v['count_user'];
                $result['data'][$k]['vote_count'] = $v['count_score'];
                if($v['user_img']){
                    if(substr($v['user_img'],0,4) == 'http'){
                        $result['data'][$k]['avatar'] = $v['user_img'];//头像
                    }else{
                        $result['data'][$k]['avatar'] = $this->base_url.$v['user_img'];//头像
                    }
                }else{
                    $result['data'][$k]['avatar']  = '';//无头像
                }
            }
        }else{
            $result['data'] = [];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //投票动作
    public function voting(Request $request)
    {
        try{
            $data = $request->only('user_id','vote_id');

			\DB::beginTransaction();
			 $time = date('Y-m-d H:i:s');
             $res =$this->recordsModel->where(['user_id'=>$data['user_id'],'vote_id'=>$data['vote_id']])->first();
             $user = \DB::table('users')->where(['id'=>$data['user_id']])->select('score')->first();

			if(!$data['user_id'] || !$data['vote_id']){//参数丢失
			    return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
			}
             if($user->score<1){//红人圈分值不够
				return response()->json(['msg'=>Msg::getMsg(Msg::$err_noScore),'code'=>Msg::$err_noScore]);
            }
            if(empty($res)){//第一次投票，插入操作
                $data['voted_count'] = 1;
                $data['create_time'] = $time;
                $data['update_time'] = $time;
                $record = \DB::table('acts_records')->insert($data);
            }else{//多次投票，更新操作
                $data['voted_count'] = $res->voted_count + 1;
                $data['update_time'] = $time;
                $record = $this->recordsModel->where(['id'=>$res->id])->update($data);

            }
            $detailData = ['count_score'=>\DB::raw('count_score + 1'),'count_user'=>\DB::raw('count_user + 1')];
            $detail = $this->detailModel->where(['id'=>$data['vote_id']])->update($detailData);
            // increment('count_score',1);//被投票数量加1
            $score = \DB::table('users')->where(['id'=>$data['user_id']])->decrement('score',$this->reward['fans_like']);//投票者红人圈分值减1
           if($record && $detail && $score){
               \DB::commit();
			    $return = ['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none];
           }else{
               \DB::rollback();
                $return = ['msg'=>Msg::getMsg(Msg::$err_failedVote),'code'=>Msg::$err_failedVote];
           }
				return response()->json($return);

        }catch (\Exception $e){
			return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedVote),'code'=>Msg::$err_failedVote]);
        }


    }


}
