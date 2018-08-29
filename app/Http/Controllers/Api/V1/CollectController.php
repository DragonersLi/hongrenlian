<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\CollectModel;
use App\Models\Admin\ActsModel;
use App\Models\Admin\ActsRecordsModel;
use App\Models\Admin\CrowdfundingModel;
use App\Models\Admin\CrowdfundingDetailModel;
class CollectController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->collectModel = new CollectModel;
        $this->actsModel = new ActsModel;
        $this->recordsModel = new ActsRecordsModel;
        $this->crowdfundingModel = new CrowdfundingModel;
        $this->detailModel = new CrowdfundingDetailModel;
    }

    //列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;//分页
        $size = $request->size ?  $request->size : $this->page_size;//分页取记录数
        $keywords = $request->keywords ? $request->keywords : '';//关键词
        $status = $request->status ? $request->status : 0;//状态
        $datetime = date('Y-m-d H:i:s');
        $where = " 1 = 1  and ( ('{$datetime}' between start_time and end_time) or (status = 1) or (status = 0 and '{$datetime}' > end_time))";
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
                $res[$k]['start_time'] = strtotime($v['start_time']);
                $res[$k]['end_time'] = strtotime($v['end_time']);
                $res[$k]['create_time'] = $v['create_time'];
                $res[$k]['update_time'] = $v['update_time'];
                $res[$k]['count_user'] = $v['count_user'];
                $res[$k]['gift'] = $v['gift'];
                $res[$k]['index_img'] = $this->base_url.$v['index_img'];
            }

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

    //添加或取消收藏 type（0:红人，1：众筹，2：投票）
    public function addOrCancel(Request $request){

        $data = $request->only('user_id','type','id');
        if(!$data['user_id'] || !isset($data['type']) || !$data['id']){//参数丢失
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $collect = $this->collectModel->where(['x_id'=>$data['id'],'x_type'=>$data['type'],'user_id'=>$data['user_id']])->first();
        if(!empty($collect)){//已收藏，则取消
            $collect = $this->collectModel->where(['x_id'=>$data['id'],'x_type'=>$data['type'],'user_id'=>$data['user_id']])->delete();
            if($collect){
                $code = Msg::$err_none;
                $status = 0;
            }else{
                $code = Msg::$err_failedCancelCollect;
                $status = 0;
            }
        }else{//未收藏，则收藏
            $collect_data['user_id'] = $data['user_id'];
            $collect_data['x_id'] = $data['id'];
            $collect_data['x_type'] = $data['type'];
            $collect_data['create_time'] = time();
            $result = $this->collectModel->insert($collect_data);
            if($result){
                $code = Msg::$err_none;
                $status = 1;

            }else{
                $code = Msg::$err_failedCollect;
                $status = 1;
            }
        }
        return response()->json(['msg'=>Msg::getMsg($code),'code'=>$code,'result'=>['status'=>$status]]);

    }




    //我的众筹1，投票2列表
    public function my(Request $request){
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        $type = $request->type ? $request->type : 0;
        if(empty($user_id) || !in_array($type,[1,2])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }

        $list = $this->collectModel->where(['user_id'=>$user_id,'x_type'=>$type])->select('x_id as id')->get()->toArray();

        $result = ['data'=>[]];
        if(!empty($list)){
            $ids = [];
            foreach($list as $k=>$v){
                $ids[] = $v['id'];
            }
            $str = implode(',',$ids);
            if($type > 1){//投票
                $datetime = date('Y-m-d H:i:s');
                $data = $this->actsModel->whereRaw("id in({$str})")->paginate($size)->toArray();
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
            } else{//众筹
                $datetime = time();
                $data = $this->crowdfundingModel->whereRaw("id in ({$str})")->paginate($size)->toArray();
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

                    // 众筹进度
                    $res[$k]['plan_rate'] = (string)(sprintf("%.2f", $v['current_score'] / $v['count_score']) * 100);
                    $res[$k]['id'] = $v['id'];
                    $res[$k]['title'] = $v['title'];
                    $res[$k]['intro'] = $v['intro'];
                    $res[$k]['pv'] = $v['pv'];
                    $res[$k]['count_score'] = $v['count_score'];
                    $res[$k]['current_score'] = $v['current_score'];
                    $res[$k]['scale'] = $v['scale'];
                    $res[$k]['start_time'] = strtotime($v['start_time']);
                    $res[$k]['end_time'] = strtotime($v['end_time']);
                    $res[$k]['create_time'] = $v['create_time'];
                    $res[$k]['update_time'] = $v['update_time'];
                    $res[$k]['count_user'] = $v['count_user'];
                    $res[$k]['gift'] = $v['gift'];
                    $res[$k]['index_img'] = $this->base_url.$v['index_img'];

                }
            }
            if(!empty($data['data'])){
                $result =[
                    'total'=>$data['total'],
                    'count'=>count($data['data']),
                    'page'=>$data['current_page'],
                    'size'=>$data['per_page'],
                    'last'=>$data['last_page'],
                    'data'=>$res,
                ];
            }
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //我的活动众筹1，投票2列表
    public function myact(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ? $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        $type = $request->type ? $request->type : 0;
        if (empty($user_id) || !in_array($type, [1, 2])) {
            return response()->json(['msg' => Msg::getMsg(Msg::$err_noParameter), 'code' => Msg::$err_noParameter]);
        }

        $result = ['data' => []];
        if ($type > 1){//投票
            $datetime = date('Y-m-d H:i:s');
            $list = $this->recordsModel->where(['user_id'=>$user_id])->join('acts_detail','acts_detail.id','acts_records.vote_id')->select(\DB::raw('distinct(act_id) as id'))->get()->toArray();

            if (!empty($list)) {
                $ids = [];
                foreach ($list as $k => $v) {
                    $ids[] = $v['id'];
                }
                $str = implode(',', $ids);
                $data = $this->actsModel->whereRaw("id in({$str})")->paginate($size)->toArray();
                foreach ($data['data'] as $k => $v) {

                    $res[$k]['id'] = $v['id'];
                    $res[$k]['header'] = $v['header'];
                    $res[$k]['title'] = $v['title'];
                    $res[$k]['index_img'] = $this->base_url . $v['index_img'];
                    $lunbo_img = unserialize($v['lunbo_img']);
                    foreach ($lunbo_img as $key => $val) {
                        $res[$k]['lunbo_img'][$key] = $this->base_url . $val;
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
            }
        }else{//众筹
            $datetime = time();
            $list = $this->detailModel->where(['uid'=>$user_id])->select(\DB::raw('distinct(cid) as id'))->get()->toArray();
            if (!empty($list)) {
                $ids = [];
                foreach ($list as $k => $v) {
                    $ids[] = $v['id'];
                }
                $str = implode(',', $ids);
                $data = $this->crowdfundingModel->whereRaw("id in ({$str})")->paginate($size)->toArray();
                foreach ($data['data'] as $k => $v) {
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

                    // 众筹进度
                    $res[$k]['plan_rate'] = (string)(sprintf("%.2f", $v['current_score'] / $v['count_score']) * 100);
                    $res[$k]['id'] = $v['id'];
                    $res[$k]['title'] = $v['title'];
                    $res[$k]['intro'] = $v['intro'];
                    $res[$k]['pv'] = $v['pv'];
                    $res[$k]['count_score'] = $v['count_score'];
                    $res[$k]['current_score'] = $v['current_score'];
                    $res[$k]['scale'] = $v['scale'];
                    $res[$k]['start_time'] = strtotime($v['start_time']);
                    $res[$k]['end_time'] = strtotime($v['end_time']);
                    $res[$k]['create_time'] = $v['create_time'];
                    $res[$k]['update_time'] = $v['update_time'];
                    $res[$k]['count_user'] = $v['count_user'];
                    $res[$k]['gift'] = $v['gift'];
                    $res[$k]['index_img'] = $this->base_url . $v['index_img'];

                }
            }
        }
        if (!empty($data['data'])) {
            $result = [
                'total' => $data['total'],
                'count' => count($data['data']),
                'page' => $data['current_page'],
                'size' => $data['per_page'],
                'last' => $data['last_page'],
                'data' => $res,
            ];
        }
        return response()->json(['msg' => Msg::getMsg(Msg::$err_none), 'code' => Msg::$err_none, 'result' => $result]);
    }

}
