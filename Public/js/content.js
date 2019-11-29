// var $parentNode = window.parent.document;

// function $childNode(name) {
//     return window.frames[name]
// }

// // tooltips
// $('.tooltip-demo').tooltip({
//     selector: "[data-toggle=tooltip]",
//     container: "body"
// });

// // 使用animation.css修改Bootstrap Modal
// $('.modal').appendTo("body");

// $("[data-toggle=popover]").popover();


// //判断当前页面是否在iframe中
// if (top == this) {
//     var gohome = '<div class="gohome"><a class="animated bounceInUp" href="index.html?v=4.0" title="返回首页"><i class="fa fa-home"></i></a></div>';
//     $('body').append(gohome);
// }

//animation.css
function animationHover(element, animation) {
    element = $(element);
    element.hover(
        function () {
            element.addClass('animated ' + animation);
        },
        function () {
            //动画完成之前移除class
            window.setTimeout(function () {
                element.removeClass('animated ' + animation);
            }, 2000);
        });
}

//拖动面板
function WinMove() {
    var element = "[class*=col]";
    var handle = ".ibox-title";
    var connect = "[class*=col]";
    $(element).sortable({
            handle: handle,
            connectWith: connect,
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            opacity: 0.8,
        })
        .disableSelection();
};


$('#loginBtn').click(function(e) {
    $nickname = $.trim($('input[name=nickname]').val());
    $password = $.trim($('input[name=password]').val());
    if ($nickname == "" || $password == "") {
       layer.confirm('用户名或密码不能为空！', {
          btn: ['确定'] //按钮
        });
        return false;
    }    
    $.ajax({
            url:"/thinkphp/index.php/Home/Index/login",
            type:'post',
            dataType:'json',
            data:{
                nickname:$nickname, 
                password:$password
            },
            success:function(json){     
                // status = 1, 登录信息有误
                if(json.status == '1') {
                    layer.confirm(json.info, {
                      btn: ['确定'] //按钮
                    });
                } else {
                    // 登录成功
                    $('#modal-form').modal('toggle');
                    
                    $page = $('#hidden-identify').text();                    
                    if ($page == 'index') {
                        $('#divForLogin').hide();
                        $('#divForUser').show();
                        $('#user').html(json.user.nickname);
                        $('#userId').html(json.user.id);
                    } else if ($page == 'index_v1') {
                        $('#divForLogin', window.parent.document).hide();
                        $('#divForUser', window.parent.document).show();
                        $('#user', window.parent.document).html(json.user.nickname);
                        $('#userId', window.parent.document).html(json.user.id);
                    }
                }
            },
            error:function(){
                // do nothing        
            }

        });    
});

$('#user').click(function() {
    $.ajax({
        url:"/thinkphp/index.php/Home/Index/userInfo",
        type:'post',
        dataType:'json',
        data:{id:$('#userId').text()},
        success: function(json){
            if (json.status == '0') {
                $('#user_nickname').text(json.user.nickname);
                $('#user_name').text(json.user.name);
                $('#user_unit').text(json.user.unit);
                $('#user_position').text(json.user.position);
            }
        },
        error: function(){
            // do nothing
        }
    });
});

$('#logoutBtn').click(function() {
    $.ajax({
        url:"/thinkphp/index.php/Home/Index/logout",
        type:'post',            
        success: function(){
            $('#divForLogin').show();
            $('#divForUser').hide();
            $('#user').html('');
            $('#userId').html('');
            $('#modal-form-user').modal('toggle');
            $('#J_iframe').attr('src',"index.php/Home/Index/index_v1");
        },
        error: function(){
            // do nothing
        }
    });
});

$('#modifyPasswordBtn').click(function() {    
    $('#modal-form-user').modal('toggle');
    $('#J_iframe').attr('src',"index.php/Home/Index/modifyPassword");
});


$('select[id*=selProject]').change(function() {
    $num = this.id.charAt(this.id.length - 1);
    $projectId = $(this).val();
    $identify = $(this).attr('identify');    
    $.ajax({
        url:"/thinkphp/index.php/Home/Common/ajaxSelectLifecycle",
        type:'post',
        dataType:'json',
        data:{
            projectId:$projectId,
            identify:$identify
        },
        success: function(json){
            var msg = '';
            $.each(json, function(index, val){
                msg += '<option value="'+val['id']+'">'+val['name']+'</option>';
            });            
            $('#selLifecycle'+$num).html(msg);
            $('#selLifecycle'+$num).trigger("change");
        },
        error: function(){
            $('#selLifecycle'+$num).html('');
            $('#selContent'+$num).html('');
        }
    });
});

$('select[id*=selLifecycle]').change(function() {    
    $num = this.id.charAt(this.id.length - 1);
    $projectId = $('#selProject'+$num).val();
    $lifecycleId = $(this).val();
    $.ajax({
        url:"/thinkphp/index.php/Home/Common/ajaxSelectArtifact",
        type:'post',
        dataType:'json',
        data:{
            projectId:$projectId,
            lifecycleId:$lifecycleId
        },
        success: function(json){
            var msg = '';
            $.each(json, function(index, val){
                msg += '<option value="'+val['id']+'">'+val['content']+'</option>';
            });            
            $('#selContent'+$num).html(msg);
        },
        error: function(){
            $('#selContent'+$num).html('');
        }
    });
});



