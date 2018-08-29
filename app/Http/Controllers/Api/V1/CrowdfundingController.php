<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\CrowdfundingModel;
use App\Models\Admin\CrowdfundingDetailModel;
use App\Models\Admin\FinanceModel;
use App\Models\Admin\UsersModel;
use App\Models\Admin\CollectModel;
class CrowdfundingController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new CrowdfundingModel;
        $this->detailModel = new CrowdfundingDetailModel;
        $this->financeModel = new FinanceModel;
        $this->usersModel = new UsersModel;
        $this->collectModel = new CollectModel;
    }

    //列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;//分页
        $size = $request->size ?  $request->size : $this->page_size;//分页取记录数
        $keywords = $request->keywords ? $request->keywords : '';//关键词
        $status = $request->status ? $request->status : 0;//状态
        $id = $request->id ? $request->id : 0;
        $user_id = $request->user_id ? $request->user_id : 0;
        $datetime = time();
        $where = " 1 = 1  and ( ('{$datetime}' between start_time and end_time) or (status = 1) or (status = 0 and '{$datetime}' > end_time))";
        if($id){
            $where.= " and id ={$id}";
        }
        if($status){
            switch ($status) {
                case 1: // 未开始
                    $where .= " and '{$datetime}' < start_time";
                    break;
                case 2: // 进行中
                    $where.= " and '{$datetime}' between start_time and end_time";
                    break;
                case 3: // 已成功
                    $where.= " and status = 1 ";
                    break;
                case 4: // 未成功
                   $where.=" and status = 0 and '{$datetime}' > end_time";
                    break;
            }
        }

        $data = $this->model->whereRaw($where)->orderBy('id','desc')->paginate($size)->toArray();
            foreach($data['data'] as $k=>$v){
                // 众筹状态
                switch ($v) {
                    case $v['start_time'] > $datetime:
                        $res[$k]['status'] = 1;// 未开始
                        break;
                    case $v['start_time'] < $datetime && $v['end_time'] > $datetime:
                        $res[$k]['status'] = 2;// 进行中
                        break;
                    case $v['status'] == 1:
                        $res[$k]['status'] = 3;// 已成功
                        break;
                    case $v['status'] == 0 && $v['end_time'] < time():
                        $res[$k]['status'] = 4;// 未成功
                        break;
                }
                if(in_array($res[$k]['status'],[2,3,4])){
                    // 众筹进度
                    $res[$k]['plan_rate'] = (string)(sprintf("%.2f", $v['current_score'] / $v['count_score']) * 100);
                    $res[$k]['id'] = $v['id'];
                    $res[$k]['title'] = $v['title'];
                    $res[$k]['intro'] = $v['intro'];
                    $res[$k]['pv'] = $v['pv'];
                    $res[$k]['count_score'] = $v['count_score'];
                    $res[$k]['current_score'] = $v['current_score'];
                    $res[$k]['scale'] = $v['scale'];
                    $res[$k]['start_time'] = $v['start_time'];
                    $res[$k]['end_time'] = $v['end_time'];
                    $res[$k]['create_time'] = $v['create_time'];
                    $res[$k]['update_time'] = $v['update_time'];
                    $res[$k]['count_user'] = $v['count_user'];
                    $res[$k]['gift'] = $v['gift'];
                    $res[$k]['index_img'] = $this->base_url.$v['index_img'];
                    $collect = $this->collectModel->where(['x_type'=>1,'user_id'=>$user_id,'x_id'=>$v['id']])->first();
                    $res[$k]['is_collect'] = empty($collect) ? 0 :1 ;

                }

            }
        $result = $id ?  ['data'=>$res]:  [
            'total'=>$data['total'],
            'count'=>count($data['data']),
            'page'=>$data['current_page'],
            'size'=>$data['per_page'],
            'last'=>$data['last_page'],
            'data'=>$res,
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //众筹支持
    public function pay(Request $request){

        $id = $request->id ? $request->id : 0;
        $count = $request->count ? $request->count : 0;
        $user_id = $request->user_id ? $request->user_id :0;
        $detail = $this->model->where(['id'=>$id])->first();
        if(empty($detail)){//众筹项目不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noCrowdfunding),'code'=>Msg::$err_noCrowdfunding,'result'=>'众筹项目不存在']);

         }
        $detail = $detail->toArray();
        $current_score = $detail['scale'] * $count;//需支付红人圈数
        $user = $this->usersModel->where(['id'=>$user_id])->first();
        if(empty($user)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter,'result'=>'用户不存在！']);
        }
        $user = $user->toArray();
        if($user['score'] < $current_score){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noScore),'code'=>Msg::$err_noScore,'result'=>'您的剩余红人圈不足']);
        }

        // 众筹状态
        switch ($detail) {
            case $detail['status'] == 0 && $detail['start_time'] > time(): // 未开始
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noStartCrowdfunding),'code'=>Msg::$err_noStartCrowdfunding,'result'=>'该众筹项目还未开始']);
                break;
            case $detail['status'] == 0 && $detail['end_time'] < time(): // 众筹失败
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_overdueCrowdfunding),'code'=>Msg::$err_overdueCrowdfunding,'result'=>'该众筹项目已过期']);
                break;
            case $detail['status'] == 1: // 众筹成功
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_finishCrowdfunding),'code'=>Msg::$err_finishCrowdfunding,'result'=>'该众筹项目已完成']);
                break;
        }
        // 是否超出众筹
        if($current_score > $detail['count_score'] - $detail['current_score']){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_overdueScoreRange),'code'=>Msg::$err_overdueScoreRange,'result'=>'众筹红人圈超出范围']);
        }

        // 查询用户是否已支持本项目
        $user_pay = $this->detailModel->where(['cid'=>$id,'uid'=>$user_id])->count('id');
		try {
			\DB::beginTransaction();
            // 众筹记录
            $data['cid'] = $id;
            $data['uid'] = $user_id;
            $data['current_count'] = $detail['scale'];
            $data['current_score'] = $current_score;
            $data['create_time'] = time();
            $this->detailModel->insert($data);
            // 众筹项目更新

            $detail['current_score'] += $current_score;
            // 是否完成
            if($detail['current_score'] == $detail['count_score']){
                $detail['status'] = 1;
            }
            if(!$user_pay){
                $detail['count_user'] += 1;
            }
            $this->model->where(['id'=>$detail['id']])->update($detail);

            // // 用户红圈更新
            $this->usersModel->where(['id'=>$user_id])->decrement('score',$current_score);

            // 用户支出记录
            $this->financeModel->insert([
                 'user_id' => $user_id,
                 'type' => 3,
                 'number' => -$current_score,
                 'action' => 1,
                 'note' => '众筹支持红人圈',
                 'create_time'=> time()
             ]);
            
            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>'参加众筹成功']);
			
	    } catch (\Exception $e) { //dd($e->getMessage());
            \DB::rollback(); 
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedCrowdfunding),'code'=>Msg::$err_failedCrowdfunding,'result'=>'参加众筹失败']);
        }

    }


}
