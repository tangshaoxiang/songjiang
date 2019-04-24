@extends('/admin/layouts/main')
@section('title','同步订单')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('title')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">@yield('title')</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"></div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div id="example1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: left">
                                            <label>
                                                供应商编码:
                                                <input id="SupplierCode" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="1791" readonly>
                                                开始时间:
                                                <input id="Kssj" type="date" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="">
                                                结束时间:
                                                <input id="Jssj" type="date" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="">
                                                下载状态:
                                                <input id="DownloadState" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="1">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 col-md-9">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: left">
                                            <label>
                                               查询记录数:
                                                <input id="Count" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="100">
                                            </label>
                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            <label><button id="search_button" >同步订单</button>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                               <div id="table">
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
                                                   <td><button><a href="{{asset('admin/order_detail?id='.$val->Id)}}">详情</a></button>&nbsp            <button class="excel" data-id="{{$val->Id}}">生成excel</button></td>
                                               </tr>
                                           @endforeach
                                       @else
                                           <tr><td colspan="9" style="text-align: center">暂无记录</td></tr>
                                       @endif
                                       </tbody>
                                   </table>
                                   {{ $data->links() }}
                               </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>

    <script>
        $("#search_button").click(function () {
            var SupplierCode = $('#SupplierCode').val();
            var Kssj = $('#Kssj').val();
            var Jssj = $('#Jssj').val();
            var DownloadState = $('#DownloadState').val();
            var Count = $('#Count').val();
            var url = "{{url('admin/order')}}";
            $.ajax({
                url: url,
                type: 'POST',
                data: {'SupplierCode': SupplierCode, 'Kssj': Kssj, 'Jssj': Jssj, 'DownloadState': DownloadState, 'Count': Count},
                success: function (msg) {
                    
                    if (msg.code=='206'){
                        alert(msg.error);
                    }else{
                        $('#table').html(msg);
                        alert('获取订单成功')
                    }

                }

            });
        });



        $('.excel').on('click', function () {
                   var id =  $(this).attr('data-id')
                    var url = "{{url('admin/order_excel')}}";
                    $.post(url, {'id':id}, function (data) {
                        var ev = eval('(' + data + ')');
                        if (ev.code==0) {
                                document.location.href ='{{url("")}}'+ev.url;
                        } else {
                            alert('生成excel失败');
                        }
                    }, 'text')
        })



        {{--$('#excel_button').on('click', function () {--}}
            {{--if ($('tbody input:checked').length == 0) {--}}
                {{--alert('请至少选中一个数据')--}}
            {{--} else {--}}
                {{--if (confirm('确定推送吗')) {--}}
                    {{--var id = '';--}}
                    {{--$('tbody input:checked').each(function (k, v) {--}}
                        {{--id = id + $(v).attr('data-id') + ','--}}
                    {{--})--}}

                    {{--var url = "{{url('admin/order_excel')}}";--}}
                    {{--var SupplierCode = $('#SupplierCode').val();--}}
                    {{--var Kssj = $('#Kssj').val();--}}
                    {{--var Jssj = $('#Jssj').val();--}}
                    {{--var DownloadState = $('#DownloadState').val();--}}
                    {{--var Count = $('#Count').val();--}}

                    {{--$.post(url, {'SupplierCode': SupplierCode, 'Kssj': Kssj, 'Jssj': Jssj, 'DownloadState': DownloadState, 'Count': Count,'id':id}, function (data) {--}}
                        {{--var ev = eval('(' + data + ')');--}}
                        {{--if (ev.code==0) {--}}
                            {{--$('tbody input:checked').each(function (k, v) {--}}
                                {{--document.location.href ='{{url("")}}'+ev.url;--}}
                                {{--var id = $(v).attr('data-id');--}}
                                {{--$('tr[order-id=' + id + ']').remove();--}}
                            {{--})--}}
                            {{--alert('推送成功');--}}
                        {{--} else {--}}
                            {{--alert('推送失败');--}}
                        {{--}--}}
                    {{--}, 'text')--}}
                {{--}--}}
            {{--}--}}
        {{--})--}}


    </script>

@stop


