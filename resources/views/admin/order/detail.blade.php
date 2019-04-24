@extends('/admin/layouts/main')
@section('title','订单详情')
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
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 col-md-9">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: left">
                                            <label>
                                                订单编号:{{$order->OrderNo}}
                                            </label>
                                            &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                            <label>
                                                制单人:{{$order->Creator}}
                                            </label>
                                            &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                            <label>
                                                获取订单时间:{{$order->CreatedAt}}
                                            </label>
                                            &nbsp &nbsp &nbsp &nbsp &nbsp
                                            <button class="excel" data-id="{{$order->Id}}">生成excel</button>
                                        </div>
                                    </div>
                                </div>



                               <div id="table">
                                   <table  id="example1" class="table table-bordered table-striped">
                                       <thead>
                                       <th>物资名称</th>
                                       {{--<th>品牌</th>--}}
                                       <th>规格</th>
                                       <th>单位</th>
                                       <th>数量</th>
                                       <th>单价</th>
                                       <th>总金额</th>
                                       <th>科室名称</th>
                                       <th>备注</th>
                                       </thead>
                                       <tbody>
                                           @foreach($detail as $key=>$val)
                                               <tr order-id="{{$val->Id}}">
                                                   <td>{{$val->Name}}</td>
{{--                                                   <td>{{$val->Manufacturer}}</td>--}}
                                                   <td>{{$val->Spec}}</td>
                                                   <td>{{$val->Unit}}</td>
                                                   <td>{{$val->Quantity}}</td>
                                                   <td>{{$val->Price}}</td>
                                                   <td>{{$val->Amount}}</td>
                                                   <td>{{$val->DeptName}}</td>
                                                   <td>{{$val->Memo}}</td>
                                               </tr>
                                           @endforeach
                                       </tbody>
                                   </table>
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


