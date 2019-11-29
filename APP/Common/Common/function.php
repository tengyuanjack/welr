<?php
	
	function p($arg) {
		echo '<pre>';
		print_r($arg);
		echo '</pre>';
	} 

	function truncate_cn($string,$length=0,$ellipsis='…',$start=0){
		$string=strip_tags($string);
		$string=preg_replace('/\n/is','',$string);
		//$string=preg_replace('/ |　/is','',$string);//清除字符串中的空格
		$string=preg_replace('/&nbsp;/is','',$string);
		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/",$string,$string);
		if(is_array($string)&&!empty($string[0])){
			$string=implode('',$string[0]);
			if(strlen($string)<$start+1){
				return '';
			}
			preg_match_all("/./su",$string,$ar);
			$string2='';
			$tstr='';
			//www.phpernote.com
			for($i=0;isset($ar[0][$i]);$i++){
				if(strlen($tstr)<$start){
					$tstr.=$ar[0][$i];
				}else{
					if(strlen($string2)<$length+strlen($ar[0][$i])){
						$string2.=$ar[0][$i];
					}else{
						break;
					}
				}
			}
			return $string==$string2?$string2:$string2.$ellipsis;
		}else{
			$string='';
		}
		return $string;
	}


	//文件上传
	function upload(){
		$upload = new \Think\Upload();
		$upload->maxsize = 31457280;
		$upload->exts  = array('docx');// 设置附件上传类型
		$upload->rootPath = $_SERVER[DOCUMENT_ROOT].'/thinkphp/Public/upload/';
		$upload->savePath =  '';// 设置附件上传目录
		$info = $upload -> upload();
		
		if(!$info) {// 上传错误提示错误信息
			E($upload->getError());
		}

		return $info;
	}


	// 中文单词翻译为英文
	// 规则： 1）判断是否为纯中文单词 2）在发动机词典库中查找看能不能匹配，如果能则返回，如不能，进入步骤3 3）调用有道智云接口查询
	function translate($word) {
		$r = check_str($word);
		if ($r == 1) {
			return removePunc($word);
		}
		if ($r != 2) {
			return "";
		}
		$arr = M("dictionary")->where(array("chname"=>$word))->select();		
		if (count($arr) > 0) {
			return removePunc($arr[0]["enname"]);
		}
		$translation = new \Home\Lib\Translation();
		$rst = $translation->translate($word,"zh-CHS","EN");
		if (array_key_exists("translation", $rst) && count($rst["translation"]) > 0) {
			return removePunc($rst["translation"][0]);
		}
		return "";
	}

	/*
	*function：检测字符串是否由纯英文，纯中文，中英文混合组成
	*param string
	*return 1:纯英文;2:纯中文;3:中英文混合
	*/
	function check_str($str=''){
		if(trim($str)==''){
			return '';
		}
		$m=mb_strlen($str,'utf-8');
		$s=strlen($str);
		if($s==$m){
			return 1;
		}
		if($s%$m==0&&$s%3==0){
			return 2;
		}
		return 3;
	}

	/*
	* 去掉字符串中的标点符号
	*/
	function removePunc($str) {
		$char = "。、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐¸﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";
		$pattern = array(
		    "/[[:punct:]]/i", //英文标点符号
		    '/['.$char.']/u', //中文标点符号
		    '/[ ]{2,}/'
		);
		$str = preg_replace($pattern, ' ', $str);
		return $str;
	}

	/*
	*	由于不支持array_column，因此重写该函数（copy from internet）
	*/
	function i_array_column($input, $columnKey, $indexKey=null){
	    if(!function_exists('array_column')){ 
	        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
	        $indexKeyIsNull            = (is_null($indexKey))?true :false; 
	        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
	        $result                         = array(); 
	        foreach((array)$input as $key=>$row){ 
	            if($columnKeyIsNumber){ 
	                $tmp= array_slice($row, $columnKey, 1); 
	                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
	            }else{ 
	                $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
	            } 
	            if(!$indexKeyIsNull){ 
	                if($indexKeyIsNumber){ 
	                  $key = array_slice($row, $indexKey, 1); 
	                  $key = (is_array($key) && !empty($key))?current($key):null; 
	                  $key = is_null($key)?0:$key; 
	                }else{ 
	                  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
	                } 
	            } 
	            $result[$key] = $tmp; 
	        } 
	        return $result; 
	    }else{
	        return array_column($input, $columnKey, $indexKey);
	    }
	}

	/**
	 * 根据pid lifecycleId artifactId得到计算用的targetData
	 */
	function getTargetData($project, $lifecycle, $artifact=null) {
		$where = array('pid'=>$project, 'class'=>$lifecycle);
		$artArr = M("artifact")->where($where)->select();
		if (count($artArr) == 0) {
			return "";
		}
		$aids = array();
		foreach ($artArr as $val) {
			$aids[] = $val["id"];
		}
		if (!empty($artifact)) {
			$key = array_search($artifact, $aids);
			array_slice($aids, $key, 1);
		}
		$where1['aid'] = array('in', $aids);
		$targetArr = M("translation")->where($where1)->select();
        $targetData = [];
        foreach ($targetArr as $val) {
            $targetData[$val["aid"]] = $val["words"];
        }
        $targetData = json_encode($targetData, JSON_UNESCAPED_UNICODE);
        return $targetData;
	}

	function getSingleLayerList($projectId, $lifecycleId, $artifactId) {		
		$targetData = getTargetData($projectId, $lifecycleId);
		if ($targetData == "") {
			return [];
		}
        $sourceArr = M("translation")->where("aid=".$artifactId)->find();
        
        $cmd = 'python '.C('PYTHON_SCRIPT_DIR').'welr/script_calculateLayer.py '.$projectId.' '.$artifactId.' \''.$sourceArr["words"].'\' \''.$targetData.'\'';
        // p("cmd:  ".$cmd);
        exec($cmd, $rst, $ret);        
        // foreach ($rst as $v) {                
        //     p($v);
        // }        
        if ($ret == 0) {
        	$arr = explode(']', trim($rst[0],']'));
        	$keyStr = trim($arr[0],'[');
        	$simStr = trim($arr[1],'[');        	
        	$keyArr = explode(',', $keyStr);
        	$simArr = explode(',', $simStr);        	
        	$result = array();
        	for ($i=0; $i < count($keyArr); $i++) { 
        		$keyArr[$i] = trim($keyArr[$i]," '");
        		$simArr[$i] = trim($simArr[$i], ' [');
        		$result[intval($keyArr[$i])] = floatval($simArr[$i]);
        	}
        	// $result = json_decode(str_replace("'",'"',$rst[0]), true); 
        	foreach ($result as $key => $val) {
        		if ($key == 0) {
        			unset($result[$key]);
        		} 
        	}
        	// p($result);
        	// die;
        	// arsort($result);  // 使用SVM模型了，就不用arsort了
        	return $result;
        }        
	}

	/**
     * 找artifact的下一层与该artifact相关的内容
     */
    function recursiveLayer($projectId, $depth, $iDepth, $artifactId) {      	  	
        if ($iDepth > $depth) {
            return;
        }
        $artifact = M('artifact')->where(array("id"=>$artifactId))->find();
        // $artifact["content"] = htmlspecialchars($artifact["content"]);
        $artifact["children"] = [];        
        $key = array_search($artifact["class"], C('DEFAULT_LIFECYCLE'));
        if (($key + 1)>=count(C("DEFAULT_LIFECYCLE"))) {        
        	return;
        }        
        $rst = getSingleLayerList($projectId, C('DEFAULT_LIFECYCLE')[$key + 1], $artifactId);             
        // p($projectId.' '.$depth.' '.$iDepth.' '.$artifactId.' ');
        // p($rst);
        if (count($rst) == 0) {
        	return $artifact;
        }   
        foreach ($rst as $key => $val) {
            $art = M('artifact')->where(array("id"=>$key))->find();
            // $art["content"] = htmlspecialchars($art["content"]);
            $art["score"] = $val;
            $art["children"] = [];
            $artifact["children"][] = $art;
        }        
        $iDepth++;
        $newArr = [];
        foreach ($artifact["children"] as $val) {        	
           $new = recursiveLayer($projectId, $depth, $iDepth, $val["id"]);   
           if ($new != null && count($new) > 0) 
           		$val = $new;
           $newArr[] = $val;
        }
        if (count($newArr) == 0) {
        	unset($artifact["children"]);
        } else {
	        $artifact["children"] = $newArr;
	    }
        return $artifact;
    }
	
	/**
	 * 将recursiveLayer生成的数组，转为跟踪矩阵所需形式
	 */
	function listLayer($arr) {
		$result = array();
		if (count($arr) == 0) {
			return $result;
		}
		$arr1 = array();
		$arr2 = array();
		$arr3 = array();
		$arr4 = array();
		$arr1[] = $arr;		
		foreach ($arr['children'] as $v2) {
			if (!testInArray($v2, $arr2)) {
				$arr2[] = $v2;				
			}			
			foreach ($v2['children'] as $v3) {
				if (!testInArray($v3, $arr3)) {
					$arr3[] = $v3;
				}
				foreach ($v3['children'] as $v4) {
					if (!testInArray($v4, $arr4)) {
						$arr4[] = $v4;
					}
				}
			}
		}		
		$maxSize = max(count($arr1), count($arr2));
		$maxSize = max($maxSize, count($arr3));
		$maxSize = max($maxSize, count($arr4));

		// 确定arr1属于什么需求（arr1中必定有元素）
		$key = array_search($arr1[0]['class'], C('DEFAULT_LIFECYCLE'));
		switch ($key) {
			case 0:
				for ($i = 0 ; $i < $maxSize; $i++) {
					$row['sysReq'] = $arr1[$i]['name']."<span class='hide'>".$arr1[$i]['id']."</span>";
					$row['hlReq'] = $arr2[$i]['name']."<span class='hide'>".$arr2[$i]['id']."</span>";
					$row['llReq'] = $arr3[$i]['name']."<span class='hide'>".$arr3[$i]['id']."</span>";
					$row['scReq'] = $arr4[$i]['name']."<span class='hide'>".$arr4[$i]['id']."</span>";
					$result[] = $row;
				}
				break;
			case 1:
				for ($i = 0 ; $i < $maxSize; $i++) {
					$row['sysReq'] = '';
					$row['hlReq'] = $arr1[$i]['name']."<span class='hide'>".$arr1[$i]['id']."</span>";
					$row['llReq'] = $arr2[$i]['name']."<span class='hide'>".$arr2[$i]['id']."</span>";
					$row['scReq'] = $arr3[$i]['name']."<span class='hide'>".$arr3[$i]['id']."</span>";
					$result[] = $row;
				}
				break;
			case 2:
				for ($i = 0 ; $i < $maxSize; $i++) {
					$row['sysReq'] = '';
					$row['hlReq'] = '';
					$row['llReq'] = $arr1[$i]['name']."<span class='hide'>".$arr1[$i]['id']."</span>";
					$row['scReq'] = $arr2[$i]['name']."<span class='hide'>".$arr2[$i]['id']."</span>";
					$result[] = $row;
				}
				break;			
			default:
				# code...
				break;
		}		
		return $result;
	}

	function testInArray($needle, $hack) {
		foreach ($hack as $val) {
			if ($val['id'] == $needle['id']) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 通过userId找到角色名称
	 */
	function identifyRoleName($userId) {
		$roleUser = M('role_user')->where('user_id=\''.$userId.'\'')->find();
		$role = M('role')->where('id=\''.$roleUser['role_id'].'\'')->find();
		return $role['name'];
	}

	/**
	* RBAC
	**/
	function node_merge($node,$access=null,$pid=0){
		$arr = array();
		foreach($node as $v){
			if(is_array($access)){
				$v['access'] = in_array($v['id'], $access) ? 1:0;
			}
			if($v['pid'] == $pid){
				$v['child'] = node_merge($node,$access,$v['id']);
				$arr[] = $v;
			}
		}
		return $arr;
	}

	function assembleWhere($searchField, $searchOper, $searchString) {
		return $searchField.' '.$searchOper.' '.$searchString;
	}


?>