<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-03-21
 * Time: 00:37
 */
namespace App\Http\Controllers\Admin;

use App\GetMac;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller{


    /**
     * 同步退货单
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if ($this->request->isMethod('post')) {
            $obj = new GetMac('linux');
            $param['BusinessType'] = "MY104";
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
//        $param['Data'] = array("SupplierCode" => $supplierCode, "Count" => $count, "DownloadState" => $downloadState, "Kssj" => $kssj, "Jssj" => $jssj);
            $param['Data'] = array("SupplierCode" => "SupplierCode", "Count" => "Count", "DownloadState" => "DownloadState", "Kssj" => "Kssj", "Jssj" => "Jssj");


//            $res = $this->getCurl('www.songjiang.cn:8000/admin/return_get?' . http_build_query($param));
//            dd($res);
//            $res = json_decode($res, true);


            $res = $this->http_post_json('https://tj.sjzxyy.com/wz/dep/business/get', json_encode($param));
//            file_put_contents(public_path('erp.log'), 'order--' . $date . ':' . json_encode($res) . PHP_EOL, FILE_APPEND | LOCK_EX);
            $res = $res['data'];
            $res = json_decode($res, true);


//        $p = array('BusinessType', 'HospitalCode', 'IP', 'MAC', 'HostName', 'Data');
//        for ($i = 0; $i < count($p); $i++) {
//            $j = $p[$i];
//            if (!array_key_exists($j, $res))
//                exit($j . '不存在');
//        }
            $token = time() . uniqid();
            $data_arr = $res['Data'];
            $refund_no = DB::table('dic_sales_return')->get(['RefundNO'])->toArray();
            $refund_no = array_column($refund_no,'RefundNO');

            foreach ($data_arr as $k=>$v){
                if (in_array($v['RefundNO'],$refund_no)){
                    unset($data_arr[$k]);
                }
            }


            foreach ($data_arr as $k => $v) {
                foreach ($v['RefundDetail'] as $k1 => $v1) {
                    $data_arr[$k]['RefundDetail'][$k1]['Token'] = $token;
                    $data_arr[$k]['RefundDetail'][$k1]['CreatedAt'] = $date;
                }
            }

          if (!empty($data_arr)){
            foreach ($data_arr as $k => $detail) {
                $sales_return = $k + 1;
                $res_detail = DB::table('dic_sales_return_detail')->insert($detail['RefundDetail']);

                $id = DB::getPdo()->lastInsertId();
                $count = count($detail['RefundDetail']) + $id;
                $RefundDetail = '';
                if ($res_detail) {
                    for ($i = $id; $i < $count; $i++) {
                        $RefundDetail = $RefundDetail . $i . ',';
                    }
                    $RefundDetail = rtrim($RefundDetail, ",");
                } else {
                    return $this->errorResponse('第' . $sales_return . '条退货明细插入失败', '206');
                }
                $data = $detail;
                $data['RefundDetail'] = $RefundDetail;
                $data['BusinessType'] = $res['BusinessType'];
                $data['IP'] = $res['IP'];
                $data['MAC'] = $res['MAC'];
                $data['HostName'] = $res['HostName'];
                $data['Token'] = $token;
                $data['CreatedAt'] = $date;
                $insert_sales_return_data[$k] = $data;
            }
          }else{
              $insert_sales_return_data = [];
          }

            $res_data = DB::table('dic_sales_return')->insert($insert_sales_return_data);
            if ($res_data) {
                $data = DB::table('dic_sales_return')->orderBy('id', 'DESC')->paginate(15);
                return view('admin/saleReturn/table', ['data' => $data]);
            } else {
                return $this->errorResponse('退货数据入库失败', '206');
            }
        }else{
            $data = DB::table('dic_sales_return')->orderBy('id', 'DESC')->paginate(15);
            return view('admin/saleReturn/index', ['data' => $data]);
        }
    }

    public function get()
    {
        return '{
    "BusinessType":"YY104",
    "HospitalCode":"test02",
    "IP":"192.168.0.1",
    "MAC":"123456789012",
    "HostName":"**",
    "Data":[
        {
            "HospitalCode":"test01",
            "RefundNO":"thd0010",
            "SupplierCode":"苏 0001",
            "DistributionSiteCode":"03",
            "Creator":"制单人",
            "CreateTime":"2016-12-28 14:55:01",
            "Memo":"备注",
            "CloseDate":"关闭时间",
            "RefundDetail":[
                {
                    "UniCode":"A.101116.1",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                },
                {
                    "UniCode":"A.101116.2",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                },
                {
                    "UniCode":"A.101116.3",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                }
            ]
        },
        {
            "HospitalCode":"test02",
            "RefundNO":"thd0010",
            "SupplierCode":"苏 0001",
            "DistributionSiteCode":"03",
            "Creator":"制单人",
            "CreateTime":"2016-12-28 14:55:01",
            "Memo":"备注",
            "CloseDate":"关闭时间",
            "RefundDetail":[
                {
                    "UniCode":"A.101116.1",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                },
                {
                    "UniCode":"A.101116.2",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                },
                {
                    "UniCode":"A.101116.3",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                }
            ]
        },
        {
            "HospitalCode":"test03",
            "RefundNO":"thd0010",
            "SupplierCode":"苏 0001",
            "DistributionSiteCode":"03",
            "Creator":"制单人",
            "CreateTime":"2016-12-28 14:55:01",
            "Memo":"备注",
            "CloseDate":"关闭时间",
            "RefundDetail":[
                {
                    "UniCode":"A.101116.1",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                },
                {
                    "UniCode":"A.101116.2",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                },
                {
                    "UniCode":"A.101116.3",
                    "Quantity":1,
                    "Price":1.2,
                    "Amount":1.2,
                    "BatchNO":"生产批号",
                    "ProductionDate":"2016-01-01",
                    "InvalidDate":"2017-01-01",
                    "Reason":"原因",
                    "DistributionDetailID":"",
                    "Memo":"明细备注"
                }
            ]
        }
    ]
}';
    }
}