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
                <label>职位搜索:
                    <input id="search" type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1" value="{{$search}}">
                </label>
            </div>
        </div>
    </div>
    <table id="example1" class="table table-bordered table-striped">
        <thead>
        <button id="delAll">批量删除</button>
        <button id="selectAll">全选</button>
        <button id="unSelect">全不选</button>
        <button id="reverse">反选</button>
        <tr>
            <th width="10px"><input type="checkbox" id="all"></th>
            <th width="120px">职位名称</th>
            <th>对应权限</th>
            <th width="100px">是否禁用</th>
            <th width="100px">添加时间</th>
            <th width="60px">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $v)
            <tr role-id="{{$v->r_id}}">
                <td><input type="checkbox" data-id="{{$v->r_id}}"></td>
                <td>{{$v->r_name}}</td>
                <td>{{$v->p_name}}</td>
                @if($v->status==1)
                    <td>启用</td>
                @else
                    <td>禁用</td>
                @endif
                <td>{{date('Y-m-d',$v->addtime)}}</td>
                <td>
                    <a href="{{url('admin/role_up?r_id='.$v->r_id)}}">修改</a>
                    <a href="#" onclick="del({{$v->r_id}})">删除</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $data->links() }}
</div>
<script>
    $("#number").change(function(){
        var number = $('#number').val();
        var search = $('#search').val();
        var url = "{{url('admin/role_list')}}";
        $.post(url,{'number':number,'search':search},function (msg) {
            $('.card-body').html(msg);
        });
    });
    $("#search").blur(function(){
        var number = $('#number').val();
        var search = $('#search').val();
        var url = "{{url('admin/role_list')}}";
        $.post(url,{'number':number,'search':search},function (msg) {
            $('.card-body').html(msg);
        });
    });
    function del(id) {
        //alert(111);
        var url = '{{url('admin/role_del')}}';
        layer.confirm('确定删除吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.post(url,'id='+id,function (data) {
                console.log(data);
                if(data.msg==1){
                    layer.msg('删除成功', {icon: 1});
                }else{
                    layer.msg('删除失败', {icon: 1});
                }
                $('tr[role-id='+id+']').remove();
            },'json')
        }, function(){
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
                var url = "{{url('admin/roledel')}}"
                $.post(url, 'id=' +id, function (data) {
                    console.log(data);
                    if (data) {
                        $('tbody input:checked').each(function (k, v) {
                            var id = $(v).attr('data-id');
                            $('tr[role-id='+id+']').remove();
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