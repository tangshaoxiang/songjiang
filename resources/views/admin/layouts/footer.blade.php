<footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-sm-none d-md-block">
        Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2018 <a href="#">AdminLTE.io</a>.</strong> All rights reserved.
</footer>
@if(!empty(session('checkPrivilege')))
    <div id="checkPrivilege" hidden>{{session('checkPrivilege')}}</div>
@endif
<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>

<script src="{{asset('/localResizeIMG/dialog.js')}}"></script>

<script>
    function prompt(title,content)
    {
        return dialog({
            title: title,
            content: content
        });
    }
    $(function () {
        $('.nav-link').each(function (k, v) {
            if($(this).attr("href")==window.location.href||$(this).attr("href")+'#'==window.location.href){
                $(this).addClass('active');
                $(this).parents('.has-treeview').addClass('menu-open');
                $(this).parents('.nav-treeview').prev().addClass('active');
            }
        })
    })

    $(function(){
        var hospital_name = '', hospital_key = '';
        var html  =
            "<label>{{session('checkPrivilege')}}</label>";
        if($('#checkPrivilege').html()!=undefined){
            var load = dialog({
                title:'提示信息',
                content:html,
                width:500,
                okValue: '确定',
                ok: function () {
                    var hospital = $('input[name="app_name"]').val();
                    var app_key = $('input[name="app_key"]').val();
                    $.ajax({
                        type:     "post",
                        dataType: "json",
                        url:      "{{url('admin/hospital/add')}}",
                        data:{
                            hospital:hospital,
                            app_key:app_key
                        },
                        success:function(result){
                            if(result.code === 0){
                                prompt('成功',result.msg).show();
                                location.reload();
                            }else{
                                prompt('失败',result.msg).show();
                            }
                        },
                        error:function(){
                            load.close();
                            console.log('没有权限');
                        }
                    })
                }
            }).show();
        };
    });
</script>

