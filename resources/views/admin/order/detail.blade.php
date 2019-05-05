@extends('/admin/layouts/main')
@section('title','订单详情')
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
                            <div class="card-title"></div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div id="example1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: left">
                                            <button id="export_pdf">生成pdf</button>
                                        </div>
                                    </div>
                                </div>
                                <style type="text/css">
                                    * {
                                        padding: 0;
                                        margin: 0;
                                    }

                                    .align-center {
                                        text-align: center !important
                                    }

                                    .align-left {
                                        text-align: left;
                                    }

                                    .align-right {
                                        text-align: right !important
                                    }

                                    .pdf-content {
                                        padding: 40px 40px;
                                    }
                                    #export_pdf {
                                        padding:0 40px;
                                        margin: 0 55px;
                                    }

                                    .order-content {
                                        padding: 0 16px;
                                    }

                                    table.order-info {
                                        width: 100%;
                                        font-size: 14px;
                                        color: #333333;
                                        border-color: #000000;
                                        border-collapse: collapse;
                                    }

                                    table.order-info tr {
                                        text-align: left;
                                    }

                                    table.order-info td {
                                        width: 45%
                                    }

                                    table.order-body {
                                        width: 100%;
                                        font-size: 14px;
                                        color: #333333;
                                        border-width: 1px;
                                        border-color: #000000;
                                        border-collapse: collapse;
                                    }

                                    table.order-body th,
                                    table.order-body td {
                                        height: 37px;
                                        border-width: 1px;
                                        padding:0 8px;
                                        text-align: center;
                                        border-style: solid;
                                        border-color: #000000;
                                    }

                                    #table-foot td {
                                        font-size: 16px;
                                        text-align: left;
                                        font-weight: 700;
                                    }

                                    .order-footer {
                                        display: flex;
                                        flex-direction: column;
                                    }

                                    .footer-item-wrapper,
                                    .footer-desc {
                                        display: flex;
                                        justify-content: space-between;
                                    }
                                </style>
                                <div class="pdf-content">
                                <div class="order-content">
                                    <h1 class="align-center">上海松卫医工贸有限公司商品销售清单</h1>
                                    <table class="order-info">
                                        <tr>
                                            <td>客户: 上海松江区中心医院</td>
                                            <td>科室： {{$detail[0]->DeptName}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>单据号:{{$order->OrderNo}}</td>
                                            <td>开票日期:
                                                <span id="kp_time">
                                                <span></span>-
                                                <span></span>-
                                                <span></span>
                                                </span>
                                            </td>
                                            <td class="align-right">第1页 共1页</td>
                                        </tr>
                                    </table>

                                    <table class='order-body'>
                                        <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>物资名称</th>
                                            <th>科室名称</th>
                                            <th>规格</th>
                                            <th>单位</th>
                                            <th>数量</th>
                                            <th>单价</th>
                                            <th>总金额</th>
                                            <th>备注</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($detail as $key=>$val)
                                            <tr order-id="{{$val->Id}}">
                                                <td>{{$key+1}}</td>
                                                <td>{{$val->Name}}</td>
                                                <td>{{$val->DeptName}}</td>
                                                <td>{{$val->Spec}}</td>
                                                <td>{{$val->Unit}}</td>
                                                <td>{{$val->Quantity}}</td>
                                                <td>{{$val->Price}}</td>
                                                <td>{{$val->Amount}}</td>
                                                <td>{{$val->Memo}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                        <tfoot id="table-foot">
                                        <tr>
                                            <td colspan="2">本页金额小计</td>
                                            <td colspan="4"></td>
                                            <td colspan="2" class="align-right">{{$total}}</td>
                                            <td colspan="1" ></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9">价税合计(大写):&nbsp&nbsp&nbsp {{$big}}
                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                价税合计(小写):&nbsp&nbsp{{$total}}
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>

                                    <footer class="order-footer">
                                        <div class="footer-remarks">备&nbsp&nbsp&nbsp注：</div>
                                        <div class="footer-item-wrapper">
                                            <div class="footer-item">制作人：</div>
                                            <div class="footer-item">发货员：</div>
                                            <div class="footer-item">复核员：</div>
                                            <div class="footer-item" style="width:300px">用户签名：</div>
                                        </div>
                                        <div class="footer-desc">
                                            <div class="desc-item"></div>
                                            <div id="export_time" class="desc-item" style="width:300px">打印时间：
                                                <span class="sj">
                                                    <span></span>-
                                                    <span></span>-
                                                    <span></span>&nbsp&nbsp
                                                    <span></span>:
                                                    <span></span>:
                                                    <span></span>
                                                </span>
                                            </div>
                                        </div>
                                    </footer>
                                </div>
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
        $("#search_button").click(function () {
            var SupplierCode = $('#SupplierCode').val();
            var Kssj = $('#Kssj').val();
            var Jssj = $('#Jssj').val();
            var DownloadState = $('#DownloadState').val();
            var Count = $('#Count').val();
            var url = "{{url('admin/order')}}";
            $.ajax({
                url: url,
                type: 'POST',
                data: {'SupplierCode': SupplierCode, 'Kssj': Kssj, 'Jssj': Jssj, 'DownloadState': DownloadState, 'Count': Count},
                success: function (msg) {
                    
                    if (msg.code=='206'){
                        alert(msg.error);
                    }else{
                        $('#table').html(msg);
                        alert('获取订单成功')
                    }

                }

            });
        });



        $('.excel').on('click', function () {
                   var id =  $(this).attr('data-id')
                    var url = "{{url('admin/order_excel')}}";
                    $.post(url, {'id':id}, function (data) {
                        var ev = eval('(' + data + ')');
                        if (ev.code==0) {
                                document.location.href ='{{url("")}}'+ev.url;
                        } else {
                            alert('生成excel失败');
                        }
                    }, 'text')
        })


        $(document).ready(function() {
            function time() {
                var date = new Date();
                var n = date.getFullYear();
                var y = date.getMonth()+1;
                var t = date.getDate();
                var h = date.getHours();
                var m = date.getMinutes();
                var s = date.getSeconds();

                $('.sj span').eq(0).html(n);
                $('.sj span').eq(1).html(y);
                $('.sj span').eq(2).html(t);
                $('.sj span').eq(3).html(h);
                $('.sj span').eq(4).html(m);
                $('.sj span').eq(5).html(s);


                $('#kp_time span').eq(0).html(n);
                $('#kp_time span').eq(1).html(y);
                $('#kp_time span').eq(2).html(t);
                for (var i = 0; i < $('span').length; i++) {
                    console.log($('span').length)
                    if ($('span').eq(i).text().length == 1) {
                        $('span').eq(i).html(function(index, html) {
                            return 0 + html;
                        });
                    }
                }
            }
            time();
            setInterval(time, 1000);
        });

        $('#export_pdf').click(function () {
            // var element = $(".pdf-content");    // 这个dom元素是要导出pdf的div容器
            // element.css("background-color","white");
            // // element.style.background = "#FFFFFF";
            // var w = element.width();    // 获得该容器的宽
            // var h = element.height();    // 获得该容器的高
            // console.log(h)
            // var offsetTop = element.offset().top;    // 获得该容器到文档顶部的距离
            // console.log(offsetTop)
            // var offsetLeft = element.offset().left;    // 获得该容器到文档最左的距离
            // console.log(offsetLeft);
            // var canvas = document.createElement("canvas");
            // var abs = 0;
            // var win_i = $(".order-content").width();    // 获得当前可视窗口的宽度（不包含滚动条）
            // var win_o = window.innerWidth;    // 获得当前窗口的宽度（包含滚动条）
            // if(win_o>win_i){
            //     abs = (win_o - win_i)/2;    // 获得滚动条长度的一半
            // }
            // console.log(abs)
            // var ab = 0;
            // var win_h = $(".order-content").height();    // 获得当前可视窗口的宽度（不包含滚动条）
            // console.log(win_h)
            // var win_e = window.innerHeight;    // 获得当前窗口的宽度（包含滚动条）
            // console.log(win_e)
            // if(win_e<win_h){
            //     ab = (win_h-win_e)/2;    // 获得滚动条长度的一半
            // }
            // console.log(ab)
            // canvas.width = w * 2;    // 将画布宽&&高放大两倍
            // canvas.height = h * 2;
            //
            // canvas.style.width = w + "px";
            // canvas.style.height = h + "px";
            // var context = canvas.getContext("2d");
            // context.scale(2, 2);
            // console.log(offsetLeft)
            // console.log(offsetTop)


            var copyDom = $(".pdf-content");
            var width = copyDom.offsetWidth;//dom宽
            var height = copyDom.offsetHeight;//dom高
            var scale = 2;//放大倍数
            var canvas = document.createElement('canvas');
            canvas.width = width*scale;//canvas宽度
            canvas.height = height*scale;//canvas高度
            var content = canvas.getContext("2d");
            content.scale(scale,scale);
            var rect = copyDom.get(0).getBoundingClientRect();//获取元素相对于视察的偏移量
            content.translate(-rect.left,-rect.top);//设置context位置，值为相对于视窗的偏移量负值，让图片复位



            // context.translate(-offsetLeft,-offsetTop);
            // 这里默认横向没有滚动条的情况，因为offset.left(),有无滚动条的时候存在差值，因此
            // translate的时候，要把这个差值去掉

            // var cntElem = $('#demo')[0];
            // // cntElem.style.background = "#FFFFFF";
            // var shareContent = cntElem;//需要截图的包裹的（原生的）DOM 对象
            // var width = shareContent.offsetWidth; //获取dom 宽度
            // var height = shareContent.offsetHeight; //获取dom 高度
            // var offsetTop = shareContent.offsetTop;    // 获得该容器到文档顶部的距离
            // var offsetLeft = shareContent.offsetLeft;    // 获得该容器到文档最左的距离
            // var canvas = document.createElement("canvas"); //创建一个canvas节点
            //
            //
            // var abs = 0;
            // var win_i = $(window).width();    // 获得当前可视窗口的宽度（不包含滚动条）
            // var win_o = window.innerWidth;    // 获得当前窗口的宽度（包含滚动条）
            // if(win_o>win_i){
            //     abs = (win_o - win_i)/2;    // 获得滚动条长度的一半
            // }
            //
            // var scale = 2; //定义任意放大倍数 支持小数
            // canvas.width = width * scale; //定义canvas 宽度 * 缩放
            // canvas.height = height * scale; //定义canvas高度 *缩放
            // canvas.getContext("2d").scale(scale, scale); //获取context,设置scale

            // canvas.getContext("2d").translate(-offsetLeft,-offsetTop);
            // 这里默认横向没有滚动条的情况，因为offset.left(),有无滚动条的时候存在差值，因此
            // translate的时候，要把这个差值去掉
            // var opts = {
            //     scale: 2, // 添加的scale 参数
            //     canvas: canvas, //自定义 canvas
            //     // logging: true, //日志开关，便于查看html2canvas的内部执行流程
            //     width: w, //dom 原始宽度
            //     height: h,
            //     backgroundColor:'#ffffff',
            //     useCORS: true ,// 【重要】开启跨域配置,
            //     dpi: window.devicePixelRatio*2,
            // };
            var opts = {
                scale:scale,
                canvas:canvas,
                width:width,
                heigth:height,
                useCORS: true ,// 【重要】开启跨域配置,
            };

            // translate的时候，要把这个差值去掉
            html2canvas(copyDom[0],opts).then(function(canvas) {
                var contentWidth = canvas.width;
                var contentHeight = canvas.height;
                //一页pdf显示html页面生成的canvas高度;
                var five = 592.28;
                var eight = 841.89;
                var pageHeight = contentWidth / five * eight;
                //未生成pdf的html页面高度
                var leftHeight = contentHeight;
                //页面偏移
                var position = 0;
                //a4纸的尺寸[595.28,841.89]，html页面生成的canvas在pdf中图片的宽高
                var imgWidth = five;
                var imgHeight = five/contentWidth * contentHeight;
                var pageData = canvas.toDataURL('image/jpeg', 1.0);
                var pdf = new jsPDF('', 'pt', 'a4');

                //有两个高度需要区分，一个是html页面的实际高度，和生成pdf的页面高度(841.89)
                //当内容未超过pdf一页显示的范围，无需分页
                if (leftHeight < pageHeight) {
                    pdf.addImage(pageData, 'JPEG', 0, 0, imgWidth, imgHeight);
                } else {    // 分页
                    while(leftHeight > 0) {
                        pdf.addImage(pageData, 'JPEG', 0, position, imgWidth, imgHeight)
                        leftHeight -= pageHeight;
                        position -= eight;
                        //避免添加空白页
                        if(leftHeight > 0) {
                            pdf.addPage();
                        }
                    }
                }
                pdf.save("销售清单-{{date('YmdHis')}}.pdf");
            })
        })
        {{--$('#excel_button').on('click', function () {--}}
            {{--if ($('tbody input:checked').length == 0) {--}}
                {{--alert('请至少选中一个数据')--}}
            {{--} else {--}}
                {{--if (confirm('确定推送吗')) {--}}
                    {{--var id = '';--}}
                    {{--$('tbody input:checked').each(function (k, v) {--}}
                        {{--id = id + $(v).attr('data-id') + ','--}}
                    {{--})--}}

                    {{--var url = "{{url('admin/order_excel')}}";--}}
                    {{--var SupplierCode = $('#SupplierCode').val();--}}
                    {{--var Kssj = $('#Kssj').val();--}}
                    {{--var Jssj = $('#Jssj').val();--}}
                    {{--var DownloadState = $('#DownloadState').val();--}}
                    {{--var Count = $('#Count').val();--}}

                    {{--$.post(url, {'SupplierCode': SupplierCode, 'Kssj': Kssj, 'Jssj': Jssj, 'DownloadState': DownloadState, 'Count': Count,'id':id}, function (data) {--}}
                        {{--var ev = eval('(' + data + ')');--}}
                        {{--if (ev.code==0) {--}}
                            {{--$('tbody input:checked').each(function (k, v) {--}}
                                {{--document.location.href ='{{url("")}}'+ev.url;--}}
                                {{--var id = $(v).attr('data-id');--}}
                                {{--$('tr[order-id=' + id + ']').remove();--}}
                            {{--})--}}
                            {{--alert('推送成功');--}}
                        {{--} else {--}}
                            {{--alert('推送失败');--}}
                        {{--}--}}
                    {{--}, 'text')--}}
                {{--}--}}
            {{--}--}}
        {{--})--}}


    </script>

@stop


