@extends('/admin/layouts/main')
@section('title','推送配送单')
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
                                    <div class="col-sm-12 col-md-3">

                                    </div>

                                    <div class="col-sm-12 col-md-9">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: right">
                                            <label><button id="search_button" >推送配送单</button>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                               <div id="table">
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

        //批量删除
        $('#search_button').on('click', function () {
            if ($('tbody input:checked').length == 0) {
                alert('请至少选中一个数据')
            } else {
                if (confirm('确定删除吗')) {
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
                        console.log(data);
                        if (data==1) {
                            $('tbody input:checked').each(function (k, v) {
                                var id = $(v).attr('data-id');
                                $('tr[order-id=' + id + ']').remove();
                            })
                            alert('删除成功');
                        } else {
                            alert('删除失败');
                        }
                    }, 'text')
                }
            }
        })

    </script>

@stop


