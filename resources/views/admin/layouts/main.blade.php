<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>松江医院erp  - @yield('title')</title>
    <style>
        .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
            border-radius: .25rem;
        }
        .pagination li{
            position: relative;
            display: block;
            padding: .5rem .75rem;
            margin-left: -1px;
            line-height: 1.25;
            background-color: #fff;
            border: 1px solid #dee2e6
        }
        .active{
            color: #343a40;
        }
    </style>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('css/ionicons.min.css')}}">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{asset('plugins/font-awesome/css/font-awesome.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap4.min.css')}}">
    <!-- jQuery -->
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <!-- layer -->
    <script src="{{asset('layer/layer.js')}}"></script>
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    @include('/admin/layouts/nav')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('/admin/layouts/aside')


    @yield('content')

    <!-- Main Footer -->
    @include('/admin/layouts/footer')
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

</div>
<!-- ./wrapper -->


<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- SlimScroll -->
<script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
{{--<!-- AdminLTE App -->--}}
{{--<script src="../../dist/js/adminlte.min.js"></script>--}}
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<script>
    $(function(){
        /* 单机li进行页面跳转 */
        $("ul li").click(function(){
            /*当前标签下的a标签*/
            var obj = $(this).children("a");
            /*获取第一个a标签，进行跳转*/
            if(obj['0']){
                window.location.href=$(obj[0]).attr("href");
            }
        });

        $('.affirm-del').click(function(){
            var $del = confirm('确认删除？');
            if($del){
                $(this).href();
            }
            return false;
        });
    })
</script>
</body>
</html>
