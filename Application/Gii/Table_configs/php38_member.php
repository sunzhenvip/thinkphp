<?php
return array(
	'tableName' => 'php38_member',    // 表名
	'tableCnName' => '会员',  // 表的中文名
	'moduleName' => 'Admin',  // 代码生成到的模块
	'withPrivilege' => FALSE,  // 是否生成相应权限的数据
	'topPriName' => '',        // 顶级权限的名称
	'digui' => 0,             // 是否无限级（递归）
	'diguiName' => '',        // 递归时用来显示的字段的名字，如cat_name（分类名称）
	'pk' => 'id',    // 表中主键字段名称
	/********************* 要生成的模型文件中的代码 ******************************/
	// 添加时允许接收的表单中的字段
	'insertFields' => "array('email','name','password','cpassword','gender','must_click')",
	// 修改时允许接收的表单中的字段
	'updateFields' => "array('id','email','name','password','cpassword','gender')",
	'validate' => "
		array('must_click', 'require', '必须同意注册协议！', 1, 'regex', 3),
		array('email', 'require', 'Email不能为空！', 1, 'regex', 3),
		array('email', 'email', 'Email格式不正确！', 1, 'regex', 3),
		array('email', '1,150', 'Email的值最长不能超过 150 个字符！', 1, 'length', 3),
		array('name', '1,30', '昵称的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('password', 'require', '密码不能为空！', 1, 'regex', 3),
		array('password', '6,20', '密码必须是6-20位字符！', 1, 'length', 3),
		array('cpassword', 'password', '两次密码不一致！', 1, 'confirm', 3),
		array('gender', '男,女,保密', \"性别的值只能是在 '男,女,保密' 中的一个值！\", 2, 'in', 3),
	",
	/********************** 表中每个字段信息的配置 ****************************/
	'fields' => array(
		'email' => array(
			'text' => 'Email',
			'type' => 'text',
			'default' => '',
		),
		'name' => array(
			'text' => '昵称',
			'type' => 'text',
			'default' => '',
		),
		'password' => array(
			'text' => '密码',
			'type' => 'password',
			'default' => '',
		),
		'face' => array(
			'text' => '头像',
			'type' => 'file',
			'thumbs' => array(
				array(150, 150, 2),
			),
			'save_fields' => array('face', 'face'),
			'default' => '',
		),
		'gender' => array(
			'text' => '性别',
			'type' => 'radio',
			'values' => array(
				'男' => '男',
				'女' => '女',
				'保密' => '保密',
			),
			'default' => '保密',
		),
		'status' => array(
			'text' => '是否通过验证',
			'type' => 'radio',
			'values' => array(
				'0' => '未验证',
				'1' => '已验证',
			),
			'default' => '0',
		),
	),
	/**************** 搜索字段的配置 **********************/
	'search' => array(
		array('email', 'normal', '', 'like', 'Email'),
		array('name', 'normal', '', 'like', '昵称'),
		array('regtime', 'betweenTime', 'st,et', '', '注册时间'),
		array('regip', 'normal', '', 'eq', '注册时的IP'),
		array('gender', 'in', '男-男,女-女,保密-保密', '', '性别'),
		array('status', 'in', '2-全部,0-未验证,1-已验证', '', '是否已验证'),
	),
);