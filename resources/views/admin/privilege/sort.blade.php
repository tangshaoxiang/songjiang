@extends('/admin/layouts/main')
@section('title','权限排序')
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
                            <li class="breadcrumb-item"><a href="{{asset('admin/priadd')}}">权限添加</a></li>
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
                            <div class="card-title">排序</div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div id="example1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                </div>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr  pids="0">
                                        <th><input type="checkbox" id="all"></th>
                                        <th>权限名称</th>
                                        <th>权限排序</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $v)
                                        <tr>
                                            <th><input type="checkbox"></th>
                                            <td>{{$v->p_name}}</td>
                                            <td><input type="text" class="sort" value="{{$v->order}}"  p_id="{{$v->p_id}}"></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <button id="tj"  class="btn btn-primary float-right">提交</button>
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
        {{--function showTr(obj) {--}}
            {{--var sort = obj.value;--}}
            {{--var id = obj.getAttribute('p_id');--}}
            {{--var url = "{{url('admin/prisort')}}";--}}
            {{--$.post(url,{'sort':sort,'id':id},function (msg) {--}}
                {{--console.log(msg)--}}
                {{--location.reload();--}}
            {{--})--}}
        {{--}--}}
        
        $('#tj').click(function () {
            var map = {};
            $('.sort').each(function (k,v) {
                map[$(this).attr('p_id')]=$(this).val()
            })
            var url = "{{url('admin/pri_sort')}}";
            $.post(url,map,function (msg) {
                alert('修改成功')
                location.reload();
            })
        })
    </script>

@stop


