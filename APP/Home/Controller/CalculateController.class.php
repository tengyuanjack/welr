<?php
namespace Home\Controller;
use Think\Controller;
class CalculateController extends CommonController {
    public function index(){    
        $this->display();
    }
    public function welr() {        
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) {             
            $this->project = M("project")->select();
            $this->content = M("artifact")->select();
        } else {            
            $this->project = M("project")->where("isForExperiment!=1")->select();
            $proIdArr = [];
            foreach ($this->project as $val) {
                $proIdArr[] = $val['id'];
            }            
        }

        $arr = M("artifact")->field("distinct class")->where("pid=".$this->project[0]['id'])->order('class asc')->select();
        $lifecycleIds = array();
        foreach ($arr as $v) {
            $lifecycleIds[] = $v["class"];
        }
        $map["id"] = array('in', $lifecycleIds);
        $this->lifecycle = M("lifecycle")->where($map)->select();
              
        $this->content = M("artifact")->where('pid='.$this->project[0]['id'].' and class='.$this->lifecycle[0]['id'])->select();
        
        $this->display();
    }

    // 分词handler
    public function partitionHandler() {
        $projectId = I("project");
        $artifactArr = M("artifact")->where(array("pid"=>$projectId))->select();
        $chData = [];
        foreach ($artifactArr as $val) {
            $chData[$val["id"]] = $val["content"];
        }
        $chData = json_encode($chData,JSON_UNESCAPED_UNICODE);        
        $cmd = 'python '.C('PYTHON_SCRIPT_DIR').'partition/partition.py \''.$chData.'\'';
        exec($cmd, $rst, $ret);       
        // p($cmd); 
        // foreach ($rst as $v) {                
        //        p($v);
        // } die;  
        if ($ret == 0) {
            $splitedChJson = json_decode($rst[0]);
            $enData = [];
            foreach ($splitedChJson as $key => $val) {
                $enRst = [];
                $lineArr = explode(" ", $val);
                foreach ($lineArr as $v) {
                    if (empty($v)) {
                        continue;
                    }
                    $en = translate($v);
                    $enRst[] = $en;
                    // p($key." ".$v." ".$en);
                }
                $enData[$key] = implode(" ", $enRst);
            }
            // p($enData);           
            M("translation")->where(array("pid"=>$projectId))->delete();           
            foreach ($enData as $key => $val) {
                $data["aid"] = $key;
                $data["pid"] = $projectId;
                $data["words"] = $val;
                M("translation")->add($data);
            }
            $this->success("分词并翻译成功");
            
        } else {
            $this->error("分词失败，请检查错误");
        }
    }

    // 预处理handler
    public function preprocess() {        
        // $str = "fail-operative 'FO) demand";
        // p(removePunc($str));die;
        $projectId = I("project");        
        $artifactArr = M("artifact")->where(array("pid"=>$projectId))->select();

        $data = [];
        foreach ($artifactArr as $val) {           
            $enArr = M("translation")->where(array("aid"=>$val["id"]))->find(); 
            $data[$val["id"]] = $enArr["words"];
        }
        $data = json_encode($data);        
        $cmd = 'python '.C('PYTHON_SCRIPT_DIR').'welr/script_preprocess.py '.$projectId.' \''.$data.'\'';
        // p($cmd);
        exec($cmd, $rst, $ret);
                
        // foreach ($rst as $v) {                
        //     p($v);
        // } die;         
        if ($ret == 0) {
            $this->success("预处理完成！");
        } else {
            $this->error("预处理出错，请检查！");
        }
        
        
    }

    // 计算
    public function calculate() {
        $projectId = I("project");
        $lifecycleId = I("lifecycle");
        $contentId = I("content");
        
        $targetArr = M("translation")->where("pid = ".$projectId." and aid!=".$contentId)->select();
        $targetData = [];
        foreach ($targetArr as $val) {
            $targetData[$val["aid"]] = $val["words"];
        }
        $targetData = json_encode($targetData, JSON_UNESCAPED_UNICODE);
        $sourceArr = M("translation")->where("aid=".$contentId)->find();
        
        $cmd = 'python '.C('PYTHON_SCRIPT_DIR').'welr/script_calculate.py '.$projectId.' '.$contentId.' \''.$sourceArr["words"].'\' \''.$targetData.'\'';
        // p($cmd);
        exec($cmd, $rst, $ret);
        // foreach ($rst as $v) {                
        //     p($v);
        // } die;  
        if ($ret == 0) {
            $this->success("计算完成！", U("displayResult", array("contentId"=>$contentId, "projectId"=>$projectId)));
        } else {
            $this->error("计算过程出错，请检查！");
        }
    }

    public function displayResult() {
        $contentId = I("contentId");
        $projectId = I("projectId");
        $arr = M("text_similarity")->where(array("source"=>$contentId, "dataset"=>$projectId))->select();
        $this->rowNum = count($arr);
        $targetArr = [];
        foreach ($arr as $v) {
            $item = [];
            $item["similarity"] = floatval($v["similarity"]);
            $t = M("artifact")->where("id=".$v["target"])->find();
            $item["content"] = $t["content"];
            $targetArr[] = $item;
        }
        array_multisort(i_array_column($targetArr, "similarity"), SORT_DESC, $targetArr);
        $t = M("artifact")->where("id=".$contentId)->find();
        $this->sourceStr = $t["content"];
        $this->firstTarget = $targetArr[0];
        $this->targetArr = array_slice($targetArr, 1);
        $this->display();
    }

    /**
     * 计算两文档关系
     */
    public function tupleRelation() {        
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) { 
            $this->project = M("project")->select();
        } else {
            $this->project = M("project")->where("isForExperiment!=1")->select();
        }
        $arr = M("artifact")->field("distinct class")->where("pid=".$this->project[0]['id'])->order('class asc')->select();
        $lifecycleIds = array();
        foreach ($arr as $v) {
            $lifecycleIds[] = $v["class"];
        }
        $map["id"] = array('in', $lifecycleIds);
        $this->lifecycle = M("lifecycle")->where($map)->select();
        $this->content = M("artifact")->where('pid='.$this->project[0]['id'].' and class='.$this->lifecycle[0]['id'])->select();
        $this->display();
    }

    public function ajaxTupleRelationHanlder() {
        $artId1 = I('artifactId1');
        $artId2 = I('artifactId2');
        if (empty($artId1) || empty($artId2)) {
            $data["error"] = "请选择文档后计算！";
        } else {
            $art1 = M("translation")->where('aid='.$artId1)->find();
            $art2 = M("translation")->where('aid='.$artId2)->find();
            if ($art1['pid'] != $art2['pid']) {
                $data["error"] = "请选择同一项目文档！";
            } else {
                $projectId = $art1['pid'];
                $cmd = 'python '.C('PYTHON_SCRIPT_DIR').'welr/script_calculateTuple.py '.$projectId.' '.$artId1.' \''.$art1["words"].' \' '.' '.$artId2.' \''.$art2["words"].'\'';        
                exec($cmd, $rst, $ret);
                $data["score"] = 0.00;
                $data["error"] = "计算出错~";
                if ($ret == 0) {
                    $data["score"] = round($rst[0] * 100, 2);
                    $art1Ch = M('artifact')->where('id='.$artId1)->find();
                    $art2Ch = M('artifact')->where('id='.$artId2)->find();
                    if ($art1Ch['class'] == $art2Ch['class']) {
                        $data["reference"] = "同级别文档无跟踪关系";
                    }else {
                        $data["reference"] = $rst[0] >= 0.72 ? "具有跟踪关系" : "不具有跟踪关系";
                    }
                    $data["error"] = "";
                } 
            }
        }
        $this->ajaxReturn($data);
    }

    /**
     * 层级跟踪关系， 即根据当前所处软件生命周期，能够计算某一需求对应的数据集中下一级（或下n级）生命周期的结果（列出相似度列表，以图形的形式展示，n可以动态设置）
     */
    public function layerRelation() {
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) { 
            $this->project = M("project")->select();
        } else {
            $this->project = M("project")->where("isForExperiment!=1")->select();
        }
        $arr = M("artifact")->field("distinct class")->where("pid=".$this->project[0]['id'])->order('class asc')->select();
        $lifecycleIds = array();
        foreach ($arr as $v) {
            if (in_array($v["class"], C('DEFAULT_LIFECYCLE'))) {
                $lifecycleIds[] = $v["class"];
            }
        }
        $map["id"] = array('in', $lifecycleIds);        
        $this->lifecycle = M("lifecycle")->where($map)->select();
        $this->content = M("artifact")->where('pid='.$this->project[0]['id'].' and class='.$this->lifecycle[0]['id'])->select();
        $this->display();
    }

    

    public function test() {
        $a = recursiveLayer(1,3,1,9);
        // $json = json_encode($a);
        // p($json);
        // p($a);
        $b = listLayer($a);
        p($b);
    }

    /**
     * ajax 返回层级关系数据
     */
    public function ajaxLayerRelation() {
        $projectId = I('projectId');
        $lifecycleId = I('lifecycleId');
        $artifactId = I('artifactId');
        $depth = I('depth');

        $key = array_search($lifecycleId, C('DEFAULT_LIFECYCLE'));
        if (($key + $depth) >= count(C("DEFAULT_LIFECYCLE"))) {        
            $data['status'] = 0;
            $data['error'] = "所选深度值超过范围，请重新选择！";
        }else {
            $data['status'] = 1;
            $data['error'] = '';
            $rst = recursiveLayer($projectId, $depth, 1, $artifactId);
            $data["data"] = $rst; 
            // 为跟踪矩阵生成数据， 
            $data["matrix"] = listLayer($rst);
        }

        $this->ajaxReturn($data);
    }

    public function ajaxGetContentById() {
        $str = I("cellValue");    // 举例： 功能细化<span class='hide'>15</span>                
                     
        $str = htmlspecialchars_decode($str);
        $index = strpos($str,"'hide'>");
        $id = substr($str, $index+7, -7);
        $art = M("artifact")->where('id='.$id)->find();
        $data["name"] = $art["name"];
        $data["content"] = $art["content"];          
        $this->ajaxReturn($data);

    }










}