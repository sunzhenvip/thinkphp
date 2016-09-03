<?php
return array(
	'tableName' => 'php38_comment',    // 表名
	'tableCnName' => '评论',  // 表的中文名
	'moduleName' => 'Admin',  // 代码生成到的模块
	'withPrivilege' => FALSE,  // 是否生成相应权限的数据
	'topPriName' => '',        // 顶级权限的名称
	'digui' => 0,             // 是否无限级（递归）
	'diguiName' => '',        // 递归时用来显示的字段的名字，如cat_name（分类名称）
	'pk' => 'id',    // 表中主键字段名称
	/********************* 要生成的模型文件中的代码 ******************************/
	// 添加时允许接收的表单中的字段
	'insertFields' => "array('member_id','goods_id','content','star')",
	// 修改时允许接收的表单中的字段
	'updateFields' => "array('id','member_id','goods_id','content','star')",
	'validate' => "
		array('member_id', 'require', '会员id不能为空！', 1, 'regex', 3),
		array('member_id', 'number', '会员id必须是一个整数！', 1, 'regex', 3),
		array('goods_id', 'require', '商品id不能为空！', 1, 'regex', 3),
		array('goods_id', 'number', '商品id必须是一个整数！', 1, 'regex', 3),
		array('content', 'require', '评论内容不能为空！', 1, 'regex', 3),
		array('content', '1,200', '评论内容的值最长不能超过 200 个字符！', 1, 'length', 3),
		array('star', 'number', '评分分值必须是一个整数！', 2, 'regex', 3),
	",
	/********************** 表中每个字段信息的配置 ****************************/
	'fields' => array(
		'member_id' => array(
			'text' => '会员id',
			'type' => 'text',
			'default' => '',
		),
		'goods_id' => array(
			'text' => '商品id',
			'type' => 'text',
			'default' => '',
		),
		'content' => array(
			'text' => '评论内容',
			'type' => 'text',
			'default' => '',
		),
		'star' => array(
			'text' => '评分分值',
			'type' => 'text',
			'default' => '5',
		),
	),
	/**************** 搜索字段的配置 **********************/
	'search' => array(
		array('member_id', 'normal', '', 'eq', '会员id'),
		array('goods_id', 'normal', '', 'eq', '商品id'),
		array('addtime', 'normal', '', 'eq', '评论时间'),
		array('content', 'normal', '', 'like', '评论内容'),
		array('star', 'normal', '', 'eq', '评分分值'),
	),
);