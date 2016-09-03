CREATE DATABASE php38;
USE php38;
SET NAMES utf8;

DROP TABLE IF EXISTS php38_goods;
CREATE TABLE php38_goods
(
	id mediumint unsigned not null auto_increment comment 'Id',
	cat_id mediumint unsigned not null comment '主分类id',
	goods_name varchar(150) not null comment '商品名称',
	market_price decimal(10,2) not null comment '市场价格',
	shop_price decimal(10,2) not null comment '本店价格',
	logo varchar(150) not null default '' comment '图片',
	sm_logo varchar(150) not null default '' comment '小图片',
	mid_logo varchar(150) not null default '' comment '中图片',
	goods_desc longtext comment '商品描述',
	is_on_sale enum('是','否') not null default '是' comment '是否上架',
	addtime int unsigned not null comment '添加时间',
	admin_id mediumint unsigned not null comment '添加这件商品的管理员id',
	type_id tinyint unsigned not null default '0' comment '类型id',
	promote_price decimal(10,2) not null default '0.00' comment '促销价格',
	promote_start_date int unsigned not null default '0' comment '促销开始时间',
	promote_end_date int unsigned not null default '0' comment '促销结束时间',
	is_hot enum('是','否') not null default '否' comment '是否热销',
	is_rec enum('是','否') not null default '否' comment '是否推荐',
	is_new enum('是','否') not null default '否' comment '是否新品',
	is_floor enum('是','否') not null default '否' comment '是否推荐到楼层',
	sort_number tinyint unsigned not null default '100' comment '排序的数字',
	is_sphinx_updated tinyint unsigned not null default '0' comment '是否被修改了，需要重新建索引',
	primary key (id),
	key shop_price(shop_price),
	key addtime(addtime),
	key cat_id(cat_id),
	key admin_id(admin_id),
	key is_sphinx_updated(is_sphinx_updated),
	key promote_start_date(promote_start_date),
	key promote_end_date(promote_end_date),
	key is_hot(is_hot),
	key is_rec(is_rec),
	key is_new(is_new),
	key is_floor(is_floor),
	key is_on_sale(is_on_sale)
)engine=MyISAM default charset=utf8;

DROP TABLE IF EXISTS php38_goods_ext_cat;
CREATE TABLE php38_goods_ext_cat
(
	goods_id mediumint unsigned not null comment '商品Id',
	cat_id mediumint unsigned not null comment '分类Id',
	key goods_id(goods_id),
	key cat_id(cat_id)
)engine=MyISAM default charset=utf8 comment '商品的扩展分类';

DROP TABLE IF EXISTS php38_category;
CREATE TABLE php38_category
(
	id mediumint unsigned not null auto_increment comment 'Id',
	cat_name varchar(150) not null comment '分类名称',
	parent_id mediumint unsigned not null default '0' comment '上级分类ID，0：代表顶级分类',
	is_floor enum('是','否') not null default '否' comment '是否推荐到楼层',
	primary key (id),
	key is_floor(is_floor)
)engine=MyISAM default charset=utf8 comment '分类';

/*********************** RBAC ****************************/

DROP TABLE IF EXISTS php38_privilege;
CREATE TABLE php38_privilege
(
	id mediumint unsigned not null auto_increment comment 'Id',
	pri_name varchar(150) not null comment '权限名称',
	module_name varchar(30) not null default '' comment '模块名称',
	controller_name varchar(30) not null default '' comment '控制器名称',
	action_name varchar(30) not null default '' comment '方法名称',
	parent_id mediumint unsigned not null default '0' comment '上级权限ID，0：代表顶级分类',
	primary key (id)
)engine=MyISAM default charset=utf8 comment '权限';

DROP TABLE IF EXISTS php38_role_pri;
CREATE TABLE php38_role_pri
(
	role_id mediumint unsigned not null comment '角色id',
	pri_id mediumint unsigned not null comment '权限id',
	key role_id(role_id),
	key pri_id(pri_id)
)engine=MyISAM default charset=utf8 comment '角色拥有的权限';

DROP TABLE IF EXISTS php38_role;
CREATE TABLE php38_role
(
	id mediumint unsigned not null auto_increment comment 'Id',
	role_name varchar(150) not null comment '角色名称',
	primary key (id)
)engine=MyISAM default charset=utf8 comment '角色';

DROP TABLE IF EXISTS php38_admin_role;
CREATE TABLE php38_admin_role
(
	admin_id mediumint unsigned not null comment '管理员id',
	role_id mediumint unsigned not null comment '角色id',
	key role_id(role_id),
	key admin_id(admin_id)
)engine=MyISAM default charset=utf8 comment '管理员所在角色';

DROP TABLE IF EXISTS php38_admin;
CREATE TABLE php38_admin
(
	id mediumint unsigned not null auto_increment comment 'Id',
	username varchar(150) not null comment '用户名',
	password char(32) not null comment '密码',
	status enum('正常','禁用') not null default '正常' comment '状态',
	primary key (id)
)engine=MyISAM default charset=utf8 comment '管理员';
INSERT INTO php38_admin VALUES(1,'root','99de37ebe3fc968924ff1d82dec33cd2','正常');

DROP TABLE IF EXISTS php38_admin_goods_cat;
CREATE TABLE php38_admin_goods_cat
(
	admin_id mediumint unsigned not null comment '管理员id',
	cat_id mediumint unsigned not null comment '分类id',
	key cat_id(cat_id),
	key admin_id(admin_id)
)engine=MyISAM default charset=utf8 comment '管理员可以管理的分类表';

/*********************** 权限表数据 ************************/
INSERT INTO `php38_privilege` (`id`, `pri_name`, `module_name`, `controller_name`, `action_name`, `parent_id`) VALUES
(1, '商品模块', 'null', 'null', 'null', 0),
(2, '添加商品', 'Admin', 'Goods', 'add', 3),
(3, '商品列表', 'Admin', 'Goods', 'lst', 1),
(4, '修改商品', 'Admin', 'Goods', 'edit', 3),
(5, 'RBAC', 'null', 'null', 'null', 0),
(6, '权限列表', 'Admin', 'Privilege', 'lst', 5),
(7, '添加权限', 'Admin', 'Privilege', 'add', 6),
(8, '角色列表', 'Admin', 'Role', 'lst', 5),
(9, '修改权限', 'Admin', 'Privilege', 'edit', 6),
(10, '删除商品', 'Admin', 'Goods', 'delete', 3),
(11, '删除权限', 'Admin', 'Privilege', 'delete', 6),
(12, '添加角色', 'Admin', 'Role', 'add', 8),
(13, '修改角色', 'Admin', 'Role', 'edit', 8),
(14, '删除角色', 'Admin', 'Role', 'delete', 8),
(15, '管理员列表', 'Admin', 'Admin', 'lst', 5),
(16, '添加管理员', 'Admin', 'Admin', 'add', 15),
(17, '修改管理员', 'Admin', 'Admin', 'edit', 15),
(18, '删除管理员', 'Admin', 'Admin', 'delete', 15),
(19, '商品分类列表', 'Admin', 'Category', 'lst', 1),
(20, '添加分类', 'Admin', 'Category', 'add', 19),
(21, '修改分类', 'Admin', 'Category', 'edit', 19),
(22, '删除分类', 'Admin', 'Category', 'delete', 19),
(23, '会员模块', 'null', 'null', 'null', 0),
(24, '会员级别列表', 'Admin', 'MemberLevel', 'lst', 23),
(25, '添加级别', 'Admin', 'MemberLevel', 'add', 24),
(26, '修改级别', 'Admin', 'MemberLevel', 'edit', 24),
(27, '删除级别', 'Admin', 'MemberLevel', 'delete', 24),
(28, '类型列表', 'Admin', 'Type', 'lst', 1),
(29, '添加类型', 'Admin', 'Type', 'add', 28),
(30, '修改类型', 'Admin', 'Type', 'edit', 28),
(31, '删除类型', 'Admin', 'Type', 'delete', 28),
(32, '属性列表', 'Admin', 'Attribute', 'lst', 28),
(33, '添加属性', 'Admin', 'Attribute', 'add', 32),
(34, '修改属性', 'Admin', 'Attribute', 'edit', 32),
(35, '删除属性', 'Admin', 'Attribute', 'delete', 32),
(36, 'ajax获取商品属性', 'Admin', 'Goods', 'ajax_get_attr', 3),
(37, 'ajax删除商品属性', 'Admin', 'Goods', 'ajaxDelGoodsAttr', 4),
(38, 'ajax删除商品相册图片', 'admin', 'Goods', 'ajax_delete_image', 4),
(39, '清空缓存', 'Admin', 'Goods', 'deleteTempImages', 3),
(40, 'ajax上传商品相册图片', 'Admin', 'Goods', 'ajax_upload_pic', 3),
(41, '库存量管理', 'Admin', 'Goods', 'gn', 3);

DROP TABLE IF EXISTS php38_goods_pics;
CREATE TABLE php38_goods_pics
(
	id mediumint unsigned not null auto_increment comment 'Id',
	goods_id mediumint unsigned not null comment '商品id',
	pic varchar(150) not null comment '原图路径',
	sm_pic varchar(150) not null comment '小图路径',
	mid_pic varchar(150) not null comment '中图路径',
	primary key (id),
	key goods_id(goods_id)
)engine=MyISAM default charset=utf8 comment '相册';

DROP TABLE IF EXISTS php38_member_level;
CREATE TABLE php38_member_level
(
	id tinyint unsigned not null auto_increment comment 'Id',
	level_name varchar(30) not null comment '级别名称',
	level_rate tinyint unsigned not null default '100' comment '折扣率，100=10折 98=9.8折 90=9折，用时除100',
	jifen_bottom mediumint unsigned not null comment '积分下限',
	jifen_top mediumint unsigned not null comment '积分上限',
	primary key (id)
)engine=MyISAM default charset=utf8 comment '会员级别';

DROP TABLE IF EXISTS php38_member_price;
CREATE TABLE php38_member_price
(
	goods_id mediumint unsigned not null comment '商品id',
	level_id tinyint unsigned not null comment '级别id',
	price decimal(10,2) not null comment '价格',
	key goods_id(goods_id),
	key level_id(level_id)
)engine=MyISAM default charset=utf8 comment '会员价格';

DROP TABLE IF EXISTS php38_type;
CREATE TABLE php38_type
(
	id tinyint unsigned not null auto_increment comment 'Id',
	type_name varchar(30) not null comment '类型名称',
	primary key (id)
)engine=MyISAM default charset=utf8 comment '类型';

DROP TABLE IF EXISTS php38_attribute;
CREATE TABLE php38_attribute
(
	id mediumint unsigned not null auto_increment comment 'Id',
	attr_name varchar(30) not null comment '属性名称',
	attr_type enum('唯一','可选') not null comment '属性类型',
	attr_option_values varchar(150) not null default '' comment '属性可选值',
	type_id tinyint unsigned not null comment '类型id',
	primary key (id),
	key type_id(type_id)
)engine=MyISAM default charset=utf8 comment '属性';

DROP TABLE IF EXISTS php38_goods_attr;
CREATE TABLE php38_goods_attr
(
	id mediumint unsigned not null auto_increment comment 'Id',
	goods_id mediumint unsigned not null comment '商品id',
	attr_id mediumint unsigned not null comment '属性id',
	attr_value varchar(150) not null default '' comment '属性值',
	primary key (id),
	key goods_id(goods_id),
	key attr_id(attr_id)
)engine=MyISAM default charset=utf8 comment '商品属性';

DROP TABLE IF EXISTS php38_goods_number;
CREATE TABLE php38_goods_number
(
	goods_id mediumint unsigned not null comment '商品id',
	attr_list varchar(150) not null default '' comment '商品属性id，规则 1：如果一件商品有多个属性用，隔开 规则2：如果一件商品有多个属性ID就升降拼字符串，所以如果有两个属性ID5,6,那么不能拼成6,5，我们定义了这个规则之后，前台要取库存量也按这个规则就不会出错',
	goods_number mediumint unsigned not null comment '库存量',
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '库存量';

DROP TABLE IF EXISTS php38_member;
CREATE TABLE php38_member
(
	id int unsigned not null auto_increment comment 'Id',
	email varchar(150) not null comment 'Email',
	name varchar(30) not null default '' comment '昵称',
	password char(32) not null comment '密码',
	regtime int unsigned not null comment '注册时间',
	regip int unsigned not null comment '注册时的IP',
	face varchar(150) not null default '' comment '头像',
	gender enum('男','女','保密') not null default '保密' comment '性别',
	status tinyint unsigned not null default '0' comment '状态，0：未验证 1：正常',
	jifen mediumint unsigned not null default '0' comment '积分可以消费',
	jyz mediumint unsigned not null default '0' comment '经验值【只增不减】计算级别',
	qq_openid char(32) not null default '' comment '关联的qq',
	primary key (id),
	key qq_openid(qq_openid),
	key email(email)
)engine=MyISAM default charset=utf8 comment '会员';

DROP TABLE IF EXISTS php38_email_chk_code;
CREATE TABLE php38_email_chk_code
(
	member_id mediumint unsigned not null comment '会员id',
	chk_email_code char(32) not null default '' comment 'email验证码',
	chk_email_code_time int unsigned not null comment '验证码生成时间',
	primary key (chk_email_code)
)engine=MyISAM default charset=utf8 comment 'email验证码';

DROP TABLE IF EXISTS php38_login_error;
CREATE TABLE php38_login_error
(
	ip int unsigned not null comment 'ip',
	logtime int unsigned not null comment '登录时间',
	key ip(ip)
)engine=MyISAM default charset=utf8 comment '登录失败表';

DROP TABLE IF EXISTS php38_cart;
CREATE TABLE php38_cart
(
	id int unsigned not null auto_increment comment 'Id',
	goods_id mediumint unsigned not null comment '商品id',
	goods_number smallint unsigned not null comment '商品数量',
	goods_attr_id varchar(150) not null default '' comment '商品属性ID，如果有多个，用，隔开',
	member_id mediumint unsigned not null comment '会员id',
	primary key (id),
	key member_id(member_id)
)engine=MyISAM default charset=utf8 comment '购物车';

DROP TABLE IF EXISTS php38_member_shr;
CREATE TABLE php38_member_shr
(
	id mediumint unsigned not null auto_increment comment 'Id',
	member_id mediumint unsigned not null comment '会员id',
	shr_name varchar(60) not null comment '收货姓名',
	shr_province varchar(30) not null comment '收货人省',
	shr_city varchar(30) not null comment '收货人城市',
	shr_area varchar(30) not null comment '收货人地址',
	shr_address varchar(30) not null comment '收货人详细地址',
	shr_tel varchar(30) not null comment '收货人电话',
	primary key (id),
	key member_id(member_id)
)engine=MyISAM default charset=utf8 comment '收货人地址';

DROP TABLE IF EXISTS php38_order
CREATE TABLE php38_order
(
	id int unsigned not null auto_increment comment 'Id',
	member_id mediumint unsigned not null comment '会员id',
	addtime int unsigned not null comment '下单时间',
	pay_status tinyint unsigned not null default '0' comment '支付状态：0：未支付 1：已支付',
	pay_time int unsigned not null default '0' comment '支付时间',
	total_price decimal(10,2) not null comment '定单总价',
	shr_name varchar(60) not null comment '收货姓名',
	shr_province varchar(30) not null comment '收货人省',
	shr_city varchar(30) not null comment '收货人城市',
	shr_area varchar(30) not null comment '收货人地址',
	shr_address varchar(30) not null comment '收货人详细地址',
	shr_tel varchar(30) not null comment '收货人电话',
    beizhu varchar(200) not null comment '定单备注',
    post_method varchar(30) not null comment '发货方式',
    pay_method varchar(30) not null comment '支付支付',
	primary key (id),
	key member_id(member_id),
	key addtime(addtime),
	key pay_status(pay_status)
)engine=InnoDB default charset=utf8 comment '定单基本信息';

DROP TABLE IF EXISTS php38_order_goods
CREATE TABLE php38_order_goods
(
	member_id mediumint unsigned not null comment '会员id',
	order_id int unsigned not null comment '定单id',
    goods_id mediumint unsigned not null comment '商品id',
    goods_attr_id varchar(150) not null default '' comment '商品属性id列表',
    goods_number mediumint unsigned not null comment '购买的数量',
    price decimal(10,2) not null comment '下单时的价格',
	key member_id(member_id),
	key order_id(order_id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '定单商品';

DROP TABLE IF EXISTS php38_sphinx_data
CREATE TABLE php38_sphinx_data
(
	id int unsigned not null comment '上次已经创建好索引的最大ID'
)engine=InnoDB default charset=utf8 comment 'sphinx自动更新';
INSERT INTO php38_sphinx_data(id) SELECT MAX(id) FROM php38_goods;

DROP TABLE IF EXISTS php38_comment
CREATE TABLE php38_comment
(
	id int unsigned not null auto_increment comment 'Id',
	member_id mediumint unsigned not null comment '会员id',
	goods_id mediumint unsigned not null comment '商品id',
	addtime int unsigned not null comment '评论时间',
	content varchar(200) not null comment '评论内容',
	star tinyint unsigned not null default '5' comment '评分分值',
	primary key (id),
	key goods_id(goods_id),
	key addtime(addtime),
	key member_id(member_id)
)engine=InnoDB default charset=utf8 comment '评论';

DROP TABLE IF EXISTS php38_yinxiang
CREATE TABLE php38_yinxiang
(
	id int unsigned not null auto_increment comment 'Id',
	goods_id mediumint unsigned not null comment '商品id',
	yx_name varchar(30) not null comment '印象名称',
	yx_count smallint unsigned not null comment '印象出现的次数',
	primary key (id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '印象';











