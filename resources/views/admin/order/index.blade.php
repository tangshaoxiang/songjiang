@extends('/admin/layouts/main')
@section('title','同步订单')
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
        <section class="content" >
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"></div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body right-aside" >
                            <div id="example1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: left">
                                            <label>
                                                供应商编码:
                                                <input id="SupplierCode" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="2778" readonly>
                                                开始时间:
                                                <input id="Kssj" type="date" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="">
                                                结束时间:
                                                <input id="Jssj" type="date" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="">
                                                下载状态:
                                                <input id="DownloadState" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="1">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 col-md-9">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: left">
                                            <label>
                                               查询记录数:
                                                <input id="Count" name="example1_length"  class="form-control form-control-sm" aria-controls="example1" value="100">
                                            </label>
                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            <label><button id="search_button" >同步订单</button>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                               <div id="table" >
                                   <table  id="example1" class="table table-bordered table-striped">
                                       <thead>
                                       <th>订单编号</th>
                                       <th>总数量</th>
                                       <th>总金额</th>
                                       <th>配送单名称</th>
                                       <th>制单时间</th>
                                       <th width="80">制单人</th>
                                       <th>获取订单时间</th>
                                       <th>操作</th>
                                       </thead>
                                       <tbody>
                                       @if(!$data->isEmpty())
                                           @foreach($data as $key=>$val)
                                               <tr order-id="{{$val->Id}}">
                                                   <td>{{$val->OrderNo}}</td>
                                                   <td>{{$val->SumQuantity}}</td>
                                                   <td>{{$val->Amount}}</td>
                                                   <td>{{$val->DistributionSite}}</td>
                                                   <td>{{$val->CreateTime}}</td>
                                                   <td>{{$val->Creator}}</td>
                                                   <td>{{$val->CreatedAt}}</td>
                                                   <td><button><a href="{{asset('admin/order_detail?id='.$val->Id)}}">详情</a></button>&nbsp
                                                       <button class="excel" data-id="{{$val->Id}}">生成excel</button>
                                                   </td>
                                               </tr>
                                           @endforeach
                                       @else
                                           <tr><td colspan="9" style="text-align: center">暂无记录</td></tr>
                                       @endif
                                       </tbody>
                                   </table>
                                   {{ $data->links() }}
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


        $('#export_pd').click(function () {

            var cntElem = $('#demo')[0];
            cntElem.style.background = "#FFFFFF";
            var shareContent = cntElem;//需要截图的包裹的（原生的）DOM 对象
            var width = shareContent.offsetWidth; //获取dom 宽度
            var height = shareContent.offsetHeight; //获取dom 高度
            var canvas = document.createElement("canvas"); //创建一个canvas节点
            var scale = 2; //定义任意放大倍数 支持小数
            canvas.width = width * scale; //定义canvas 宽度 * 缩放
            canvas.height = height * scale; //定义canvas高度 *缩放
            canvas.getContext("2d").scale(scale, scale); //获取context,设置scale
            var opts = {
                scale: scale, // 添加的scale 参数
                canvas: canvas, //自定义 canvas
                // logging: true, //日志开关，便于查看html2canvas的内部执行流程
                width: width, //dom 原始宽度
                height: height,
                useCORS: true // 【重要】开启跨域配置
            };

            // translate的时候，要把这个差值去掉
            html2canvas(cntElem,opts).then(function(canvas) {
                var contentWidth = canvas.width;
                var contentHeight = canvas.height;
                //一页pdf显示html页面生成的canvas高度;
                var pageHeight = contentWidth / 592.28 * 841.89;
                //未生成pdf的html页面高度
                var leftHeight = contentHeight;
                //页面偏移
                var position = 0;
                //a4纸的尺寸[595.28,841.89]，html页面生成的canvas在pdf中图片的宽高
                var imgWidth = 595.28;
                var imgHeight = 592.28/contentWidth * contentHeight;

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
                        position -= 841.89;
                        //避免添加空白页
                        if(leftHeight > 0) {
                            pdf.addPage();
                        }
                    }
                }
                pdf.save('我的简历.pdf');
            })
            })


        $('#export_pdf').click(function () {

            var element = $("#table");    // 这个dom元素是要导出pdf的div容器
            element.css("background-color","white");
            // element.style.background = "#FFFFFF";
            var w = element.width();    // 获得该容器的宽
            var h = element.height();    // 获得该容器的高
            console.log(h)
            var offsetTop = element.offset().top;    // 获得该容器到文档顶部的距离
            console.log(offsetTop)
            var offsetLeft = element.offset().left;    // 获得该容器到文档最左的距离
            console.log(offsetLeft);
            var canvas = document.createElement("canvas");
            var abs = 0;
            var win_i = $(window).width();    // 获得当前可视窗口的宽度（不包含滚动条）
            var win_o = window.innerWidth;    // 获得当前窗口的宽度（包含滚动条）
            if(win_o>win_i){
                abs = (win_o - win_i)/2;    // 获得滚动条长度的一半
            }
            console.log(abs)
            var ab = 0;
            var win_h = $("#table").height();    // 获得当前可视窗口的宽度（不包含滚动条）
            console.log(win_h)
            var win_e = window.innerHeight;    // 获得当前窗口的宽度（包含滚动条）
            console.log(win_e)
            if(win_e<win_h){
                ab = (win_h-win_e)/2;    // 获得滚动条长度的一半
            }
            console.log(ab)
            canvas.width = w * 2;    // 将画布宽&&高放大两倍
            canvas.height = h * 2;
            var context = canvas.getContext("2d");
            context.scale(2, 2);
            context.translate(-offsetLeft-abs,-offsetTop+ab);
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
            // canvas.getContext("2d").translate(-offsetLeft-abs,-offsetTop);
            // 这里默认横向没有滚动条的情况，因为offset.left(),有无滚动条的时候存在差值，因此
            // translate的时候，要把这个差值去掉
            var opts = {
                scale: 2, // 添加的scale 参数
                canvas: canvas, //自定义 canvas
                // logging: true, //日志开关，便于查看html2canvas的内部执行流程
                width: w, //dom 原始宽度
                height: h,
                backgroundColor:'#ffffff',
                useCORS: true ,// 【重要】开启跨域配置,
                // windowWidth:document.body.scrollWidth,
                // windowHeight:document.body.scrollHeight,
                // x:0,
                // y:window.pageYOffset
            };

            // translate的时候，要把这个差值去掉
            html2canvas(element,opts).then(function(canvas) {
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
                pdf.save('我的简历.pdf');
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


        //将html页面导出.pdf格式文件(适用于jQuery、vue库)  -- xzz 2018/04/24
        function makeMpdf(pdfName) {
            if(confirm("您确认下载该PDF文件吗?")){
                var target = $("#demo").get(0);
                target.style.background = "#FFFFFF";

                html2canvas(target, {
                    onrendered:function(canvas) {
                        var contentWidth = canvas.width;
                        var contentHeight = canvas.height;

                        //一页pdf显示html页面生成的canvas高度;
                        var pageHeight = contentWidth / 592.28 * 841.89;
                        //未生成pdf的html页面高度
                        var leftHeight = contentHeight;
                        //页面偏移
                        var position = 0;
                        //a4纸的尺寸[595.28,841.89]，html页面生成的canvas在pdf中图片的宽高
                        var imgWidth = 595.28;
                        var imgHeight = 592.28/contentWidth * contentHeight;

                        var pageData = canvas.toDataURL('image/jpeg', 1.0);

                        var pdf = new jsPDF('', 'pt', 'a4');

                        //有两个高度需要区分，一个是html页面的实际高度，和生成pdf的页面高度(841.89)
                        //当内容未超过pdf一页显示的范围，无需分页
                        if (leftHeight < pageHeight) {
                            pdf.addImage(pageData, 'JPEG', 0, 0, imgWidth, imgHeight );
                        } else {
                            while(leftHeight > 0) {
                                pdf.addImage(pageData, 'JPEG', 0, position, imgWidth, imgHeight)
                                leftHeight -= pageHeight;
                                position -= 841.89;
                                //避免添加空白页
                                if(leftHeight > 0) {
                                    pdf.addPage();
                                }
                            }
                        }
                        pdf.save(pdfName+".pdf");
                    }
                })
            }
        }



        function makePdf() {
            var target = document.getElementsByClassName("right-aside")[0];
            target.style.background = "#FFFFFF";

            html2canvas(target, {
                onrendered:function(canvas) {
                    var contentWidth = canvas.width;
                    var contentHeight = canvas.height;

                    //一页pdf显示html页面生成的canvas高度;
                    var pageHeight = contentWidth / 592.28 * 841.89;
                    //未生成pdf的html页面高度
                    var leftHeight = contentHeight;
                    //页面偏移
                    var position = 0;
                    //a4纸的尺寸[595.28,841.89]，html页面生成的canvas在pdf中图片的宽高
                    var imgWidth = 595.28;
                    var imgHeight = 592.28/contentWidth * contentHeight;

                    var pageData = canvas.toDataURL('image/jpeg', 1.0);

                    var pdf = new jsPDF('', 'pt', 'a4');

                    //有两个高度需要区分，一个是html页面的实际高度，和生成pdf的页面高度(841.89)
                    //当内容未超过pdf一页显示的范围，无需分页
                    if (leftHeight < pageHeight) {
                        pdf.addImage(pageData, 'JPEG', 0, 0, imgWidth, imgHeight );
                    } else {
                        while(leftHeight > 0) {
                            pdf.addImage(pageData, 'JPEG', 0, position, imgWidth, imgHeight)
                            leftHeight -= pageHeight;
                            position -= 841.89;
                            //避免添加空白页
                            if(leftHeight > 0) {
                                pdf.addPage();
                            }
                        }
                    }

                    pdf.save("content.pdf");
                }
            })
        }
        
        function pdfs() {

                var cntElem = $('#demo')[0];

                var shareContent = cntElem;//需要截图的包裹的（原生的）DOM 对象
                var width = shareContent.offsetWidth; //获取dom 宽度
                var height = shareContent.offsetHeight; //获取dom 高度
                var canvas = document.createElement("canvas"); //创建一个canvas节点
                var scale = 2; //定义任意放大倍数 支持小数
                canvas.width = width * scale; //定义canvas 宽度 * 缩放
                canvas.height = height * scale; //定义canvas高度 *缩放
                canvas.getContext("2d").scale(scale, scale); //获取context,设置scale
                var opts = {
                    scale: scale, // 添加的scale 参数
                    canvas: canvas, //自定义 canvas
                    // logging: true, //日志开关，便于查看html2canvas的内部执行流程
                    width: width, //dom 原始宽度
                    height: height,
                    useCORS: true // 【重要】开启跨域配置
                };

                html2canvas(shareContent, opts).then(function (canvas) {

                    var context = canvas.getContext('2d');
                    // 【重要】关闭抗锯齿
                    context.mozImageSmoothingEnabled = false;
                    context.webkitImageSmoothingEnabled = false;
                    context.msImageSmoothingEnabled = false;
                    context.imageSmoothingEnabled = false;

                    // 【重要】默认转化的格式为png,也可设置为其他格式
                    var img = Canvas2Image.convertToJPEG(canvas, canvas.width, canvas.height);

                    document.body.appendChild(img);

                    $(img).css({
                        "width": canvas.width / 2 + "px",
                        "height": canvas.height / 2 + "px",
                    }).addClass('f-full');



                });

        }

    </script>

@stop


