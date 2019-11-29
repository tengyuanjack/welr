<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--360浏览器优先以webkit内核解析-->


    <title> - 主页示例</title>

    <link rel="shortcut icon" href="/thinkphp/Public/favicon.ico"> <link href="/thinkphp/Public/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/thinkphp/Public/css/font-awesome.css?v=4.4.0" rel="stylesheet">

    <link href="/thinkphp/Public/css/animate.css" rel="stylesheet">
    <link href="/thinkphp/Public/css/style.css?v=4.1.0" rel="stylesheet">

</head>

<body class="gray-bg">
    <div class="wrapper wrapper-content">
        <div class="row">
            
        </div>
    </div>

     <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-8 b-r">
                            <h3 class="m-t-none m-b">登录</h3>
                            <div role="form" >
                                <div class="form-group">
                                    <label>用户名：</label>
                                    <input type="text" name="nickname" placeholder="请输入用户名" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>密码：</label>
                                    <input type="password" name="password" placeholder="请输入密码" class="form-control">
                                </div>
                                <div>
                                    <button id='loginBtn' class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>登录</strong>
                                    </button>
                                    <span id='hidden-identify' class='hide'>index_v1</span>
                                    <label>
                                        <input type="checkbox" class="i-checks">自动登录</label>
                                </div>
                            </div>
                        </div>
                       <div class="col-sm-4">
                        <img src="/thinkphp/Public/img/tr.jpeg" alt="登录logo"  class='img-responsive'>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 全局js -->
    <script src="/thinkphp/Public/js/jquery.min.js?v=2.1.4"></script>
    <script src="/thinkphp/Public/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/thinkphp/Public/js/plugins/layer/layer.min.js"></script>
    
    <!-- 自定义js -->
    <script src="/thinkphp/Public/js/content.js"></script>
    
     <?php if($needLogin): ?><script type="text/javascript">
            $(document).ready(function () {
                $('#modal-form').modal('toggle');
            });
        </script><?php endif; ?>   

</body>

</html>