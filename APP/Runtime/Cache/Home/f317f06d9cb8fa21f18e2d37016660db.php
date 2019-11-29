<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title> - jqGird</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico"> <link href="/thinkphp/Public/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/thinkphp/Public/css/font-awesome.css?v=4.4.0" rel="stylesheet">

    <!-- jqgrid-->
    <link href="/thinkphp/Public/css/plugins/jqgrid/ui.jqgrid.css?0820" rel="stylesheet">

    <link href="/thinkphp/Public/css/animate.css" rel="stylesheet">
    <link href="/thinkphp/Public/css/style.css?v=4.1.0" rel="stylesheet">

</head>

<body class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox ">                        
                        <div class="jqGrid_wrapper">
                            <table id="table_list_2"></table>
                            <div id="pager_list_2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 全局js -->
    <script src="/thinkphp/Public/js/jquery.min.js?v=2.1.4"></script>
    <script src="/thinkphp/Public/js/bootstrap.min.js?v=3.3.6"></script>



    <!-- Peity -->
    <script src="/thinkphp/Public/js/plugins/peity/jquery.peity.min.js"></script>

    <!-- jqGrid -->
    <script src="/thinkphp/Public/js/plugins/jqgrid/i18n/grid.locale-cn.js?0820"></script>
    <script src="/thinkphp/Public/js/plugins/jqgrid/jquery.jqGrid.min.js?0820"></script>

    <!-- 自定义js -->
    <script src="/thinkphp/Public/js/content.js?v=1.0.0"></script>

    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function () {

            $.jgrid.defaults.styleUI = 'Bootstrap';
            
            // Configuration for jqGrid Example 2
            $("#table_list_2").jqGrid({
                // data: mydata,
                datatype: "json",
                url: "<?php echo U('listArtifactData');?>",      
                editurl:"<?php echo U('editArtifactData');?>",          
                mtype: "POST",
                height: 550,
                autowidth: true,
                shrinkToFit: true,
                rowNum: 20,
                rowList: [10, 20, 30],
                colNames: ['序号', '项目', '所属生命周期', '名称', '内容'],
                colModel: [
                    {
                        name: 'id',
                        index: 'id',
                        editable: false,
                        width: 30,
                        sortable: false,
                        search: false
                    },
                    {
                        name: 'project',
                        index: 'project',
                        editable: true,
                        width: 60,
                        sortable: false
                    },
                    {
                        name: 'lifecycle',
                        index: 'lifecycle',
                        editable: true,
                        width: 60,
                        sortable: false
                    },
                    {
                        name: 'name',
                        index: 'name',
                        editable: true,
                        width: 60,
                        sortable: false
                    },
                    {
                        name: 'content',
                        index: 'content',
                        editable: true,
                        width: 200,
                        sortable: false
                    }
                ],
                pager: "#pager_list_2",
                viewrecords: true,
                caption: "软件制品数据",
                add: true,
                edit: true,
                addtext: 'Add',
                edittext: 'Edit',
                hidegrid: false
            });

            // Add selection
            $("#table_list_2").setSelection(4, true);


            // Setup buttons
            $("#table_list_2").jqGrid('navGrid', '#pager_list_2', {
                edit: false,
                add: false,
                del: true,
                search: false                
            }, 

            {
                height: 200,
                reloadAfterSubmit: true
            });

            //$("#table_list_2").jqGrid('searchGrid',{sopt:['cn','eq','ne']});

            // Add responsive to jqGrid
            $(window).bind('resize', function () {
                var width = $('.jqGrid_wrapper').width();
                $('#table_list_1').setGridWidth(width);
                $('#table_list_2').setGridWidth(width);
            });
        });
    </script>

</body>

</html>