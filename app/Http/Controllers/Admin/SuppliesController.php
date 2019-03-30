<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-03-02
 * Time: 18:22
 */
namespace App\Http\Controllers\Admin;

use App\GetMac;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SuppliesController extends Controller{


    /**
     * 获取物资字典
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function index()
    {

        if ($this->request->isMethod('post')){

//        echo date('yyyy-MM-dd HH:mm:ss');exit();
//        set_time_limit(0) ;
//        $obj = new GetMac('windows');
        $obj = new GetMac('linux');
        $param['BusinessType'] = "MY003";
        $param['HospitalCode'] = "Test001";
        $param['IP'] = $this->get_real_ip();
        $param['MAC'] = $obj->macAddr;
//        $id = $this->request->post('ID3');
        $id = '1791';
        $lastTime = DB::table('dic_consumable_time')->select('time')->where('ID3',$id)->orderBy('id','DESC')->limit(1)->get()->toArray();
        $lastTime =$this->request->post('time');
//        $lastTime ="2018-01-01 01:01:01";
//        if (empty($id)){
//            return $this->errorResponse('供应商编码为空','206');
//        }
            $insert_time =date("Y-m-d h:m:s");
        DB::table('dic_consumable_time')->insert(['ID3'=>$id,'time'=>$insert_time]);
        $param['Data'] = array("ID3" => $id, "Last_time" => $lastTime);
//        echo json_encode($param);exit();
        $res = $this->http_post_json('http://222.72.92.35:8091/dep/business/get', json_encode($param));
        $data = $res['data'];
        $date = date('Y-m-d H:i:s',time());
//        file_put_contents('./public/param.txt','consumable--'.$date.':'.json_encode($res).PHP_EOL,FILE_APPEND|LOCK_EX);
        $data =  json_decode($data,true);
        if ($res['code']==200){
//            $p = array('Code','Completed','Data');
//            for($i = 0;$i < count($p);$i ++ ){
//                $j = $p[$i];
//                if(!array_key_exists($j,$res))
//                    exit($j.'不存在');
//            }
            if (empty($data['Data'])){
                $data = DB::table('dic_consumable')->orderBy('id', 'DESC')->paginate(15);
                return view('admin/consumable/table',['data'=>$data]);
            }


            if($data['Code']==0&&$data['Completed']==true){

                $data = $data['Data'];

                $uniCode = DB::table('dic_consumable')->get(['UniCode'])->toArray();
                $uniCode = array_column($uniCode,'UniCode');

                foreach ($data as $k=>$v){
                    if (in_array($v['UniCode'],$uniCode)){
                        unset($data[$k]);
                    }
                }

                if (empty($data)){
                    $data = DB::table('dic_consumable')->orderBy('id', 'DESC')->paginate(15);
                    return view('admin/consumable/table',['data'=>$data]);
                }


                foreach ($data as $k=>$v){
                    $data[$k]['CreatedAt'] = $date;
                    $data[$k]['Token']     = time().uniqid();
                }
                foreach ($data as $k=>$v){
                    unset($data[$k]['MaterialsProps']);
                    unset($data[$k]['Enable']);
                    unset($data[$k]['ColdChainTempFrom']);
                    unset($data[$k]['ColdChainTempTo']);
                }

                DB::table('dic_consumable')->truncate();
//             dd($data);
                foreach ($data as $k=>$v){
                    $result = DB::table('dic_consumable')->insert($v);
                }
                if ($result){

                    $data = DB::table('dic_consumable')->orderBy('id', 'DESC')->paginate(15);

                    return view('admin/consumable/table',['data'=>$data,'res'=>'成功']);
                }else {
                    return $this->errorResponse('插入失败','206');
                }
            }
        }else{
            return $this->errorResponse('威宁获取物资接口错误','404');
        }

        }else{
            $data = DB::table('dic_consumable')->orderBy('id', 'DESC')->paginate(15);

            return view('admin/consumable/index',['data'=>$data]);
        }
    }


    public function get(){
        echo '{
    "Code":0,
    "Message":"成功",
    "Completed":true,
    "Data":[
        {
            "UniCode":"A.176183.10",
            "Name":"耗材 01",
            "TradeName":"name",
            "Spec":"耗材规格",
            "UseBarCode ":"0",
            "Unit":"瓶",
            "Price":60,
            "Manufacturer":"贵州天安药业股份有限公司",
            "ApprovalNumber":"黔 20160024",
            "ManagementCategory":"3",
            "ManagementCategory":"4",
            "Source":"0",
            "UpdateTime":"2018-01-10 01:01:01"
        },
        {
            "UniCode":"A.185770.1",
            "Name":"耗材 02",
            "TradeName":"name",
            "Spec":"耗材规格",
            "UseBarCode ":"0",
            "Unit":"瓶",
            "Price":60,
            "Manufacturer":"贵州天安药业股份有限公司",
            "ApprovalNumber":"黔 20160024",
            "ManagementCategory":"2",
            "ManagementCategory":"4",
            "Source":"0",
            "UpdateTime":"2018-01-10 01:01:01"
        },
        {
            "UniCode":"A.101116.1",
            "Name":"耗材 03",
            "TradeName":"name",
            "Spec":"耗材规格",
            "UseBarCode ":"0",
            "Unit":"瓶",
            "Price":60,
            "Manufacturer":"贵州天安药业股份有限公司",
            "ApprovalNumber":"黔 20160024",
            "ManagementCategory":"1",
            "ManagementCategory":"4",
            "Source":"0",
            "UpdateTime":"2018-01-10 01:01:01"
        }
    ]
}';
    }



    public function getTime(){
            $ID3 = Request()->input('ID3');
            $lastTime = DB::table('dic_consumable_time')->select('time')->where('ID3',$ID3)->orderBy('id','DESC')->first();
            if (empty($lastTime)) {
                $lastTime = '2018-01-01 01:01:01';
            }else{
                $lastTime = $lastTime->time;
            }
            return $lastTime;
    }


}