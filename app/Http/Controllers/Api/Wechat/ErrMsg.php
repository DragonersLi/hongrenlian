<?php
namespace App\Http\Controllers\Api\Wechat;
class ErrMsg{
    public static $err_none = 0;//结果正确
    public static $err_failedLogin = 1;
    public static $err_noParameter = 2;
    public static $err_errToken = 3;
    public static $err_failureToken = 4;
    public static $err_FrozenLogin = 5;
    public static $err_failedUpload = 6;
    public static $err_alreadyFile = 7;

    public static $err_failed = 1000;//操作失败
    public static $err_noMobile = 1001;
    public static $err_noOpenid = 1002;
    public static $err_noVerify = 1003;
    public static $err_noSmsCode = 1004;
    public static $err_errVerify = 1005;
    public static $err_errSmsCode = 1006;
    public static $err_failedSendSms = 1007;
    public static $err_failedSign = 1008;
    public static $err_alreadySign = 1009;
    public static $err_failedInvite = 1010;
    public static $err_failedVote = 1011;
    public static $err_noScore = 1012;
    public static $err_failedCollect = 1013;
    public static $err_failedCancelCollect = 1014;
    public static $err_failedComment =1015;
    public static $err_noCrowdfunding =1016;
    public static $err_noStartCrowdfunding =1017;
    public static $err_overdueCrowdfunding =1018;
    public static $err_finishCrowdfunding =1019;
    public static $err_overdueScoreRange =1020;
    public static $err_failedCrowdfunding =1021;
    public static $err_noAct =1022;
    public static $err_noActDetail =1023;
    public static $err_noStartAct =1024;
    public static $err_overdueAct =1025;
    public static $err_alreadyVote =1026;
    public static $err_failedAddAddress = 1027;
    public static $err_failedEditAddress = 1028;
    public static $err_failedDelAddress = 1029;
    public static $err_differenceSafePwd =1030;
    public static $err_onlySixNumberSafePwd =1031;
    public static $err_failedSetSafePwd =1032;
    public static $err_errorOldSafePwd = 1033;
    public static $err_failedResetSafePwd =1034;
    public static $err_cannotSameSafePwd = 1035;
    public static $err_failedSetName = 1036;
    public static $err_noUser = 1037;
    public static $err_cannotLikeSelf = 1038;
    public static $err_failedLike = 1039;
    public static $err_failedFollow = 1040;
    public static $err_failedCancelFollow = 1041;
    public static $err_cannotFollowSelf = 1042;
    public static $err_noSafePwd = 1043;
    public static $err_errorSafePwd = 1044;
    public static $err_noPayee = 1045;
    public static $err_failedTransfer = 1046;
    public static $err_noGoods = 1047;
    public static $err_noProduct = 1048;
    public static $err_noAddress = 1049;
    public static $err_failedOrder = 1050;
    public static $err_noStore = 1051;
    public static $err_notYourOder = 1052;
    public static $err_alreadyPay = 1053;
    public static $err_failedPay = 1054;
    public static $err_noOrder = 1055;
    public static $err_cannotCancelOrder = 1056;
    public static $err_cannotDeleteOrder = 1057;
    public static $err_cannotSureOrder = 1058;
    public static $err_illegalRequest = 1059;
    public static $err_alreadyCollectScore = 1060;
    public static $err_overtimeScore = 1061;
    public static $err_noSpecIndex = 1062;
    public static $err_errorSpecIndex = 1063; 
	public static $err_cannotTransferSelf = 1064;
    public static $err_errorTransferScore = 1065;

    public static function getMsg($code){
        $msgs = [
            self::$err_none => "结果正确！",
            self::$err_failed => "操作失败！",
            self::$err_failedLogin => "登录失败！",
            self::$err_FrozenLogin => "账号被冻结！",
            self::$err_noParameter => "参数丢失！",
            self::$err_errToken => "TOKEN验证失败！",
            self::$err_failureToken => "TOKEN失效！",
            self::$err_failedUpload => "上传文件失败！",
            self::$err_alreadyFile => "此文件已存在！",

            self::$err_noMobile => "手机号不能为空！",
            self::$err_noOpenid => "微信OPENID不能为空！",
            self::$err_noVerify => "图形验证码不能为空！",
            self::$err_noSmsCode => "短信验证码不能为空！",
            self::$err_errVerify => "图形验证码不正确！",
            self::$err_errSmsCode => "短信验证码不正确！",
            self::$err_failedSendSms => "短信发送失败！",
            self::$err_failedSign => "签到失败！",
            self::$err_alreadySign => "已经签到！",
            self::$err_failedInvite => "邀请失败！",

            self::$err_noScore => "您的红人圈数量不足！",
            self::$err_failedCollect => "收藏失败！",
            self::$err_failedCancelCollect => "取消收藏失败！",
            self::$err_failedComment => "评论失败！",
            //投票
            self::$err_failedVote => "参加投票失败！",
            self::$err_noAct => "投票活动不存在！",
            self::$err_noActDetail => "该投票活动候选人不存在！",
            self::$err_noStartAct => "该投票项目未开始！",
            self::$err_overdueAct => "该投票项目已结束！",
            self::$err_alreadyVote => "对不起，您今日的投票次数已用完，请明日再投！",
            //众筹
            self::$err_noCrowdfunding => "众筹项目不存在！",
            self::$err_noStartCrowdfunding => "该众筹项目还未开始！",
            self::$err_overdueCrowdfunding => "该众筹项目已过期！",
            self::$err_finishCrowdfunding => "该众筹项目已完成！",
            self::$err_overdueScoreRange => "众筹红人圈超出范围！",
            self::$err_failedCrowdfunding => "参加众筹失败！",
            //收货地址
            self::$err_failedAddAddress => "添加收货地址失败！",
            self::$err_failedEditAddress => "编辑收货地址失败！",
            self::$err_failedDelAddress => "删除收货地址失败！",
            //交易密码
            self::$err_differenceSafePwd => "交易密码和确认交易密码不一致！",
            self::$err_onlySixNumberSafePwd => "交易密码只能为6位纯数字！",
            self::$err_failedSetSafePwd => "交易密码设置失败！",
            self::$err_errorOldSafePwd => "旧的交易密码错误！",
            self::$err_failedResetSafePwd => "交易密码重置失败！",
            self::$err_cannotSameSafePwd => "交易密码不能和旧的交易密码一样！",
            self::$err_noSafePwd => "交易密码未设置！",
            self::$err_errorSafePwd => "交易密码错误！",
            //修改昵称
            self::$err_failedSetName => "修改昵称失败！",
            self::$err_noUser => "用户不存在！",
            self::$err_cannotLikeSelf => "不能给自己点赞！",
            self::$err_failedLike => "点赞失败！",
            self::$err_failedFollow => "关注失败！",
            self::$err_failedCancelFollow => "取消关注失败！",
            self::$err_cannotFollowSelf => "不能关注自己！",
            //转账
            self::$err_noPayee => "收款人不存在！",
            self::$err_failedTransfer => "转账失败！",
            self::$err_cannotTransferSelf => "不能给自己转账！",
            self::$err_errorTransferScore => "转账数额不符合要求！",
            //兑换商品
            self::$err_noGoods => "商品不存在！",
            self::$err_noProduct => "商品sku不存在！",
            self::$err_noAddress => "收货地址不存在！",
            self::$err_failedOrder => "生成订单失败！",
            self::$err_noStore => "商品库存不足！",
            self::$err_noSpecIndex => "请选择商品规格！",
            self::$err_errorSpecIndex => "商品规格选择有误！",
            //支付
            self::$err_notYourOder => "订单不属于该用户！",
            self::$err_alreadyPay => "订单已支付，请勿重复支付！",
            self::$err_failedPay => "订单支付失败！",
            self::$err_noOrder => "订单不存在！",
            self::$err_cannotCancelOrder => "已兑换订单不能取消！",
            self::$err_cannotDeleteOrder => "已兑换订单不能删除！",
            self::$err_cannotSureOrder => "非待收货状态的订单不能确认收货！",
            self::$err_illegalRequest => "非法请求！",
            self::$err_alreadyCollectScore => "红人圈已收！",
            self::$err_overtimeScore => "红人圈已超时！",


        ];
        return $msgs[$code];
    }


}