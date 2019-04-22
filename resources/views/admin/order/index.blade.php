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

    </script>

@stop


