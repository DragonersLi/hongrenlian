<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Tools\AES as AES;
use App\Tools;
use Session;
use Gregwar\Captcha\CaptchaBuilder;
use GuzzleHttp\Client;
use Gregwar\Captcha\PhraseBuilder;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Http\Controllers\Api\Wechat\zone as zone;
use App\Models\Admin\MessageModel as MessageModel;
class IndexController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        echo uidDecode('5DZOS6Z');
            echo uidEncode('2099999999');die;
        $arr =  getimagesize('./upload/image/goods/2018081517512415343266845344.png');
       dd($arr); die;
        $spec_index[] = ['spec_id'=>1,'spec_title'=>'颜色','spec_value_id'=>1,'spec_value'=>'黑色'];
        $spec_index[] = ['spec_id'=>2,'spec_title'=>'尺码','spec_value_id'=>8,'spec_value'=>'S'];

         echo serialize($spec_index);die;
//a:2:{i:0;a:4:{s:7:"spec_id";i:1;s:10:"spec_title";s:6:"颜色";s:13:"spec_value_id";i:1;s:10:"spec_value";s:6:"黑色";}i:1;a:4:{s:7:"spec_id";i:2;s:10:"spec_title";s:6:"尺码";s:13:"spec_value_id";i:8;s:10:"spec_value";s:1:"S";}}
         $arr = unserialize($str);
        dd($arr);
        echo getOrderSn();die;

    }
    public function test($tmp)
    {
        // $day = mktime(0,0,0,date('m'),date('d'),date('Y'));
        // $count = LikesModel::where('user_id',$user_id)->where('create_time', '>', $day)->count();
        // if($count >= 5) {
        //     return $this->json(40031, '今日点赞次数已用完，请明日再来。');
        // }
    }

    public function index(Request $request)
    {
        $res = MessageModel::addMessage(1,'1','111');
        dd($res);
        echo $this->make_coupon_card();
        $res = get_all_header();
        dd($res);


    }
    public function getInviteCode($length = 4) {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)].strtoupper(dechex(date('m'))).date('d').substr(time(),-5) .substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        for( $a = md5( $rand, true ), $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV', $d = '', $f = 0;
             $f < $length;
             $g = ord( $a[ $f ] ), $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ], $f++ );
        return $d;
    }

    //生成验证码
    public function captcha($tmp)
    {
        $builder = new CaptchaBuilder; //生成验证码图片的Builder对象，配置相应属性
        $builder->build($width = 100, $height = 40, $font = null);  //可以设置图片宽高及字体
        $phrase = $builder->getPhrase();  //获取验证码的内容
        \Cache::put('verify_code',$phrase,10);
        //Session::flash('verify_code', $phrase); //把内容存入session
        // Session::put('verify_code',$phrase);
        // header("Cache-Control: no-cache, must-revalidate");
        // header('Content-Type: image/jpeg');
        // $builder->output();
        return  $builder->inline(); //生成图片
    }
    //验证验证码
    public function verify_captcha($code)
    {
        $verify_code =  \Cache::get('verify_code');//Session::get('verify_code');
        if(empty($code)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noVerify),'code'=>Msg::$err_noVerify]);
        }
        if($verify_code != $code){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_errVerify),'code'=>Msg::$err_errVerify]);
        }else{
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        }
    }

}
