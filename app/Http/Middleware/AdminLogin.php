<?php

namespace App\Http\Middleware;

use App\AdminPrivilege;
use App\AdminRoleRelevance;
use App\RolePrivilegeRelevance;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
//        return redirect('/admin/login')->with('msg', '费用到期');
        if ($request->cookie('adminCookie') != null && session('adminSession') == null) {
        session()->put('adminSession', $request->cookie('adminCookie'));
        }
        if (session('adminSession')) {
            //取session中的admin
            $admin = session()->get('adminSession');

            if(isset($admin->aid)){
                $aid = $admin->aid;
                //获取当前用户操作的控制器和方法
                $accessinfo = $this->getCurrentAction(request()->route()->getActionName());
//            var_dump($accessinfo);
                //获取该用户拥有的所有权限controller@action 一维数组
                $currentPri = $this->getPris($aid);
//            dd($currentPri);
//            if (!in_array($accessinfo['access'],$currentPri)&&!in_array($accessinfo['access'],config('admin.commonAccess'))){
//                return redirect()->back()->with('checkPrivilege', '没有此权限');
//            }
                $data = DB::table('admin_role_relevance')
                    ->join('role_privilege_relevance', 'admin_role_relevance.r_id', '=', 'role_privilege_relevance.r_id')
                    ->join('privilege', 'role_privilege_relevance.p_id', '=', 'privilege.p_id')
                    ->select('p_name','controller_name','method_name','route','privilege.p_id','pid','order')
                    ->where(['a_id' => $aid, 'is_show' => 1])
                    ->orderBy('order','ASC')
                    ->distinct()
                    ->get();
                $result = [];
                $result = self::getTree($data);
                view()->share('result', $result);
                return $next($request);
            }else{
                return redirect('/admin/login')->with('msg', '请重新登陆');
            }

        }else{
//            return $next($request);
            return redirect('/admin/login')->with('msg', '请先登录');
        }
    }

    //获取当前用户拥有的权限
    public function getPris($aid)
    {
        //用模型查询此用户aid对应的角色r_id
        $rids = AdminRoleRelevance::getRid($aid);
        //用模型查层由r_id获取所有权限详情
        $priInfo = AdminPrivilege::getPriInfo($rids);
        //遍历此用户所有权限
        $routes = [];
        foreach ($priInfo as $k => $v) {
            array_push($routes, $v['controller_name'] . "@" . $v['method_name']);
        }
//        dd($routes);
        return $routes;
    }

    //无限极获取权限树
    public function getTree($data, $pid = 0)
    {
        $tree = [];
        foreach ($data as $key => $val) {
            if ($val->pid == $pid) {
                $son = self::getTree($data, $val->p_id);
                if (!empty($son)) $val->son = $son;
                $tree[] = $val;
            }
        }
        return $tree;
    }

    //获取访问控制器的信息
    public function getCurrentAction($path)
    {
        $arr = explode("@", $path);
        $controller_name = basename($arr[0]);
        if($_SERVER['HTTP_HOST']=='green.tangyijiqiren.com'){
            $name = explode('\\',$controller_name);
            $controller_name = $name[4];
        }
        return ['method_name' => $arr[1], "controller_name" => $controller_name, "access" => $controller_name . "@" . $arr[1]];
    }
}
