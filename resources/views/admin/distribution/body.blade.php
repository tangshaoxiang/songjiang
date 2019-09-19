<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Commpatible" content="IE=edge">
    <title>body 页面</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        html, body {
            width: 100%;
            height: 100%;
        }

        .outer {
            width: 200px;
            height: 120px;
            position: relative;
            top: 50%;
            left: 50%;
        }

        .inner {
            width: 200px;
            height: 120px;
            position: relative;
            top: -50%;
            left: -50%;
        }

        .button {
            width: 200px;
            height: 40px;
            position: relative;
        }

        .button#btnShowLoading {
            top: 0;
        }

        .button#btnShowModal {
            top: 30%;
        }

    </style>
    <script type="text/javascript">

        function showOverlay() {
            // 调用父窗口显示遮罩层和Loading提示
            window.top.window.showLoading();

            // 使用定时器模拟关闭Loading提示
            setTimeout(function() {
                window.top.window.hideLoading();
            }, 3000);

        }

        function showModal() {
            // 调用父窗口方法模拟弹出模态窗口
            window.top.showModal($('#modalContent').html());
        }

    </script>
</head>
<body>
<div class='outer'>
    <div class='inner'>
        <button id='btnShowLoading' class='button' onclick='showOverlay();'>点击弹出遮罩层</button>
        <button id='btnShowModal' class='button' onclick='showModal();'>点击弹出模态窗口</button>
    </div>
</div>

<!-- 模态窗口内容DIV，将本页面DIV内容设置到父窗口DIV上并模态显示 -->
<div id='modalContent' style='display: none;'>
    <div class='modal-container' style='width: 100%;height: 100%;background-color: white;'>
        <div style='width: 100%;height: 49px;position: relative;left: 50%;top: 50%;'>
            <span style='font-size: 36px; width: 100%; text-align:center; display: inline-block; position:inherit; left: -50%;top: -50%;'>模态窗口1</span>
        </div>
        <button class='btn-close' style='width: 100px; height: 30px; position: absolute; right: 30px; bottom: 20px;'>关闭</button>
    </div>
</div>
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
</body>
</html>