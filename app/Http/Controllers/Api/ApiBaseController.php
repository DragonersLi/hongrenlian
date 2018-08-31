<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController; 
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
class ApiBaseController extends BaseController
{
    public $page_size = 10;//分页值
    public $over_time = 3600*24*30;//登陆过期时间
    public $base_url = 'https://api.hongrenlian.cn';// 'https://api.hongrenlian.cn';//网站url
    public $appid = 'wxbc961ba394a4a986';//配置appid
    public $secret = '7461458c82a9a6d240afa0eea791e688';//配置appscret
    public $public_key = 'AYHT-4800-0BE6-852A';//短信公钥
    public $private_key = 'BE6852A358FA4EA1';//短信私钥
    public $sendsms_url = 'http://59.110.0.201:6085/ayht-interface/sendsms';//短信平台

    public $salt = 'hongrenlian';//盐值
    public $default_sms_code = '0000';//默认手机短信验证码
	public $order_close_time = 120;//1天未支付关闭 3600*24
	public $order_sure_time = 120;//10天未支付关闭 3600*24*10
	public $user_ranking = 2000;//用户基数排名
    //奖励设置
	public $reward = [ 
		'first_login' => 100,//首次登录 
		'invite_user' => 50,//邀请用户
		'sign_user' => 5, //粉丝签到
		'fans_like' => 1,//粉丝点赞
	];
	
	/* 
        每人每天20个，从04:00-24:00算有效时间，每隔2个小时生成1次，
        1天生成10次，每次1.50-2.50，保留两位有效数字，如果第10次有剩余，
        第10次把所有剩余的一次性生完，如果第10次没有剩余，第10次就不生成
        48小时不收过期
	
	*/

    //红人圈随机产生范围
    public $rand_circle = [
        'min' => 0.10,
        'max' => 0.60
    ];

    //每人每天最多产生红人圈
    public $max_circle = 5;

	 //收入类型
    public $income = [
        '1' => '日常领取',
        '2' => '转账收入',
        '3' => '点赞收入',
        '4' => '粉丝投票收入',
        /*奖励类型*/
        '5' => '注册奖励',
        '6' => '邀友奖励',
        '7' => '护驾有功奖励',
        '8' => '红人星探推荐奖励',
        /*退回类型*/
        '9' => '众筹失败退回',
        '10'=> '星探冻结退回',
        /*追加类型*/
        '11' => '签到收入',
        '12' => '商家核销收入',
        '13' => '所有奖励类型（5,6,7,8）',
        '14' => '购买收入'
    ];
    //支出类型
    public $expend = [
        '1' => '转账支出',
        '2' => '点赞支出',
        '3' => '众筹支出',
        '4' => '投票支出',
        '5' => '兑换支出',
    ];
    //冻结类型
    public $freeze = [
        '1' => '点赞收入冻结',
        '2' => '推荐红人冻结',
        '3' => '粉丝投票收入冻结',
        '4' => '购买收入冻结'
    ];
    //升级
    public $level = [
        '1' => [
            'likes' => 0,//星探推荐成为1级红人
        ],
        '2' => [
            'likes' => 20000,//粉丝点赞超过2w
        ],
        '3' => [
            'likes' => 40000,//粉丝点赞超过4w
        ],
        '4' => [
            'likes' => 70000,//粉丝点赞超过7w
        ],
        '5' => [
            'likes' => 100000,//粉丝点赞超过10w
        ]
    ];
    public $invite_title = '分享标题';//分享标题
    public $invite_desc = '分享描述内容';//分享描述
    public $invite_logo = 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqJ95KeutpACR3TgjQ5qGVibPOlWstGld8WYAp67zcOXy7rJfSYOKn29yTcgrhHnDBlWCuYw6aJ6Gw/132';//分享logo图片
    public $invite_url = 'http://api.hongrenlian.cn';//分享链接
    public function __construct()
    {
        parent::__construct();
  
		

 
    }
    //上传图片
    public function upload($type='')
    {
        if(empty($type)){
            $code = Msg::$err_noParameter;
            $data = "参数type丢失！";
        }else{
            //上传图片具体操作
            $file_name = $_FILES['file']['name'];
            //$file_type = $_FILES["file"]["type"];
            $file_tmp = $_FILES["file"]["tmp_name"];
            $file_error = $_FILES["file"]["error"];
            $file_size = $_FILES["file"]["size"];

            if ($file_error > 0) { // 出错
                $code = Msg::$err_failedUpload;
                $data = "上传文件出错，错误代码是：".$file_error;
            } elseif($file_size > 10485760) { // 文件太大了
                $code = Msg::$err_failedUpload;
                $data = "上传文件不能大于10MB";
            }else{
                $date = date('Ymd');
                $file_name_arr = explode('.', $file_name);
                $new_file_name = date('YmdHis') . '.' . $file_name_arr[1];
                $path = "upload/image/".$type."/".$date."/";
                $file_path = $path . $new_file_name;
                if (file_exists($file_path)) {
                    $code = Msg::$err_alreadyFile;
                    $data = "路径不存在！";
                } else {
                    //TODO 判断当前的目录是否存在，若不存在就新建一个!
                    if (!is_dir($path)){mkdir($path,0777);}
                    $upload_result = move_uploaded_file($file_tmp, $file_path);
                    //此函数只支持 HTTP POST 上传的文件
                    if ($upload_result) {
                        $code = Msg::$err_none;
                        $data = '/'.$file_path;//$this->base_url.
                    } else {
                        $code = Msg::$err_failedUpload;
                        $data = "上传文件失败，请重试！";
                    }

                }
            }
        }
        return  ['code'=>$code,'msg'=>Msg::getMsg($code),'result'=>['data'=>$data]];
    }


}
