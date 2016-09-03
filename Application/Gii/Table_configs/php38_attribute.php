<?php
return array(
	'tableName' => 'php38_attribute',    // 表名
	'tableCnName' => '属性',  // 表的中文名
	'moduleName' => 'Admin',  // 代码生成到的模块
	'withPrivilege' => FALSE,  // 是否生成相应权限的数据
	'topPriName' => '',        // 顶级权限的名称
	'digui' => 0,             // 是否无限级（递归）
	'diguiName' => '',        // 递归时用来显示的字段的名字，如cat_name（分类名称）
	'pk' => 'id',    // 表中主键字段名称
	/********************* 要生成的模型文件中的代码 ******************************/
	// 添加时允许接收的表单中的字段
	'insertFields' => "array('attr_name','attr_type','attr_option_values','type_id')",
	// 修改时允许接收的表单中的字段
	'updateFields' => "array('id','attr_name','attr_type','attr_option_values','type_id')",
	'validate' => "
		array('attr_name', 'require', '属性名称不能为空！', 1, 'regex', 3),
		array('attr_name', '1,30', '属性名称的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('attr_type', 'require', '属性类型不能为空！', 1, 'regex', 3),
		array('attr_type', '唯一,可选', \"属性类型的值只能是在 '唯一,可选' 中的一个值！\", 1, 'in', 3),
		array('attr_option_values', '1,150', '属性可选值的值最长不能超过 150 个字符！', 2, 'length', 3),
		array('type_id', 'require', '类型id不能为空！', 1, 'regex', 3),
		array('type_id', 'number', '类型id必须是一个整数！', 1, 'regex', 3),
	",
	/********************** 表中每个字段信息的配置 ****************************/
	'fields' => array(
		'attr_name' => array(
			'text' => '属性名称',
			'type' => 'text',
			'default' => '',
		),
		'attr_type' => array(
			'text' => '属性类型',
			'type' => 'radio',
			'values' => array(
				'唯一' => '唯一',
				'可选' => '可选',
			),
			'default' => '',
		),
		'attr_option_values' => array(
			'text' => '属性可选值',
			'type' => 'text',
			'default' => '',
		),
		'type_id' => array(
			'text' => '类型id',
			'type' => 'text',
			'default' => '',
		),
	),
	/**************** 搜索字段的配置 **********************/
	'search' => array(
		array('attr_name', 'normal', '', 'like', '属性名称'),
		array('attr_type', 'in', '唯一-唯一,可选-可选', '', '属性类型'),
		array('type_id', 'normal', '', 'eq', '类型id'),
	),
);