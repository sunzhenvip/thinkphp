<?php
return array(
	'tableName' => 'php38_admin',    // 表名
	'tableCnName' => '管理员',  // 表的中文名
	'moduleName' => 'Admin',  // 代码生成到的模块
	'withPrivilege' => FALSE,  // 是否生成相应权限的数据
	'topPriName' => '',        // 顶级权限的名称
	'digui' => 0,             // 是否无限级（递归）
	'diguiName' => '',        // 递归时用来显示的字段的名字，如cat_name（分类名称）
	'pk' => 'id',    // 表中主键字段名称
	/********************* 要生成的模型文件中的代码 ******************************/
	// 添加时允许接收的表单中的字段
	'insertFields' => "array('username','password','status')",
	// 修改时允许接收的表单中的字段
	'updateFields' => "array('id','username','password','status')",
	'validate' => "
		array('username', 'require', '用户名不能为空！', 1, 'regex', 3),
		array('username', '1,150', '用户名的值最长不能超过 150 个字符！', 1, 'length', 3),
		array('password', 'require', '密码不能为空！', 1, 'regex', 3),
		array('password', '1,32', '密码的值最长不能超过 32 个字符！', 1, 'length', 3),
		array('status', '正常,禁用', \"状态的值只能是在 '正常,禁用' 中的一个值！\", 2, 'in', 3),
	",
	/********************** 表中每个字段信息的配置 ****************************/
	'fields' => array(
		'username' => array(
			'text' => '用户名',
			'type' => 'text',
			'default' => '',
		),
		'password' => array(
			'text' => '密码',
			'type' => 'password',
			'default' => '',
		),
		'status' => array(
			'text' => '状态',
			'type' => 'radio',
			'values' => array(
				'正常' => '正常',
				'禁用' => '禁用',
			),
			'default' => '正常',
		),
	),
	/**************** 搜索字段的配置 **********************/
	'search' => array(
		array('username', 'normal', '', 'like', '用户名'),
		array('status', 'in', '正常-正常,禁用-禁用', '', '账号状态'),
	),
);