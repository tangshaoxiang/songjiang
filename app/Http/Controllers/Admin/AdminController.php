<?php
/**
 * Created by PhpStorm.
 * User: Ty_Ro
 * Date: 2018/8/28
 * Time: 15:56
 */

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\AdminPrivilege;
use App\Http\Controllers\Controller;
use App\Role;
use App\RolePrivilegeRelevance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{


    /**
     * 管理员添加
     */
    public function adminAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => 'required|unique:admin|max:35',
                'nickname' => 'required|unique:admin|max:35',
                'pwd' => 'required',
                'head_p' => 'required',
                'position' => 'required'
            ], [
                'required' => ':attribute 不能为空',
                'name.required' => '管理员名称 不能为空',
                'unique' => ':attribute 已存在',
                'max' => ':attribute 应小于35位'
            ], [
                'head_p' => '头像',
                'pwd' => '密码',
                'name' => '管理员',
                'nickname' => '昵称',
                'position' => '职位',
            ]);
            $data = $request->all();
            $role_id = request()->input('r_id');
            if($role_id){
                $rids = implode(',', $role_id);
                $data['r_id'] = $rids;
            }
            $pwd = $request->input('pwd');
            $data['pwd'] = md5($pwd);
            $name = $request->input('name');
            $file = $request->file('head_p');
            if (!empty($file)) {
                $ext = $file->getClientOriginalExtension();
                $temp = $file->getRealPath();
                if ($file->isValid()) {
                    $newfile = 'greenO' . date('YmdHis') . '_' . $name . '_admin' . '.' . $ext;
                    Storage::disk('photo')->put($newfile, file_get_contents($temp));
                    $data['head_p'] = '/images/' . $newfile;
                } else {
                    $errorFile = '上传的错误码' . $file->getErrorMessage();
                    return back()->with($errorFile);
                }
            }
            $data['created_at'] = time();
            $res = DB::table('admin')->insert($data);
            if ($res) {
                return redirect('admin/admin_list');
            } else {
                return back();
            }
        } else {
            $admin = Role::all();
            return view('admin.admin.admin_add',compact('admin'));
        }

    }

    /**
     * 管理员修改
     */
    public function adminUp(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('aid');
            $data = $request->all();
            $this->validate($request, [
                'name' => 'required|max:35|unique:admin,name,'.$id.',aid',
                'nickname' => 'required|max:35|unique:admin,nickname,'.$id .',aid',
                'pwd' => 'required|max:35|',
                'head_p' => 'required'
            ], [
                'required' => ':attribute 不能为空',
                'name.required' => '管理员名称 不能为空',
                'unique' => ':attribute 已存在',
                'max' => ':attribute 应小于35位'
            ], [
                'head_p' => '头像',
                'pwd' => '密码',
                'name' => '管理员',
                'nickname' => '昵称',
            ]);
            $role_id = request()->input('r_id');
            if($role_id){
                $rids = implode(',', $role_id);
                $data['r_id'] = $rids;
            }
            $pwd = $request->input('pwd');
            $oldPwd = Admin::where(['aid' => $id])->value('pwd');
            if ($oldPwd == $pwd) {
                $data['pwd'] = $pwd;
            } else {
                $data['pwd'] = md5($pwd);
            }
            $name = $request->input('name');
            $file = $request->file('head_p');
            if (!empty($file)) {
                $ext = $file->getClientOriginalExtension();
                $temp = $file->getRealPath();
                if ($file->isValid()) {
                    $newfile = 'greenO' . date('YmdHis') .'_admin' . '.' . $ext;
                    Storage::disk('photo')->put($newfile, file_get_contents($temp));
                    $data['head_p'] = '/images/' . $newfile;
                } else {
                    $errorFile = '上传的错误码' . $file->getErrorMessage();
                    return back()->with($errorFile);
                }
            }

            $data['updated_at'] = time();
            $res = DB::table('admin')->where(['aid' => $id])->update($data);
            if ($res) {
                return redirect('admin/admin_list');
            } else {
                return back();
            }
        } else {
            $id = $request->get('aid');
            $data = Admin::where(['aid' => $id])->first();
            return view('admin.admin.admin_add', ['data' => $data]);
        }
    }

    /**
     * 管理员列表
     */
    public function adminList(Request $request)
    {
        $number = $request->input('number') ?: 10;
        $search = $request->input('search') ?: '';
        $data = DB::table('admin')
            ->select('aid', 'name', 'nickname', 'iphone','position', 'email', 'head_p', 'status', 'created_at', 'updated_at');
        if ($search) $data->where('name', 'like', "%{$search}%");
        $data = $data->paginate($number);
        $data->appends(array(
            'number' => $number,
            'search' => $search,
        ));
        if ($request->isMethod('post')) {
            return view('admin/admin/admin_list_search', ['data' => $data, 'number' => $number, 'search' => $search]);
        } else {
            return view('admin/admin/admin_list', ['data' => $data, 'number' => $number, 'search' => $search]);
        }
    }

    /**
     * 职位列表
     */
    public function roleList(Request $request)
    {
        $number = $request->input('number') ?: 10;
        $search = $request->input('search') ?: '';
        $data = DB::table('role')->select('r_id', 'r_name', 'addtime', 'status');
        if ($search) $data->where('r_name', 'like', "%{$search}%");
        $data = $data->paginate($number);
        foreach ($data as $key => $val) {
            $data[$key]->p_name = AdminPrivilege::getPriname($val->r_id);
        }
        $data->appends(array(
            'number' => $number,
            'search' => $search,
        ));
        if ($request->isMethod('post')) {
            return view('admin/role/role_list_search', ['data' => $data, 'number' => $number, 'search' => $search]);
        } else {
            return view('admin/role/role_list', ['data' => $data, 'number' => $number, 'search' => $search]);
        }
    }

    /**
     * 职位添加
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function roleAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'r_name' => 'required',
            ], [
                'required' => ':attribute 不能为空',
            ], [
                'r_name' => '职位名称',
            ]);
            $roles = new Role();
            $roles->r_name = request()->input('r_name');
            $roles->addtime = time();
            $roles->status = request()->input('status');
            $res = $roles->save();
            $r_id = $roles->id;
            if ($res) {
                if (request()->input('p_id')) {
                    $arr = [];
                    foreach (request()->input('p_id') as $v) {
                        $arr[] = ['r_id' => $r_id, 'p_id' => $v];
                    }
                    DB::table('role_privilege_relevance')->insert($arr);
                }
                return redirect('admin/role_list');
            } else {
                return redirect()->back()->with('roles', '角色添加失败');
            }
        } else {
            $allPriInfo = AdminPrivilege::getAllPri();
            $data = AdminPrivilege::getPriTree($allPriInfo);
            return view('admin/role/add', ['priData' => $data]);
        }
    }

    /**
     * 职位修改
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function roleUp(Request $request)
    {
        $id = $request->input('r_id');
//        echo $id;
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'r_name' => 'required',
            ], [
                'required' => ':attribute 不能为空',
            ], [
                'r_name' => '职位名称',
            ]);

            $data = $request->all();
            $res = DB::table('role')->where(['r_id' => $id])->update(
                [
                    'r_name' => $data['r_name'],
                    'addtime' => time(),
                    'status' => $data['status']
                ]);

            if ($res) {
                //先删除原有权限,再添加新权限
                RolePrivilegeRelevance::where('r_id', $id)->delete();
                if (request()->input('p_id')) {
                    $arr = [];
                    foreach (request()->input('p_id') as $v) {
                        $arr[] = ['r_id' => $id, 'p_id' => $v];
                    }
                    DB::table('role_privilege_relevance')->insert($arr);
                }
                return redirect('admin/role_list');
            } else {
                return back();

            }

        } else {
            $data = Role::where(['r_id' => $id])->first();;
            $pri = RolePrivilegeRelevance::where('r_id', $id)->get(['p_id'])->toArray();
            $pris = AdminPrivilege::get(['p_id', 'p_name', 'pid', 'status', 'is_show']);
            $pri = array_column($pri, 'p_id');
            $priss = [];
            foreach ($pris as $k => $v) {
                $v->checked = in_array($v->p_id, $pri) ? 1 : 0;
                $priss[$k] = $v;
            }
            $priData = AdminPrivilege::getPriTree($priss);
            return view('admin/role/add', compact('data', 'priData'));
        }
    }

    /**
     * 职位删除
     * @param Request $request
     * @return array
     */
    public function roleDel(Request $request)
    {
        $id = $request->input('id');
        $id = rtrim($id, ',');
        $id = explode(',', $id);
        $res = DB::table('role')->whereIn('r_id', $id)->delete();
        if ($res) {
            return ['msg' => 1];
        } else {
            return ['msg' => 0];
        }
    }

    /**
     * 权限添加
     */
    public function priAdd(Request $request)
    {
        if (request()->isMethod('post')) {
            $data = $request->all();
            $data['addtime'] = time();
            $pid = $request->input('pid');
            if ($pid == 0) {
                $order = AdminPrivilege::where('pid', '=', 0)->orderBy('order', 'DESC')->first(['order'])->toArray();
                $order = $order['order'] + 1;
                $level = 0;
                $data['order'] = $order;
            } else {
                $level = AdminPrivilege::where('p_id', '=', $pid)->value('level');
                $level = $level + 1;
            }
            $data['level'] = $level;
            $res = AdminPrivilege::create($data);
            if ($res) {
                return redirect('admin/pri_list');
            } else {
                return back();
            }
        } else {
            //scandir 列出指定目录的文件,为一个数组
            $all = scandir(app_path('Http/Controllers/Admin'));
            $except = ['.', '..', 'LoginController.php'];
            $controller = [];
            foreach ($all as $v) {
                if (strpos($v, 'Controller') === false) {
                    $son = scandir(app_path('Http/Controllers/Admin/' . $v));
                    foreach ($son as $v1) {
                        if (!in_array($v, $except)) {
                            // $controller[] = trim($v,'.php');
                            //或者用array_push函数追加到数组中,键为数字索引,值为追加的值
                            array_push($controller, trim($v1, '.php'));
                        }
                    }
                } else {
                    if (!in_array($v, $except)) {
                        // $controller[] = trim($v,'.php');
                        //或者用array_push函数追加到数组中,键为数字索引,值为追加的值
                        array_push($controller, trim($v, '.php'));
                    }
                }
            }
            $controller = array_filter($controller);
            $nodes = AdminPrivilege::getAllPri();
            $pri = AdminPrivilege::getOrder($nodes);
            return view('admin/privilege/add', ['pri' => $pri, 'controller' => $controller]);
        }
    }

    /**
     * 权限单个删除
     * @param Request $request
     * @return array
     */
    public function priDel(Request $request)
    {
        $id = $request->input('id');
        $id = rtrim($id, ',');
        $id = explode(',', $id);
        $pid = DB::table('privilege')->whereIn('p_id',$id)->value('pid');
        if($pid==0){
            $res = false;
            return ['msg'=>2];
        }else{
            $res = DB::table('privilege')->whereIn('p_id', $id)->delete();
        }
        if ($res) {
            return ['msg' => 1];
        } else {
            return ['msg' => 0];
        }
    }

    /**
     * 权限修改
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function priUp(Request $request, $id)
    {
        if (request()->isMethod('post')) {
            $p_id = $request->input('p_id');
            $pid = $request->input('pid');
            if ($pid == 0) {
                $level = 0;
            } else {
                $level = AdminPrivilege::where('p_id', '=', $pid)->value('level');
                $level = $level + 1;
            }
            $pri = AdminPrivilege::find($p_id);
            $pri->p_name = $request->input('p_name');
            $pri->controller_name = $request->input('controller_name');
            $pri->method_name = $request->input('method_name');
            $pri->route = $request->input('route');
            $pri->addtime = time();
            $pri->status = $request->input('status');
            $pri->pid = $pid;
            $pri->level = $level;
            $pri->is_show = $request->input('is_show');
            $result = $pri->save();
            if ($result) {
                return redirect('admin/pri_list');
            } else {
                return back();
            }
        } else {
            //scandir 列出指定目录的文件,为一个数组
            $all = scandir(app_path('Http/Controllers/Admin'));
            $except = ['.', '..', 'LoginController.php'];
            $controller = [];
            foreach ($all as $v) {
                if (!in_array($v, $except)) {
                    // $controller[] = trim($v,'.php');
                    //或者用array_push函数追加到数组中,键为数字索引,值为追加的值
                    array_push($controller, trim($v, '.php'));
                }

            }
//            var_dump($controller);exit;
            $nodes = AdminPrivilege::getAllPri();
            $pri = AdminPrivilege::getOrder($nodes);
//            dd($pri);
            $data = DB::table('privilege')->where('p_id', $id)->first();
//            dd($data);
            return view('admin/privilege/add', ['data' => $data, 'pri' => $pri, 'controller' => $controller]);
        }
    }

    /**
     * 权限列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function priList(Request $request)
    {
        $page = $request->input('page')?:1;
        $number = $request->input('number')?:2;
        $offset = ($page-1)*$number;
        $search = $request->input('search')?:'';
        $data = DB::table('privilege')
            ->select('p_id','p_name','controller_name','method_name','route','addtime','status','pid','level');
        if($search) $data->where('p_name','like',"%$search%");
        $data = $data->get()->toArray();
        $data = AdminPrivilege::getPriTreeObject($data);
        $new_data = DB::table('privilege')
            ->select('p_id','p_name','controller_name','method_name','route','addtime','status','pid','level')->where('pid','=',0);
        if ($search) $new_data->where('p_name', 'like', "%{$search}%");
        $new_data = $new_data->skip($offset)->take($number)->get()->toArray();
        $total = DB::table('privilege')->where('pid','=',0)->count();
        $totalPage = ceil($total/$number);
        foreach ($new_data as $key => $val) {
            foreach ($data as $k=>$v){
                if($val->p_id==$v->p_id){
                    $new_data[$key]= $v;
                }
            }
        }
//        dd($new_data);
        if($request->isMethod('post')){
            return view('admin/privilege/privilege_list_search',['data'=>$new_data,'number'=>$number,'search'=>$search,'page'=>$page,'totalPage'=>$totalPage]);
        }else{
            return view('admin/privilege/privilege_list',['data'=>$new_data,'number'=>$number,'search'=>$search,'page'=>$page,'totalPage'=>$totalPage]);
        }
    }

    public function priListTest(Request $request)
    {
        $page = $request->input('page')?:1;
        $number = $request->input('number')?:10;
        $search = $request->input('search')?:'';
        $offset = ($page-1)*$number;
echo $offset;
//        DB::connection()->enableQueryLog();#开启执行日志
        $data = DB::table('privilege')
            ->select('p_id','p_name','controller_name','method_name','route','addtime','status','pid','level')->skip($offset)->take($number)->get()->toArray();
        $total = DB::table('privilege')->count();
        $totalPage = ceil($total/$number);
//        print_r(DB::getQueryLog());

        if($request->isMethod('post')){
            return view('admin/privilege/privilege_list_test_search',['data'=>$data,'number'=>$number,'search'=>$search,'page'=>$page,'totalPage'=>$totalPage]);
        }else{
            return view('admin/privilege/privilege_list_test',['data'=>$data,'number'=>$number,'search'=>$search,'page'=>$page,'totalPage'=>$totalPage]);
        }
    }

    //分配权限
    public function priAllot(Request $request)
    {
        if ($request->isMethod('post')) {
            $r_id = $request->input('r_id');
            //先删除原有权限,再添加新权限
            RolePrivilegeRelevance::where('r_id', $r_id)->delete();
            if (request()->input('p_id')) {
                $arr = [];
                foreach (request()->input('p_id') as $v) {
                    $arr[] = ['r_id' => $r_id, 'p_id' => $v];
                }
                $res = DB::table('role_privilege_relevance')->insert($arr);
            }else{
                $res = true;
            }
            if ($res) {
                return redirect('admin/role_list');
            } else {
                return back();
            }
        } else {
            $role = DB::table('role')->get();
            $r_id = $role[0]->r_id;
            $allpid = AdminPrivilege::get(['p_id', 'p_name', 'pid']);
            $somepid = RolePrivilegeRelevance::where(['r_id' => $r_id])->get(['p_id'])->toArray();
            $somepid = array_column($somepid, 'p_id');
            $result = [];
            foreach ($allpid as $k => $v) {
                $v->checked = in_array($v->p_id, $somepid) ? 1 : 0;
                $result[$k] = $v;
            }
            $data = AdminPrivilege::getPriTree($result);
            return view('admin.privilege.fp', ['role' => $role, 'data' => $data]);
        }
    }


    //选择职位返回对应的权限
    public function re_pri(Request $request)
    {
        $r_id = $request->input('id');
        $allpid = AdminPrivilege::get(['p_id', 'p_name', 'pid']);
        $somepid = RolePrivilegeRelevance::where(['r_id' => $r_id])->get(['p_id'])->toArray();
        $somepid = array_column($somepid, 'p_id');
        $result = [];
        foreach ($allpid as $k => $v) {
            $v->checked = in_array($v->p_id, $somepid) ? 1 : 0;
            $result[$k] = $v;
        }
        $result = AdminPrivilege::getPriTree($result);
        return view('admin/privilege/table', ['data' => $result]);
    }


    //左侧栏排序功能
    public function pri_sort(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            foreach ($data as $k=>$v){
                DB::table('privilege')->where('p_id','=',$k)->update(['order'=>$v]);
            }
        }else{
            $data = AdminPrivilege::where('pid', '=', '0')
                ->orderBy('order', 'ASC')->get(['p_name', 'order', 'p_id']);
            return view('admin/privilege/sort', ['data' => $data]);
        }
    }

}