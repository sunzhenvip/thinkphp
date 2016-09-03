<?php
return array(
	'tableName' => 'php38_member_level',    // 表名
	'tableCnName' => '会员级别',  // 表的中文名
	'moduleName' => 'Admin',  // 代码生成到的模块
	'withPrivilege' => FALSE,  // 是否生成相应权限的数据
	'topPriName' => '',        // 顶级权限的名称
	'digui' => 0,             // 是否无限级（递归）
	'diguiName' => '',        // 递归时用来显示的字段的名字，如cat_name（分类名称）
	'pk' => 'id',    // 表中主键字段名称
	/********************* 要生成的模型文件中的代码 ******************************/
	// 添加时允许接收的表单中的字段
	'insertFields' => "array('level_name','level_rate','jifen_bottom','jifen_top')",
	// 修改时允许接收的表单中的字段
	'updateFields' => "array('id','level_name','level_rate','jifen_bottom','jifen_top')",
	'validate' => "
		array('level_name', 'require', '级别名称不能为空！', 1, 'regex', 3),
		array('level_name', 'number', '级别名称必须是一个整数！', 1, 'regex', 3),
		array('level_rate', 'number', '折扣率，100=10折 98=9.8折 90=9折，用时除100必须是一个整数！', 2, 'regex', 3),
		array('jifen_bottom', 'require', '积分下限不能为空！', 1, 'regex', 3),
		array('jifen_bottom', 'number', '积分下限必须是一个整数！', 1, 'regex', 3),
		array('jifen_top', 'require', '积分上限不能为空！', 1, 'regex', 3),
		array('jifen_top', 'number', '积分上限必须是一个整数！', 1, 'regex', 3),
	",
	/********************** 表中每个字段信息的配置 ****************************/
	'fields' => array(
		'level_name' => array(
			'text' => '级别名称',
			'type' => 'text',
			'default' => '',
		),
		'level_rate' => array(
			'text' => '折扣率，100=10折 98=9.8折 90=9折，用时除100',
			'type' => 'text',
			'default' => '100',
		),
		'jifen_bottom' => array(
			'text' => '积分下限',
			'type' => 'text',
			'default' => '',
		),
		'jifen_top' => array(
			'text' => '积分上限',
			'type' => 'text',
			'default' => '',
		),
	),
	/**************** 搜索字段的配置 **********************/
	'search' => array(
	),
);