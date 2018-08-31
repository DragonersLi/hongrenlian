<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\UsersModel;
use App\Models\Admin\SignModel;
use App\Models\Admin\FreezeModel;
use App\Models\Admin\FinanceModel;
use App\Models\Admin\AddressModel;
use App\Models\Admin\InviteModel;
use App\Models\Admin\FollowModel;
use App\Models\Admin\MessageModel as MessageModel;
class MyController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->usersModel = new UsersModel;
        $this->signModel = new SignModel;
        $this->freezeModel = new FreezeModel;
        $this->financeModel = new FinanceModel;
        $this->addressModel = new AddressModel;
        $this->inviteModel = new InviteModel;
        $this->followModel = new FollowModel;
    }

    //个人中心
    public function index(Request $request)
    {
        $user_id = $request->user_id ? $request->user_id :0;
        $user = $this->usersModel->where(['id'=>$user_id])->first();
        if(empty($user)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
        }
        $hot_images = empty($user->hot_images) ? [] : unserialize($user->hot_images);
        foreach($hot_images as $k=>$v){
            $hot_images[$k] = $this->base_url.$v;
        }
        if(!empty($user->avatar)){
            if(substr($user->avatar,0,4) == 'http'){
                $data['avatar'] = $user->avatar;//头像
            }else{
                $data['avatar'] = $this->base_url.$user->avatar;//头像
            }
        }else{
            $data['avatar'] = '';//无头像
        }


        $data['score'] = (float)$user->score;//用户总红人圈数
        $data['freeze_score'] = (float)$user->freeze_score;//用户冻结红人圈数
        $user_sign = $this->signModel->where(['user_id'=>$user_id])->first();
        $data['sign_status'] = 0;//未签到
        if(!empty($user_sign)){
            $now_date = date('Y-m-d'); //当前日期
            $pre_date = date('Y-m-d',strtotime($user_sign->update_time)); //上次签到日期
            $data['sign_status'] = ($now_date == $pre_date) ? 1 : 0;

        }
        $data['user_id'] = $user->id;//用户id
        $data['token'] = $user->token;//用户token
        $data['over_time'] = $user->token_overtime;//token过期时间
        $data['fans'] = $user->fans;//粉丝数
        $data['follow'] = $user->follow;//关注数
        $data['likes'] = $user->likes;//喜欢数
        $data['birthday'] = $user->birthday;//用户生日
        $data['sex'] = $user->sex;//用户性别
        $data['level'] = $user->level;//用户等级
        $data['ranking'] = $this->user_ranking+$user->id;//排名
        $data['mobile'] = $user->mobile;//用户手机号
        $data['username'] = !empty($user->username)?$user->username:substr_replace($user->mobile,'****',3,4);//用户名
        $data['constellation'] = $user->constellation;//所属星座
        $invite_count = $this->inviteModel->where(['user_id'=>$user->id])->count('id');//邀请总人数
        $data['hot_intro'] = $user->hot_intro;//红人简介
        $data['hot_images'] = $hot_images;//红人图片
        $data['hot_video'] = empty($user->hot_video) ? '': $this->base_url.$user->hot_video;//红人视频
        $data['invite_count_user'] = $invite_count;//邀请人数
        $data['invite_count_score'] = $invite_count * $this->reward['invite_user'];//邀请获得红人圈
        $data['invite_code'] = uidEncode($user->id);//邀请码
        $data['transfer'] = 1;//是否显示转账
        $data['is_businesser'] = $user->is_businesser;//是否商家
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);

    }

    //签到
    public function sign(Request $request)
    {
        try{
            $user_id = $request->user_id ? $request->user_id : 0;
            $datetime = date('Y-m-d H:i:s');
            $data['update_time'] = $datetime;
            $data['user_id'] = $user_id;
            $sign = $this->signModel->where(['user_id'=> $data['user_id']])->first();
            \DB::beginTransaction();//开启事务
            if(empty($sign)) {//第一次签到
                $data['sign_total'] = 1;
                $data['sign_count'] = 1;
                $data['create_time'] = $datetime;
                $this->signModel->insert($data);
            }else{//多次签到
                $now_date = date('Y-m-d',strtotime($datetime)); //当前日期
                $pre_date = date('Y-m-d',strtotime($sign->update_time)); //上次签到日期
                if($now_date == $pre_date){//同一天
                    return response()->json(['msg'=>Msg::getMsg(Msg::$err_alreadySign),'code'=>Msg::$err_alreadySign]);
                }
                if(strtotime($now_date) > strtotime($pre_date) + 24*3600 ){//超过一天没签到
                    $data['sign_count'] = 1;//连续签到为1
                } else{
                    $data['sign_count'] = $sign->sign_count + 1;
                }
                //后续根据sign_count连续签到次数赠送红人圈
                $data['sign_total'] = $sign->sign_total + 1;
                $this->signModel->where(['user_id'=>$user_id])->update($data);
            }

            $this->usersModel->where(['id'=>$user_id])->increment('score',$this->reward['sign_user']);//签到红人圈分值+1
            $this->financeModel->insert([
                'user_id' => $user_id,
                'type' => 0,
                'number' => $this->reward['sign_user'],
                'action' => 11,
                'note' => '签到收入',
                'create_time'=> time()
            ]);

            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        } catch (\Exception $e) {//$e->getCode(), $e->getMessage()
            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedSign),'code'=>Msg::$err_failedSign]);
        }

    }


    //设置交易密码
    public function setSafePwd(Request $request){
        $data = $request->only('user_id','safe_pwd','repeat_pwd','sms_code');
        if(empty($data['user_id']) || empty($data['safe_pwd']) || empty($data['repeat_pwd']) || empty($data['sms_code'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $user = $this->usersModel->where(['id'=>$data['user_id']])->select('mobile','safepwd')->first();
        if(empty($user)){//当前用户不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
        }
        $cache_sms_code = \Cache::get('appsms_'.$user->mobile);
        if($data['sms_code'] != $this->default_sms_code){//短信验证码不一致
            if($data['sms_code'] != $cache_sms_code){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_errSmsCode),'code'=>Msg::$err_errSmsCode]);
            }
        }
        if($data['safe_pwd'] != $data['repeat_pwd'] ){//非同
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_differenceSafePwd),'code'=>Msg::$err_differenceSafePwd]);
        }

        if(!preg_match("/^\d{6}$/",$data['safe_pwd'])){//非6位纯数字
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_onlySixNumberSafePwd),'code'=>Msg::$err_onlySixNumberSafePwd]);
        }
        $safepwd = MD5($this->salt.$data['safe_pwd']);
        $res = $this->usersModel->where(['id'=>$data['user_id']])->update(['safepwd'=>$safepwd,'update_time'=>time()]);
        if($res){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }else{
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedSetSafePwd),'code'=>Msg::$err_failedSetSafePwd]);
        }

    }
    //重置交易密码
    public function resetSafePwd(Request $request){
        $data = $request->only('user_id','old_pwd','safe_pwd','repeat_pwd');
        if(empty($data['user_id']) || empty($data['safe_pwd']) || empty($data['repeat_pwd']) || empty($data['old_pwd'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        if($data['old_pwd'] == $data['safe_pwd']){//新旧密码不能一样
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_cannotSameSafePwd),'code'=>Msg::$err_cannotSameSafePwd]);
        }
        $user = $this->usersModel->where(['id'=>$data['user_id']])->select('mobile','safepwd')->first();
        if($user->safepwd != MD5($this->salt.$data['old_pwd'])){//旧密码不一致
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_errorOldSafePwd),'code'=>Msg::$err_errorOldSafePwd]);
        }

        if($data['safe_pwd'] != $data['repeat_pwd'] ){//非同
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_differenceSafePwd),'code'=>Msg::$err_differenceSafePwd]);
        }

        if(!preg_match("/^\d{6}$/",$data['safe_pwd'])){//非6位纯数字
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_onlySixNumberSafePwd),'code'=>Msg::$err_onlySixNumberSafePwd]);
        }
        $safepwd = MD5($this->salt.$data['safe_pwd']);
        $res = $this->usersModel->where(['id'=>$data['user_id']])->update(['safepwd'=>$safepwd,'update_time'=>time()]);
        if($res){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }else{
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedResetSafePwd),'code'=>Msg::$err_failedResetSafePwd]);
        }

    }

    //验证交易密码
    public function checkSafePwd(Request $request){
        $data = $request->only('user_id','safe_pwd');
        if(empty($data['user_id']) || empty($data['safe_pwd'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }

        if(!preg_match("/^\d{6}$/",$data['safe_pwd'])){//非6位纯数字
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_onlySixNumberSafePwd),'code'=>Msg::$err_onlySixNumberSafePwd]);
        }
        $user = $this->usersModel->where(['id'=>$data['user_id']])->select('mobile','safepwd')->first();
        if(empty($user->safepwd)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noSafePwd),'code'=>Msg::$err_noSafePwd]);
        }else{
            if($user->safepwd != MD5($this->salt.$data['safe_pwd'])){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_errorSafePwd),'code'=>Msg::$err_errorSafePwd]);
            }else{
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
            }
        }
    }
    //转账交易
    public function transfer(Request $request)
    {
        $data = $request->only('user_id','mobile','score');
        if(true){//关闭转账
            return response()->json(['msg'=>'转账功能开发中...','code'=>9999]);
        }
        if(empty($data['user_id']) || empty($data['mobile'])|| empty($data['score'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $payee = $this->usersModel->where(['mobile'=>$data['mobile']])->first();
        if(empty($payee)){//收款人不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noPayee),'code'=>Msg::$err_noPayee]);
        }
        $user = $this->usersModel->where(['id'=>$data['user_id']])->first();
        if(empty($user)){//当前用户不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
        }
        if($payee->id == $user->id){//不能给自己转账
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_cannotTransferSelf),'code'=>Msg::$err_cannotTransferSelf]);

        }
        if($data['score'] < 0.01){//最小转账金额0.01
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_errorTransferScore),'code'=>Msg::$err_errorTransferScore]);
        }
        if($user->score < $data['score']){//转账红人圈不足
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noScore),'code'=>Msg::$err_noScore]);
        }
        try {
            \DB::beginTransaction();
            $mobile = substr_replace($user->mobile,'****',3,4);
            $finance_data[] = [
                'user_id' => $user->id,
                'type' => 1,
                'number' => -$data['score'],
                'action' => 1,
                'note' => '转账支出红人圈',
                'create_time'=>time()
            ];
            $finance_data[] = [
                'user_id' => $payee->id,
                'type' => 2,
                'number' => $data['score'],
                'action' => 0,
                'note' => '转账收入红人圈',
                'create_time'=>time()
            ];
            $this->financeModel->insert($finance_data);
            $this->usersModel->where(['id'=>$user->id])->decrement('score',$data['score']);
            $this->usersModel->where(['id'=>$payee->id])->increment('score',$data['score']);

            MessageModel::addMessage($payee->id, "获取转账赠送收入", "{$mobile}向你转账赠送{$data['score']}红人圈");
            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        } catch (\Exception $e) {//$e->getCode(), $e->getMessage()
            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedTransfer),'code'=>Msg::$err_failedTransfer]);
        }
    }

    //是否设置交易密码和收货地址
    public function issetUserInfo(Request $request){
        $data = $request->only('user_id');
        if(empty($data['user_id'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $user = $this->usersModel->where(['id'=>$data['user_id']])->first();
        if(empty($user)){//当前用户不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
        }
        $address = $this->addressModel->where(['user_id'=>$data['user_id']])->first();
        $default_address = $this->addressModel->where(['user_id'=>$data['user_id'],'is_default'=>1])->first();

        $result = [
            'isset_address'=> empty($address) ? 0 : 1,
            'default_address_id'=> empty($default_address) ? 0 : $default_address->id,
            'isset_safepwd'=> empty($user->safepwd)? 0 :1
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);

    }

    //修改个人信息
    public function setName(Request $request){
        $data = $request->only('user_id','username','sex','birthday','constellation');
        if(empty($data['user_id'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        if(!empty($data['username'])){//昵称
            $res = $this->usersModel->where(['id'=>$data['user_id']])->update(['username'=>$data['username'],'nickname'=>$data['username'],'update_time'=>time()]);
        }
        if(!empty($data['sex'])){//性别
            $res = $this->usersModel->where(['id'=>$data['user_id']])->update(['sex'=>$data['sex'],'update_time'=>time()]);
        }
        if(!empty($data['birthday'])){//生日
            $res = $this->usersModel->where(['id'=>$data['user_id']])->update(['birthday'=>$data['birthday'],'update_time'=>time()]);
        }
        if(!empty($data['constellation'])){//星座
            $res = $this->usersModel->where(['id'=>$data['user_id']])->update(['constellation'=>$data['constellation'],'update_time'=>time()]);
        }
        if($res){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }else{
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }

    }

    //修改红人信息
    public function setHot(Request $request){
        $data = $request->only('user_id','hot_intro','hot_images','hot_video');
        if(empty($data['user_id'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        foreach($data['hot_images'] as $k=>$v){
            $data['hot_images'][$k] = str_replace($this->base_url,'',$v);
        }
        $hot_data['hot_intro'] = $data['hot_intro'];
        $hot_data['hot_images'] = serialize($data['hot_images']);
        $hot_data['hot_video'] = str_replace($this->base_url,'',$data['hot_video']);


        if(!empty($hot_data)){//红人资料
            $hot_data['update_time'] = time();
            $res = $this->usersModel->where(['id'=>$data['user_id']])->update($hot_data);
        }
        if($res){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }else{
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }

    }

    //关注列表
    public function follow(Request $request){
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = $this->followModel
            ->join('users','users_follow.star_id','users.id')
            ->select('users.id','users.avatar','users.username','users.fans','users.follow','users.likes','users.level')
            ->where(['users_follow.user_id'=>$user_id])
            ->orderBy('users_follow.id','desc')
            ->paginate($size)
            ->toArray();

        $result = ['data'=>[]];
        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                if(!empty($v['avatar'])){
                    if(substr($v['avatar'],0,4) == 'http'){
                        $data['data'][$k]['avatar'] =$v['avatar'];//头像
                    }else{
                        $data['data'][$k]['avatar'] =$this->base_url.$v['avatar'];//头像
                    }
                }else{
                    $data['data'][$k]['avatar'] = '';//无头像
                }
            }
            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$data['data'],
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //粉丝列表
    public function fans(Request $request){
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = $this->followModel
            ->join('users','users_follow.star_id','users.id')
            ->select('users.id','users.avatar','users.username','users.fans','users.follow','users.likes','users.level')
            ->where(['users_follow.star_id'=>$user_id])
            ->orderBy('users_follow.id','desc')
            ->paginate($size)
            ->toArray();

        $result = ['data'=>[]];
        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                if(!empty($v['avatar'])){
                    if(substr($v['avatar'],0,4) == 'http'){
                        $data['data'][$k]['avatar'] =$v['avatar'];//头像
                    }else{
                        $data['data'][$k]['avatar'] =$this->base_url.$v['avatar'];//头像
                    }
                }else{
                    $data['data'][$k]['avatar'] = '';//无头像
                }
            }
            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$data['data'],
            ];
        }

        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);

    }

    //我的消息
    public function message(Request $request){
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = MessageModel::where(['user_id'=>$user_id,'status'=>1])->paginate($size)->toArray();

        $result = ['data'=>[]];
        if(!empty($data['data'])){

            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$data['data'],
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //设置消息已读
    public function readMsg(Request $request){
        $user_id = $request->user_id ? $request->user_id : 0;
        $id = $request->id ? $request->id : 0;
        $where = "user_id = {$user_id} and status = 0";//全部已读
        $id  && $where.= " and id ={$id}"; //具体
        MessageModel::whereRaw($where)->update(['status'=>1,'update_time'=>time()]);
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
    }

    //我的邀请
    public function invite(Request $request){
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = $this->inviteModel->where(['user_id'=>$user_id])->select('invited_uid as user_id','create_time')->paginate($size)->toArray();
        $result = ['data'=>[]];
        if(!empty($data['data'])){

            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$data['data'],
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //我的冻结记录
    public function freeze(Request $request){
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = $this->freezeModel->where(['user_id'=>$user_id])->paginate($size)->toArray();
        $result = ['data'=>[]];
        if(!empty($data['data'])){

            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$data['data'],
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

}
