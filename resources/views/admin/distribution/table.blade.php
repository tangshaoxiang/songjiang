<table  id="example1" class="table table-bordered table-striped">
    <thead>
    <th><input type="checkbox" id="all"></th>
    <th>订单编号</th>
    <th>订单类型</th>
    <th>供应商编码</th>
    <th>配送单编码</th>
    <th>配送单名称</th>
    <th>采购计划单号</th>
    <th>采购员</th>
    <th>总数量</th>
    <th>时间</th>
    </thead>
    <tbody>
    @if(!$data->isEmpty())
        @foreach($data as $key=>$val)
            <tr order-id="{{$val->Id}}">
                <td><input type="checkbox" data-id="{{$val->Id}}"></td>
                <td>{{$val->OrderNo}}</td>
                <td>{{$val->OrderType}}</td>
                <td>{{$val->SupplierCode}}</td>
                <td>{{$val->DistributionSiteCode}}</td>
                <td>{{$val->DistributionSite}}</td>
                <td>{{$val->PPOrderNo}}</td>
                <td>{{$val->Employee}}</td>
                <td>{{$val->SumQuantity}}</td>
                <td>{{$val->CreatedAt}}</td>
            </tr>
        @endforeach
    @else
        <tr><td colspan="9" style="text-align: center">暂无记录</td></tr>
    @endif
    </tbody>
</table>
<script>
    //批量删除
    $('#search_button').on('click', function () {
        if ($('tbody input:checked').length == 0) {
            alert('请至少选中一个数据')
        } else {
            if (confirm('确定推送吗')) {
                var id = '';
                $('tbody input:checked').each(function (k, v) {
                    id = id + $(v).attr('data-id') + ','
                })

                var url = "{{url('admin/distribution')}}";
                var SupplierCode = $('#SupplierCode').val();
                var Kssj = $('#Kssj').val();
                var Jssj = $('#Jssj').val();
                var DownloadState = $('#DownloadState').val();
                var Count = $('#Count').val();

                $.post(url, {'SupplierCode': SupplierCode, 'Kssj': Kssj, 'Jssj': Jssj, 'DownloadState': DownloadState, 'Count': Count,'id':id}, function (data) {
                    if (data==0){
                        alert('推送失败')
                    } else{
                        window.location.reload();
                        // $('#table').html(data);
                        alert('推送成功')
                    }
                    {{--var ev = eval('(' + data + ')');--}}
                    {{--if (ev.code==0) {--}}
                    {{--$('tbody input:checked').each(function (k, v) {--}}
                    {{--document.location.href ='{{url("")}}'+ev.url;--}}
                    {{--var id = $(v).attr('data-id');--}}
                    {{--// $('tr[order-id=' + id + ']').remove();--}}
                    {{--})--}}
                    {{--alert('推送成功');--}}
                    {{--} else {--}}
                    {{--alert('推送失败');--}}
                    {{--}--}}
                }, 'text')
            }
        }
    })

    $("#all").click(function () {
        if (this.checked) {
            $("tbody input:checkbox").prop("checked", true);
        } else {
            $("tbody input:checkbox").prop("checked", false);
        }
    });
</script>
{{ $data->links() }}