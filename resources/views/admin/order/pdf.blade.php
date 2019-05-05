<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        * {
            padding: 0;
            margin: 0;
        }

        .align-center {
            text-align: center !important
        }

        .align-left {
            text-align: left;
        }

        .align-right {
            text-align: right !important
        }

        .order-content {
            padding: 0 16px;
        }

        table.order-info {
            width: 100%;
            font-size: 14px;
            color: #333333;
            border-color: #000000;
            border-collapse: collapse;
        }

        table.order-info tr {
            text-align: left;
        }

        table.order-info td {
            width: 33.333%
        }

        table.order-body {
            width: 100%;
            font-size: 14px;
            color: #333333;
            border-width: 1px;
            border-color: #000000;
            border-collapse: collapse;
        }

        table.order-body th,
        table.order-body td {
            height: 37px;
            border-width: 1px;
            padding:0 8px;
            text-align: center;
            border-style: solid;
            border-color: #000000;
        }

        #table-foot td {
            font-size: 16px;
            text-align: left;
            font-weight: 700;
        }

        .order-footer {
            display: flex;
            flex-direction: column;
        }

        .footer-item-wrapper,
        .footer-desc {
            display: flex;
            justify-content: space-between;
        }
    </style>

</head>

<body>
<div class="order-content">
    <h1 class="align-center">上海松卫医工贸有限公司商品销售清单</h1>
    <table class="order-info">
        <tr>
            <td>客户: 上海松江区中心医院</td>
            <td>料事</td>
            <td></td>
        </tr>
        <tr>
            <td>单据号</td>
            <td>开票日期</td>
            <td class="align-right">工sssss</td>
        </tr>
    </table>

    <table class='order-body'>
        <thead>
        <tr>
            <th>序号</th>
            <th>名称/规格/生产厂商/</th>
            <th>注册账号</th>
            <th>批号</th>
            <th>效期</th>
            <th>单位</th>
            <th>数量</th>
            <th>单价</th>
            <th>金额</th>
            <th>要货数量</th>
            <th>备注</th>
            <th>质量状况</th>
        </tr>
        </thead>
        <tbody>
        @foreach($detail as $key=>$val)
            <tr order-id="{{$val->Id}}">
                <td>{{$key+1}}</td>
                <td>{{$val->Name}}</td>
                <td>{{$val->Spec}}</td>
                <td>{{$val->Unit}}</td>
                <td>{{$val->Quantity}}</td>
                <td>{{$val->Price}}</td>
                <td>{{$val->Amount}}</td>
                <td>{{$val->DeptName}}</td>
                <td>{{$val->Memo}}</td>
                <td>{{$val->Memo}}</td>
            </tr>
        @endforeach
        <tr>
            <td>1</td>
            <td>透明胶带</td>
            <td></td>
            <td></td>
            <td></td>
            <td>1</td>
            <td>5.00</td>
            <td>3500</td>
            <td>17.5</td>
            <td>5</th>
            <td></td>
            <td>合格</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
        <tfoot id="table-foot">
        <tr>
            <td colspan="2">本页金额小记</td>
            <td colspan="5"></td>
            <td colspan="2" class="align-center">17.00</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">价税合计(大写) 一千一百一十一</td>
            <td colspan="10" class="align-right">价税合计(小写)19.9</td>
        </tr>
        </tfoot>
    </table>

    <footer class="order-footer">
        <div class="footer-remarks">备注</div>
        <div class="footer-item-wrapper">
            <div class="footer-item">制作人</div>
            <div class="footer-item">发货员</div>
            <div class="footer-item">复核员</div>
            <div class="footer-item" style="width:400px">用户签名</div>
        </div>
        <div class="footer-desc">
            <div class="desc-item">一去二三里</div>
            <div class="desc-item" style="width:400px">2019-1-1</div>
        </div>
    </footer>
</div>

</body>

</html>