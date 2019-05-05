<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-03-20
 * Time: 22:48
 */
namespace App\Http\Controllers\Admin;

use App\DicConsumable;
use App\DicOrder;
use App\DicOrderDetail;
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

            foreach ($res_data as $k=>$v){
                foreach ($v['PurchaseDetail'] as $k1=>$v1){
                    if (empty($v1['DeptCode'])||empty($v1['StoreCode'])){
                        unset($res_data[$k]);
                    }
                }
            }
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

    public function orderDetail(){
        $id = Request()->input('id');
        $order = DB::table('dic_order')->where('id',$id)->first();
        $idArr = explode(',',$order->PurchaseDetail);
        $detail =DB::table('dic_order_detail')->whereIn('Id',$idArr)->get()->toArray();
        foreach ($detail as $k=>$v){
          $goods = DicConsumable::select('Name','Spec','Manufacturer')->where('UniCode',$v->UniCode)->orderBy('id','DESC')->first()->toArray();
          $detail[$k]->Name =   $goods['Name'];
          $detail[$k]->Spec =   $goods['Spec'];
          $detail[$k]->Manufacturer =   $goods['Manufacturer'];
        }

        $total = array_column($detail,'Amount');
        $total = array_sum($total);
        $big   = $this->num_to_rmb($total);

        return view('admin/order/detail',['detail'=>$detail,'order'=>$order,'total'=>$total,'big'=>$big]);
    }

    public function orderPdf(){
        $id = Request()->input('id');
        $order = DB::table('dic_order')->where('id',$id)->first();
        $idArr = explode(',',$order->PurchaseDetail);
        $detail =DB::table('dic_order_detail')->whereIn('Id',$idArr)->get()->toArray();
        foreach ($detail as $k=>$v){
            $goods = DicConsumable::select('Name','Spec','Manufacturer')->where('UniCode',$v->UniCode)->orderBy('id','DESC')->first()->toArray();
            $detail[$k]->Name =   $goods['Name'];
            $detail[$k]->Spec =   $goods['Spec'];
            $detail[$k]->Manufacturer =   $goods['Manufacturer'];
        }

        return view('admin/order/pdf',['detail'=>$detail,'order'=>$order]);
    }


     public function num_to_rmb($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num)-1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        }else{
            return $c . "整";
        }
    }




    public function orderExcel()
    {
        if ($this->request->isMethod('post')){
            $id  = Request()->input('id');
            $order = DicOrder::where('status',1)->where('id',$id)->get()->toArray();
            //获取订单明细数据$orderDetail
            $orderDetail = array_column($order,'PurchaseDetail');
            foreach ($orderDetail as $k=>$id_str){
                $orderDetail[$k] = explode(',',$id_str);
            }

            foreach ($orderDetail as $k=>$id_arrs){
                foreach ($id_arrs as $k1=>$id){
                    $orderDetail[$k][$k1] = DicOrderDetail::where('id',$id)->first()->toArray();
                }
            }

            //根据订单明细数据中的UniCode统一编码获取商品明细（excel导入数据)
            foreach ($orderDetail as $k=>$v){
                foreach ($v as $k1=>$v1){
                    $orderDetail[$k][$k1]['PurchaseDetailID'] = $v1['Id'];
                    $orderDetail[$k][$k1]['PurchaseDetailSeq'] = $v1['Seq'];
                    $orderDetail[$k][$k1]['PurchaseQuantity'] = $v1['Quantity'];
                    $aAndM = DB::table('dic_consumable')->select('ApprovalNumber','ManagementCategory')->where('UniCode',$v1['UniCode'])->orderBy('id','DESC')->first();
//                    $orderDetail[$k][$k1]['ApprovalNumber'] = isset($aAndM)?$aAndM->ApprovalNumber:'';
                    $orderDetail[$k][$k1]['ManagementCategory'] = isset($aAndM)?$aAndM->ManagementCategory:'';
                    unset($orderDetail[$k][$k1]['PurchaseCategory']);
                    unset($orderDetail[$k][$k1]['Id']);
                    unset($orderDetail[$k][$k1]['Seq']);
                    $goodsDetail[$k][$k1] = DicConsumable::select('Name','Spec','Manufacturer')->where('UniCode',$v1['UniCode'])->orderBy('id','DESC')->first()->toArray();

//                    $goodsDetail[$k][$k1] = DB::table('dic_consumable')->select('Name','Spec','Manufacturer')->where('UniCode',$v1['UniCode'])->orderBy('id','DESC')->first();
                }
            }



            //根据订单明细和商品明细组合成配送单明细
            foreach ($orderDetail as $k=>$order_v){
                foreach ($goodsDetail as $k1=>$good_v){
                    if ($k==$k1){
                        foreach ($order_v as $k2=>$v2){
                            foreach ($good_v as $k3=>$v3){
                                if($k2==$k3){
                                    unset($v3['Id']);
                                    $purchaseDetail[$k][$k2] = array_merge($v2,$v3);
                                }
                            }
                        }
                    }
                }
            }


            foreach ($purchaseDetail as $k=>$detail){
                foreach ($detail as $k1=>$v){
                    if ($v['ManagementCategory']==4) {
                        $purchaseDetail[$k][$k1]['BatchNO'] = '';
                        $purchaseDetail[$k][$k1]['ProductionDate'] = '';
                        $purchaseDetail[$k][$k1]['InvalidDate'] = '';
                    }else{
                        $purchaseDetail[$k][$k1]['BatchNO'] = '1';
                        $purchaseDetail[$k][$k1]['ProductionDate'] = '2019-01-01';
                        $purchaseDetail[$k][$k1]['InvalidDate'] = '2099-01-01';
                    }
                }
            }
            $token = time().uniqid();

            foreach ($order as $k=>$v){
                $insert[$k]['PurchaseOrderNo'] = $v['OrderNo'];
                $insert[$k]['HospitalCode'] = $v['HospitalCode'];
                $insert[$k]['SupplierCode'] = $v['SupplierCode'];
                $insert[$k]['ID3'] = $v['ID3'];
                $insert[$k]['Unit'] = '配送单位';
                $insert[$k]['OrderNO'] = time().uniqid();
                $insert[$k]['SaleType'] = 0;
                $insert[$k]['Barcode'] = '单据条码';
                $insert[$k]['Saler'] = '销售人';
                $insert[$k]['DeliveryDate'] = date('Y-m-d H:i:s',time());
                $orderDetailArr = explode(',',$v['PurchaseDetail']);
                $insert[$k]['Count'] = count($orderDetailArr);
                $insert[$k]['Direct'] = '1';
                $insert[$k]['CreatedAt'] = date('Y-m-d H:i:s',time());
                $insert[$k]['OnlyToken'] = $token;
                $insert[$k]['OrderId'] = $v['Id'];
                DB::table('dic_delivery')->insert($insert[$k]);
                unset($insert[$k]['CreatedAt']);
                unset($insert[$k]['OnlyToken']);
                unset($insert[$k]['OrderId']);
                $data[$k] = $insert[$k];
                $data[$k]['OrderID'] =  DB::getPdo()->lastInsertId();
                $data[$k]['DistributionSiteCode'] = $v['DistributionSiteCode'];
                $data[$k]['DistributionSite'] = $v['DistributionSite'];
                $data[$k]['Amount'] = $v['Amount'];
                $data[$k]['Creator'] = $v['Creator'];
                $data[$k]['CreateTime'] = $v['CreateTime'];
                $data[$k]['Memo'] = $v['Memo'];
                $data[$k]['SumQuantity'] = $v['SumQuantity'];
                $data[$k]['StoreCode'] = $v['DistributionSiteCode'];
                $data[$k]['StoreName'] = $v['DistributionSite'];
            }

            foreach ($data as $k=>$v){
                foreach ($purchaseDetail as $k1=>$detail_v){
                    if($k==$k1){
                        $data[$k]['DistributionDetail'] = $detail_v; //
                    }
                }
            }
//return $param;
//            $url = "www.songjiang.cn:8000/admin/get_back";


//            $url = "http://222.72.92.35:8091/dep/business/post";
//            $jsonStr = json_encode($param);
//           echo $jsonStr;exit();
//            $httpResult = $this->http_post_json($url, $jsonStr);
//            $code = json_decode($httpResult['data'],true)['Code'];
            $code = 200;
            if ($code==200){
//                DB::table('dic_order')->whereIn('id',$id_arr)->update(['status'=>2]);
                return $this->export($data);
            }else{
                return ['code'=>1,'url'=>''];
            }

        }else{
            $data = DB::table('dic_order')->where('status','1')->orderBy('id', 'DESC')->paginate(15);
            return view('admin/distribution/index',['data'=>$data]);
        }
    }


    public function export($data){
//        $data = Request()->input('data');
//        $data = '[{"PurchaseOrderNo":"DD201903300006","HospitalCode":"\u533b\u9662\u4ee3\u7801","SupplierCode":"1791","ID3":"7639","Unit":"\u914d\u9001\u5355\u4f4d","OrderNO":"15546264335ca9b781a54f3","SaleType":0,"Barcode":"\u5355\u636e\u6761\u7801","Saler":"\u9500\u552e\u4eba","DeliveryDate":"2019-04-07 16:40:33","Count":2,"Direct":"1","OrderID":"7","DistributionSiteCode":"1001","DistributionSite":"\u91c7\u8d2d\u79d1\u5ba4","Amount":"21.8500","Creator":"\u7ba1\u7406\u5458","CreateTime":"2019-03-30 18:27:30","Memo":"\u81ea\u52a8\u751f\u6210","SumQuantity":"5.0000","StoreCode":"1001","StoreName":"\u91c7\u8d2d\u79d1\u5ba4","DistributionDetail":[{"ID3":"39051","Manufacturer":"\u79a7\u5929\u9f99","Name":"\u65b9\u5170","OverAmount":null,"OverQuantity":null,"Spec":"32*23*8","State":null,"UniCode":"18118","Unit":"\u53ea","Quantity":"2.0000","Price":"9.5000","Amount":"19.0000","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":1,"PurchaseDetailSeq":"39051","PurchaseQuantity":"2.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552680\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"},{"ID3":"39052","Manufacturer":"\u5f97\u529b","Name":"\u6a61\u76ae\u7b4b","OverAmount":null,"OverQuantity":null,"Spec":"\/","State":null,"UniCode":"18138","Unit":"\u53ea","Quantity":"3.0000","Price":"0.9500","Amount":"2.8500","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":2,"PurchaseDetailSeq":"39052","PurchaseQuantity":"3.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552700\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"}]},{"PurchaseOrderNo":"DD201903300005","HospitalCode":"\u533b\u9662\u4ee3\u7801","SupplierCode":"1791","ID3":"7638","Unit":"\u914d\u9001\u5355\u4f4d","OrderNO":"15546264335ca9b781a5bd4","SaleType":0,"Barcode":"\u5355\u636e\u6761\u7801","Saler":"\u9500\u552e\u4eba","DeliveryDate":"2019-04-07 16:40:33","Count":1,"Direct":"1","OrderID":"8","DistributionSiteCode":"1001","DistributionSite":"\u91c7\u8d2d\u79d1\u5ba4","Amount":"22.3250","Creator":"\u7ba1\u7406\u5458","CreateTime":"2019-03-30 16:20:25","Memo":"\u81ea\u52a8\u751f\u6210","SumQuantity":"1.0000","StoreCode":"1001","StoreName":"\u91c7\u8d2d\u79d1\u5ba4","DistributionDetail":[{"ID3":"39050","Manufacturer":"\u591a\u6797","Name":"A4\u590d\u5370\u7eb8","OverAmount":null,"OverQuantity":null,"Spec":"70g","State":null,"UniCode":"18143","Unit":"\u5305","Quantity":"1.0000","Price":"22.3250","Amount":"22.3250","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":3,"PurchaseDetailSeq":"39050","PurchaseQuantity":"1.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552705\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"}]},{"PurchaseOrderNo":"DD201903300003","HospitalCode":"\u533b\u9662\u4ee3\u7801","SupplierCode":"1791","ID3":"7636","Unit":"\u914d\u9001\u5355\u4f4d","OrderNO":"15546264335ca9b781a5ed2","SaleType":0,"Barcode":"\u5355\u636e\u6761\u7801","Saler":"\u9500\u552e\u4eba","DeliveryDate":"2019-04-07 16:40:33","Count":2,"Direct":"1","OrderID":"9","DistributionSiteCode":"1001","DistributionSite":"\u91c7\u8d2d\u79d1\u5ba4","Amount":"12.5400","Creator":"\u7ba1\u7406\u5458","CreateTime":"2019-03-30 12:24:10","Memo":"\u81ea\u52a8\u751f\u6210","SumQuantity":"8.0000","StoreCode":"1001","StoreName":"\u91c7\u8d2d\u79d1\u5ba4","DistributionDetail":[{"ID3":"39046","Manufacturer":"\u771f\u771f","Name":"\u771f\u771f\u5377\u7b52\u7eb8","OverAmount":null,"OverQuantity":null,"Spec":"10\u7b52\/\u5305 100\u7b52\/\u7bb1","State":null,"UniCode":"18088","Unit":"\u7b52","Quantity":"5.0000","Price":"1.4250","Amount":"7.1250","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":4,"PurchaseDetailSeq":"39046","PurchaseQuantity":"5.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552650\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"},{"ID3":"39047","Manufacturer":"\u548c\u6cc9","Name":"2.4cm\u53cc\u9762\u80f6","OverAmount":null,"OverQuantity":null,"Spec":"AAA","State":null,"UniCode":"18090","Unit":"\u53ea","Quantity":"3.0000","Price":"1.8050","Amount":"5.4150","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":5,"PurchaseDetailSeq":"39047","PurchaseQuantity":"3.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552652\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"}]},{"PurchaseOrderNo":"DD201903300002","HospitalCode":"\u533b\u9662\u4ee3\u7801","SupplierCode":"1791","ID3":"7635","Unit":"\u914d\u9001\u5355\u4f4d","OrderNO":"15546264335ca9b781a62fe","SaleType":0,"Barcode":"\u5355\u636e\u6761\u7801","Saler":"\u9500\u552e\u4eba","DeliveryDate":"2019-04-07 16:40:33","Count":2,"Direct":"1","OrderID":"10","DistributionSiteCode":"1001","DistributionSite":"\u91c7\u8d2d\u79d1\u5ba4","Amount":"1380.8250","Creator":"\u7ba1\u7406\u5458","CreateTime":"2019-03-30 11:16:53","Memo":"\u81ea\u52a8\u751f\u6210","SumQuantity":"45.0000","StoreCode":"1001","StoreName":"\u91c7\u8d2d\u79d1\u5ba4","DistributionDetail":[{"ID3":"39044","Manufacturer":"\u6668\u5149","Name":"\u6c34\u7b14\u82af","OverAmount":null,"OverQuantity":null,"Spec":"\u9ed1 20\u652f\/\u76d2","State":null,"UniCode":"18083","Unit":"\u652f","Quantity":"15.0000","Price":"0.8550","Amount":"12.8250","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":6,"PurchaseDetailSeq":"39044","PurchaseQuantity":"15.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552645\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"},{"ID3":"39045","Manufacturer":"\u5f85\u5b9a","Name":"\u811a\u8e0f\u5783\u573e\u6876","OverAmount":null,"OverQuantity":null,"Spec":"18L \u7070270*240*350","State":null,"UniCode":"18086","Unit":"\u53ea","Quantity":"30.0000","Price":"45.6000","Amount":"1368.0000","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":7,"PurchaseDetailSeq":"39045","PurchaseQuantity":"30.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552648\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"}]},{"PurchaseOrderNo":"DD201903300007","HospitalCode":"\u533b\u9662\u4ee3\u7801","SupplierCode":"1791","ID3":"7640","Unit":"\u914d\u9001\u5355\u4f4d","OrderNO":"15546264335ca9b781a6737","SaleType":0,"Barcode":"\u5355\u636e\u6761\u7801","Saler":"\u9500\u552e\u4eba","DeliveryDate":"2019-04-07 16:40:33","Count":2,"Direct":"1","OrderID":"11","DistributionSiteCode":"1001","DistributionSite":"\u91c7\u8d2d\u79d1\u5ba4","Amount":"104.5500","Creator":"\u7ba1\u7406\u5458","CreateTime":"2019-03-30 19:19:17","Memo":"\u81ea\u52a8\u751f\u6210","SumQuantity":"11.0000","StoreCode":"1001","StoreName":"\u91c7\u8d2d\u79d1\u5ba4","DistributionDetail":[{"ID3":"39053","Manufacturer":"\u6668\u5149","Name":"\u6c34\u7b14","OverAmount":null,"OverQuantity":null,"Spec":"\u7ea2\u8272 12\u652f\/\u76d2","State":null,"UniCode":"18104","Unit":"\u652f","Quantity":"4.0000","Price":"1.2000","Amount":"4.8000","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":8,"PurchaseDetailSeq":"39053","PurchaseQuantity":"4.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552666\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"},{"ID3":"39054","Manufacturer":"\u503e\u57ce\u9e7f","Name":"\u5851\u6599\u62d6\u978b","OverAmount":null,"OverQuantity":null,"Spec":"AAA","State":null,"UniCode":"18111","Unit":"\u53cc","Quantity":"7.0000","Price":"14.2500","Amount":"99.7500","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":9,"PurchaseDetailSeq":"39054","PurchaseQuantity":"7.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552673\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"}]},{"PurchaseOrderNo":"DD201903300008","HospitalCode":"\u533b\u9662\u4ee3\u7801","SupplierCode":"1791","ID3":"7641","Unit":"\u914d\u9001\u5355\u4f4d","OrderNO":"15546264335ca9b781a6afb","SaleType":0,"Barcode":"\u5355\u636e\u6761\u7801","Saler":"\u9500\u552e\u4eba","DeliveryDate":"2019-04-07 16:40:33","Count":1,"Direct":"1","OrderID":"12","DistributionSiteCode":"1001","DistributionSite":"\u91c7\u8d2d\u79d1\u5ba4","Amount":"133.9500","Creator":"\u7ba1\u7406\u5458","CreateTime":"2019-03-30 19:44:38","Memo":"\u81ea\u52a8\u751f\u6210","SumQuantity":"6.0000","StoreCode":"1001","StoreName":"\u91c7\u8d2d\u79d1\u5ba4","DistributionDetail":[{"ID3":"39055","Manufacturer":"\u591a\u6797","Name":"A4\u590d\u5370\u7eb8","OverAmount":null,"OverQuantity":null,"Spec":"70g","State":null,"UniCode":"18143","Unit":"\u5305","Quantity":"6.0000","Price":"22.3250","Amount":"133.9500","DeptCode":"F1101","DeptName":"\u4fe1\u606f\u4e2d\u5fc3","StoreCode":"F1101","StoreName":"\u4fe1\u606f\u4e2d\u5fc3\u5e93\u623f","Memo":"\u81ea\u52a8\u751f\u6210","PurchaseDetailID":10,"PurchaseDetailSeq":"39055","PurchaseQuantity":"6.0000","ApprovalNumber":"\u56fd\u98df\u836f\u76d1\u68b0\uff08\u8fdb\uff09\u5b572011\u7b2c2552705\u53f7","ManagementCategory":"","BatchNO":"1","ProductionDate":"2019-01-01","InvalidDate":"2099-01-01"}]}]';
//        $data = json_decode($data,true);
//        dd($data);
        //利用excel导出插件PHPExcel
        // 引入phpexcel核心类文件
        require_once(base_path() . '/vendor/phpexcel/PHPExcel.php');
        // 实例化excel类
        $objPHPExcel = new \PHPExcel();
        // 操作第一个工作表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置sheet名
        $objPHPExcel->getActiveSheet()->setTitle('配送单sheet');
        // 设置表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);



        // 列名表头文字加粗
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        // 列表头文字居中
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // 列名赋值
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '上海雨桓实业有限公司');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', '送货单');
        $objPHPExcel->getActiveSheet()->setCellValue('A3', '客户名称：松江区中心医院');
        $objPHPExcel->getActiveSheet()->setCellValue('J3', '送货日期');

        $objPHPExcel->getActiveSheet()->mergeCells('A1'.':'.'J1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2'.':'.'J2');

        $objPHPExcel->getActiveSheet()->getStyle('A1' . ':' . 'J1')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2' . ':' . 'J2')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->setCellValue('A5', '物资名称');
        $objPHPExcel->getActiveSheet()->setCellValue('B5', '规格');
        $objPHPExcel->getActiveSheet()->setCellValue('C5', '单位');
        $objPHPExcel->getActiveSheet()->setCellValue('D5', '单价');
        $objPHPExcel->getActiveSheet()->setCellValue('E5', '数量');
        $objPHPExcel->getActiveSheet()->setCellValue('F5', '金额');
        $objPHPExcel->getActiveSheet()->setCellValue('G5', '科室名称');
        $objPHPExcel->getActiveSheet()->setCellValue('H5', '备注');
        $objPHPExcel->getActiveSheet()->setCellValue('I5', '');



        // 数据起始行
        $row_num = 6;
        $res =$data;
//        dd($res);
        //向每行单元格插入数据
        foreach($res as $value)
        {
            // 设置所有垂直居中
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_num . ':' . 'J' . $row_num)->getAlignment()
                ->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            // 设置价格为数字格式
//            $objPHPExcel->getActiveSheet()->getStyle('D' . $row_num)->getNumberFormat()
//                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            // 居中
            $objPHPExcel->getActiveSheet()->getStyle('E' . $row_num . ':' . 'H' . $row_num)->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // 设置单元格数值
//            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, $value['Id']);


            $count = count($value['DistributionDetail']);

            foreach ($value['DistributionDetail'] as $v){
                $row_num++;

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, $v['Name']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, $v['Spec']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, $v['Unit']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $v['Price']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, $v['Quantity']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $v['Amount']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $v['DeptName']);
            }
            $row_num = $row_num + abs($count - 3) ;
            $row_num++;


            $one = $row_num+1;
            $two = $row_num+2;
            $three = $row_num+3;
            $last = $row_num+5;


            $objPHPExcel->getActiveSheet()->setCellValue('J' . $one, '订单编号：'.$value['PurchaseOrderNo']);
//            $objPHPExcel->getActiveSheet()->setCellValue('A' . $two, '订单编码：'.$value['OrderNO']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $two, '制单人:'.$value['Creator']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $three, '备注：'.$value['Memo']);


            $objPHPExcel->getActiveSheet()->setCellValue('A' . $last, '送货单位及经手人：');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $last, '收货单位及经手人：');



//            $objPHPExcel->getActiveSheet()->mergeCells('A'.$row_num.':'.'G'.$row_num);
//            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, '推送单编号：'.$value['PurchaseOrderNo']
//            .'订单编号：'.$value['OrderNO']
//            .'订单编号：'.$value['Barcode']
//            .'订单编号：'.$value['Amount']
//            .'订单编号：'.$value['Creator']
//            .'订单编号：'.$value['Memo']
//            );

        }

        $outputFileName = '配送单_' . date('YmdHis') . '.xls';
        ob_end_clean();//清除缓冲区,避免乱码
        $xlsWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $outputFileName . '"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
//        $xlsWriter->save("php://output");
//        echo file_get_contents($outputFileName);




//返回已经存好的文件目录地址提供下载
        $response = [   'code' => 0,
            'url'  => $this->saveExcelToLocalFile($xlsWriter,$outputFileName)];
        return $response;
    }

    public function saveExcelToLocalFile($objWriter,$filename){
        // make sure you have permission to write to directory
        $filePath = public_path().'/tmp/'.$filename;
        $objWriter->save($filePath);
        return '/tmp/'.$filename;
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