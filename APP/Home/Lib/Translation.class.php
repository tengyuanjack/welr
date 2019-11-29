<?php
	namespace Home\Lib;

	class Translation{

		const CURL_TIMEOUT = 20;
		const URL = "http://openapi.youdao.com/api";
		const APP_KEY = "17c6ea6ab6da42da"; //替换为您的APPKey
		const SEC_KEY = "F2DisaKHJZndUoRXXIBpmvGGTct4DJe2"; //替换为您的密钥

		// define("CURL_TIMEOUT",   20); 
		// define("URL",            "http://openapi.youdao.com/api"); 
		// define("APP_KEY",         "17c6ea6ab6da42da"); //替换为您的APPKey
		// define("SEC_KEY",        "F2DisaKHJZndUoRXXIBpmvGGTct4DJe2");//替换为您的密钥

		//翻译入口
		public function translate($query, $from, $to)
		{
		    $args = array(
		        'q' => $query,
		        'appKey' => self::APP_KEY,
		        'salt' => rand(10000,99999),
		        'from' => $from,
		        'to' => $to,

		    );
		    $args['sign'] = self::buildSign(self::APP_KEY, $query, $args['salt'], self::SEC_KEY);
		    $ret = self::call(self::URL, $args);		    
		    $ret = json_decode($ret, true);
		    return $ret; 
		}

		//加密
		private function buildSign($appKey, $query, $salt, $secKey)
		{/*{{{*/
		    $str = $appKey . $query . $salt . $secKey;
		    $ret = md5($str);
		    return $ret;
		}/*}}}*/

		//发起网络请求
		private function call($url, $args=null, $method="post", $testflag = 0, $timeout = self::CURL_TIMEOUT, $headers=array())
		{/*{{{*/
		    $ret = false;
		    $i = 0; 
		    while($ret === false) 
		    {
		        if($i > 1)
		            break;
		        if($i > 0) 
		        {
		            sleep(1);
		        }
		        $ret = self::callOnce($url, $args, $method, false, $timeout, $headers);
		        $i++;
		    }
		    return $ret;
		}/*}}}*/

		private function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = self::CURL_TIMEOUT, $headers=array())
		{/*{{{*/
		    $ch = curl_init();
		    if($method == "post") 
		    {
		        $data = self::convert($args);
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		        curl_setopt($ch, CURLOPT_POST, 1);
		    }
		    else 
		    {
		        $data = self::convert($args);
		        if($data) 
		        {
		            if(stripos($url, "?") > 0) 
		            {
		                $url .= "&$data";
		            }
		            else 
		            {
		                $url .= "?$data";
		            }
		        }
		    }
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    if(!empty($headers)) 
		    {
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		    }
		    if($withCookie)
		    {
		        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
		    }
		    $r = curl_exec($ch);
		    curl_close($ch);
		    return $r;
		}/*}}}*/

		private function convert(&$args)
		{/*{{{*/
		    $data = '';
		    if (is_array($args))
		    {
		        foreach ($args as $key=>$val)
		        {
		            if (is_array($val))
		            {
		                foreach ($val as $k=>$v)
		                {
		                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
		                }
		            }
		            else
		            {
		                $data .="$key=".rawurlencode($val)."&";
		            }
		        }
		        return trim($data, "&");
		    }
		    return $args;
		}/*}}}*/
	}
?>