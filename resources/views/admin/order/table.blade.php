<table  id="example1" class="table table-bordered table-striped">
    <thead>
    <th>订单编号</th>
    <th>订单类型</th>
    <th>供应商编码</th>
    {{--<th>规格</th>--}}
    <th>配送单编码</th>
    <th>配送单名称</th>
    <th>采购计划单号</th>
    <th>采购员</th>
    <th>总数量</th>
    </thead>
    <tbody>
    @if(!$data->isEmpty())
        @foreach($data as $key=>$val)
            <tr data-id="{{$val->Id}}">
                {{--<td><input type="checkbox" data-id="{{$val->id}}"></td>--}}
                <td>{{$val->OrderNo}}</td>
                <td>{{$val->OrderType}}</td>
                <td>{{$val->SupplierCode}}</td>
                {{--<td>{{$val->Unit}}</td>--}}
                <td>{{$val->DistributionSiteCode}}</td>
                <td>{{$val->DistributionSite}}</td>
                <td>{{$val->PPOrderNo}}</td>
                <td>{{$val->Employee}}</td>
                <td>{{$val->SumQuantity}}</td>
            </tr>
        @endforeach
    @else
        <tr><td colspan="9" style="text-align: center">暂无记录</td></tr>
    @endif
    </tbody>
</table>
{{ $data->links() }}