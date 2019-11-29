<?php
namespace Home\Controller;
use Think\Controller;
class RbacController extends CommonController {
    public function index(){    
    	
        $this->display();
    }

    public function userManage() {
        $this->users = M('user')->select();
        $this->roles = M('role')->select();       
        $this->display();
    }

    public function addUserHandler() {
        $nickname = I('nickname');
        $name = I('name');
        if (empty($nickname) || empty($name)) {
            $this->error("用户名和真实姓名不能为空！");
        }
        $data['nickname'] = I('nickname');
        $data['name'] = I('name');
        $data['unit'] = I('unit');
        $data['position'] = I('position');
        $data['password'] = md5("123456");
        $uid = M('user')->add($data);
        if (!$uid) {
            $this->error("添加用户出错，请检查网络连接或联系管理员！");
        }
        $data2['user_id'] = $uid;
        $data2['role_id'] = I('userRole');
        if (!M('role_user')->add($data2)) {
            $this->error("添加用户出错，请检查网络连接或联系管理员！");
        }
        $this->success("添加用户成功！");
    }

    public function modifyUser(){
        $id = I('id');
        $this->user = M('user')->where('id='.$id)->find();
        $this->roles = M('role')->select();       
        $this->role_user = M('role_user')->where('user_id='.$this->user['id'])->find();
        $this->display();
    }

    public function modifyUserHandler(){
        $id = I('id');        
        $nickname = I('nickname');
        $name = I('name');
        if (empty($nickname) || empty($name)) {
            $this->error("用户名和真实姓名不能为空！");
        }
        $data['nickname'] = I('nickname');
        $data['name'] = I('name');
        $data['unit'] = I('unit');
        $data['position'] = I('position');        
        M('user')->where('id='.$id)->save($data);        
        $data2['user_id'] = $id;
        $data2['role_id'] = I('userRole');
        M('role_user')->where('user_id='.$id)->delete();
        if (!M('role_user')->add($data2)) {
            $this->error("修改用户出错，请检查网络连接或联系管理员！");
        }
        $this->success("修改用户成功！", U('userManage'));
    }

    public function deleteUser() {
        $id = I('id');
        M('user')->where('id='.$id)->delete();
        M('role_user')->where('user_id='.$id)->delete();
        $this->success("删除用户成功！");
    }

    public function roleManage() {
        $this->roles = M('role')->select();
        $this->display();
    }

    public function addRoleHandler() {
        $name = I('name');
        if (empty($name)) {
            $this->error("角色名称不能为空！");
        }
        $data['name'] = I('name');
        $data['remark'] = I('remark');
        $data['status'] = 1;
        if(!M('role')->add($data)) {
            $this->error("添加角色出错，请检查网络连接或联系管理员！");
        }
        $this->success("添加角色成功！");
    }

    public function modifyRole() {
        $id = I('id');
        $this->role = M('role')->where('id='.$id)->find();
        $this->display();
    }
    public function modifyRoleHandler() {
        $id = I('id');
        $name = I('name');
        if (empty($name)) {
            $this->error("角色名称不能为空！");
        }
        $data['name'] = I('name');
        $data['remark'] = I('remark');
        $data['status'] = 1;
        if(!M('role')->where('id='.$id)->save($data)) {
            $this->error("修改角色出错，请检查网络连接或联系管理员！");
        }
        $this->success("修改角色成功！", U('roleManage'));
    }

    public function deleteRole() {
        $id = I('id');
        M('role')->where('id='.$id)->delete();
        M('role_user')->where('role_id='.$id)->delete();
        $this->success("删除用户成功！");
    }


    public function assignAccess() {
        $rid = I('role_id');
        $map = array();
        //如果是超级管理员，则有rbac选项；否则就没有
        
        if(C('RBAC_SUPERADMIN') != session(C("USER_AUTH_NAME"))){
            $rbac_node_id = M('node')->where(array('name'=>'Rbac'))->getField('id');
            $map['id'] = array('neq',$rbac_node_id);
            $arr = M('node')->where('pid='.$rbac_node_id)->getField('id',true);
            $map['pid'] = array('not in',$arr);
        }
        $node = M('node')->field('id,remark,sort,pid,level')->where($map)->select();
        

        $access = M('access')->where('role_id='.$rid)->getField('node_id',true);
        $node = node_merge($node,$access);
        $this->node = $node;
        $this->rid = $rid;
        $this->role_name = M('role')->where('id='.$rid)->getField('remark');
        $this->display();
    }

    public function assignAccessHandler() {
        //清除原有权限
        $count = M('access')->where('role_id='.$_POST['role_id'])->count();
        $res0 = M('access')->where('role_id='.$_POST['role_id'])->delete();
        if($res0 === false){
            $this->error("数据库连接失败，请联系管理员");
        }

        $data = array();
        foreach ($_POST['access'] as $v) {
            $tmp = explode('_', $v);
            $data[] = array(
                'role_id'=>$_POST['role_id'],
                'node_id'=>$tmp[0],
                'level'=>$tmp[1]
                );
        }
        if($data != null){
            $res = M('access')->addAll($data);
            if($res === false){
                $this->error("数据库连接失败，请联系管理员");
            }
        }
        
        $this->success('权限修改成功',U('Rbac/roleManage'));
    }

    
    public function nodeManage() {
        $node = M('node')->select();
        $node = node_merge($node);
        $this->node = $node;
        $this->display();
    }
   
    public function addNode() {
        $pid = I('pid', 0);
        $this->pNode = M('node')->where('id='.$pid)->find();
        $this->level = $this->pNode['level'] + 1;
        $this->display();
    }
    public function addNodeHandler() {       
        if(!M('node')->add($_POST)){
            $this->error('数据库连接失败，请联系管理员');
        }
        $this->success('保存节点成功',U('Rbac/nodeManage'));
    }
    public function alterNodeHandler() {
        $res = M('node')->where('id='.I('id'))->save($_POST);
        if($res === false){
            $this->error('数据库连接失败，请联系管理员');
        }
        $this->success('修改节点成功',U('Rbac/nodeManage'));
    }

    public function alterNode() {
        $id = I('id');
        $this->node = M('node')->where('id='.$id)->find();        
        $this->display();
    }

    public function deleteNode() {
        $this->error("删除节点功能已被禁用！");
        $id = I('id');
        if (!M('node')->where('id='.$id)->delete()) {
            $this->error("删除出错，请检查网络连接或联系管理员！");
        }
        $this->success("删除成功！");
    }
}