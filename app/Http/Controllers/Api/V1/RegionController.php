<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\RegionModel;
class RegionController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new RegionModel;
    }
	
    //省市区
    public function index($id = 0)
    {
        $id = $id ? $id : 100000;
        $list =  $this->model->where(['pid'=>$id])->get()->toArray();

      $list = empty($list) ? [] : $list;

        $result =[
            'count'=>count($list),
            'data'=>$list
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }
    //省市区
    public function test()
    {

        $list =  $this->model->get()->toArray();
        foreach($list as $k=>$v){
            if($v['pid'] == 100000){
                $data[$v['id']] = $v;
                foreach($list as $k1=>$v1){
                    if($v['id'] == $v1['pid']){
                        $data[$v['id']]['child'][$v1['id']]= $v1;
                        foreach($list as $k2=>$v2){
                            if($v1['id'] == $v2['pid']){
                                $data[$v['id']]['child'][$v1['id']]['child'][$v2['id']][]= $v2;
                            }
                        }
                    }
                }
            }
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>[ 'data'=>$data  ]]);
    }
}
