<table  id="example1" class="table table-bordered table-striped">
    <thead>
    <th>退货单编号</th>
    <th>制单人</th>
    <th>供应商编码</th>
    <th>配送点编码</th>
    <th>制单时间</th>
    <th>关闭时间</th>
    <th>备注</th>
    </thead>
    <tbody>
    @if(!$data->isEmpty())
        @foreach($data as $key=>$val)
            <tr data-id="{{$val->Id}}">
                {{--<td><input type="checkbox" data-id="{{$val->id}}"></td>--}}
                <td>{{$val->RefundNO}}</td>
                <td>{{$val->Creator}}</td>
                <td>{{$val->SupplierCode}}</td>
                <td>{{$val->DistributionSiteCode}}</td>
                <td>{{$val->CreateTime}}</td>
                <td>{{$val->CloseDate}}</td>
                <td>{{$val->Memo}}</td>
            </tr>
        @endforeach
    @else
        <tr><td colspan="9" style="text-align: center">暂无记录</td></tr>
    @endif
    </tbody>
</table>
{{ $data->links() }}