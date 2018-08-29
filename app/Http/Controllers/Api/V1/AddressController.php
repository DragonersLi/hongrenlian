<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\AddressModel;
use App\Models\Admin\UsersModel;
use App\Models\Admin\RegionModel;
class AddressController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new AddressModel;
        $this->usersModel = new UsersModel;
        $this->regionModel = new RegionModel;
    }

    //收货地址列表
    public function index(Request $request)
    {
        $user_id = $request->user_id ? $request->user_id :0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);

        }
        //用户所有收货地址
        $data = $this->model->where(['user_id'=>$user_id])->get();
        $list = [];
        if(!empty($data)){
            $data = $data->toArray();
            foreach($data as $k=>$v){
                $list[$k]['id'] = $v['id'];
                $list[$k]['receive_name'] = $v['receive_name'];
                $list[$k]['receive_phone'] = $v['receive_phone'];
                $list[$k]['is_default'] = $v['is_default'];
                $list[$k]['province'] = $v['province'];
                $list[$k]['city'] = $v['city'];
                $list[$k]['area'] = $v['area'];
                $addr = $this->regionModel->whereRaw( "id = {$v['province']} or id = {$v['city']} or id = {$v['area']} ")->select(\DB::raw('GROUP_CONCAT(name separator "") as addr'))->first();
                $list[$k]['address'] = (!empty($addr) ? $addr->addr:'').$v['address'];
            }
        }
        $result =[
            'count'=>count($list),
            'data'=>$list,
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);

    }
    //添加收货地址
    public function add(Request $request)
    {
        $data = $request->only('user_id','province','city','area','address','zip','receive_name','receive_phone','is_default');
        !isset($data['is_default']) && $data['is_default'] = 0;
        $time = time();

        try{

            \DB::beginTransaction();
            $address = $this->model->where(['user_id'=>$data['user_id']])->first();//已存在地址
            if(!empty($address)){//已存在地址
                $is_default_address = $this->model->where(['user_id'=>$data['user_id'],'is_default'=>1])->first();//已存在的默认地址

                if($is_default_address && $data['is_default']){//有默认地址,且当前要设置默认，旧数据更新为非默认
                    $this->model->where(['user_id'=>$data['user_id'],'is_default'=>1])->update(['is_default'=>0,'update_time'=>$time]);
                }

            }else{//不存在，第一次添加设置成默认地址

                $data['is_default'] = 1;

            }

            $data['create_time'] = $time;
            $this->model->insert($data);

            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }catch (\Exception $e){

            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedAddAddress),'code'=>Msg::$err_failedAddAddress]);
        }

    }

    //修改收货地址
    public function edit(Request $request)
    {
        $data = $request->only('id','user_id','province','city','area','address','zip','receive_name','receive_phone','is_default');
        $data['update_time'] = time();

        try{
            if(empty($data['id'])){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);

            }

            \DB::beginTransaction();

            if(isset($data['is_default']) &&  $data['is_default'] == 1 ){//默认地址
                $address = $this->model->whereRaw(" id !={$data['id']} and user_id ={$data['user_id']} and is_default = 1 ")->first();
                if(!empty($address)){//已存在默认地址,则更新为非默认
                    $this->model->where(['id'=>$address->id])->update(['is_default'=>0,'update_time'=>time()]);
                }
            }else{//非默认
                $address = $this->model->whereRaw(" id !={$data['id']}  and user_id ={$data['user_id']}  and is_default = 1 ")->first();
                if(empty($address)){//无默认地址
                    $address = $this->model->whereRaw(" id !={$data['id']}  and user_id ={$data['user_id']}  and is_default = 0 ")->first();//取非默认第一条
                    if(empty($address)){//没有非默认
                        $data['is_default'] = 1;
                    }else{
                        $this->model->where(['id'=>$address->id])->update(['is_default'=>1,'update_time'=>time()]);
                    }

                }


            }
            $this->model->where(['id'=>$data['id']])->update($data);
            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }catch (\Exception $e){

            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedEditAddress),'code'=>Msg::$err_failedEditAddress]);
        }


    }

    //显示收货地址
    public function show(Request $request)
    {
        $id = $request->id ? $request->id :0;
        if(!$id){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = $this->model->where(['id'=>$id])->select('id','user_id','province','city','area','address','zip','receive_name','receive_phone','is_default')->first();
        if(!empty($data)){
            $data = $data->toArray();
            $addr = $this->regionModel->whereRaw( "id = {$data['province']} or id = {$data['city']} or id = {$data['area']} ")->select(\DB::raw('GROUP_CONCAT(name separator "|") as addr'))->first();
            $arr = explode('|',$addr->addr);
            isset($arr[0]) && $data['province_name'] = $arr[0];
            isset($arr[1]) && $data['city_name'] = $arr[1];
            isset($arr[2]) && $data['area_name'] = $arr[2];
        }else{
            $data = [];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);
    }

    //删除收货地址
    public function del(Request $request)
    {


        try{
            $id = $request->id ? $request->id : 0;
            $user_id = $request->user_id ? $request->user_id :0;
            if(!$user_id){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);

            }
            if(!$id){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
            }

            $address = $this->model->where(['id'=>$id])->first();

            if(empty($address)){//已删除
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
            }

            \DB::beginTransaction();
            if($address->is_default){//如果是默认
                $res = $this->model->whereRaw("user_id = {$user_id} and id !={$id}")->orderBy('id','asc')->first();

                if(!empty($res)){//把第一条设默认
                    $this->model->where(['id'=>$res->id])->update(['is_default'=>1,'update_time'=>time()]);
                }

            }
            $this->model->where(['id'=>$id])->delete();
            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }catch (\Exception $e){ dd($e);

            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedDelAddress),'code'=>Msg::$err_failedDelAddress]);
        }
    }

}

