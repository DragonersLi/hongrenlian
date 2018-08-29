<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

#V1版本接口路由分组
#prefix=v1相对于url路由简写
#namespace=Api\V1相对于控制器方法简写
//APP接口路由
Route::group(['prefix' => 'v1','namespace'=>'Api\V1'],function() {

    //不需要验证登陆问的接口
    Route::group(['middleware'=>'appvistAuth'],function() {

        Route::any('index', 'IndexController@index'); //首页
        Route::get('test', 'IndexController@test'); //首页测试
        Route::get('wxlogin', 'PublicController@index'); //微信登陆
        Route::get('sendsms/{mobile}','PublicController@sendsms');//小程序发短信
        Route::get('appsendsms/{mobile}','PublicController@appsendsms');//app发短信
        Route::post('avatar','PublicController@avatar');//app上传头像
        Route::post('avatarEdit','PublicController@avatarEdit');//app修改头像
        Route::get('region/{id?}', 'RegionController@index')->where(['id' => '[0-9]+']); //省、市、区
        Route::post('appLogin','PublicController@appLogin');//app登录
        Route::get('goods/{cid?}', 'GoodsController@index')->where(['cid' => '[0-9]+']); //商品列表
        Route::get('goods/detail/{goods_id}', 'GoodsController@detail')->where(['goods_id' => '[0-9]+']); //商品详情
        Route::get('goods/exchange/{goods_id}', 'GoodsController@exchange')->where(['goods_id' => '[0-9]+']); //商品兑换
        Route::any('comment','CommentController@index');//评论列表
        Route::any('star','StarController@index');//红人广场
        Route::any('star/search','StarController@search');//红人搜索
        Route::any('star/detail/{id}/{user_id?}','StarController@detail')->where(['id' => '[0-9]+','user_id' => '[0-9]+']);//红人详情
        Route::any('crowdfunding/{id?}','CrowdfundingController@index')->where(['id' => '[0-9]+']);//众筹列表
        Route::any('crowdfunding/detail/{id?}/{user_id?}','CrowdfundingController@detail')->where(['id' => '[0-9]+','user_id' => '[0-9]+']);//众筹详情
        Route::get('votes','VotesController@index');//投票列表
        Route::any('votes/detail/{id}','VotesController@detail')->where(['id' => '[0-9]+','user_id' => '[0-9]+']);//投票详情



    });


    //需要验证登陆才能访问的接口
    Route::group(['middleware'=>'appAuth'],function() {

        Route::any('getUserInfo/{mobile?}', 'PublicController@getUserInfo')->where(['mobile' => '1[0-9]+']); //获取用户信息
        Route::get('logout', 'PublicController@logout'); //登出
        Route::get('addresslist', 'AddressController@index'); //收货地址列表
        Route::post('addressadd', 'AddressController@add'); //收货地址添加
        Route::any('addressedit', 'AddressController@edit'); //收货地址编辑
        Route::get('addressdel/{id?}', 'AddressController@del')->where(['id' => '[0-9]+']); //收货地址删除
        Route::get('addressshow/{id?}', 'AddressController@show')->where(['id' => '[0-9]+']); //收货地址显示
        Route::any('star/like/{star_id}','StarController@like')->where(['star_id' => '[0-9]+']);//红人点赞
        Route::any('star/follow/{star_id}','StarController@follow')->where(['star_id' => '[0-9]+']);//红人关注
        Route::any('star/invite/{resource_id?}/{type?}','StarController@invite')->where(['resource_id' => '[0-9]+','type' => '[0-2]']);//分享链接
        Route::any('my', 'MyController@index'); //我的首页
        Route::match(['get','post'],'my/sign', 'MyController@sign'); //我的签到
        Route::match(['get','post'],'acts/voting', 'ActsController@voting'); //投票
        Route::any('my/freeze', 'MyController@freeze');//我的冻结
        Route::get('my/invite', 'MyController@invite');//我的邀请
        Route::any('my/follow', 'MyController@follow');//我的关注
        Route::any('my/fans', 'MyController@fans');//我的粉丝
        Route::any('my/message', 'MyController@message');//我的消息
        Route::any('my/readMsg', 'MyController@readMsg');//设置已读
        Route::post('my/setname', 'MyController@setName'); //设置用户信息
        Route::post('my/sethot', 'MyController@setHot'); //设置红人信息
        Route::post('my/setsafepwd', 'MyController@setSafePwd'); //设置交易密码
        Route::post('my/resetsafepwd', 'MyController@resetSafePwd'); //重置交易密码
        Route::post('my/checksafepwd', 'MyController@checkSafePwd'); //交易密码验证
        Route::post('my/transfer', 'MyController@transfer'); //转账
        Route::get('my/issetUserInfo', 'MyController@issetUserInfo'); //是否设置交易密码和收货地址
        Route::any('order', 'OrderController@index'); //订单列表
        Route::get('order/detail/{order_id}', 'OrderController@detail')->where(['order_id' => '[0-9]+']); //订单详情
        Route::post('order/create', 'OrderController@create'); //订单生成
        Route::post('order/pay', 'OrderController@pay'); //订单支付
        Route::post('order/changeStatus', 'OrderController@changeStatus'); //订单更改状态
        Route::get('goods/getGoodsStatus/{goods_id}', 'GoodsController@getGoodsStatus')->where(['goods_id' => '[0-9]+']); //商品状态
        Route::get('collect','CollectController@index');//众筹，投票收藏列表
        Route::any('collect/my/{type?}', 'CollectController@my')->where(['type' => '[1-2]+']);//我的收藏
        Route::any('collect/myact/{type?}', 'CollectController@myact')->where(['type' => '[1-2]+']);//我参与的
        Route::any('collect/addOrCancel','CollectController@addOrCancel');//众筹，投票收藏或取消
        Route::any('votes/pay','VotesController@pay');//投票支持
        Route::any('crowdfunding/pay','CrowdfundingController@pay');//众筹支持
        Route::any('comment/add','CommentController@add');//评论添加
        Route::post('goods/exchangeSure', 'GoodsController@exchangeSure'); //确认兑换
        Route::any('circle','CircleController@index');//收圈列表
        Route::any('circle/read','CircleController@read');//读取收圈
        Route::get('circle/update/{id}','CircleController@update')->where(['id' => '[0-9]+']);//更新收圈
        Route::get('circle/delete/{id}','CircleController@delete')->where(['id' => '[0-9]+']);//删除收圈
        Route::post('finance','FinanceController@index');//收支记录

    });

});
