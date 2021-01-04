<?php
/**
 * Created by PhpStorm.
 * User: Ty_Ro
 * Date: 2018/8/28
 * Time: 13:55
 */

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\AdminPrivilege;
use App\AdminRoleRelevance;
use App\Http\Controllers\Controller;
use App\Production;
use App\Status;
use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{

    public $request;
    public $admin;

    public function __construct(Request $request, Admin $admin)
    {
        $this->request = $request;
        $this->admin = $admin;
    }

    public function index()
    {
        $data['php_v'] = PHP_VERSION;                                                                               //PHP程式版本
        $data['zend_v'] = zend_version();                                                                           //ZEND版本
        $data['php_os'] = PHP_OS;                                                                                   //服务器操作系统
        $data['server_v'] = $_SERVER ['SERVER_SOFTWARE'];                                                           //服务器端信息
        $data['upload_max'] = get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize"): "不允许上传附件"; //最大上传限制
        $data['max_time'] = get_cfg_var("max_execution_time")."秒 ";                                         //最大执行时间
        $data['max_limit'] = get_cfg_var("memory_limit") ? get_cfg_var("memory_limit") : "无";        //脚本运行占用最大内存
        return view('admin.index',['data'=>$data]);
    }

    public function logout()
    {
        session()->forget('adminSession');
        Cookie::queue(Cookie::forget('adminCookie'));
        return view('admin/login');
    }



    public function testTime(){

//        //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
//        ignore_user_abort();
//// 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
//        set_time_limit(0);
//// 每隔5分钟运行
//        $interval=5;
//        do{
//            $url = 'http://erp.chunyingkeji.com//admin/test_time';
//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch, CURLOPT_TIMEOUT, 2);
//$result = curl_exec($ch);
//curl_close($ch);
//// 等待5分钟
//sleep($interval);
//}while(true);
        ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
        set_time_limit(0);// 通过set_time_limit(0)可以让程序无限制的执行下去
        $interval=5;// 每隔半小时运行
        do{
            file_put_contents(public_path().'/log.txt',date('YmdHis').'test'.PHP_EOL,FILE_APPEND);
            //这里是你要执行的代码
            sleep($interval);// 等待5分钟
        }while(true);

    }

    public function login()
    {

        if ($this->request->isMethod('post')) {
            return back()->withErrors('费用已到期');
            $this->validate($this->request, [
                'admin_name' => 'required',
                'admin_pwd' => 'required',
            ], [
                'required' => ':attribute 不能为空',
            ], [
                'admin_name' => '名称',
                'admin_pwd' => '密码',
            ]);
            $name = $this->request->get('admin_name');
            $pwd = $this->request->get('admin_pwd');
            $seven = $this->request->get('seven');
            $admin = $this->admin->where([
                'name' => $name,
                'pwd' => md5($pwd),
                'status' => 1
            ])->first();

            if ($admin) {
                //若选七天免登陆，则将用户信息存cookie七天
                if (md5($pwd) == $admin->pwd) {
                    if ($seven) {
                        Cookie::queue('adminCookie', $admin, 7 * 24 * 60);
                    }
                }
                session(['adminSession' => $admin]);
                return redirect('admin/index')->with('登录成功');
            }
            return back()->withErrors('登录失败');
        } else {
            return view('admin.login');
        }
    }
}