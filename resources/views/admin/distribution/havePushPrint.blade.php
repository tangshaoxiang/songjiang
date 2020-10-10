@extends('/admin/layouts/main')
@section('title','打印已推送配送单')
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
        <style>
            * {
                margin: 0;
                padding: 0;
            }

            html, body {
                width: 100%;
                height: 100%;
                font-size: 14px;
            }

            div.header {
                width: 100%;
                height: 100px;
                border-bottom: 1px dashed blue;
            }

            div.title-outer {
                position: relative;
                top: 50%;
                height: 30px;
            }
            span.title {
                text-align: left;
                position: relative;
                left: 3%;
                top: -50%;
                font-size: 22px;
            }

            div.body {
                width: 100%;
            }
            .overlay {
                position: absolute;
                top: 0px;
                left: 0px;
                z-index: 10001;
                display:none;
                filter:alpha(opacity=60);
                background-color: #777;
                opacity: 0.5;
                -moz-opacity: 0.5;
            }
            .loading-tip {
                z-index: 10002;
                position: fixed;
                display:none;
            }
            .loading-tip img {
                width:100px;
                height:100px;
            }

            .modal {
                position:absolute;
                width: 600px;
                height: 360px;
                border: 1px solid rgba(0, 0, 0, 0.2);
                box-shadow: 0px 3px 9px rgba(0, 0, 0, 0.5);
                display: none;
                z-index: 10003;
                border-radius: 6px;
            }
        </style>
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
                                    <div class="col-sm-12 col-md-3">

                                    </div>

                                    <div class="col-sm-12 col-md-9">
                                        <div id="example1_filter" class="dataTables_filter" style="text-align: right">
                                            <label><button id="print_button" >打印已配送单</button>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div id="table" >
                                    <table  id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <th><input type="checkbox" id="all"></th>
                                        <th>订单编号</th>
                                        <th>总数量</th>
                                        <th>总金额</th>
                                        <th>配送点名称</th>
                                        <th>供应商编码</th>
                                        <th>配送单编码</th>
                                        <th>制单时间</th>
                                        <th width="80">制单人</th>
                                        <th>获取订单时间</th>
                                        <th>操作</th>
                                        </thead>
                                        <tbody>
                                        @if(!$data->isEmpty())
                                            @foreach($data as $key=>$val)
                                                <tr order-id="{{$val->Id}}">
                                                    <td><input type="checkbox" value="{{$val->Id}}" name="fruit" data-id="{{$val->Id}}"></td>
                                                    <td>{{$val->OrderNo}}</td>
                                                    <td>{{$val->SumQuantity}}</td>
                                                    <td>{{$val->Amount}}</td>
                                                    <td>{{$val->DistributionSite}}</td>
                                                    <td>{{$val->SupplierCode}}</td>
                                                    <td>{{$val->DistributionSiteCode}}</td>
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
    <!-- 遮罩层DIV -->
    <div id="overlay" class="overlay"></div>
    <div id="loadingTip" class="loading-tip">
        <img src="http://erp.chunyingkeji.com/images/o_loading.gif" />
    </div>

    <script type="text/javascript" >
        function rightIFrameLoad(iframe) {
            var pHeight = getWindowInnerHeight() - $('#header').height() - 5;

            $('div.body').height(pHeight);
            console.log(pHeight);

        }



        // 浏览器兼容 取得浏览器可视区高度
        function getWindowInnerHeight() {
            var winHeight = window.innerHeight
                || (document.documentElement && document.documentElement.clientHeight)
                || (document.body && document.body.clientHeight);
            return winHeight;

        }

        // 浏览器兼容 取得浏览器可视区宽度
        function getWindowInnerWidth() {
            var winWidth = window.innerWidth
                || (document.documentElement && document.documentElement.clientWidth)
                || (document.body && document.body.clientWidth);
            return winWidth;

        }

        /**
         * 显示遮罩层
         */
        function showOverlay() {
            // 遮罩层宽高分别为页面内容的宽高
            $('.overlay').css({'height':$(document).height(),'width':$(document).width()});
            $('.overlay').show();
        }

        /**
         * 显示Loading提示
         */
        function showLoading() {
            // 先显示遮罩层
            showOverlay();
            // Loading提示窗口居中
            $("#loadingTip").css('top',
                (getWindowInnerHeight() - $("#loadingTip").height()) / 2 + 'px');
            $("#loadingTip").css('left',
                (getWindowInnerWidth() - $("#loadingTip").width()) / 2 + 'px');

            $("#loadingTip").show();
            $(document).scroll(function() {
                return false;
            });
        }

        /**
         * 隐藏Loading提示
         */
        function hideLoading() {
            $('.overlay').hide();
            $("#loadingTip").hide();
            $(document).scroll(function() {
                return true;
            });
        }

        /**
         * 模拟弹出模态窗口DIV
         * @param innerHtml 模态窗口HTML内容
         */
        function showModal(innerHtml) {
            // 取得显示模拟模态窗口用DIV
            var dialog = $('#modalDiv');

            // 设置内容
            dialog.html(innerHtml);

            // 模态窗口DIV窗口居中
            dialog.css({
                'top' : (getWindowInnerHeight() - dialog.height()) / 2 + 'px',
                'left' : (getWindowInnerWidth() - dialog.width()) / 2 + 'px'
            });

            // 窗口DIV圆角
            dialog.find('.modal-container').css('border-radius','6px');

            // 模态窗口关闭按钮事件
            dialog.find('.btn-close').click(function(){
                closeModal();
            });

            // 显示遮罩层
            showOverlay();

            // 显示遮罩层
            dialog.show();
        }

        /**
         * 模拟关闭模态窗口DIV
         */
        function closeModal() {
            $('.overlay').hide();
            $('#modalDiv').hide();
            $('#modalDiv').html('');
        }




    </script>
    <script>
        window.onload = loadSettleChecks;//不要括号
        //批量删除
        $('#print_button').on('click', function () {
            var settleChecksArr= Cookies.getJSON('settleChecks') || [];
            console.log(settleChecksArr.length)
            if (settleChecksArr <= 0) {
                alert('请至少选中一个数据')
            } else {
                if (confirm('确定打印吗')) {
                    var id = '';
                    var ids = settleChecksArr;
                    $('tbody input:checked').each(function (k, v) {
                        id = id + $(v).attr('data-id') + ',';
                        ids.push($(v).attr('data-id'))
                    })

                    var url = "{{url('admin/havePushManyExcel')}}";

                    // $.post(url, {'SupplierCode': SupplierCode, 'Kssj': Kssj, 'Jssj': Jssj, 'DownloadState': DownloadState, 'Count': Count,'id':id}, function (data) {
                    //    if (data==0){
                    //        alert('推送失败')
                    //    } else{
                    //        window.location.reload();
                    //        // $('#table').html(data);
                    //        alert('推送成功')
                    //    }
                        {{--var ev = eval('(' + data + ')');--}}
                        {{--if (ev.code==0) {--}}
                            {{--$('tbody input:checked').each(function (k, v) {--}}
                                {{--document.location.href ='{{url("")}}'+ev.url;--}}
                                {{--var id = $(v).attr('data-id');--}}
                                {{--// $('tr[order-id=' + id + ']').remove();--}}
                            {{--})--}}
                            {{--alert('推送成功');--}}
                        {{--} else {--}}
                            {{--alert('推送失败');--}}
                        {{--}--}}
                    // }, 'text')
                    $.post(url, {'id':settleChecksArr}, function (data) {
                        var ev = eval('(' + data + ')');
                        if (ev.code==0) {
                            deleteSettleChecksByIds(ids);
                            document.location.href ='{{url("")}}'+ev.url;
                        } else {
                            alert('生成excel失败');
                        }
                    }, 'text')
                }
            }
        })

        $('.excel').on('click', function () {
            var id =  $(this).attr('data-id')
            var url = "{{url('admin/havePushExcel')}}";
            $.post(url, {'id':id}, function (data) {
                var ev = eval('(' + data + ')');
                if (ev.code==0) {
                    document.location.href ='{{url("")}}'+ev.url;
                } else {
                    alert('生成excel失败');
                }
            }, 'text')
        })

        // $("#all").click(function () {
        //     if (this.checked) {
        //         $("tbody input:checkbox").prop("checked", true);
        //     } else {
        //         $("tbody input:checkbox").prop("checked", false);
        //     }
        // });

        // 全选复选框点击事件
        $('#table').on('click','#all',function () {
            $("input[name='fruit']").prop("checked", this.checked);
            $("input[name='fruit']").each(function () {
                setSettleChecks($(this).val(),this.checked);
            })
        })

        //checkbox点击事件
        $(document).on('click',"input[name='fruit']",function () {
            console.log($(this).val())
            setSettleChecks($(this).val(),$(this).is(':checked'));
        })

        //存储选中的id,flag=true新增，flag=false删除
        function setSettleChecks(settleId,flag) {
            //debugger;
            var settleChecksArr=Cookies.getJSON('settleChecks') || []
            if(flag && $.inArray(settleId,settleChecksArr)<0){
                settleChecksArr.push(settleId)
            }else if(!flag){
                settleChecksArr.splice($.inArray(settleId,settleChecksArr),1);
            }
            console.log(settleChecksArr)
            Cookies.set("settleChecks",settleChecksArr)
            console.log("settleChecks:"+Cookies.getJSON("settleChecks"));
        }


        //删除已经提交成功的id（批量操作时将批量操作的id从cookies中删除）
        function deleteSettleChecksByIds(ids) {
            //debugger
            for(var i=0;i<ids.length;i++){
                setSettleChecks(ids[i],false);
            }
            $('input[name="fruit"]').each(function () {
                if($.inArray($(this).val(),ids)>-1){
                    $(this).prop("checked",false);
                }
            })
            console.log("settleChecks:"+Cookies.getJSON("settleChecks"));
        }


        //进入页面或者刷新页面时加载选中的checkbox
        function loadSettleChecks() {
            var settleChecksArr=Cookies.getJSON('settleChecks') || [];
            var $fruits = $("input[name='fruit']");
            $fruits.each(function () {
                if($.inArray($(this).val(),settleChecksArr)>-1){
                    $(this).prop("checked",true);
                }
            })
            //全选复选框设置选中或者不选中
            $("#all").prop("checked" , ($fruits.length == $fruits.filter(":checked").length &&  $fruits.length>0) ? true :false);
            console.log(settleChecksArr)
        }
    </script>

@stop


