<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-03-21
 * Time: 01:26
 */
namespace App\Http\Controllers\Admin;

use App\DicConsumable;
use App\DicOrder;
use App\DicOrderDetail;
use App\GetMac;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller{

    /**
     * 推送配送单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|int
     */
    public function index()
    {
        if ($this->request->isMethod('post')){
            $id  = Request()->input('id');
            $id = rtrim($id,',');
            $id_arr = explode(',',$id);
            $obj = new GetMac('linux');
            $param['BusinessType'] = "MY102";
            $param['HospitalCode'] = "Test001";
            $param['IP'] = $this->get_real_ip();
            $param['MAC'] = $obj->macAddr;
            $param['HostName'] = $_SERVER['SERVER_NAME'];
            $order = DicOrder::where('status',1)->get()->toArray();
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
                    $orderDetail[$k][$k1]['ApprovalNumber'] = isset($aAndM)?$aAndM->ApprovalNumber:'';
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
            $param['Data'] = $data;

//            $url = "www.songjiang.cn:8000/admin/get_back";
        $url = "http://222.72.92.35:8091/dep/business/post";
            $jsonStr = json_encode($param);
//           echo $jsonStr;exit();
            $httpResult = $this->http_post_json($url, $jsonStr);
            if ($httpResult['code']==200){
              DB::table('dic_order')->whereIn('id',$id_arr)->update(['status'=>2]);
              return 1;
            }else{
              return 2;
            }

            $data = DB::table('dic_order')->where('status','1')->orderBy('id', 'DESC')->paginate(15);
            return view('admin/distribution/table',['data'=>$data]);

        }else{
            $data = DB::table('dic_order')->where('status','1')->orderBy('id', 'DESC')->paginate(15);
            return view('admin/distribution/index',['data'=>$data]);
        }
    }


}