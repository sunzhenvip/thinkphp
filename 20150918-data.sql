-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 09 月 18 日 10:00
-- 服务器版本: 5.5.20
-- PHP 版本: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `php38`
--

-- --------------------------------------------------------

--
-- 表的结构 `php38_admin`
--

CREATE TABLE IF NOT EXISTS `php38_admin` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `username` varchar(150) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `status` enum('正常','禁用') NOT NULL DEFAULT '正常' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员' AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `php38_admin`
--

INSERT INTO `php38_admin` (`id`, `username`, `password`, `status`) VALUES
(1, 'root', '99de37ebe3fc968924ff1d82dec33cd2', '正常'),
(7, 'rbac', 'f853c62c649eeaf52676fcc356228f60', '正常');

-- --------------------------------------------------------

--
-- 表的结构 `php38_admin_goods_cat`
--

CREATE TABLE IF NOT EXISTS `php38_admin_goods_cat` (
  `admin_id` mediumint(8) unsigned NOT NULL COMMENT '管理员id',
  `cat_id` mediumint(8) unsigned NOT NULL COMMENT '分类id',
  KEY `cat_id` (`cat_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员可以管理的分类表';

--
-- 转存表中的数据 `php38_admin_goods_cat`
--

INSERT INTO `php38_admin_goods_cat` (`admin_id`, `cat_id`) VALUES
(7, 14),
(7, 8),
(7, 6),
(7, 3),
(7, 19),
(7, 17),
(7, 16),
(7, 1);

-- --------------------------------------------------------

--
-- 表的结构 `php38_admin_role`
--

CREATE TABLE IF NOT EXISTS `php38_admin_role` (
  `admin_id` mediumint(8) unsigned NOT NULL COMMENT '管理员id',
  `role_id` mediumint(8) unsigned NOT NULL COMMENT '角色id',
  KEY `role_id` (`role_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员所在角色';

--
-- 转存表中的数据 `php38_admin_role`
--

INSERT INTO `php38_admin_role` (`admin_id`, `role_id`) VALUES
(7, 1);

-- --------------------------------------------------------

--
-- 表的结构 `php38_attribute`
--

CREATE TABLE IF NOT EXISTS `php38_attribute` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `attr_name` varchar(30) NOT NULL COMMENT '属性名称',
  `attr_type` enum('唯一','可选') NOT NULL COMMENT '属性类型',
  `attr_option_values` varchar(150) NOT NULL DEFAULT '' COMMENT '属性可选值',
  `type_id` tinyint(3) unsigned NOT NULL COMMENT '类型id',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='属性' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `php38_attribute`
--

INSERT INTO `php38_attribute` (`id`, `attr_name`, `attr_type`, `attr_option_values`, `type_id`) VALUES
(1, '内存', '可选', '4g,8g,16G,32G', 2),
(2, '颜色', '可选', '白色,黑色,绿色,蓝色', 2),
(3, '作者', '唯一', '', 1),
(4, '出厂日期', '唯一', '', 2),
(5, 'CPU', '唯一', 'i5,i7', 2);

-- --------------------------------------------------------

--
-- 表的结构 `php38_cart`
--

CREATE TABLE IF NOT EXISTS `php38_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `goods_number` smallint(5) unsigned NOT NULL COMMENT '商品数量',
  `goods_attr_id` varchar(150) NOT NULL DEFAULT '' COMMENT '商品属性ID，如果有多个，用，隔开',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='购物车' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的结构 `php38_category`
--

CREATE TABLE IF NOT EXISTS `php38_category` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `cat_name` varchar(150) NOT NULL COMMENT '分类名称',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID，0：代表顶级分类',
  `is_floor` enum('是','否') NOT NULL DEFAULT '否' COMMENT '是否推荐到楼层',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='分类' AUTO_INCREMENT=23 ;

--
-- 转存表中的数据 `php38_category`
--

INSERT INTO `php38_category` (`id`, `cat_name`, `parent_id`, `is_floor`) VALUES
(1, '家用电器', 0, '是'),
(2, '手机、数码、京东通信', 0, '是'),
(3, '电脑、办公', 0, '否'),
(4, '家居、家具、家装、厨具', 0, '否'),
(5, '男装、女装、内衣、珠宝', 0, '否'),
(6, '个护化妆', 0, '否'),
(21, 'iphone', 2, '否'),
(8, '运动户外', 0, '否'),
(9, '汽车、汽车用品', 0, '否'),
(10, '母婴、玩具乐器', 0, '否'),
(11, '食品、酒类、生鲜、特产', 0, '否'),
(12, '营养保健', 0, '否'),
(13, '图书、音像、电子书', 0, '否'),
(14, '彩票、旅行、充值、票务', 0, '否'),
(15, '理财、众筹、白条、保险', 0, '否'),
(16, '大家电', 1, '否'),
(17, '生活电器', 1, '否'),
(18, '厨房电器', 1, '是'),
(19, '个护健康', 1, '否'),
(20, '五金家装', 1, '是'),
(22, '冰箱', 16, '否');

-- --------------------------------------------------------

--
-- 表的结构 `php38_email_chk_code`
--

CREATE TABLE IF NOT EXISTS `php38_email_chk_code` (
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `chk_email_code` char(32) NOT NULL DEFAULT '' COMMENT 'email验证码',
  `chk_email_code_time` int(10) unsigned NOT NULL COMMENT '验证码生成时间',
  PRIMARY KEY (`chk_email_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='email验证码';

-- --------------------------------------------------------

--
-- 表的结构 `php38_goods`
--

CREATE TABLE IF NOT EXISTS `php38_goods` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `goods_name` varchar(150) NOT NULL COMMENT '商品名称',
  `market_price` decimal(10,2) NOT NULL COMMENT '市场价格',
  `shop_price` decimal(10,2) NOT NULL COMMENT '本店价格',
  `logo` varchar(150) NOT NULL DEFAULT '' COMMENT '图片',
  `sm_logo` varchar(150) NOT NULL DEFAULT '' COMMENT '小图片',
  `mid_logo` varchar(150) NOT NULL DEFAULT '' COMMENT '中图片',
  `goods_desc` longtext COMMENT '商品描述',
  `is_on_sale` enum('是','否') NOT NULL DEFAULT '是' COMMENT '是否上架',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `cat_id` mediumint(8) unsigned NOT NULL COMMENT '主分类id',
  `admin_id` mediumint(8) unsigned NOT NULL COMMENT '添加这件商品的管理员id',
  `type_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型id',
  `promote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '促销价格',
  `promote_start_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '促销开始时间',
  `promote_end_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '促销结束时间',
  `is_hot` enum('是','否') NOT NULL DEFAULT '否' COMMENT '是否热销',
  `is_rec` enum('是','否') NOT NULL DEFAULT '否' COMMENT '是否推荐',
  `is_new` enum('是','否') NOT NULL DEFAULT '否' COMMENT '是否新品',
  `sort_number` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '排序的数字',
  `is_floor` enum('是','否') NOT NULL DEFAULT '否' COMMENT '是否推荐到楼层',
  PRIMARY KEY (`id`),
  KEY `shop_price` (`shop_price`),
  KEY `addtime` (`addtime`),
  KEY `is_on_sale` (`is_on_sale`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- 转存表中的数据 `php38_goods`
--

INSERT INTO `php38_goods` (`id`, `goods_name`, `market_price`, `shop_price`, `logo`, `sm_logo`, `mid_logo`, `goods_desc`, `is_on_sale`, `addtime`, `cat_id`, `admin_id`, `type_id`, `promote_price`, `promote_start_date`, `promote_end_date`, `is_hot`, `is_rec`, `is_new`, `sort_number`, `is_floor`) VALUES
(16, '新的联想商品321', '12.00', '333.00', 'Goods/2015-09-06/55eb9e60d5ffe.jpg', 'Goods/2015-09-06/sm_55eb9e60d5ffe.jpg', 'Goods/2015-09-06/mid_55eb9e60d5ffe.jpg', '', '是', 1441504302, 18, 0, 2, '300.00', 1441814400, 1442678399, '否', '否', '否', 100, '是'),
(18, '2321321', '222.00', '222.00', 'Goods/2015-09-08/55ee8ddeaa2a9.gif', 'Goods/2015-09-08/sm_55ee8ddeaa2a9.gif', 'Goods/2015-09-08/mid_55ee8ddeaa2a9.gif', '', '是', 1441526225, 2, 0, 3, '111.00', 1442073600, 1442678399, '否', '否', '否', 50, '是'),
(19, 'aaaaaaaaaaaaaaaa', '0.00', '0.00', 'Goods/2015-09-08/55ee97bf920c8.gif', 'Goods/2015-09-08/sm_55ee97bf920c8.gif', 'Goods/2015-09-08/mid_55ee97bf920c8.gif', '&lt;p&gt;&lt;script&gt;alert(123);&lt;/script&gt;&lt;/p&gt;', '是', 1441531783, 19, 0, 0, '0.00', 0, 57599, '否', '是', '否', 100, '是'),
(20, '213', '0.00', '0.00', 'Goods/2015-09-09/55efd19c8c482.jpg', 'Goods/2015-09-09/sm_55efd19c8c482.jpg', 'Goods/2015-09-09/mid_55efd19c8c482.jpg', '&lt;p&gt;&lt;img src=&quot;http://www.38s.com/Public/umeditor1_2_2-utf8-php/php/upload/20150908/14416998286796.gif&quot; _src=&quot;http://www.38s.com/Public/umeditor1_2_2-utf8-php/php/upload/20150908/14416998286796.gif&quot;/&gt;&lt;/p&gt;', '是', 1441678232, 14, 0, 0, '0.00', 0, 0, '否', '否', '否', 100, '否'),
(21, 'fdsafdsfds', '0.00', '0.00', 'Goods/2015-09-09/55eff5c4bc3d0.jpg', 'Goods/2015-09-09/thumb_1_55eff5c4bc3d0.jpg', 'Goods/2015-09-09/thumb_0_55eff5c4bc3d0.jpg', '', '是', 1441706572, 4, 0, 0, '0.00', 0, 0, '否', '否', '否', 100, '否'),
(22, 'images```', '0.00', '0.00', 'Goods/2015-09-09/55eff2bfc447d.jpg', 'Goods/2015-09-09/thumb_1_55eff2bfc447d.jpg', 'Goods/2015-09-09/thumb_0_55eff2bfc447d.jpg', '', '是', 1441788607, 18, 1, 0, '0.00', 0, 57599, '是', '否', '否', 100, '否'),
(25, '会员价格的商品', '100.00', '100.00', 'Goods/2015-09-15/55f76ce7656ef.jpg', 'Goods/2015-09-15/thumb_1_55f76ce7656ef.jpg', 'Goods/2015-09-15/thumb_0_55f76ce7656ef.jpg', '<p><span style="font-size:48px;"><span style="background-color:rgb(255,0,0);">红<strong>色</strong>的</span></span></p><p><span style="background-color:rgb(255,0,0);"></span><img src="http://www.38s.com/Public/umeditor1_2_2-utf8-php/php/upload/20150915/14423060958193.jpg" alt="14423060958193.jpg" /></p>', '是', 1441940181, 18, 1, 2, '90.00', 1441814400, 1442419199, '是', '否', '否', 100, '否'),
(26, '商品属性测试', '123.00', '111.00', 'Goods/2015-09-12/55f3990367298.gif', 'Goods/2015-09-12/thumb_1_55f3990367298.gif', 'Goods/2015-09-12/thumb_0_55f3990367298.gif', '', '是', 1442020927, 18, 1, 2, '0.00', 0, 57599, '否', '是', '是', 100, '否'),
(27, 'abababab', '100.00', '100.00', 'Goods/2015-09-15/55f76ccc7ecc1.jpg', 'Goods/2015-09-15/thumb_1_55f76ccc7ecc1.jpg', 'Goods/2015-09-15/thumb_0_55f76ccc7ecc1.jpg', '', '是', 1442201085, 17, 1, 0, '80.00', 1442073600, 1442332799, '否', '是', '是', 75, '否');

-- --------------------------------------------------------

--
-- 表的结构 `php38_goods_attr`
--

CREATE TABLE IF NOT EXISTS `php38_goods_attr` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `attr_id` mediumint(8) unsigned NOT NULL COMMENT '属性id',
  `attr_value` varchar(150) NOT NULL DEFAULT '' COMMENT '属性值',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品属性' AUTO_INCREMENT=35 ;

--
-- 转存表中的数据 `php38_goods_attr`
--

INSERT INTO `php38_goods_attr` (`id`, `goods_id`, `attr_id`, `attr_value`) VALUES
(1, 26, 1, '32G'),
(9, 26, 1, '16G'),
(8, 26, 1, '8g'),
(4, 26, 2, '白色'),
(5, 26, 2, '绿色'),
(6, 26, 4, '2015年8月9日'),
(7, 26, 5, 'i7'),
(10, 26, 2, '蓝色'),
(11, 16, 1, '4g'),
(12, 16, 2, '白色'),
(13, 16, 4, '2015年8月9日'),
(14, 16, 5, 'i7'),
(17, 16, 4, ''),
(18, 16, 5, ''),
(33, 25, 4, '2014-9-9'),
(32, 25, 2, '绿色'),
(31, 25, 2, '黑色'),
(30, 25, 2, '白色'),
(29, 25, 1, '16G'),
(28, 25, 1, '8g'),
(27, 25, 1, '4g'),
(34, 25, 5, 'i5');

-- --------------------------------------------------------

--
-- 表的结构 `php38_goods_ext_cat`
--

CREATE TABLE IF NOT EXISTS `php38_goods_ext_cat` (
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品Id',
  `cat_id` mediumint(8) unsigned NOT NULL COMMENT '分类Id',
  KEY `goods_id` (`goods_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品的扩展分类';

--
-- 转存表中的数据 `php38_goods_ext_cat`
--

INSERT INTO `php38_goods_ext_cat` (`goods_id`, `cat_id`) VALUES
(16, 12),
(16, 4),
(18, 1),
(16, 2),
(16, 18),
(16, 16),
(16, 1),
(20, 13),
(21, 18),
(19, 20);

-- --------------------------------------------------------

--
-- 表的结构 `php38_goods_number`
--

CREATE TABLE IF NOT EXISTS `php38_goods_number` (
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `attr_list` varchar(150) NOT NULL DEFAULT '' COMMENT '商品属性id，规则 1：如果一件商品有多个属性用，隔开 规则2：如果一件商品有多个属性ID就升降拼字符串，所以如果有两个属性ID5,6,那么不能拼成6,5，我们定义了这个规则之后，前台要取库存量也按这个规则就不会出错',
  `goods_number` mediumint(8) unsigned NOT NULL COMMENT '库存量',
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='库存量';

--
-- 转存表中的数据 `php38_goods_number`
--

INSERT INTO `php38_goods_number` (`goods_id`, `attr_list`, `goods_number`) VALUES
(25, '29,31', 100),
(26, '1,5', 321),
(18, '', 215),
(25, '29,32', 10),
(16, '', 123);

-- --------------------------------------------------------

--
-- 表的结构 `php38_goods_pics`
--

CREATE TABLE IF NOT EXISTS `php38_goods_pics` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `pic` varchar(150) NOT NULL COMMENT '原图路径',
  `sm_pic` varchar(150) NOT NULL COMMENT '小图路径',
  `mid_pic` varchar(150) NOT NULL COMMENT '中图路径',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='相册' AUTO_INCREMENT=19 ;

--
-- 转存表中的数据 `php38_goods_pics`
--

INSERT INTO `php38_goods_pics` (`id`, `goods_id`, `pic`, `sm_pic`, `mid_pic`) VALUES
(2, 22, 'Goods/2015-09-09/55eff2c43349d.jpg', 'Goods/2015-09-09/thumb_1_55eff2c43349d.jpg', 'Goods/2015-09-09/thumb_0_55eff2c43349d.jpg'),
(7, 24, 'Goods/2015-09-11/55f23137706d7.gif', 'Goods/2015-09-11/thumb_1_55f23137706d7.gif', 'Goods/2015-09-11/thumb_0_55f23137706d7.gif'),
(16, 25, 'Goods/2015-09-15/55f7d8c3b10be.gif', 'Goods/2015-09-15/thumb_1_55f7d8c3b10be.gif', 'Goods/2015-09-15/thumb_0_55f7d8c3b10be.gif'),
(11, 24, 'Goods/2015-09-11/55f233994a66e.jpg', 'Goods/2015-09-11/thumb_1_55f233994a66e.jpg', 'Goods/2015-09-11/thumb_0_55f233994a66e.jpg'),
(17, 25, 'Goods/2015-09-15/55f7d8c7a16ab.jpg', 'Goods/2015-09-15/thumb_1_55f7d8c7a16ab.jpg', 'Goods/2015-09-15/thumb_0_55f7d8c7a16ab.jpg'),
(13, 24, 'Goods/2015-09-11/55f233ba84189.jpg', 'Goods/2015-09-11/thumb_1_55f233ba84189.jpg', 'Goods/2015-09-11/thumb_0_55f233ba84189.jpg'),
(14, 24, 'Goods/2015-09-11/55f233bbe5808.jpg', 'Goods/2015-09-11/thumb_1_55f233bbe5808.jpg', 'Goods/2015-09-11/thumb_0_55f233bbe5808.jpg'),
(15, 24, 'Goods/2015-09-11/55f233bd775fa.jpg', 'Goods/2015-09-11/thumb_1_55f233bd775fa.jpg', 'Goods/2015-09-11/thumb_0_55f233bd775fa.jpg'),
(18, 25, 'Goods/2015-09-15/55f7d8c85b294.jpg', 'Goods/2015-09-15/thumb_1_55f7d8c85b294.jpg', 'Goods/2015-09-15/thumb_0_55f7d8c85b294.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `php38_login_error`
--

CREATE TABLE IF NOT EXISTS `php38_login_error` (
  `ip` int(10) unsigned NOT NULL COMMENT 'ip',
  `logtime` int(10) unsigned NOT NULL COMMENT '登录时间',
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='登录失败表';

-- --------------------------------------------------------

--
-- 表的结构 `php38_member`
--

CREATE TABLE IF NOT EXISTS `php38_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `email` varchar(150) NOT NULL COMMENT 'Email',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` char(32) NOT NULL COMMENT '密码',
  `regtime` int(10) unsigned NOT NULL COMMENT '注册时间',
  `regip` int(10) unsigned NOT NULL COMMENT '注册时的IP',
  `face` varchar(150) NOT NULL DEFAULT '' COMMENT '头像',
  `gender` enum('男','女','保密') NOT NULL DEFAULT '保密' COMMENT '性别',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0：未验证 1：正常',
  `jifen` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '积分可以消费',
  `jyz` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '经验值【只增不减】计算级别',
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `php38_member`
--

INSERT INTO `php38_member` (`id`, `email`, `name`, `password`, `regtime`, `regip`, `face`, `gender`, `status`, `jifen`, `jyz`) VALUES
(2, 'fortheday@126.com', '', 'bcdbcd', 1442286602, 2130706433, '', '保密', 1, 0, 200000),
(3, '56370276@qq.com', '', '8496c2aba2445b76812e1099e60d08ee', 1442299315, 2130706433, '', '保密', 1, 0, 200000);

-- --------------------------------------------------------

--
-- 表的结构 `php38_member_level`
--

CREATE TABLE IF NOT EXISTS `php38_member_level` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `level_name` varchar(30) NOT NULL COMMENT '级别名称',
  `level_rate` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '折扣率，100=10折 98=9.8折 90=9折，用时除100',
  `jifen_bottom` mediumint(8) unsigned NOT NULL COMMENT '积分下限',
  `jifen_top` mediumint(8) unsigned NOT NULL COMMENT '积分上限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员级别' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `php38_member_level`
--

INSERT INTO `php38_member_level` (`id`, `level_name`, `level_rate`, `jifen_bottom`, `jifen_top`) VALUES
(1, '初级会员', 100, 0, 10000),
(2, '中级会员', 95, 10001, 50000),
(3, '高级会员', 90, 50001, 150000),
(4, 'VIP', 85, 150001, 500000),
(5, 'VIP中P', 80, 500001, 16777215);

-- --------------------------------------------------------

--
-- 表的结构 `php38_member_price`
--

CREATE TABLE IF NOT EXISTS `php38_member_price` (
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `level_id` tinyint(3) unsigned NOT NULL COMMENT '级别id',
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  KEY `goods_id` (`goods_id`),
  KEY `level_id` (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员价格';

-- --------------------------------------------------------

--
-- 表的结构 `php38_member_shr`
--

CREATE TABLE IF NOT EXISTS `php38_member_shr` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `shr_name` varchar(60) NOT NULL COMMENT '收货姓名',
  `shr_province` varchar(30) NOT NULL COMMENT '收货人省',
  `shr_city` varchar(30) NOT NULL COMMENT '收货人城市',
  `shr_area` varchar(30) NOT NULL COMMENT '收货人地址',
  `shr_address` varchar(30) NOT NULL COMMENT '收货人详细地址',
  `shr_tel` varchar(30) NOT NULL COMMENT '收货人电话',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='收货人地址' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `php38_member_shr`
--

INSERT INTO `php38_member_shr` (`id`, `member_id`, `shr_name`, `shr_province`, `shr_city`, `shr_area`, `shr_address`, `shr_tel`) VALUES
(1, 3, '吴英雷', '上海', '东城区', '西三旗', '西三旗', '12332131');

-- --------------------------------------------------------

--
-- 表的结构 `php38_order`
--

CREATE TABLE IF NOT EXISTS `php38_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `addtime` int(10) unsigned NOT NULL COMMENT '下单时间',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态：0：未支付 1：已支付',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `total_price` decimal(10,2) NOT NULL COMMENT '定单总价',
  `shr_name` varchar(60) NOT NULL COMMENT '收货姓名',
  `shr_province` varchar(30) NOT NULL COMMENT '收货人省',
  `shr_city` varchar(30) NOT NULL COMMENT '收货人城市',
  `shr_area` varchar(30) NOT NULL COMMENT '收货人地址',
  `shr_address` varchar(30) NOT NULL COMMENT '收货人详细地址',
  `shr_tel` varchar(30) NOT NULL COMMENT '收货人电话',
  `beizhu` varchar(200) NOT NULL COMMENT '定单备注',
  `post_method` varchar(30) NOT NULL COMMENT '发货方式',
  `pay_method` varchar(30) NOT NULL COMMENT '支付支付',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `addtime` (`addtime`),
  KEY `pay_status` (`pay_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='定单基本信息' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `php38_order`
--

INSERT INTO `php38_order` (`id`, `member_id`, `addtime`, `pay_status`, `pay_time`, `total_price`, `shr_name`, `shr_province`, `shr_city`, `shr_area`, `shr_address`, `shr_tel`, `beizhu`, `post_method`, `pay_method`) VALUES
(1, 3, 1442568546, 0, 0, '1006.00', '吴英雷', '上海', '东城区', '西三旗', '西三旗', '12332131', '', '普通快递送货上门', '在线支付'),
(2, 3, 1442568814, 0, 0, '18750.00', '吴英雷', '上海', '东城区', '西三旗', '西三旗', '12332131', '', '普通快递送货上门', '在线支付');

-- --------------------------------------------------------

--
-- 表的结构 `php38_order_goods`
--

CREATE TABLE IF NOT EXISTS `php38_order_goods` (
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `order_id` int(10) unsigned NOT NULL COMMENT '定单id',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `goods_attr_id` varchar(150) NOT NULL DEFAULT '' COMMENT '商品属性id列表',
  `goods_number` mediumint(8) unsigned NOT NULL COMMENT '购买的数量',
  `price` decimal(10,2) NOT NULL COMMENT '下单时的价格',
  KEY `member_id` (`member_id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='定单商品';

--
-- 转存表中的数据 `php38_order_goods`
--

INSERT INTO `php38_order_goods` (`member_id`, `order_id`, `goods_id`, `goods_attr_id`, `goods_number`, `price`) VALUES
(3, 1, 18, '', 6, '111.00'),
(3, 1, 25, '29,32', 4, '85.00'),
(3, 2, 25, '29,32', 90, '85.00'),
(3, 2, 18, '', 100, '111.00');

-- --------------------------------------------------------

--
-- 表的结构 `php38_privilege`
--

CREATE TABLE IF NOT EXISTS `php38_privilege` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `pri_name` varchar(150) NOT NULL COMMENT '权限名称',
  `module_name` varchar(30) NOT NULL DEFAULT '' COMMENT '模块名称',
  `controller_name` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `action_name` varchar(30) NOT NULL DEFAULT '' COMMENT '方法名称',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级权限ID，0：代表顶级分类',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='权限' AUTO_INCREMENT=47 ;

--
-- 转存表中的数据 `php38_privilege`
--

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
(36, 'ajax获取商品属性', 'Admin', 'Goods', 'ajaxGetAttr', 3),
(37, 'ajax删除商品属性', 'Admin', 'Goods', 'ajaxDelGoodsAttr', 4),
(38, 'ajax删除商品相册图片', 'admin', 'Goods', 'ajaxDelPic', 4),
(39, '清空缓存', 'Admin', 'Goods', 'deleteTempImages', 3),
(40, 'ajax上传商品相册图片', 'Admin', 'Goods', 'ajax_upload_pic', 3),
(41, '库存量管理', 'Admin', 'Goods', 'gn', 3),
(42, '清除缓存', 'Admin', 'Goods', 'clearCache', 1),
(43, '会员列表', 'Admin', 'Member', 'lst', 23),
(44, '添加会员', 'Admin', 'Member', 'add', 43),
(45, '修改会员', 'Admin', 'Member', 'edit', 43),
(46, '删除会员', 'Admin', 'Member', 'delete', 43);

-- --------------------------------------------------------

--
-- 表的结构 `php38_role`
--

CREATE TABLE IF NOT EXISTS `php38_role` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `role_name` varchar(150) NOT NULL COMMENT '角色名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='角色' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `php38_role`
--

INSERT INTO `php38_role` (`id`, `role_name`) VALUES
(1, 'RBAC管理员');

-- --------------------------------------------------------

--
-- 表的结构 `php38_role_pri`
--

CREATE TABLE IF NOT EXISTS `php38_role_pri` (
  `role_id` mediumint(8) unsigned NOT NULL COMMENT '角色id',
  `pri_id` mediumint(8) unsigned NOT NULL COMMENT '权限id',
  KEY `role_id` (`role_id`),
  KEY `pri_id` (`pri_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色拥有的权限';

--
-- 转存表中的数据 `php38_role_pri`
--

INSERT INTO `php38_role_pri` (`role_id`, `pri_id`) VALUES
(1, 11),
(1, 9),
(1, 7),
(1, 6),
(1, 5),
(1, 35),
(1, 34),
(1, 33),
(1, 32),
(1, 31),
(1, 30),
(1, 29),
(1, 28),
(1, 22),
(1, 21),
(1, 20),
(1, 19),
(1, 41),
(1, 40),
(1, 39),
(1, 36),
(1, 10),
(1, 38),
(1, 37),
(1, 4),
(1, 2),
(1, 3),
(1, 1),
(1, 8),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18);

-- --------------------------------------------------------

--
-- 表的结构 `php38_type`
--

CREATE TABLE IF NOT EXISTS `php38_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `type_name` varchar(30) NOT NULL COMMENT '类型名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='类型' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `php38_type`
--

INSERT INTO `php38_type` (`id`, `type_name`) VALUES
(1, '书'),
(2, '笔记本'),
(3, '手机'),
(4, '服装');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
