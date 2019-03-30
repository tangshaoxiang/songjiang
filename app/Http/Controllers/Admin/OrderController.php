<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-03-20
 * Time: 22:48
 */
namespace App\Http\Controllers\Admin;

use App\GetMac;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller{

    /**
     * 同步订单
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if ($this->request->isMethod('post')) {

            $obj = new GetMac('linux');
            $param['BusinessType'] = "MY101";
            $param['HospitalCode'] = "Test001";
            $param['IP'] = $this->get_real_ip();
            $param['MAC'] = $obj->macAddr;
            $param['HostName'] = $_SERVER['SERVER_NAME'];
            $supplierCode = $this->request->post('SupplierCode');
            $count = $this->request->post('Count');
            $downloadState = $this->request->post('DownloadState');
            $kssj = $this->request->post('Kssj');
            $jssj = $this->request->post('Jssj');
            if (empty($supplierCode)) {
                return $this->errorResponse('供应商编码为空', '206');
            }
            if (empty($count)) {
                return $this->errorResponse('查询记录数为空', '206');
            }

            if (empty($kssj)) {
                return $this->errorResponse('开始时间为空', '206');
            }
            if (empty($jssj)) {
                return $this->errorResponse('结束时间为空', '206');
            }
            $date = date('Y-m-d H:i:s', time());
            $param['Data'] = array("SupplierCode" => $supplierCode, "Count" => $count, "DownloadState" => $downloadState, "Kssj" => $kssj, "Jssj" => $jssj);


//            $res = $this->getCurl('www.songjiang.cn:8000/admin/order_get?' . http_build_query($param));
//            $res = json_decode($res, true);


            $res = $this->http_post_json('http://222.72.92.35:8091/dep/business/get', json_encode($param));
//            file_put_contents(public_path('erp.log'), 'order--' . $date . ':' . json_encode($res) . PHP_EOL, FILE_APPEND | LOCK_EX);
            $res = $res['data'];
//            dd($res);
            $res = json_decode($res, true);








//            $p = array('BusinessType', 'HospitalCode', 'IP', 'MAC', 'HostName', 'Data');
//            for ($i = 0; $i < count($p); $i++) {
//                $j = $p[$i];
//                if (!array_key_exists($j, $res))
//                    exit($j . '不存在');
//            }
            $token = time() . uniqid();
            $res_data  = $res['Data'];
//
//            foreach ($res_data as $k=>$v){
//                foreach ($v as $k1=>$v1){
//                    if (empty($v1['DeptCode'])||empty($v1['StoreCode'])){
//                        unset($res_data[$k]);
//                    }
//                }
//            }
//            dd($res_data);
            $order_no = DB::table('dic_order')->get(['OrderNo'])->toArray();
            $order_no = array_column($order_no,'OrderNo');



            foreach ($res_data as $k=>$v){
                 if (in_array($v['OrderNO'],$order_no)){
                     unset($res_data[$k]);
                 }
            }

           if (empty($res_data)){
               $data = DB::table('dic_order')->orderBy('id', 'DESC')->paginate(15);
               return view('admin/order/table',['data'=>$data]);
           }
            foreach ($res_data as $k => $detail) {

                $order = $k + 1;
                $res_detail = DB::table('dic_order_detail')->insert($detail['PurchaseDetail']);

                $id = DB::getPdo()->lastInsertId();
                $count = count($detail['PurchaseDetail']) + $id;
                $PurchaseDetail = '';
                if ($res_detail) {
                    for ($i = $id; $i < $count; $i++) {
                        $PurchaseDetail = $PurchaseDetail . $i . ',';
                    }
                    $PurchaseDetail = rtrim($PurchaseDetail, ",");
                } else {
                    Log::useFiles(storage_path().'/logs/laravel.log')->info('用户注册原始数据:','第' . $order . '条订单明细插入失败');
                    return $this->errorResponse('第' . $order . '条订单明细插入失败', '206');
                }

                $data = $detail;
                $data['PurchaseDetail'] = $PurchaseDetail;
//                $data['BusinessType'] = $res['BusinessType'];
//                $data['IP'] = $res['IP'];
//                $data['MAC'] = $res['MAC'];
//                $data['HostName'] = $res['HostName'];
                $data['Token'] = $token;
                $data['CreatedAt'] = $date;
                $insert_order_data[$k] = $data;
            }
            $res_data = DB::table('dic_order')->insert($insert_order_data);
            if ($res_data) {
                $data = DB::table('dic_order')->orderBy('id', 'DESC')->paginate(15);
                return view('admin/order/table',['data'=>$data]);

            } else {
               return $this->errorResponse('订单数据入库失败', '206');
            }

        }else{
            $data = DB::table('dic_order')->orderBy('id', 'DESC')->paginate(15);
            return view('admin/order/index',['data'=>$data]);
        }

        }




    public function get()
    {
        echo '{
    "BusinessType": "YY101",
    "HospitalCode": "Test001",
    "IP": "192.168.0.1",
    "MAC": "123456789012",
    "HostName": "**",
    "Data": [
        {
            "HospitalCode": "Test004",
            "OrderNO": "Test002",
            "OrderType": 0,
            "OrderLevel": 0,
            "SupplierCode": "苏 20160343",
            "DistributionSiteCode": "01",
            "DistributionSite": "配送点名称",
            "PPOrderNO": "JH991",
            "Employee": "**",
            "SumQuantity": 4,
            "Amount": 13.8,
            "ArrivalTime": "2016-12-28 14:55:01",
            "Creator": "制单人",
            "CreateTime": "2016-12-28 14:55:01",
            "Memo": "备注",
            "PurchaseDetail": [
                {
                    "Seq": 1,
                    "UniCode": "A.101116.1",
                    "Quantity": 1,
                    "Price": 1.2,
                    "Amount": 1.2,
                    "DeptCode": "科室编码 1",
                    "DeptName": "科室名称 1",
                    "StoreCode": "库房编码 1",
                    "StoreName": "库房名称 1",
                    "Memo": "明细备注"
                },
                {
                    "Seq": 2,
                    "UniCode": "A.185770.1",
                    "Quantity": 3,
                    "Price": 4.2,
                    "Amount": 12.6,
                    "DeptCode": "科室编码 2",
                    "DeptName": "科室名称 1",
                    "StoreCode": "库房编码 2",
                    "StoreName": "库房名称 2",
                    "Memo": "明细备注 2"
                }
            ],
            "SupplierReadDate": "2018-01-01",
            "SupplierConfirmer": "供应商确认人",
            "SupplierConfirmDate": "2018-01-01",
            "CloseDate": "2018-01-01",
            "Code1": "",
            "Code2": "",
            "Code3": ""
        },
        {
            "HospitalCode": "Test005",
            "OrderNO": "Test002",
            "OrderType": 0,
            "OrderLevel": 0,
            "SupplierCode": "苏 20160343",
            "DistributionSiteCode": "01",
            "DistributionSite": "配送点名称",
            "PPOrderNO": "JH991",
            "Employee": "**",
            "SumQuantity": 4,
            "Amount": 13.8,
            "ArrivalTime": "2016-12-28 14:55:01",
            "Creator": "制单人",
            "CreateTime": "2016-12-28 14:55:01",
            "Memo": "备注",
            "PurchaseDetail": [
                {
                    "Seq": 1,
                    "UniCode": "A.101116.1",
                    "Quantity": 1,
                    "Price": 1.2,
                    "Amount": 1.2,
                    "DeptCode": "科室编码 1",
                    "DeptName": "科室名称 1",
                    "StoreCode": "库房编码 1",
                    "StoreName": "库房名称 1",
                    "Memo": "明细备注"
                },
                {
                    "Seq": 2,
                    "UniCode": "A.185770.1",
                    "Quantity": 3,
                    "Price": 4.2,
                    "Amount": 12.6,
                    "DeptCode": "科室编码 2",
                    "DeptName": "科室名称 1",
                    "StoreCode": "库房编码 2",
                    "StoreName": "库房名称 2",
                    "Memo": "明细备注 2"
                }
            ],
            "SupplierReadDate": "2018-01-01",
            "SupplierConfirmer": "供应商确认人",
            "SupplierConfirmDate": "2018-01-01",
            "CloseDate": "2018-01-01",
            "Code1": "",
            "Code2": "",
            "Code3": ""
        },
        {
            "HospitalCode": "Test006",
            "OrderNO": "Test002",
            "OrderType": 0,
            "OrderLevel": 0,
            "SupplierCode": "苏 20160343",
            "DistributionSiteCode": "01",
            "DistributionSite": "配送点名称",
            "PPOrderNO": "JH991",
            "Employee": "**",
            "SumQuantity": 4,
            "Amount": 13.8,
            "ArrivalTime": "2016-12-28 14:55:01",
            "Creator": "制单人",
            "CreateTime": "2016-12-28 14:55:01",
            "Memo": "备注",
            "PurchaseDetail": [
                {
                    "Seq": 1,
                    "UniCode": "A.101116.1",
                    "Quantity": 1,
                    "Price": 1.2,
                    "Amount": 1.2,
                    "DeptCode": "科室编码 1",
                    "DeptName": "科室名称 1",
                    "StoreCode": "库房编码 1",
                    "StoreName": "库房名称 1",
                    "Memo": "明细备注"
                },
                {
                    "Seq": 2,
                    "UniCode": "A.185770.1",
                    "Quantity": 3,
                    "Price": 4.2,
                    "Amount": 12.6,
                    "DeptCode": "科室编码 2",
                    "DeptName": "科室名称 1",
                    "StoreCode": "库房编码 2",
                    "StoreName": "库房名称 2",
                    "Memo": "明细备注 2"
                }
            ],
            "SupplierReadDate": "2018-01-01",
            "SupplierConfirmer": "供应商确认人",
            "SupplierConfirmDate": "2018-01-01",
            "CloseDate": "2018-01-01",
            "Code1": "",
            "Code2": "",
            "Code3": ""
        }
    ]
}';
    }

}