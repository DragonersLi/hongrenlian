<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\CircleModel;
use App\Models\Admin\UsersModel;
use App\Models\Admin\FinanceModel;
class CircleController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new CircleModel;
        $this->usersModel = new UsersModel;
        $this->financeModel = new FinanceModel;
    }


    //收圈记录
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = $this->model->where(['user_id'=>$user_id,'status'=>1])->select('number','update_time')->orderBy('id','desc')->paginate($size)->toArray();
        $result = ['data'=>[]];
        if(!empty($data['data'])){
            $result = [
                'total' => $data['total'],
                'count' => count($data['data']),
                'page' => $data['current_page'],
                'size' => $data['per_page'],
                'last' => $data['last_page'],
                'data' => $data['data'],
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //读取
    public function read(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        //超时的红人圈
        $time = time();
        $start_time  = date('Ymd04');
        $end_time = date('YmdH');
        $this->model->whereRaw("user_id = {$user_id} and status = 0 and timeout < {$time}")->update(['status'=>-1,'update_time'=>time()]);

        //生成新的红人圈
        //查询总数今日已生成次数
        $circle = $this->model->whereRaw("user_id = {$user_id} and cycle between {$start_time} and {$end_time}")->select(\DB::raw('sum(number) as total'),\DB::raw('count(id) as count'))->first();

        //查询已生成周期
        $cycles = $this->model->whereRaw("user_id = {$user_id} and cycle between {$start_time} and {$end_time}")->select('cycle')->get()->toArray();
			$cycles_old = [];
          foreach($cycles as $k=>$v){
              $cycles_old[] = $v['cycle'];
          }
        if($end_time > $start_time && $circle && $circle->count < 10 && $circle->total <= $this->max_circle) {
            $counter = $circle->total; //计数器

            for ($i = $start_time; $i <= $end_time; $i++) {
                if(!($i & 1)){
                //如果达到每日生成最大值结束生产
                if($counter >= $this->max_circle) {
                    break;
                }

                    $cycle = (string) $i;

                //如果这个周期已经生成，直接跳过
                if(in_array($cycle, $cycles_old)){
                    continue;
                }
                $randFloat = round($this->rand_circle['min'] + mt_rand() / mt_getrandmax() * ($this->rand_circle['max'] - $this->rand_circle['min']), 2);

                //如果累计生成数 + 当前随机生成超过最大值，取 最大值 - 累计值
                if(($counter + $randFloat) > $this->max_circle){
                    $randFloat = $this->max_circle - $counter;
                }

                $counter += $randFloat;
                $timeout = substr($cycle, 0, 4) . '-' . substr($cycle, 4, 2) . '-' .substr($cycle, 6, 2) . ' ' .substr($cycle, 8, 2) . ':00';

                $data = [
                    'cycle' => $cycle,
                    'number' => $randFloat,
                    'user_id' => $user_id,
                    'timeout' => strtotime($timeout) + (3600 * 48),
                    'create_time'=> time()
                ];
                $this->model->insert($data);
                }
            }
        }

        //可收的红人圈
        $data = $this->model->whereRaw("user_id = {$user_id} and status = 0 and timeout > {$time} ")->select('id','number')->paginate($size)->toArray();
        $result = ['data'=>[]];
        if(!empty($data['data'])){
            $result = [
                'total' => $data['total'],
                'count' => count($data['data']),
                'page' => $data['current_page'],
                'size' => $data['per_page'],
                'last' => $data['last_page'],
                'data' => $data['data'],
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);

    }

    //收圈
    public function update(Request $request)
    {
        $id = $request->id ? $request->id : 0;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id) || empty($id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }

        $circle = $this->model->where(['id'=>$id])->first();
        if(empty($circle)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }
        if($user_id != $circle->user_id){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_illegalRequest),'code'=>Msg::$err_illegalRequest]);
        }
        if(empty($circle)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }
        if($circle->status == 1){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_alreadyCollectScore),'code'=>Msg::$err_alreadyCollectScore]);
        }
        if($circle->status == -1){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_overtimeScore),'code'=>Msg::$err_overtimeScore]);
        }

        try {
            \DB::beginTransaction();
            // 记录
            $finance = $this->financeModel->insert([
                'user_id' => $circle->user_id,
                'type' => 1,
                'number' => $circle->number,
                'action' => 0,
                'note' => '日常红人圈领取',
                'create_time'=> time()
            ]);
            //领取
            $this->model->where(['id'=>$id])->update(['status'=>1,'update_time'=>time()]);
            //更新总数
            $this->usersModel->where(['id'=>$user_id])->increment('score',$circle->number);
            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }
    }

    //删除
    public function delete(Request $request)
    {
        $id = $request->id ? $request->id : 0;
        if(empty($id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        if($this->model->where(['id'=>$id])->delete()){//删除成功
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }else{
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }
    }

}

