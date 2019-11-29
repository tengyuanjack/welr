<?php
return array(
	//'配置项'=>'配置值'
	'RBAC_SUPERADMIN'=>'root',//超级管理员名称
	'ADMIN_AUTH_KEY'=>'superadmin',//超级管理员识别号
	'USER_AUTH_KEY'=>'id',//用户识别号
	'USER_AUTH_NAME'=>'username',//用户识别号
	'USER_AUTH_TYPE'=>1, //1是登陆验证 2是实时验证
	'USER_AUTH_ON'=>true, //是否开启验证

	'REQUIRE_AUTH_MODULE'=>'',
	'REQUIRE_AUTH_ACTION'=>'',	
	'NOT_AUTH_MODULE'=>'Index,TermDictionary', // 无需验证的模块（控制器）
	'NOT_AUTH_ACTION'=>'ajaxHlToSysHandler,ajaxLlToHlHandler,ajaxScToLlHandler,ajaxHandler,downloadInspectTable,
						partitionHandler,preprocess,calculate,displayResult,ajaxTupleRelationHanlder,ajaxLayerRelation,ajaxGetContentById,
						ajaxSelectLifecycle,ajaxSelectArtifact,
						artifactHandler,listArtifactData,editArtifactData,projectHandler,listProject,editProject,lifeCycleHandler,listLifeCycle,editLifeCycle,importWordAfter,importWordHandler,
						addUserHandler,modifyUser,modifyUserHandler,deleteUser,addRoleHandler,modifyRole,modifyRoleHandler,deleteRole,assignAccess,assignAccessHandler,addNode,addNodeHandler,alterNodeHandler,alterNode,deleteNode,
						',

	'RBAC_ROLE_TABLE'=>'role',//角色表
	'RBAC_USER_TABLE'=>'role_user',//角色用户中间表
	'RBAC_ACCESS_TABLE'=>'access',
	'RBAC_NODE_TABLE'=>'node',



	'DEFAULT_LIFECYCLE' => array(1,2,3,8),
);