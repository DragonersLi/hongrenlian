<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
Route::post('login', 'LoginController@login');
Route::any('logout', 'LoginController@logout'); //后台登出

//Route::any('resetPwd', 'LoginController@resetPwd'); //修改密码



Route::get('index', ['as' => 'admin.index', 'uses' => function () {
    //return redirect('/admin/log-viewer');
    return redirect('/admin/acts/index');
}]);

Route::get('/', 'IndexController@index');
Route::get('/', function () {
    return redirect('/admin/index');
});

Route::group(['middleware' => ['auth:admin', 'menu', 'authAdmin']], function () {
    Route::match(['get','post'],'resetPwd', 'IndexController@resetPwd'); //修改密码
    //权限管理路由
    Route::get('permission/{cid}/create', ['as' => 'admin.permission.create', 'uses' => 'PermissionController@create']);
    Route::get('permission/manage', ['as' => 'admin.permission.manage', 'uses' => 'PermissionController@index']);
    Route::get('permission/{cid?}', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']);
    Route::post('permission/index', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']); //查询
    Route::resource('permission', 'PermissionController', ['names' => ['update' => 'admin.permission.edit', 'store' => 'admin.permission.create']]);


    //角色管理路由
    Route::get('role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
    Route::post('role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
    Route::resource('role', 'RoleController', ['names' => ['update' => 'admin.role.edit', 'store' => 'admin.role.create']]);


    //用户管理路由
    Route::match(['get','post'],'user/index', ['as' => 'admin.user.index', 'uses' => 'UserController@index']);  //用户管理
    Route::resource('user', 'UserController', ['names' => ['update' => 'admin.role.edit', 'store' => 'admin.role.create']]);

    //众筹管理路由
    Route::match(['get','post'],'crowdfunding/index', ['as' => 'admin.crowdfunding.index', 'uses' => 'CrowdfundingController@index']);  //众筹管理
    Route::match(['get','post'],'crowdfunding/upload', ['as' => 'admin.crowdfunding.upload', 'uses' => 'CrowdfundingController@upload']);//上传图片
    Route::match(['get','post'],'crowdfunding/add', ['as' => 'admin.crowdfunding.add', 'uses' => 'CrowdfundingController@add']);
    Route::match(['get','post'],'crowdfunding/edit/{id?}', ['as' => 'admin.crowdfunding.edit', 'uses' => 'CrowdfundingController@edit'])->where(['id' => '[0-9]+']);
    Route::match(['get','post'],'crowdfunding/detail/{id?}', ['as' => 'admin.crowdfunding.detail', 'uses' => 'CrowdfundingController@detail'])->where(['id' => '[0-9]+']);
    Route::match(['get','post'],'crowdfunding/changeStatus/{id}/{status}', ['as' => 'admin.crowdfunding.changeStatus', 'uses' => 'CrowdfundingController@changeStatus'])->where(['id' => '[0-9]+','status' => '[0-9]']);

    //活动管理路由
    Route::match(['get','post'],'acts/index', ['as' => 'admin.acts.index', 'uses' => 'ActsController@index']);  //活动管理
    Route::match(['get','post'],'acts/upload', ['as' => 'admin.acts.upload', 'uses' => 'ActsController@upload']);//上传活动图片
    Route::match(['get','post'],'acts/add', ['as' => 'admin.acts.add', 'uses' => 'ActsController@add']);
    Route::match(['get','post'],'acts/edit/{act_id?}', ['as' => 'admin.acts.edit', 'uses' => 'ActsController@edit'])->where(['act_id' => '[0-9]+']);
    Route::match(['get','post'],'acts/detail/{act_id?}/{vote_id?}', ['as' => 'admin.acts.detail', 'uses' => 'ActsController@detail'])->where(['act_id' => '[0-9]+','vote_id' => '[0-9]+']);
    Route::match(['get','post'],'acts/detailAdd/{act_id?}', ['as' => 'admin.acts.detailAdd', 'uses' => 'ActsController@detailAdd'])->where(['act_id' => '[0-9]+']);
    Route::match(['get','post'],'acts/detailEdit/{act_id?}/{detail_id?}', ['as' => 'admin.acts.detailEdit', 'uses' => 'ActsController@detailEdit'])->where(['act_id' => '[0-9]+','detail_id' => '[0-9]+']);
    Route::match(['get','post'],'acts/changeActStatus/{act_id}/{status}', ['as' => 'admin.acts.changeActStatus', 'uses' => 'ActsController@changeActStatus'])->where(['act_id' => '[0-9]+','status' => '[0-2]']);
    Route::match(['get','post'],'acts/records/{act_id?}/{vote_id?}', ['as' => 'admin.acts.records', 'uses' => 'ActsController@records'])->where(['act_id' => '[0-9]+','vote_id' => '[0-9]+']);

    //前台用户管理路由
    Route::match(['get','post'],'users/index', ['as' => 'admin.users.index', 'uses' => 'UsersController@index']);  //前台用户列表
    Route::match(['get','post'],'users/upload', ['as' => 'admin.users.upload', 'uses' => 'UsersController@upload']);//上传头像
    Route::match(['get','post'],'users/edit/{id?}', ['as' => 'admin.users.edit', 'uses' => 'UsersController@edit'])->where(['id' => '[0-9]+']);
    Route::match(['get','post'],'users/changeStatus/{id}/{status}', ['as' => 'admin.users.changeStatus', 'uses' => 'UsersController@changeStatus'])->where(['id' => '[0-9]+','status' => '[0-2]']);
    Route::match(['get','post'],'users/address/{uid?}', ['as' => 'admin.users.address', 'uses' => 'UsersController@address'])->where(['uid' => '[0-9]+']); // 收货地址列表(['goods_id' => '[0-9]+','sj' => '[0-1]']);
    Route::match(['get','post'],'users/scorelock/{id?}', ['as' => 'admin.users.scorelock', 'uses' => 'UsersController@scorelock'])->where(['id' => '[0-9]+']); //获取红人圈基本信息
    Route::match(['get','post'],'users/AddScorelock', ['as' => 'admin.users.AddScorelock', 'uses' => 'UsersController@AddScorelock']); //添加红人圈
    //粉丝管理路由
    Route::match(['get','post'],'fans/index', ['as' => 'admin.fans.index', 'uses' => 'FansController@index']);  //粉丝管理
    Route::match(['get','post'],'fans/upload', ['as' => 'admin.fans.upload', 'uses' => 'FansController@upload']);//上传头像
    Route::match(['get','post'],'fans/add', ['as' => 'admin.fans.add', 'uses' => 'FansController@add']);
    Route::match(['get','post'],'fans/edit/{id?}', ['as' => 'admin.fans.edit', 'uses' => 'FansController@edit'])->where(['id' => '[0-9]+']);
    Route::match(['get','post'],'fans/changeStatus/{id}/{status}', ['as' => 'admin.fans.changeStatus', 'uses' => 'FansController@changeStatus'])->where(['id' => '[0-9]+','status' => '[0-2]']);

    //商品管理路由
    Route::match(['get','post'],'goods/index', ['as' => 'admin.goods.index', 'uses' => 'GoodsController@index']);  //商品列表
    Route::match(['get','post'],'goods/upload', ['as' => 'admin.goods.upload', 'uses' => 'GoodsController@upload']);//上传图片
    Route::match(['get','post'],'goods/skuStatus/{product_id}/{status}', ['as' => 'admin.goods.skuStatus', 'uses' => 'GoodsController@skuStatus']);//更改sku状态
 
    Route::match(['get','post'],'goods/edit/{goods_id?}', ['as' => 'admin.goods.edit', 'uses' => 'GoodsController@edit'])->where(['goods_id' => '[0-9]+']);
    Route::match(['get','post'],'goods/changeStatus/{goods_id}/{status}', ['as' => 'admin.goods.changeStatus', 'uses' => 'GoodsController@changeStatus'])->where(['goods_id' => '[0-9]+','status' => '[0-2]']);
    Route::match(['get','post'],'goods/sjStatus/{goods_id}/{sj}', ['as' => 'admin.goods.sjStatus', 'uses' => 'GoodsController@sjStatus'])->where(['goods_id' => '[0-9]+','sj' => '[0-1]']);

    Route::match(['get','post'],'goods/add', ['as' => 'admin.goods.add', 'uses' => 'GoodsController@add']);//商品添加
    Route::match(['get','post'],'goods/getSpecValues', ['as' => 'admin.goods.getSpecValues', 'uses' => 'GoodsController@getSpecValues']);//ajax根据规格名获取规格值
    Route::match(['get','post'],'goods/generateSKU', ['as' => 'admin.goods.generateSKU', 'uses' => 'GoodsController@generateSKU']);//ajax根据规格名获取规格值
	
	

    //订单管理
    Route::match(['get','post'],'order/index', ['as' => 'admin.order.index', 'uses' => 'OrderController@index']);
    Route::match(['get','post'],'order/info/{order_id?}', ['as' => 'admin.order.info', 'uses' => 'OrderController@info'])->where(['order_id' => '[0-9]+']);
    Route::match(['get','post'],'order/going/{order_id}/{status}', ['as' => 'admin.order.going', 'uses' => 'OrderController@going'])->where(['order_id' => '[0-9]+','status'=>'[0-4]']);
     Route::match(['get','post'],'order/changego', ['as' => 'admin.order.changego', 'uses' => 'OrderController@changego']);
    //商品规格管理
    Route::match(['get','post'],'goods_spec/index', ['as' => 'admin.goods_spec.index', 'uses' => 'GoodsSpecController@index']);  // 商品规格列表
    Route::match(['get','post'],'goods_spec/add', ['as' => 'admin.goods_spec.add', 'uses' => 'GoodsSpecController@add']);  // 商品规格add
    Route::match(['get','post'],'goods_spec/edit/{spec_id?}', ['as' => 'admin.goods_spec.edit', 'uses' => 'GoodsSpecController@edit'])->where(['spec_id' => '[0-9]+']);  // 商品规格edit


    //商品分类管理路由
    Route::match(['get','post'],'goods_cat/index', ['as' => 'admin.goods_cat.index', 'uses' => 'GoodsCatController@index']);  //商品分类
    Route::match(['get','post'],'goods_cat/upload', ['as' => 'admin.goods_cat.upload', 'uses' => 'GoodsCatController@upload']);//上传图片
    Route::match(['get','post'],'goods_cat/add', ['as' => 'admin.goods_cat.add', 'uses' => 'GoodsCatController@add']);
    Route::match(['get','post'],'goods_cat/getChildCat', ['as' => 'admin.goods_cat.getChildCat', 'uses' => 'GoodsCatController@getChildCat']);//获取子分类
    Route::match(['get','post'],'goods_cat/edit/{id?}', ['as' => 'admin.goods_cat.edit', 'uses' => 'GoodsCatController@edit'])->where(['id' => '[0-9]+']);
    Route::match(['get','post'],'goods_cat/changeStatus/{id}/{status}', ['as' => 'admin.goods_cat.changeStatus', 'uses' => 'GoodsCatController@changeStatus'])->where(['id' => '[0-9]+','status' => '[0-2]']);

    //财务管理路由
    Route::match(['get','post'],'finance/index', ['as' => 'admin.finance.index', 'uses' => 'FinanceController@index']);//财务列表
    Route::match(['get','post'],'finance/ScorelockIndex', ['as' => 'admin.finance.ScorelockIndex', 'uses' => 'FinanceController@ScorelockIndex']);//红人圈列表
    Route::match(['get','post'],'finance/ScorelockEdit/{id?}', ['as' => 'admin.finance.ScorelockEdit', 'uses' => 'FinanceController@ScorelockEdit']);//红人圈编辑页面
    Route::match(['get','post'],'finance/ScorelockUpdate/{id?}', ['as' => 'admin.finance.ScorelockUpdate', 'uses' => 'FinanceController@ScorelockUpdate']);//初审
    Route::match(['get','post'],'finance/ScorelockUp/{id?}', ['as' => 'admin.finance.ScorelockUp', 'uses' => 'FinanceController@ScorelockUp']);//终审

});



