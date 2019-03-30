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
                <label>名称搜索:
                    <input id="search" type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1" value="{{$search}}"><button id="search_button" >搜索</button>
                </label>
            </div>
        </div>
    </div>
    <table id="example1" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th><input type="checkbox" id="all"></th>
            <th>管理员名称</th>
            <th>昵称</th>
            <th>头像</th>
            <th>职位</th>
            <th>状态</th>
            <th width="100px">添加时间</th>
            <th width="100px">修改时间</th>
            <th width="60px">操作</th>
        </tr>
        </thead>
        <tbody>
        @if(!$data->isEmpty())
            @foreach($data as $key=>$val)
                <tr admin-id="{{$val->aid}}">
                    <td><input type="checkbox" data-id="{{$val->aid}}"></td>
                    <td>{{$val->name}}</td>
                    <td>{{$val->nickname}}</td>
                    <td>
                        <div>
                            <img src="{{ URL::asset($val->head_p) }}" width="50px" height="50px"/>
                        </div>
                    </td>
                    <td>{{$val->position}}</td>
                    @if($val->status==1)
                        <td>启用</td>
                    @else
                        <td>禁用</td>
                    @endif
                    <td>{{date('Y-m-d',$val->created_at)}}</td>
                    <td>{{date('Y-m-d',$val->updated_at)}}</td>
                    <td>
                        <a href="{{url('admin/admin_up?aid='.$val->aid)}}">修改</a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="7" style="text-align: center">暂无记录</td></tr>
        @endif
        </tbody>
    </table>
    {{ $data->links() }}
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
    $("#number").change(function () {
        var number = $('#number').val();
        var search = $('#search').val();
        var url = "{{url('admin/admin_list')}}";
        $.post(url, {'number': number, 'search': search}, function (msg) {
            $('.card-body').html(msg);
        });
    });
    $("#search_button").click(function () {
        var number = $('#number').val();
        var search = $('#search').val();
        var url = "{{url('admin/admin_list')}}";
        $.post(url, {'number': number, 'search': search}, function (msg) {
            $('.card-body').html(msg);
        });
    });
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            var number = $('#number').val();
            var search = $('#search').val();
            var url = "{{url('admin/admin_list')}}";
            $.post(url, {'number': number, 'search': search}, function (msg) {
                $('.card-body').html(msg);
            });
        }
    });
</script>


