@extends('/admin/layouts/main')
@if(isset($data))
    @section('title','权限信息修改')
@else
    @section('title','权限添加')
@endif
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><?php if (isset($data)) {
                                echo '权限信息修改';
                            } else {
                                echo '权限添加';
                            } ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">权限添加</li>
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
                                <h3 class="card-title">权限添加</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                    <div class="card-body">
                                        <form action="{{isset($data)?url('admin/pri_up',['id'=>$data->p_id]):url('admin/pri_add')}}"
                                              method="post"
                                              role="form" enctype="multipart/form-data">
                                            @if(isset($data))
                                                <input type='text' hidden name='p_id' value={{$data->p_id}}>
                                            @endif
                                            <div class="form-group">
                                                <label>选择权限</label>

                                                <select class="form-control" name="pid">
                                                    <option value="0">顶级权限</option>
                                                    @if(isset($data))
                                                    @foreach($pri as $v)
                                                        <option value="{{$v->p_id}}"  <?php if ($v->p_id == $data->pid) {
                                                            echo 'selected';
                                                        } ?>>{{str_repeat("--","$v->level")}}{{$v->p_name}}</option>
                                                    @endforeach
                                                        @else
                                                        @foreach($pri as $v)
                                                            <option value="{{$v->p_id}}">{{str_repeat("--","$v->level")}}{{$v->p_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>权限名称</label>
                                                <input type="text" name="p_name" class="form-control"
                                                       value="{{isset($data)?$data->p_name:old('p_name') }}">
                                                @if($errors->has('p_name'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('p_name',':message')}}</li>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>选择器名称</label>
                                                <select class="form-control" name="controller_name">
                                                    @if(isset($data))
                                                        @foreach($controller as $v)
                                                            <option value="{{$v}}" <?php if ($v == $data->controller_name) {
                                                                echo 'selected';
                                                            } ?>>{{$v}}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($controller as $v)
                                                            <option value="{{$v}}">{{$v}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>权限方法</label>
                                                <input type="text" name="method_name" class="form-control"
                                                       value="{{isset($data)?$data->method_name:old('method_name')}}">
                                                @if($errors->has('method_name'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('method_name',':message')}}</li>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>路由</label>
                                                <input type="text" name="route" class="form-control"
                                                       value="{{isset($data)?$data->route:old('route') }}">
                                                @if($errors->has('route'))
                                                    <li style="list-style-type:none;color:#F00;padding-left:14px;">{{ $errors->first('route',':message')}}</li>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>是否禁用</label>
                                                <select class="form-control" name="status">
                                                    @if(isset($data))
                                                        <option value="1" <?php if ($data->status == 1) {
                                                            echo 'selected';
                                                        } ?>>启用
                                                        </option>
                                                        <option value="0" <?php if ($data->status == 0) {
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
                                                <label>是否显示</label>
                                                <select class="form-control" name="is_show">
                                                    @if(isset($data))
                                                        <option value="1" <?php if ($data->is_show == 1) {
                                                            echo 'selected';
                                                        } ?>>显示
                                                        </option>
                                                        <option value="0" <?php if ($data->is_show == 0) {
                                                            echo 'selected';
                                                        } ?>>不显示
                                                        </option>
                                                    @else
                                                        <option value="1">显示</option>
                                                        <option value="0">不显示</option>
                                                    @endif
                                                </select>
                                            </div>


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

@endsection