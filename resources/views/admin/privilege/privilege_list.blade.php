@extends('/admin/layouts/main')
@section('title','权限列表')
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
                            <li class="breadcrumb-item"><a href="{{asset('admin/pri_add')}}"></a></li>
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
                            <div class="card-title">权限列表</div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div id="example1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_length" id="example1_length">
                                            <label>显示
                                                <select id="number" name="example1_length" aria-controls="example1" class="form-control form-control-sm">
                                                    @if($number==5)
                                                        <option value="5" selected = "selected">5</option>
                                                        <option value="10">10</option>
                                                        <option value="15">15</option>
                                                        <option value="20">20</option>
                                                    @elseif($number==10)
                                                        <option value="5">5</option>
                                                        <option value="10" selected = "selected">10</option>
                                                        <option value="15">15</option>
                                                        <option value="20">20</option>
                                                    @elseif($number==15)
                                                        <option value="5">5</option>
                                                        <option value="10">10</option>
                                                        <option value="15" selected = "selected">15</option>
                                                        <option value="20">20</option>
                                                    @elseif($number==20)
                                                        <option value="5">5</option>
                                                        <option value="10">10</option>
                                                        <option value="15">15</option>
                                                        <option value="20" selected = "selected">20</option>
                                                    @endif
                                                </select> 条
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <div id="example1_filter" class="dataTables_filter">
                                            <label>权限名称搜索:
                                                <input id="search" type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1" value="{{$search}}"><button id="search_button" >搜索</button>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr pids="0">
                                        <th><input type="checkbox" id="all"></th>
                                        <th width="100px">权限ID</th>
                                        <th>权限名称</th>
                                        <th width="100px">权限路由</th>
                                        <th width="120px">权限控制器</th>
                                        <th width="100px">权限方法</th>
                                        <th width="60px">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $v)
                                        <tr pri_id="{{$v->p_id}}" pids="{{$v->pid}}">
                                            <td width="70px" class="csd"><span>[+]</span></td>
                                            <td>{{$v->p_id}}</td>
                                            <td>{{str_repeat('--',$v->level)}}{{$v->p_name}}</td>
                                            <td>{{$v->route}}</td>
                                            <td>{{$v->controller_name}}</td>
                                            <td>{{$v->method_name}}</td>
                                            <td>
                                                <a href="{{url('admin/pri_up',['id'=>$v->p_id])}}">修改</a>
                                                <a href="#" onclick="del({{$v->p_id}})">删除</a>
                                            </td>
                                        </tr>
                                        @if(isset($v->son))
                                            @foreach($v->son as $v1)
                                                <tr pri_id="{{$v1->p_id}}" pids="{{$v1->pid}}">
                                                    <td width="70px" class="csd"><span>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp[-]</span></td>
                                                    <td>{{$v1->p_id}}</td>
                                                    <td>{{str_repeat('----',$v1->level)}}{{$v1->p_name}}</td>
                                                    <td>{{$v1->route}}</td>
                                                    <td>{{$v1->controller_name}}</td>
                                                    <td>{{$v1->method_name}}</td>
                                                    <td>
                                                        <a href="{{url('admin/pri_up',['id'=>$v1->p_id])}}">修改</a>
                                                        <a href="#" onclick="del({{$v1->p_id}})">删除</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                                <ul class="pagination">
                                    <li id="jian">
                                        @if($page<=1)
                                            <span>«</span>
                                        @else
                                            <a href="{{asset('admin/pri_list')}}?search=<?php echo $search; ?>&page=<?php echo $page-1 ?>&number={{$number}}">«</a>
                                        @endif
                                    </li>
                                    <?php for($i=1;$i<=$totalPage;$i++) { ?>
                                    <li class="dj_jump" ><a href="{{asset('admin/pri_list')}}?search=<?php echo $search; ?>&page=<?php echo $i ?>&number={{$number}}"><?php echo $i; ?></a></li>
                                    <?php } ?>
                                    <li id="jia">
                                        @if($page<$totalPage)
                                            <a href="{{asset('admin/pri_list')}}?search=<?php echo $search; ?>&page=<?php echo $page+1 ?>&number={{$number}}">»</a>
                                        @else
                                            <span>»</span>
                                        @endif
                                    </li>
                                </ul>
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
            $("tr[pids!=0]").hide();
            //点击显示
            $('.csd').find('span').click(
                function(){
                    var pri_id = $(this).parent().parent().attr('pri_id');
                    $("tr[pids='"+pri_id+"']").toggle(500,function(){
                    })
                });

            $('.dj_jump').each(function (k,v) {
                var page = "{{$page}}";
                if($(this).children().html()==page){
                    $(this).html('<span>'+page+'</span>');
                    $(this).addClass('black')
                }
            })
        })

        $("#number").change(function(){
            var page = $('.black').children().val();
            var number = $('#number').val();
            var search = $('#search').val();
            var url = "{{url('admin/pri_list')}}";
            $.post(url,{'number':number,'search':search,'page':page},function (msg) {
                $('.card-body').html(msg);
            });
        });
        $("#search_button").click(function () {
            var page = $('.black').children().val();
            var number = $('#number').val();
            var search = $('#search').val();
            var url = "{{url('admin/pri_list')}}";
            $.post(url, {'number': number, 'search': search,'page':page}, function (msg) {
                $('.card-body').html(msg);
            });
        });
        $(document).keyup(function (event) {
            if (event.keyCode == 13) {
                var page = $('.black').children().val();
                var number = $('#number').val();
                var search = $('#search').val();
                var url = "{{url('admin/pri_list')}}";
                $.post(url, {'number': number, 'search': search,'page':page}, function (msg) {
                    $('.card-body').html(msg);
                });
            }
        });
        function del(id) {
            //alert(111);
            var url = '{{url('admin/pri_del')}}';
            layer.confirm('确定删除吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post(url,'id='+id,function (data) {
                    if(data.msg==1){
                        $('tr[pri_id='+id+']').remove();
                        layer.msg('删除成功', {icon: 1});
                    }else if(data.msg==0){
                        layer.msg('删除失败', {icon: 1});
                    }else if(data.msg==2){
                        layer.msg('此权限有子权限，请先删除子权限')
                    }
                },'json')
            }, function(){
                layer.msg('取消删除成功', {
                });
            });
        }

        //批量删除
        $('#delAll').on('click',function(){
            if($('tbody input:checked').length == 0){
                alert('请至少选中一个数据')
            }else {
                if (confirm('确定删除吗')) {
                    var id = '';
                    $('tbody input:checked').each(function (k, v) {
                        id = id + $(v).attr('data-id') + ','
                    })
                    // alert(id);exit;
                    var url = "{{url('admin/pridel')}}"
                    $.post(url, 'id=' +id, function (data) {
                        console.log(data);
                        if (data) {
                            $('tbody input:checked').each(function (k, v) {
                                var id = $(v).attr('data-id');
                                $('tr[pri-id='+id+']').remove();
                            })
                            alert('删除成功');
                        } else {
                            alert('删除失败');
                        }
                    }, 'text')
                }else{

                }
            }
        })


        //全选
        $("#all").click(function(){
            if(this.checked){
                $("tbody input:checkbox").prop("checked", true);
            }else{
                $("tbody input:checkbox").prop("checked", false);
            }
        });
        //全选包括全选旁边的chockbox
        $("#selectAll").click(function () {
            $("#che,#all,tbody input:checkbox").prop("checked", true);
        });

        //全不选
        $("#unSelect").click(function () {
            $("#che,#all,tbody input:checkbox").prop("checked", false);
        });

        //反选
        $("#reverse").click(function () {
            $("#che,#all,tbody input:checkbox").each(function () {
                $(this).prop("checked", !$(this).prop("checked"));
            });
            allchk();
        });



    </script>
@stop


