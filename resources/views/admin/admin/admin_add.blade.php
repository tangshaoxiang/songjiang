@extends('/admin/layouts/main')
@if(isset($data))
    @section('title','管理员信息修改')
@else
    @section('title','管理员添加')
@endif
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><?php if (isset($data)) {
                                echo '管理员信息修改';
                            } else {
                                echo '管理员添加';
                            } ?></h1>
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
                                <h3 class="card-title">管理员信息</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                    <div class="card-body">
                                        <form action="{{isset($data)?url('admin/admin_up'):url('admin/admin_add')}}"
                                              method="post"
                                              role="form" enctype="multipart/form-data">
                                            @if(isset($data))
                                                <input type='text' hidden name='aid' value={{$data->aid}}>
                                            @endif
                                            <div class="form-group">
                                                <label>管理员名称</label>
                                                <input type="text" name="name" class="form-control"
                                                       value="{{isset($data)?$data->name:old('name')}}">
                                                @if($errors->has('name'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('name',':message')}}</li>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>昵称</label>
                                                <input type="text" name="nickname" class="form-control"
                                                       value="{{ isset($data)?$data->nickname:old('nickname') }}">
                                                @if($errors->has('nickname'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('nickname',':message')}}</li>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>密码</label>
                                                <input type="password" name="pwd" class="form-control"
                                                       autocomplete="new-password"
                                                       value="{{ isset($data)?$data->pwd:old('pwd') }}">

                                                @if($errors->has('pwd'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('pwd',':message')}}</li>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>职位</label>
                                                <input type="text" name="position" class="form-control"
                                                       value="{{ isset($data)?$data->position:old('position') }}">
                                                @if($errors->has('position'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('position',':message')}}</li>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>头像</label>
                                                <input type="file" name="head_p" id="face" style="display: none"
                                                       value="{{ isset($data)?$data->head_p:old('head_p') }}">
                                                {{--<input type="text" name="head_p"  style="display: none"--}}
                                                       {{--value="{{ isset($data)?$data->head_p:old('head_p') }}">--}}
                                                <img src="{{ isset($data)?asset($data->head_p):'' }}" id="facePreview"
                                                     width="50px" height="50px" alt="点击添加头像">
                                                @if($errors->has('head_p'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('head_p',':message')}}</li>
                                                @endif
                                            </div>
                                            {{--<div class="form-group">--}}
                                                {{--<label>选择职位</label><br>--}}
                                                {{--@if(isset($roless))--}}
                                                    {{--@foreach($roless as $v)--}}
                                                        {{--<input type="checkbox" @if($v->checked ==1) checked--}}
                                                               {{--@endif r_name="{{$v->r_name}}" name="r_id[]"--}}
                                                               {{--value="{{$v->r_id}}">{{$v->r_name}}--}}
                                                    {{--@endforeach--}}
                                                    {{--<input class="form-control" type="text" name="r_name"--}}
                                                           {{--value="{{$data->r_name}}">--}}
                                                {{--@else--}}
                                                    {{--@foreach($admin as $v)--}}
                                                        {{--<input type="checkbox" name="r_id[]" class="r_id"--}}
                                                               {{--r_name="{{$v->r_name}}"--}}
                                                               {{--value="{{$v->r_id}}">{{$v->r_name}}--}}
                                                    {{--@endforeach--}}
                                                    {{--<br>--}}
                                                    {{--<input class="form-control" type="text" name="r_name" value="">--}}
                                                {{--@endif--}}
                                            {{--</div>--}}

                                            <div class="form-group">
                                                <label>是否禁用</label>
                                                <select class="form-control" name="status">
                                                    @if(isset($data))
                                                        <option value="1" <?php if ($data->status == '1') {
                                                            echo 'selected';
                                                        } ?>>启用
                                                        </option>
                                                        <option value="0" <?php if ($data->status == '0') {
                                                            echo 'selected';
                                                        } ?>>禁用
                                                        </option>
                                                    @else
                                                        <option value="1">启用</option>
                                                        <option value="0">禁用</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary float-right">添加</button>
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
        $('#facePreview').on('click', function () {
            $('#face').click();
        })
        $('#face').on('change', function () {
            $('#facePreview')[0].src = URL.createObjectURL($('#face')[0].files[0])
        })

        $("input[name='r_id[]']").click(function () {
            var r_name = []
            $("input[name='r_id[]']:checked").each(function () {
                //追加到空数组里
                r_name.push($(this).attr('r_name'));
                //  alert($(this).attr('r_name'))
            });
            $("input[name='r_name']").val(r_name.join(","));
        })
    </script>
@endsection