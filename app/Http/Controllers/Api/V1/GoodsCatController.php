<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
class GoodsCatController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    //分类列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $pid = $request->pid ? $request->pid : 0;
        $where = " disabled = 0 and pid = {$pid} ";
        $data = \DB::table('goods_cat')->whereRaw($where)->select('id','title','desc','pid','icon','is_hot')->orderBy('id','desc')->paginate($size)->toArray();

        $result =[
            'total'=>$data['total'],
            'count'=>count($data),
            'page'=>$data['current_page'],
            'size'=>$data['per_page'],
            'last'=>$data['last_page'],
            'data'=>$data['data']
        ];dd($result);
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

}
