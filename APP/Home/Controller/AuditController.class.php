<?php
namespace Home\Controller;
use Think\Controller;
class AuditController extends CommonController {

    public function index(){
        $this->display();
    }

    public function hlReqToSysReq() {
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) { 
            $this->project = M("project")->select();
        } else {
            $this->project = M("project")->where("isForExperiment!=1")->select();
        }
        $this->display();
    }

    public function ajaxHlToSysHandler() {
        $projectId = I("projectId");
        $data = $this->ajaxHandler($projectId,  C('DEFAULT_LIFECYCLE')[1],  C('DEFAULT_LIFECYCLE')[0]);
        $this->ajaxReturn($data);
    }    

    public function llReqToHlReq() {        
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) { 
            $this->project = M("project")->select();
        } else {
            $this->project = M("project")->where("isForExperiment!=1")->select();
        }
        $this->display();
    }

    public function ajaxLlToHlHandler() {
        $projectId = I("projectId");
        $data = $this->ajaxHandler($projectId,  C('DEFAULT_LIFECYCLE')[2],  C('DEFAULT_LIFECYCLE')[1]);
        $this->ajaxReturn($data);
    }    

    public function scToLlReq() {
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) { 
            $this->project = M("project")->select();
        } else {
            $this->project = M("project")->where("isForExperiment!=1")->select();
        }
        $this->display();
    }

    public function ajaxScToLlHandler() {
        $projectId = I("projectId");
        $data = $this->ajaxHandler($projectId,  C('DEFAULT_LIFECYCLE')[3],  C('DEFAULT_LIFECYCLE')[2]);
        $this->ajaxReturn($data);
    }    

    /**
     * 三种情况下 ajax统一处理函数
     * $from 前一个需求  $to 后一个需求  如： 高级需求追溯到系统需求， $from 为 高级需求 $to为 系统需求
     */
    private function ajaxHandler($projectId, $from, $to) {

        $hlArtifactArr = M('artifact')->where(array("pid"=>$projectId, "class"=>$from))->select();
        $resultArtifact = array("id"=>-1, "name"=>"Psudo Root", "content"=>"Psudo Root", "children"=>array());
        $sysArr = [];
        $isTraceable = 1;
        $nonTraceFrom = 0;        
        foreach ($hlArtifactArr as $val) {
            $rst = getSingleLayerList($projectId, $to, $val["id"]);
            foreach ($rst as $k => $v) {
                $art = M("artifact")->where('id='.$k)->find();
                $art["score"] = $v;
                $val["children"][] = $art;
                if (!testInArray($art, $sysArr)) {
                    $sysArr[] = $art;   
                }
            }
            if (count($val["children"]) == 0) {
                $isTraceable = 0;
                $nonTraceFrom += 1;
            }
            $resultArtifact["children"][] = $val;
        }
        $data["data"] = $resultArtifact;
        $data["num1"] = count($hlArtifactArr);
        $data["num2"] = count($sysArr);
        $data["num3"] = M("artifact")->where(array("pid"=>$projectId, "class"=>$from))->count();
        $data["num4"] = M("artifact")->where(array("pid"=>$projectId, "class"=>$to))->count();
        if ($data["num4"] != 0) {
            $data["num5"] = (round((float)$data["num2"] / $data["num4"], 2) * 100)."%";
        }else {
            $data["num5"] = "NAN";
        }
        if ($data["num1"] != 0) {
            $data["num6"] = (round((float)($data["num1"]-$nonTraceFrom) / $data["num1"], 2) * 100)."%";
        } else {
            $data["num6"] = "NAN";
        }
        $data["isTraceable"] = $isTraceable;
        return $data;
    }

    /**
     * 下载检查单
     */
    public function downloadInspectTable() {
        $orgChartData = I("orgChartData");
        $projectId = I("projectIdData");
        $type = $_GET["type"];        
        $orgChartData = htmlspecialchars_decode($orgChartData);
        $orgChartDataArr = json_decode($orgChartData, true);              
        $resultArr = array();
        $count = 0;
        foreach ($orgChartDataArr['children'] as $val) {
            $single['source'] = '【'.$val['id'].'】'.$val['name'];
            $single['relation'] = count($val['children']) > 0 ? '是 Yes' : '否 No';             
            foreach ($val['children'] as $v) {
                $single['dest'][] = '【'.$v['id'].'】'.$v['name'];
            }
            $resultArr[] = $single;
            $count += (count($single['dest']) > 0 ? count($single['dest']) : 1);
        }        
        $project = M('project')->where('id='.$projectId)->find();        
        $sheetName = "";
        $title1 = "";
        $title2 = "";
        $title3 = "";
        switch ($type) {
            case 'hl2sys':
                $sheetName = "高级需求-系统需求";
                $title1 = "高级需求\nHigh-level Requirement";
                $title2 = "是否可追溯到系统需求\nWether Can Be Traced";
                $title3 = "相关系统需求\nRelated System Requirement";
                break;
            case 'll2hl':
                $sheetName = "低级需求-高级需求";
                $title1 = "低级需求\nLow-level Requirement";
                $title2 = "是否可追溯到高级需求\nWether Can Be Traced";
                $title3 = "相关高级需求\nRelated High-level Requirement";
                break;
            case 'sc2ll':
                $sheetName = "源代码-低级需求";
                $title1 = "源代码文件\nSource Code Files";
                $title2 = "是否可追溯到低级需求\nWether Can Be Traced";
                $title3 = "相关低级需求\nRelated Low-level Requirement";
                break;
            default:
                # code...
                break;
        }
        writeExcel($resultArr, $count, $sheetName, $project["name"], $title1, $title2, $title3);

    }








}