@extends('/admin/layouts/main')
@section('title','获取物资字典')
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
                                    <div class="col-sm-12 col-md-6">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: left">
                                            <label>
                                                供应商编码:
                                                <input id="ID3" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="2778" readonly>

                                                上次获取时间:
                                                <input id="time" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" readonly value="">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <div id="example1_filter" class="dataTables_filter">
                                            <label><button id="search_button" >获取物资字典</button>
                                            </label>
                                        </div>
                                    </div>

                                </div>
                               <div id="table">
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
        $(function () {
            var ID3 = $('#ID3').val();
            var url = "{{url('admin/consumableGetTime')}}";
            $.post(url, {'ID3': ID3}, function (data) {
                $('#time').val(data);
            });
        })

        // $("#ID3").blur(function () {
        //
        // });



        $("#search_button").click(function () {
            var ID3 = $('#ID3').val();
            var time = $('#time').val();
            var url = "{{url('admin/consumable')}}";
            $.ajax({
                url:url,
                type:'POST',
                data:{'ID3': ID3, 'time': time},
                success:function(msg){
                    $('#table').html(msg);
                    alert('获取物资字典成功')
                }

            });
        });

    </script>

@stop


