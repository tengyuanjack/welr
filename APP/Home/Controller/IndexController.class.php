<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class IndexController extends Controller {
    public function index(){    
    	// phpinfo();	
        // p($_SESSION); 
        // $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");         
        // $arr = M("dictionary")->select();
        // foreach ($arr as $val) {
        //     $str = trim($val['chname']);
        //     if (strlen($str) > 3) {
        //         fwrite($myfile, $str."\n");
        //     }
        // }
        // fclose($myfile);        

        // getSingleLayerList(1, 1, 11);
        // $arr = recursiveLayer(9,2,1,1456);
        // p($arr);
        // die;



        $this->userId = $_SESSION[C('USER_AUTH_KEY')];
        $this->username = $_SESSION[C('USER_AUTH_NAME')];
        $this->display();
    }

    public function index_v1(){
        $this->needLogin = I("needLogin");        
        $exp = M('exp')->where('id=1')->find();
        $this->exp = $exp['exp_type'];
    	$this->display();
    }

    public function show() {
    	echo 111111;
    }

    /**
     * ajax登录
     */
    public function login() {        
        $userArr = M('user')->where('nickname="'.I('nickname').'"')->select();        
        $status = 1;
        $info = "数据异常，请联系管理员！";
        if (count($userArr) == 0) {            
            $info = "用户名不存在！";
        }else {
            $user = $userArr[0];
            if (md5(I("password")) != $user["password"]) {
                $info = "密码出错，请重新输入！";               
            } else {
                session(C("USER_AUTH_KEY"), $user["id"]);
                session(C("USER_AUTH_NAME"), $user["nickname"]);

                if (C("RBAC_SUPERADMIN") == $user["nickname"]) {
                    session(C("ADMIN_AUTH_KEY"), true);
                }

                RBAC::saveAccessList();

                $status = 0;
                $info = "";
                $data['user'] = $user;
            }       
        }
        $data['status'] = $status;
        $data['info'] = $info;
        $this->ajaxReturn($data);
    }

    /**
     * ajax退出登录
     */
    public function logout() {        
        session_unset();
        session_destroy();
        $this->ajaxReturn('');
    }

    /**
     * ajax获取用户信息
     */
    public function userInfo() {
        $userArr = M('user')->where('id='.I('id'))->select();        
        $data['status'] = 1;
        if (count($userArr) != 0) {
            $data['status'] = 0;
            $data['user'] = $userArr[0];
        }
        $this->ajaxReturn($data);
    }

    public function ajaxSearchHandler() {
        $keyword = I("keyword");
        $data['keyword'] = $keyword;
        $this->ajaxReturn($data);
    }

    public function searchResult() {      
        $this->keyword = I("keyword");
        $this->display();
    }


    public function ajaxSetExperiment(){
        $experiment = I('experiment');        
        $data['exp_type'] = $experiment;
        M('exp')->where('id=1')->save($data);        
        $this->ajaxReturn($data);
    }

    public function modifyPassword() {
        $this->userId = session(C('USER_AUTH_KEY'));
        $this->userName = session(C('USER_AUTH_NAME'));

        $this->display();
    }

    public function modifyPasswordHandler() {
        $id = I('id');
        $oldPassword = I('oldPassword');
        $newPassword = I('newPassword');
        $repeatNewPassword = I('repeatNewPassword');
        if (empty($oldPassword) || empty($newPassword) || empty($repeatNewPassword)) {
            $this->error('原密码与新密码都不能为空！');
        }
        if ($newPassword != $repeatNewPassword) {
            $this->error('两次密码输入不一致，请重新输入！');
        }
        $user = M('user')->where('id='.$id)->find();
        if (md5($oldPassword) != $user["password"]) {
            $this->error("原密码出错，请重新输入！");     
        }
        $data['password'] = md5($newPassword);
        M('user')->where('id='.$id)->save($data);
        $this->success("密码修改成功！",U('index_v1'));
    }





}