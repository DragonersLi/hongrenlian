<?php

namespace App\Http\Controllers\Api\V1;
use Session;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\UsersModel;
use App\Models\Admin\FinanceModel;
use App\Models\Admin\InviteModel;
use App\Models\Admin\MessageModel as MessageModel;
class PublicController extends ApiBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->usersModel = new UsersModel;
        $this->financeModel = new FinanceModel;
        $this->inviteModel = new InviteModel;
    }

    //微信授权登陆
    public function index(Request $request){
        $code = $request->code ? $request->code : '';//声明CODE，获取小程序传过来的CODE
        if(empty($code)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $curl = curl_init(); //使用curl_setopt()设置要获取的URL地址
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$this->appid.'&secret='.$this->secret.'&js_code='.$code.'&grant_type=authorization_code';
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false); // 设置是否输出header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // 设置是否输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  // 设置是否检查服务器端的证书
        $data = curl_exec($curl); // 使用curl_exec()将CURL返回的结果转换成正常数据并保存到一个变量
        curl_close($curl); // 使用 curl_close() 关闭CURL会话
        //$wechat = json_decode($data); //$data = get_object_vars($data); print_r($data['errcode']);file_put_contents('./log.php',$user_id);
        return $data;
    }



    //获取加密
    private function getHexStr($key=""){
        list($usec, $sec) = explode(" ", microtime());
        $time = date('YmdHis', time()).round($usec*1000);
        $A= openssl_encrypt($time, 'AES-128-ECB', $key,true);
        $hex="";
        for($i=0;$i<strlen($A);$i++)
        {
            $hex.= (strlen(dechex(ord($A[$i])))==1) ? '0'.dechex(ord($A[$i])) : dechex(ord($A[$i]));
        }
        return   strtoupper($hex);
    }


    //发送短信
    public function sendsms($mobile){
        if(empty($mobile)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noMobile),'code'=>Msg::$err_noMobile]);
        }

        $code = substr(time(),-4);
        \Cache::put('appsms_'.$mobile, $code,10);
        //Session::flash('sms_'.$mobile, $code); //把内容存入session
        $msg['mobiles'] = ["{$mobile}"];
        $msg['smscontent'] = "【红人链】您好，您的验证码是：{$code} 请妥善保管！";
        $msg['extendedcode'] = '001';
        $msg['sendtime'] = null;
        $sms_data = json_encode($msg);
        $sms_data = urlencode($sms_data);
        $url = $this->sendsms_url."?public_key={$this->public_key}&sign={$this->getHexStr($this->private_key)}&sms_data={$sms_data}&encry=false";
        $res = file_get_contents($url);
        $res = json_decode($res);
        if($res->code == '0000'){//发送成功
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }else{//其它状态
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedSendSms),'code'=>Msg::$err_failedSendSms]);
        }
    }

    //app登录发送验证码
    public function appsendsms($mobile){
        if(empty($mobile)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noMobile),'code'=>Msg::$err_noMobile]);
        }
        $user = $this->usersModel->where(['mobile'=>$mobile])->first();
        $type = empty($user) ? 0 : 1;//0:reg ;1:login
        $code = substr(time(),-4);
        \Cache::put('appsms_'.$mobile, $code,10);

        //Session::flash('sms_'.$mobile, $code); //把内容存入session
        $msg['mobiles'] = ["{$mobile}"];
        $msg['smscontent'] = "【红人链】您好，您的验证码是：{$code} 请妥善保管！";
        $msg['extendedcode'] = '001';
        $msg['sendtime'] = null;
        $sms_data = json_encode($msg);
        $sms_data = urlencode($sms_data);
        $url = $this->sendsms_url."?public_key={$this->public_key}&sign={$this->getHexStr($this->private_key)}&sms_data={$sms_data}&encry=false";
        $res = file_get_contents($url);
        $res = json_decode($res);
        if($res->code == '0000'){//发送成功
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>['type'=>$type]]);
        }else{//其它状态
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedSendSms),'code'=>Msg::$err_failedSendSms,'result'=>['type'=>$type]]);
        }
    }


    //app登录
    public function appLogin(Request $request){
        $mobile = $request->mobile ? $request->mobile : '';// 手机号
        $birthday = $request->birthday ? $request->birthday : '';//出生年月日
        $avatar = $request->avatar ? $request->avatar : '';//头像
        $sex = $request->sex ? $request->sex : 0;//性别
        $openid = $request->openid ? $request->openid : '';//微信唯一识别号
        $source = $request->source ? $request->source : 0;//来源
        $sms_code = $request->sms_code ? $request->sms_code : '';//手机验证码
        $invite_code = $request->invite_code ? $request->invite_code : '';//邀请码

        $cache_sms_code = \Cache::get('appsms_'.$mobile);
        if(empty($mobile)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noMobile),'code'=>Msg::$err_noMobile]);
        }
        $username = $request->username ? $request->username : substr_replace($mobile,'****',3,4);//用户名
        if(empty($sms_code)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noSmsCode),'code'=>Msg::$err_noSmsCode]);
        }
        if($sms_code != '0000'){
            if($sms_code != $cache_sms_code){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_errSmsCode),'code'=>Msg::$err_errSmsCode]);
            }
        }


        $user = $this->usersModel->where(['mobile'=>$mobile])->first();
        try {
            \DB::beginTransaction();
            $time = time();
            $over_time = $time + $this->over_time;//token过期时间
            if(empty($user)){//第一次登陆，注册账号
                $data['token'] = md5($mobile.$over_time);//token值
                $data['token_overtime'] = $over_time;
                $data['username'] = $username;
                $data['avatar']  = $avatar;
                $data['birthday'] = $birthday;
                $data['mobile']  = $mobile;
                $data['sex']  = $sex;
                $data['status'] = 1;
                $data['score'] = $this->reward['first_login'];//第一次登陆送100
                $data['openid'] = $openid;
                $data['source'] = $source;
                $data['login_count'] = 1;
                $data['password'] = md5(substr($mobile,-6));
                //$data['create_ip'] = $request->getClientIp();
                $data['create_time'] = $time;
                $id = $this->usersModel->insertGetId($data);
                if(!empty($invite_code)){//有邀请人
                    $uid = uidDecode($invite_code);//邀请者ID
                    $this->inviteModel->insert(['user_id'=>$uid,'invited_uid'=>$id,'create_time'=>$time]);//邀请表记录
                    $finance[] = [
                        'user_id' => $uid,
                        'type' => 6,
                        'number' => $this->reward['invite_user'],
                        'action' => 0,
                        'note' => '邀友奖励',
                        'create_time'=>$time
                    ];
                    MessageModel::addMessage($uid, "邀友奖励收入", "邀请好友系统赠送{$this->reward['invite_user']}红人圈");
                }
                $finance[] = [
                    'user_id' => $id,
                    'type' => 5,
                    'number' => $this->reward['first_login'],
                    'action' => 0,
                    'note' => '首次登录',
                    'create_time'=>$time
                ];
                $this->financeModel->insert($finance);
                MessageModel::addMessage($id, "首次登录收入", "首次登录系统赠送{$this->reward['first_login']}红人圈");

            }else{//账号已存在
                if(!$user->status){//账号被冻结
                    return response()->json(['msg'=>Msg::getMsg(Msg::$err_FrozenLogin),'code'=>Msg::$err_FrozenLogin]);
                }
                $res = $this->usersModel->where(['id'=>$user->id])->update(['token_overtime'=>$over_time,'login_count'=>$user->login_count + 1,'update_time'=>$time]);
            }
            \DB::commit();

            $user = $this->usersModel->where(['mobile'=>$mobile])->first();
            $result['user_id'] = $user->id;
            $result['ranking'] = $this->user_ranking + $user->id;
            $result['mobile'] = $user->mobile;
            $result['token'] = $user->token;
            $result['over_time'] = $user->token_overtime;
            $result['username'] = $user->username;
            $result['fans'] = $user->fans;
            $result['follow'] = $user->follow;
            $result['likes'] = $user->likes;
            $result['score'] = $user->score;
            $result['freeze_score'] = $user->freeze_score;
            $result['birthday'] = $user->birthday;
            $result['constellation'] = $user->constellation;
            $result['sex'] = $user->sex;
            $result['level'] = $user->level;
            $result['hot_intro'] = $user->hot_intro;
            $result['hot_video'] = empty($user->hot_video) ? '': $this->base_url.$user->hot_video;
            $result['invite_code'] = uidEncode($user->id);
            $hot_images = empty($user->hot_images) ? [] : unserialize($user->hot_images);

            foreach($hot_images as $k=>$v){
                $result['hot_images'][$k] = $this->base_url.$v;
            }

            if(!empty($user->avatar)){
                if(substr($user->avatar,0,4) == 'http'){
                    $result['avatar'] = $user->avatar;//头像
                }else{
                    $result['avatar'] = $this->base_url.$user->avatar;//头像
                }
            }else{
                $result['avatar'] = '';//无头像
            }
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);

        } catch (\Exception $e) { //dd($e->getMessage());
            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedLogin),'code'=>Msg::$err_failedLogin]);
        }
    }

    //用户信息(获取当前用户，或根据手机号获取他人)
    public function getUserInfo(Request $request)
    {
        $user_id = $request->user_id ? $request->user_id :0;
        $mobile = $request->mobile ? $request->mobile : '';
        $where = !empty($mobile) ? ['mobile'=>$mobile] : ['id'=>$user_id];//获取他人或本人信息
        $user = $this->usersModel->where($where)->first();
        if(empty($user)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
        }

        $hot_images = empty($user->hot_images) ? [] : unserialize($user->hot_images);
        foreach($hot_images as $k=>$v){
            $hot_images[$k] = $this->base_url.$v;
        }
        if(!empty($user->avatar)){
            if(substr($user->avatar,0,4) == 'http'){
                $result['avatar'] = $user->avatar;//头像
            }else{
                $result['avatar'] = $this->base_url.$user->avatar;//头像
            }
        }else{
            $result['avatar'] = '';//无头像
        }

        $result['user_id'] = $user_id;
        $result['ranking'] = $this->user_ranking+$user_id;
        $result['mobile'] = $mobile;
        $result['token'] = $user->token;
        $result['over_time'] = $user->token_overtime;
        $result['username'] = $user->username;
        $result['fans'] = $user->fans;
        $result['follow'] = $user->follow;
        $result['likes'] = $user->likes;
        $result['score'] = $user->score;
        $result['freeze_score'] = $user->freeze_score;
        $result['birthday'] = $user->birthday;
        $result['constellation'] = $user->constellation;
        $result['sex'] = $user->sex;
        $result['level'] = $user->level;
        $result['hot_intro'] = $user->hot_intro;
        $result['hot_images'] = $hot_images;
        $result['hot_video'] = empty($user->hot_video) ? '': $this->base_url.$user->hot_video;
        $result['invite_code'] = uidEncode($user_id);

        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);

    }

    //上传头像
    public function avatar(Request $request){
        $message = $this->upload($request->type);
        return response()->json($message);

    }

    //修改头像
    public function avatarEdit(Request $request)
    {
        $type = $request->type ? $request->type : 'avatar';
        $user_id = $request->user_id ? $request->user_id : 0;
        $message = $this->upload($type);
        if(empty($user_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        if(empty($message['result']['data'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }
        $res = $this->usersModel->where(['id'=>$user_id])->update(['avatar'=>$message['result']['data'],'update_time'=>time()]);
        $result = ['data'=>['avatar'=>$this->base_url.$message['result']['data']]];
        if($res){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
        }else{
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
        }

    }
    //退出登录
    public function logout(Request $request){
        $user_id = $request->user_id ? $request->user_id : 0;
        $this->usersModel->where(['id'=>$user_id])->update(['token_overtime'=>time()-3600]);
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
    }
}