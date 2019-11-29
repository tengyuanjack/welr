<?php
namespace Home\Controller;
use Think\Controller;
class DataManageController extends CommonController {
    public function index(){    	
        $this->display();
    }

    public function editData() {
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) { 
        	$lifecycleArr = M("lifecycle")->select();
        	$this->lifecycle = $lifecycleArr;
        	$this->project = M("project")->select();
        } else {
            $lifecycleArr = M("lifecycle")->where("isForExperiment!=1")->select();
            $this->lifecycle = $lifecycleArr;
            $this->project = M("project")->where("isForExperiment!=1")->select();
        }
    	$this->display();
    }

    /**
     * 保存制品，同时，使用脚本对文本进行分词和翻译
     */
    public function artifactHandler() {
    	if (!IS_POST) {
    		E("非法提交！");
    	}

    	$data['class'] = I('lifecycle');
    	$data['pid'] = I('project');
        $data['name'] = trim(I('name'));
    	$data['content'] = trim(I('content'));

    	if (empty($data['name']) || empty($data['content'])) {
    		$this->error("名称或内容不能为空！");
    	}
        $newId = M("artifact")->add($data);
    	if (!$newId) {
    		$this->error('数据库连接失败，请联系管理员');
    	}

        // 分词并翻译
        $handledStr = str_replace("'", "", $data['content']);
        $handledStr = str_replace('"', '', $handledStr);
        $chData[$newId] = $handledStr;
        $chData = json_encode($chData,JSON_UNESCAPED_UNICODE);
        $cmd = 'python '.C('PYTHON_SCRIPT_DIR').'partition/partition.py \''.$chData.'\'';
        // p($cmd);        
        exec($cmd, $rst, $ret);        
        // foreach ($rst as $val) {
        //     p($val);
        // }die;
        if ($ret == 0) {
            $splitedChJson = json_decode($rst[0]);
            $enData = [];
            // p($splitedChJson);
            foreach ($splitedChJson as $key => $val) {
                $enRst = [];
                $lineArr = explode(" ", $val);
                foreach ($lineArr as $v) {
                    if (empty($v)) {
                        continue;
                    }
                    $en = translate($v);
                    $enRst[] = $en;
                }
                $enData[$key] = implode(" ", $enRst);
            }
            // p($enData);
            foreach ($enData as $key => $val) {
                $data1["aid"] = $key;
                $data1["pid"] = I('project');
                $data1["words"] = $val;
                M("translation")->add($data1);
            }            
            
        } else {
            $this->error("分词失败，请检查错误");
        }

    	$this->success('数据录入成功！');
    }

    public function displayArtifactData() {
    	$this->display();
    }

    public function listArtifactData() {
        $oriWhere = " where ";
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] != 1) { 
            $project = M("project")->where("isForExperiment!=1")->select();
            $proIdArr = [];
            foreach ($project as $val) {
                $proIdArr[] = $val['id'];
            }
            $oriWhere .= " pid in (". implode(',', $proIdArr) .") and ";
        }
    	$_search = I('_search');
    	$searchField = I('searchField');
    	$searchOper = I('searchOper');
    	$searchString = I('searchString');
    	if ($_search == 'true') {
    		$oriWhere .= '  '.assembleWhere($searchField, $searchOper, $searchString);
    	} 
        else {
            $oriWhere .= ' 1=1';
        }
    	$page = I('page'); //获取请求的页数 
		$limit = I('rows'); //获取每页显示记录数 
		$sidx = I('sidx'); //获取默认排序字段 
		$sord = I('sord'); //获取排序方式 
		if (!$sidx) 
 			$sidx = 1;
 		$DIC = D();
 		$sqlc = 'select count(*) as rows';
 		$sqls = 'select id, pid, class,name, content';
 		$sqlt = ' from artifact'.$oriWhere;
 		$sqlc.=$sqlt;
 		$sqls.=$sqlt;
 		$row = $DIC->query($sqlc);
 		$count = $row[0]['rows'];
 		$row = $DIC->query($sqls);
 		if ($count > 0) {
 			$total_page = ceil($count / $limit);
 		} else {
 			$total_page = 0;
 		}
 		if ($page > $total_page) {
 			$page = $total_page;
 		}

 		if (!$sidx) $sidx = 1;
 		if (!$limit) $limit = 30;
 		if (!$page) $page = 1;
 		$start = $limit * $page - $limit;
 		$wh = " order by $sidx $sord limit $start, $limit";
 		$sqls.=$wh;
 		$row = $DIC->query($sqls);
 		$response->page = $page;
 		$response->total = $total_page;
 		$response->records = $count; 		
 		for ($i=0;$i<count($row);$i++) {
 			$response->rows[$i]['id'] = trim($row[$i]['id']);
 			$project = M("project")->where("id=".$row[$i]['pid'])->select();
 			$lifecycle = M("lifecycle")->where("id=".$row[$i]['class'])->select();
 			$response->rows[$i]['cell'] = array($row[$i]['id'], $project[0]['name'], $lifecycle[0]['name'], $row[$i]['name'], $row[$i]['content']);
 		}
 		echo json_encode($response);
    }

    public function editArtifactData() {
    	switch(I('oper')) {    		
    		case "del":
    			M("artifact")->delete(I('id'));
    		break;
    	} 
    }

    public function displayProject() {

    	$this->display();
    }

    public function projectHandler() {
    	if (!IS_POST) {
    		E("非法提交！");
    	}

    	$data['name'] = trim(I('name'));
    	if (empty($data['name'])) {
    		$this->error("名称不能为空！");
    	}
    	if (!M("project")->add($data)) {
    		$this->error('数据库连接失败，请联系管理员');
    	}
    	$this->success('数据录入成功！');
    }

    public function listProject() {
         $oriWhere = " where ";
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] != 1) { 
            $oriWhere .= ' isForExperiment!=1 and ';
        }
    	$_search = I('_search');
    	$searchField = I('searchField');
    	$searchOper = I('searchOper');
    	$searchString = I('searchString');
    	if ($_search == 'true') {
    		$oriWhere .= '  '.assembleWhere($searchField, $searchOper, $searchString);
    	} else {
            $oriWhere .= ' 1=1';
        }
    	$page = I('page'); //获取请求的页数 
		$limit = I('rows'); //获取每页显示记录数 
		$sidx = I('sidx'); //获取默认排序字段 
		$sord = I('sord'); //获取排序方式 
		if (!$sidx) 
 			$sidx = 1;
 		$DIC = D();
 		$sqlc = 'select count(*) as rows';
 		$sqls = 'select id, name';
 		$sqlt = ' from project'.$oriWhere;
 		$sqlc.=$sqlt;
 		$sqls.=$sqlt;
 		$row = $DIC->query($sqlc);
 		$count = $row[0]['rows'];
 		$row = $DIC->query($sqls);
 		if ($count > 0) {
 			$total_page = ceil($count / $limit);
 		} else {
 			$total_page = 0;
 		}
 		if ($page > $total_page) {
 			$page = $total_page;
 		}

 		if (!$sidx) $sidx = 1;
 		if (!$limit) $limit = 30;
 		if (!$page) $page = 1;
 		$start = $limit * $page - $limit;
 		$wh = " order by $sidx $sord limit $start, $limit";
 		$sqls.=$wh;
 		$row = $DIC->query($sqls);
 		$response->page = $page;
 		$response->total = $total_page;
 		$response->records = $count; 		
 		for ($i=0;$i<count($row);$i++) {
 			$response->rows[$i]['id'] = trim($row[$i]['id']); 			
 			$response->rows[$i]['cell'] = array($row[$i]['id'], $row[$i]['name']);
 		}
 		echo json_encode($response);
    }

    public function editProject() {
		switch(I('oper')) {    		
    		case "add":
    			$data['name'] = I('name');
    			M("project")->add($data);
    		break;
    		case "edit":
    			$id = I('id');
    			$data['name'] = I('name');
    			M("project")->where('id='.$id)->save($data);
    		break;
    		case "del":
    			M("project")->delete(I('id'));
    		break;
    	} 
    }

    public function displayLifecycle() {
        $this->display();
    }

    public function lifeCycleHandler() {
        if (!IS_POST) {
            E("非法提交！");
        }

        $data['name'] = trim(I('name'));
        if (empty($data['name'])) {
            $this->error("名称不能为空！");
        }
        if (!M("lifecycle")->add($data)) {
            $this->error('数据库连接失败，请联系管理员');
        }
        $this->success('数据录入成功！');
    }

    public function listLifeCycle() {
        $oriWhere = " where ";
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] != 1) { 
            $oriWhere .= ' isForExperiment!=1 and ';
        }
        $_search = I('_search');
        $searchField = I('searchField');
        $searchOper = I('searchOper');
        $searchString = I('searchString');
        if ($_search == 'true') {
            $oriWhere .= '  '.assembleWhere($searchField, $searchOper, $searchString);
        } else {
            $oriWhere .= ' 1=1';
        }
        $page = I('page'); //获取请求的页数 
        $limit = I('rows'); //获取每页显示记录数 
        $sidx = I('sidx'); //获取默认排序字段 
        $sord = I('sord'); //获取排序方式 
        if (!$sidx) 
            $sidx = 1;
        $DIC = D();
        $sqlc = 'select count(*) as rows';
        $sqls = 'select id, name';
        $sqlt = ' from lifecycle'.$oriWhere;
        $sqlc.=$sqlt;
        $sqls.=$sqlt;
        $row = $DIC->query($sqlc);
        $count = $row[0]['rows'];
        $row = $DIC->query($sqls);
        if ($count > 0) {
            $total_page = ceil($count / $limit);
        } else {
            $total_page = 0;
        }
        if ($page > $total_page) {
            $page = $total_page;
        }

        if (!$sidx) $sidx = 1;
        if (!$limit) $limit = 30;
        if (!$page) $page = 1;
        $start = $limit * $page - $limit;
        $wh = " order by $sidx $sord limit $start, $limit";
        $sqls.=$wh;
        $row = $DIC->query($sqls);
        $response->page = $page;
        $response->total = $total_page;
        $response->records = $count;        
        for ($i=0;$i<count($row);$i++) {
            $response->rows[$i]['id'] = trim($row[$i]['id']);           
            $response->rows[$i]['cell'] = array($row[$i]['id'], $row[$i]['name']);
        }
        echo json_encode($response);
    }

    public function editLifeCycle() {
        switch(I('oper')) {         
            case "add":
                $data['name'] = I('name');
                M("lifecycle")->add($data);
            break;
            case "edit":
                $id = I('id');
                $data['name'] = I('name');
                M("lifecycle")->where('id='.$id)->save($data);
            break;
            case "del":
                M("lifecycle")->delete(I('id'));
            break;
        } 
    }

    public function importWord() {
        $exp = M('exp')->where('id=1')->find();
        if ($exp['exp_type'] == 1) { 
            $this->project = M("project")->select();
        } else {
            $this->project = M("project")->where("isForExperiment!=1")->select();
        }
        $this->display();
    }

    public function importWordAfter() {
        if (!empty($_FILES["wordFile"]["name"])) {
            $this->projectId = I("project");
            $info = upload();
            $info = $info["wordFile"];
            $filename = C("UPLOAD_DIR").$info["savepath"].$info["savename"];
            $cmd = 'python '.C('PYTHON_SCRIPT_DIR').'docx/calc.py '.$filename;
            exec($cmd, $rst, $ret);
            // foreach ($rst as $v) {                
            //     p($v);
            // } die;        

            if ($ret != 0) {
                $this->error("调用失败");
            }

            $result = parseDoc($rst);
            $this->sysReq = $result[0];        
            $this->hlReq = $result[1];
            $this->llReq = $result[2];
            $this->sc = $result[3];
            $this->totalCount = count($result, true) - 4;
            $this->display();
        } else {
            $this->error("未上传文件，请上传后提交！");
        }   
        
    }

    public function importWordHandler() {
        $projectId = I('projectId');        
        $dumpData = array();
        if (array_key_exists("sysReq", $_POST)){
            $sysReqArr = $_POST['sysReq'];            
            foreach ($sysReqArr as $key => $val) {                
                $data = array();
                $data['pid'] = $projectId;
                $data['class'] = C("DEFAULT_LIFECYCLE")[0];
                $data['name'] = explode('-', $key)[1];
                $data['content'] = $val[0];
                $dumpData[] = $data;
            }        
        }
        if (array_key_exists("hlReq", $_POST)){
            $hlReqArr = $_POST['hlReq'];            
            foreach ($hlReqArr as $key => $val) {                
                $data = array();
                $data['pid'] = $projectId;
                $data['class'] = C("DEFAULT_LIFECYCLE")[1];
                $data['name'] = explode('-', $key)[1];
                $data['content'] = $val[0];
                $dumpData[] = $data;
            }            
        }
        if (array_key_exists("llReq", $_POST)){
            $llReqArr = $_POST['llReq'];            
            foreach ($llReqArr as $key => $val) {                
                $data = array();
                $data['pid'] = $projectId;
                $data['class'] = C("DEFAULT_LIFECYCLE")[2];
                $data['name'] = explode('-', $key)[1];
                $data['content'] = $val[0];
                $dumpData[] = $data;
            }            
        }
        if (array_key_exists("sc", $_POST)){
            $scArr = $_POST['sc'];            
            foreach ($scArr as $key => $val) {                
                $data = array();
                $data['pid'] = $projectId;
                $data['class'] = C("DEFAULT_LIFECYCLE")[3];
                $data['name'] = explode('-', $key)[1];
                $data['content'] = $val[0];
                $dumpData[] = $data;
            }
        }
        M("artifact")->addAll($dumpData);
        $this->success("导入成功，请进行分词、翻译及预处理操作！", "displayArtifactData");
    }
	   
}