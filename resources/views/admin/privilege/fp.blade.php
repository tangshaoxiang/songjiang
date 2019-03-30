@extends('/admin/layouts/main')
    @section('title','权限分配')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>权限分配</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">后台</a></li>
                            <li class="breadcrumb-item active">管理员</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-1">

                    </div>
                    <!--/.col (left) -->
                    <!-- right column -->
                    <div class="col-md-10">
                        <!-- general form elements disabled -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">权限分配</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                    <div class="card-body">
                                        <form action="{{url('admin/pri_allot')}}"
                                              method="post"
                                              role="form" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>选择角色</label>
                                                <select id="role" class="form-control" name="r_id">
                                                    @foreach($role as $v)
                                                        <option value="{{$v->r_id}}" >{{$v->r_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            {{--<label>选择权限</label>--}}
                                                <h2 class="add-article-box-title"><span>选择权限</span></h2>
                                            <div id="tb">
                                                <table rules="rows">
                                                    @foreach($data as $val)
                                                        <tr>
                                                            <td width="200px"><input type="checkbox" class="form-check-input parent" name="p_id[]"
                                                                                     value="{{$val->p_id}}" <?php if ($val->checked == 1) {
                                                                    echo 'checked';
                                                                } ?>>{{$val->p_name}}</td>
                                                            @if(isset($val->son))
                                                                <td>
                                                                    @foreach($val->son as $k=>$v)
                                                                        <input type="checkbox" class="form-check-input son" name="p_id[]"
                                                                               value="{{$v->p_id}}" <?php if ($v->checked == 1) {
                                                                            echo 'checked';
                                                                        } ?>>{{$v->p_name}}&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                                    @endforeach
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary float-right">提交问题</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-1"></div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <script>

        //点击父级,子集全部选中
        $('.parent').click(function () {
            if ($(this).prop("checked")) {
                $(this).parent().next().find('.son').prop('checked', true)
            } else {
                $(this).parent().next().find('.son').prop('checked', false)
            }
            //简写
            //$(this).parent().next().find('.son').prop('checked',$(this).prop("checked"))
        })
        //点击子集父级必选,切子集没有一个选中的话,父级也不选中
        $('.son').click(function () {
            if ($(this).prop('checked')) {
                $(this).parent().prev().find('.parent').prop('checked', true)
            } else if (!$(this).siblings('input:checked').val()) {
                $(this).parent().prev().find('.parent').prop('checked', false)
            }

        })

        $('#role').change(function () {
           $r_id = this.value;
           var url = "{{url('admin/re_pri')}}"
           $.get(url,'id='+$r_id,function (msg) {
               $('#tb').html(msg)
           })
        })
    </script>
@endsection