<table  id="example1" class="table table-bordered table-striped">
    <thead>
    <th>耗材编码</th>
    <th>耗材注册名</th>
    <th>商品名</th>
    {{--<th>规格</th>--}}
    <th>单位</th>
    <th>单价</th>
    <th>生产厂家名称</th>
    <th>注册证号、备案证号</th>
    <th>接收时间</th>
    </thead>
    <tbody>
    @if(!$data->isEmpty())
        @foreach($data as $key=>$val)
            <tr data-id="{{$val->Id}}">
                {{--<td><input type="checkbox" data-id="{{$val->id}}"></td>--}}
                <td>{{$val->UniCode}}</td>
                <td>{{$val->Name}}</td>
                <td>{{$val->TradeName}}</td>
                {{--<td>{{$val->Unit}}</td>--}}
                <td>{{$val->Unit}}</td>
                <td>{{$val->Price}}</td>
                <td>{{$val->Manufacturer}}</td>
                <td>{{$val->ApprovalNumber}}</td>
                <td>{{$val->CreatedAt}}</td>
            </tr>
        @endforeach
    @else
        <tr><td colspan="9" style="text-align: center">暂无记录</td></tr>
    @endif
    </tbody>
</table>
{{ $data->links() }}