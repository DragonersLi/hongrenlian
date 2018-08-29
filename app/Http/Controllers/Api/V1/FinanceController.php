<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\FinanceModel;
use App\Models\Admin\UsersModel;
class FinanceController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new FinanceModel;
        $this->usersModel = new UsersModel;
    }

    //收支列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id :0;
        $action = $request->action ? $request->action : 0;
        $type = $request->type ? $request->type : 0;
        if(empty($user_id) || !isset($action) || !isset($type)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $where = "user_id = {$user_id}";//action=0 && type=0 取全部
        $where .= $action ? " and action = 1":" and action = 0";
        if($type){
            $where .= ($type == 13) ? " and type in(5,6,7,8)": " and type = {$type}";
        }

        $data = $this->model->whereRaw($where)->orderBy('id','desc')->paginate($size)->toArray();

        $result =[
            'count'=>count($data),
            'data'=>$data,
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);

    }

}

