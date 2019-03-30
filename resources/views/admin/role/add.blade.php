@extends('/admin/layouts/main')
@if(isset($data))
    @section('title','角色信息修改')
@else
    @section('title','角色添加')
@endif
@section('content')
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
                            <li class="breadcrumb-item active">角色</li>
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
                                <h3 class="card-title">职位信息</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                    <div class="card-body">
                                        <form action="{{isset($data)?url('admin/role_up'):url('admin/role_add')}}"
                                              method="post"
                                              role="form" enctype="multipart/form-data">
                                            @if(isset($data))
                                                <input type='text' hidden name='r_id' value={{$data->r_id}}>
                                            @endif
                                            <div class="form-group">
                                                <label>职位名称</label>
                                                <input type="text" name="r_name" class="form-control"
                                                       value="{{isset($data)?$data->r_name:old('r_name')}}">
                                                @if($errors->has('r_name'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('r_name',':message')}}</li>
                                                @endif
                                            </div>
                                                <div class="form-group">
                                                    <label>是否禁用</label>
                                                    <select class="form-control" name="status">
                                                        @if(isset($data))
                                                            <option value="1" <?php if($data->status=='1'){echo 'selected';} ?>>启用</option>
                                                            <option value="0" <?php if($data->status=='0'){echo 'selected';} ?>>禁用</option>
                                                        @else
                                                            <option value="1">启用</option>
                                                            <option value="0">禁用</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            <label>选择权限</label>
                                            <table rules="rows">
                                                @foreach($priData as $val)
                                                    <tr>
                                                        <td width="200px">
                                                            <input type="checkbox" class="form-check-input parent"
                                                                   name="p_id[]"  <?php if(isset($data)){if($val->checked==1){echo 'checked';}} ?>
                                                                   value="{{$val->p_id}}">
                                                            {{$val->p_name}}</td>
                                                        @if(isset($val->son))
                                                            <td>
                                                                @foreach($val->son as $k=>$v)
                                                                    <input type="checkbox" class="form-check-input son"
                                                                           name="p_id[]"  <?php if(isset($data)){if($v->checked==1){echo 'checked';}} ?>
                                                                           value="{{$v['p_id']}}">{{$v['p_name']}}
                                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                                @if(isset($v->son))
                                                                <td>
                                                                    @foreach($v->son as $k1=>$v1)
                                                                        <input type="checkbox"
                                                                               class="form-check-input son"
                                                                               name="p_id[]" <?php if(isset($data)){if($v1->checked==1){echo 'checked';}} ?>
                                                                               value="{{$v1['p_id']}}">{{$v1['p_name']}}
                                                                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                                    @endforeach
                                                                </td>
                                                                @endif
                                                                @endforeach
                                                                </td>
                                                            @endif
                                                    </tr>
                                                @endforeach
                                            </table>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary float-right">提交</button>
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
        $(function () {
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

        })
    </script>
@endsection