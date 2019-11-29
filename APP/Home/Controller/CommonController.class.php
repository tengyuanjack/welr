<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class CommonController extends Controller {
    public function _initialize(){            
        // // ini_set('date.timezone', 'Asia/Shanghai');
        $flag1 = isset($_SESSION[C("USER_AUTH_KEY")]);        
        if(!$flag1){
            $this->redirect('Index/index_v1',array("needLogin"=>true));
        }        

        //RBAC
        // p("MODULE_NAME: ".MODULE_NAME." CONTROLLER_NAME: ".CONTROLLER_NAME."  ACTION_NAME: ".ACTION_NAME);
        // p($_SESSION);
        $access = RBAC::getAccessList(3);
        // p($access);
        $notAuth = in_array(MODULE_NAME, explode(',',C('NOT_AUTH_MODULE'))) ||
                    in_array(ACTION_NAME, explode(',',C('NOT_AUTH_ACTION')));
        if(C("USER_AUTH_ON") && !$notAuth){
            
            RBAC::AccessDecision() || $this->error("您没有权限");
            // RBAC::AccessDecision();            
        }
        
    }

    /**
     * 根据选择的项目，得到其下的所有生命周期
     */
    public function ajaxSelectLifecycle() {
        $projectId = I("projectId");  
        $identify = I("identify");      
        $arr = M("artifact")->field("distinct class")->where("pid=".$projectId)->order('class asc')->select();
        $lifecycleIds = array();
        foreach ($arr as $v) {
            if ($identify == "layer") {
                if (in_array($v["class"], C("DEFAULT_LIFECYCLE"))) {
                    $lifecycleIds[] = $v["class"];
                }
            } else {
                $lifecycleIds[] = $v["class"];
            }
        }
        $map["id"] = array('in', $lifecycleIds);
        $lifecycle = M("lifecycle")->where($map)->select();
        $this->ajaxReturn($lifecycle);
    }

    /**
     * 根据选择的项目和生命周期，得到其下的所有制品
     */
    public function ajaxSelectArtifact() {
        $projectId = I("projectId");       
        $lifecycleId = I("lifecycleId");        
        $artifact = M("artifact")->where(array('pid'=>$projectId, 'class'=>$lifecycleId))->order('ctime asc')->select();        
        $this->ajaxReturn($artifact);
    }


}