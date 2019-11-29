<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title> - 源代码可追溯低级需求</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico"> <link href="/thinkphp/Public/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/thinkphp/Public/css/font-awesome.css?v=4.4.0" rel="stylesheet">

    <!-- jqgrid-->
    <link href="/thinkphp/Public/css/plugins/jqgrid/ui.jqgrid.css?0820" rel="stylesheet">

    <link href="/thinkphp/Public/css/animate.css" rel="stylesheet">
    <link href="/thinkphp/Public/css/style.css?v=4.1.0" rel="stylesheet">
    <link rel="stylesheet" href="/thinkphp/Public/css/jquery.orgchart.css">

    <style>
        #chart-container {
          height: 500px;          
          /*border: 2px solid #aaa;*/
        }
         .orgchart {
          background: #fff;
        }
        .num-info{
            font-size:20px;margin-left:3px;margin-right:3px;
        }

    </style>

</head>

<body class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>检查源代码可追溯到低级需求</h5>
                    </div>
                                        
                    <div class="ibox-content">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">项目选择</label>
                                <div class="col-sm-5">
                                    <select class="form-control m-b" name="project" >
                                        <?php if(is_array($project)): foreach($project as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <button class="btn btn-primary" id="btnCheck">检查</button>
                                </div>
                            </div>                                
                        </form>
                    </div>
                </div>
            </div>
        </div> 
        <div class="row" id="resultDiv" style="display:none;">
            <div class="col-sm-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>检查详情</h5>
                    </div>
                                        
                    <div class="ibox-content">
                        <div id="chart-container"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>统计</h5>
                    </div>
                                        
                    <div class="ibox-content">
                        <div id="statistic">
                            <h4>跟踪关系中共有<span id="num1" class="text-info num-info"></span>个源代码文件，共有<span id="num2" class="text-info num-info"></span>个低级需求</h4>
                            <h4>数据库中共有<span id="num3" class="text-info num-info"></span>个源代码文件，共有<span id="num4" class="text-info num-info"></span>个低级需求</h4>
                            <h4>跟踪关系覆盖率：<span id="num6" class="text-warning num-info"></span>&nbsp;&nbsp;&nbsp;&nbsp;系统需求覆盖率：<span id="num5" class="text-warning num-info"></span></h4>
                        </div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>检查结果</h5>
                    </div>
                                        
                    <div class="ibox-content">
                        <h4 id="checkResult"></h4>    
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>表格下载</h5>
                    </div>
                                        
                    <div class="ibox-content">
                        <form action="<?php echo U('Audit/downloadInspectTable',array('type'=>'sc2ll'));?>" method="post">
                            <input type="hidden" id="orgchart_data" name="orgChartData"></input>
                            <input type="hidden" id="projectId_data" name="projectIdData"></input>
                            <button id='btnDownload' class="btn btn-primary btn-lg center-block">适航审定检查单下载</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>         
    </div>

    <!-- 全局js -->
    <script src="/thinkphp/Public/js/jquery.min.js?v=2.1.4"></script>
    <script src="/thinkphp/Public/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/thinkphp/Public/js/plugins/layer/layer.min.js"></script>  
    <!-- Peity -->
    <script src="/thinkphp/Public/js/plugins/peity/jquery.peity.min.js"></script>
    
    <!-- orgChart -->
    <script src="/thinkphp/Public/js/jquery.orgchart.js"></script>

    <!-- 自定义js -->
    <script src="/thinkphp/Public/js/content.js?v=1.0.0"></script>

    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function () {
            $('#btnCheck').click(function(e) {                
                var layerIndex;
                $.ajax({
                    url:"/thinkphp/index.php/Home/Audit/ajaxScToLlHandler",
                    type:'post',
                    dataType:'json',
                    cache:false,
                    data:{
                        projectId:$('select[name=project]').val()
                    },
                    beforeSend: function() {
                        layerIndex = layer.msg('需求检查中', {
                          icon: 16
                          ,time:2000000
                          ,shade: 0.01
                        });
                    },
                    success: function(json) {
                        // 显示orgchart
                        $('#chart-container').html("");
                        $('#chart-container').orgchart({                            
                            'data' : json.data,
                            'nodeContent': 'content',                               
                            'direction': 'l2r',                               
                            'createNode': function($node, data) {
                                $node.on('click', function(event) {
                                    if (!$(event.target).is('.edge')) {                    
                                        $('#selected-node').val(data.name).data('node', $node);
                                        // 在此处添加点击节点后的操作
                                        layer.open({
                                            type:0,
                                            title:data.name,
                                            closeBtn:0,
                                            btn:[],
                                            shade:0.2,
                                            content: data.content,
                                            skin:'layui-layer-lan',
                                            shadeClose: true,
                                        });
                                    }
                                });
                            }                   
                        });  
                        $jsonDataStr = JSON.stringify(json.data);                        
                        $('#orgchart_data').val($jsonDataStr);
                        $('#projectId_data').val($('select[name=project]').val());

                        $('#num1').text(json.num1);
                        $('#num2').text(json.num2);
                        $('#num3').text(json.num3);
                        $('#num4').text(json.num4);
                        $('#num5').text(json.num5);
                        $('#num6').text(json.num6);
                        if (json.isTraceable) {
                            $('#checkResult').text("所有高级需求都可追溯到系统需求");
                        } else {
                            $('#checkResult').text("不是所有高级需求都可追溯到系统需求");
                        }
                        $('#resultDiv').addClass("animated");
                        $('#resultDiv').addClass("bounceInLeft");
                        $('#resultDiv').attr("style","");

                        layer.close(layerIndex);
                    },
                    error: function() {
                        layer.close(layerIndex);  
                    }

                });
                    
                e.preventDefault(); 
            });
        });
    </script>

</body>

</html>