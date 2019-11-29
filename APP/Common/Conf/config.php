<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_PARSE_STRING'=>array(
		//'__PUBLIC__'=>__ROOT__.'/'.APP_NAME.'/Public',
		'__CLASS__'=>__ROOT__.'/'.APP_NAME.'/Public'.'/Class',		
		),	
	'DB_TYPE' => 'mysql',
	'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => 'ocr',          // 数据库名
    'DB_USER'=> 'root',      // 用户名
    'DB_PWD' => 'root',          // 密码
    'DB_PREFIX'=>'',

    'UPLOAD_DIR' => $_SERVER[DOCUMENT_ROOT].'/thinkphp/Public/upload/',
    'PYTHON_SCRIPT_DIR' => $_SERVER[DOCUMENT_ROOT].'/thinkphp/Public/python/',
);