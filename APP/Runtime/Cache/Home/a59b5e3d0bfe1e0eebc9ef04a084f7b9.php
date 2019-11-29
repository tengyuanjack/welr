<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">

    <title> 需求跟踪系统 </title>

    <meta name="keywords" content="">
    <meta name="description" content="">

    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->

    <link rel="shortcut icon" href="/thinkphp/Public/favicon.ico"> <link href="/thinkphp/Public/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/thinkphp/Public/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/thinkphp/Public/css/animate.css" rel="stylesheet">
    <link href="/thinkphp/Public/css/style.css?v=4.1.0" rel="stylesheet">
</head>

<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <span class="block m-t-xs" style="font-size:20px;">
                                        <i class="fa fa-area-chart"></i>
                                        <strong class="font-bold">需求跟踪</strong>
                                    </span>
                                </span>
                            </a>
                        </div>
                        <div class="logo-element">需求跟踪
                        </div>
                    </li>
                    <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                        <span class="ng-scope">主页</span>
                    </li>
                    <li>
                        <a class="J_menuItem" href="<?php echo U('index_v1');?>"> 
                            <i class="fa fa-home"></i>
                            <span class="nav-label">主页</span>
                        </a>
                    </li>                    
                    <li class="line dk"></li>
                    <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                        <span class="ng-scope">数据管理和计算</span>
                    </li>
                    <li>
                        <a href="mailbox.html"><i class="fa fa-envelope"></i> <span class="nav-label">数据管理 </span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="J_menuItem" href="<?php echo U('DataManage/editData');?>">录入软件制品数据</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('DataManage/displayArtifactData');?>">查看软件制品数据</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('DataManage/displayProject');?>">项目数据</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('DataManage/displayLifecycle');?>">生命周期分类</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('DataManage/importWord');?>">导入word</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-edit"></i> <span class="nav-label">计算</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            
                            <li><a class="J_menuItem" href="<?php echo U('Calculate/welr');?>">WELR计算</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('Calculate/tupleRelation');?>">两文档跟踪关系</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('Calculate/layerRelation');?>">层级跟踪关系</a>
                            </li>
                        </ul>
                    </li>  
                    <li>
                        <a href="#"><i class="fa fa-edit"></i> <span class="nav-label">适航审定目标</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="J_menuItem" href="<?php echo U('Audit/hlReqToSysReq');?>">高级需求可追溯系统需求</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('Audit/llReqToHlReq');?>">低级需求可追溯高级需求</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('Audit/scToLlReq');?>">源代码可追溯低级需求</a>
                            </li>                                                        
                        </ul>
                    </li>                    
                    <li class="line dk"></li>
                    <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                        <span class="ng-scope">其他</span>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-flask"></i> <span class="nav-label">权限管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="J_menuItem" href="<?php echo U('Rbac/userManage');?>">用户管理</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('Rbac/roleManage');?>">角色管理</a>
                            </li>
                            <li><a class="J_menuItem" href="<?php echo U('Rbac/nodeManage');?>">权限列表</a>
                            </li>
                            
                        </ul>
                    </li>
                    <li>
                        <a class="J_menuItem" href="<?php echo U('TermDictionary/index');?>"><i class="fa fa-table"></i> <span class="nav-label">自定义术语词典</span></a>
                       
                    </li>

                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-info " href="#"><i class="fa fa-bars"></i> </a>
                        <form id="searchForm" role="search" class="navbar-form-custom">
                            <div class="form-group">
                                <input type="text" placeholder="请输入您需要查找的内容 …" class="form-control" name="keyword" id="top-search">
                            </div>
                        </form>
                    </div>
                    <div class="navbar-right center-block"  style="margin-top:10px;margin-right:10px;">  
                        <div id="divForLogin" class="text-center">
                            <a id="login" data-toggle="modal" class="btn btn-primary btn-sm" href="index.html#modal-form">登录</a>
                        </div>  
                         <div id="divForUser" class="text-center">
                            <a id="user" data-toggle="modal" class="btn btn-primary btn-sm" href="index.html#modal-form-user"></a>
                            <span id="userId" class="hide"></span>
                        </div>                         
                    </div>
                </nav>
            </div>
            <div class="row J_mainContent" id="content-main">
                <iframe id="J_iframe" width="100%" height="100%" src="<?php echo U('index_v1');?>" frameborder="0" data-id="index_v1.html" seamless></iframe>
            </div>
        </div>
        <!--右侧部分结束-->
    </div>

    <!-- 登录toggle页面 -->
   <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-8 b-r">
                            <h3 class="m-t-none m-b">登录</h3>
                            <div role="form">
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
                                    <span id='hidden-identify' class='hide'>index</span>
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

    <!-- 用户信息及登出界面 -->
     <div id="modal-form-user" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-8 b-r">
                            <h3 class="m-t-none m-b">用户信息</h3>
                            <div role="form">
                                <div class="form-group">
                                    <label>用户名：</label> <span id='user_nickname'></span>                                    
                                </div>
                                <div class="form-group">
                                    <label>真实姓名：</label> <span id='user_name'></span>
                                </div>
                                <div class="form-group">
                                    <label>工作单位：</label> <span id='user_unit'></span>
                                </div>
                                <div class="form-group">
                                    <label>职务：</label> <span id='user_position'></span>
                                </div>
                                <div>
                                    <button id='logoutBtn' class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>退出登录</strong>
                                    </button>
                                </div>
                            </div>
                        </div>
                       <div class="col-sm-4">
                        <img src="/thinkphp/Public/img/tr.jpeg" alt="登录logo" class='img-responsive'>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 全局js -->
    <script src="/thinkphp/Public/js/jquery.min.js?v=2.1.4"></script>
    <script src="/thinkphp/Public/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/thinkphp/Public/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/thinkphp/Public/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/thinkphp/Public/js/plugins/layer/layer.min.js"></script>

    <!-- 自定义js -->
    <script src="/thinkphp/Public/js/hAdmin.js?v=4.1.0"></script>
    <script src="/thinkphp/Public/js/content.js"></script>
    <script type="text/javascript" src="/thinkphp/Public/js/index.js"></script>

    <!-- 第三方插件 -->
    <script src="/thinkphp/Public/js/plugins/pace/pace.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#divForUser').hide();

            $uid = "<?php echo ($userId); ?>";
            $uname = "<?php echo ($username); ?>"; 

            if ($uid != "") {
                $('#divForLogin').hide();
                $('#divForUser').show();                
                $('#user').html($uname);
                $('#userId').text($uid);
            }

            $('#searchForm').submit(function(e){
                $.ajax({
                    url:"/thinkphp/index.php/Home/Index/ajaxSearchHandler",
                    type:'post',
                    dataType:'json',
                    data:{
                        keyword:$('#top-search').val()
                    },
                    beforeSend: function() {
                        layerIndex = layer.msg('正在查询', {
                          icon: 16
                          ,time:2000000
                          ,shade: 0.01
                        });
                    },
                    success: function(json){                        
                        alert($('#top-search').val());
                        layer.close(layerIndex);
                    },
                    error: function(){
                        alert("error");
                        layer.close(layerIndex);
                    }
                });
                e.preventDefault();
            });
            
            
        });
    </script>

   

<div style="text-align:center;">
<p>来源:<a href="http://www.mycodes.net/" target="_blank">源码之家</a></p>
</div>
</body>

</html>