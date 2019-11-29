<?php
namespace Home\Controller;
use Think\Controller;
class TermDictionaryController extends CommonController {
    public function index(){
        $this->display();
    }

    public function listDict() {
    	$_search = I('_search');
    	$searchField = I('searchField');
    	$searchOper = I('searchOper');
    	$searchString = I('searchString');
    	if ($_search == 'true') {
    		$oriWhere = ' where '.assembleWhere($searchField, $searchOper, $searchString);
    	}
    	$page = I('page'); //获取请求的页数 
		$limit = I('rows'); //获取每页显示记录数 
		$sidx = I('sidx'); //获取默认排序字段 
		$sord = I('sord'); //获取排序方式 
		if (!$sidx) 
 			$sidx = 1;
 		$DIC = D();
 		$sqlc = 'select count(*) as rows';
 		$sqls = 'select id, chname, enname';
 		$sqlt = ' from dictionary'.$oriWhere;
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
 			$response->rows[$i]['cell'] = array($row[$i]['id'], $row[$i]['chname'], $row[$i]['enname']);
 		}
 		echo json_encode($response);
 		
    }

    public function editDict() {
    	switch(I('oper')) {
    		case "add":
    			$data['chname'] = I('chname');
    			$data['enname'] = I('enname');
    			M("dictionary")->add($data);
    		break;
    		case "edit":
    			$id = I('id');
    			$data['chname'] = I('chname');
    			$data['enname'] = I('enname');
    			M("dictionary")->where('id='.$id)->save($data);
    		break;
    		case "del":
    			M("dictionary")->delete(I('id'));
    		break;
    	} 
    }
    //'cn', 'bw', 'eq', 'ne', 'lt', 'gt', 'ew'
    public function assembleWhere($field, $oper, $str) {
    	switch ($oper) {
    		case 'eq':
    			return $field.' = '.$str;
    			break;
    		case 'ne':
    			return $field.' != '.$str;
    			break;
    		case 'cn':
    			return $field." like '%".$str."%'";
    			break;
    		default:
    			return $field." like '%".$str."%'";
    			break;
    	}
    }
}