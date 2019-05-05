<table  id="example1" class="table table-bordered table-striped">
    <thead>
    <th>订单编号</th>
    <th>总数量</th>
    <th>总金额</th>
    <th>配送单名称</th>
    <th>制单时间</th>
    <th width="80">制单人</th>
    <th>获取订单时间</th>
    <th>操作</th>
    </thead>
    <tbody>
    @if(!$data->isEmpty())
        @foreach($data as $key=>$val)
            <tr order-id="{{$val->Id}}">
                <td>{{$val->OrderNo}}</td>
                <td>{{$val->SumQuantity}}</td>
                <td>{{$val->Amount}}</td>
                <td>{{$val->DistributionSite}}</td>
                <td>{{$val->CreateTime}}</td>
                <td>{{$val->Creator}}</td>
                <td>{{$val->CreatedAt}}</td>
                <td><button><a href="{{asset('admin/order_detail?id='.$val->Id)}}">详情</a></button>&nbsp
                    <button class="excel" data-id="{{$val->Id}}">生成excel</button>
                </td>
            </tr>
        @endforeach
    @else
        <tr><td colspan="9" style="text-align: center">暂无记录</td></tr>
    @endif
    </tbody>
</table>
{{ $data->links() }}