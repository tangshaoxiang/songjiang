<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin',

], function () {
    /**
     * 登陆管理
     */
    Route::any('login', 'LoginController@login');                      //登陆页面
    Route::any('logout', 'LoginController@logout');                    //登陆页面

    Route::any('test', 'UserController@test');                         //测试跨域

});

Route::group([
    'prefix' => '',
    'namespace' => ''
], function () {
    //后台管理
    Route::group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => 'admin.login'
    ], function () {
        Route::any('index', 'LoginController@index');                    //首页

        /**
         * 管理员管理
         */
        Route::any('admin_add', 'AdminController@adminAdd');             //管理员添加
        Route::any('admin_up', 'AdminController@adminUp');               //管理员修改
        Route::any('admin_list', 'AdminController@adminList');           //管理员列表


        /**
         * 角色管理
         */
        Route::any('role_list', 'AdminController@roleList');            //角色列表
        Route::any('role_add', 'AdminController@roleAdd');              //角色添加
        Route::any('role_up', 'AdminController@roleUp');                //角色修改
        Route::any('role_del', 'AdminController@roleDel');              //角色删除

        /**
         * 权限管理
         */
        Route::any('pri_add', 'AdminController@priAdd');                  //权限添加
        Route::any('pri_del', 'AdminController@priDel');                  //权限删除
        Route::any('pri_up/{id?}', 'AdminController@priUp');              //权限修改
        Route::any('pri_list', 'AdminController@priList');                //权限列表
        Route::any('pri_list_test', 'AdminController@priListTest');       //权限列表测试
        Route::any('pri_allot', 'AdminController@priAllot');             //权限分配
        Route::any('re_pri', 'AdminController@re_pri');                  //权限分配中选择某个角色返回对应权限
        Route::any('pri_sort', 'AdminController@pri_sort');              //左侧栏排序



        /**
         * 获取物资字典
         */
        Route::any('consumable','SuppliesController@index');             //erp获取物资字典
        Route::any('consumableGetTime','SuppliesController@getTime');    //erp获取最后一次获取时间


        /**
         * 获取订单
         */
        Route::any('order','OrderController@index');                      //erp获取订单
        Route::any('order_get','OrderController@get');                      //erp
        Route::any('order_excel','OrderController@orderExcel');           //erp获取订单excel


        /**
         * 同步退货单
         */
        Route::any('sale_return','SaleReturnController@index');                      //erp获取退货单
        Route::any('return_get','SaleReturnController@get');



        /**
         * 推送配送单
         */
        Route::any('distribution','DistributionController@index');                      //erp获取退货单
        Route::any('get_back','DistributionController@getBack');
        Route::any('export','DistributionController@export');



    });
});