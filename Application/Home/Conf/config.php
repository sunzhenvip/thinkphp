<?php
return array(
	'HTML_CACHE_ON'     =>    TRUE, // 开启静态缓存
	'HTML_CACHE_TIME'   =>    60,   // 全局静态缓存有效期（秒）
	'HTML_FILE_SUFFIX'  =>    '.shtml', // 设置静态缓存文件后缀
	// 以下可以配置哪些页面生成缓存
	'HTML_CACHE_RULES' => array( 
		'Index:index' => array('index', 3600),  // ==> 首页生成1小时的缓存 文件，文件名叫index
		'Index:goods' => array('goods-{id}', 4800),
	),
	/*********** 注册相关配置 *************/
	'reg_email_title' => 'PHP38网邮箱验证',
	'reg_email_content' => '欢迎您加入PHP38网，请点击以下链接地址完成EMAIL验证：<p><a target="_blank" href="http://www.38s.com/index.php/Home/Login/email_chk/code/#code#">点击完成验证</a></p>',
	'email_chkcode_expire' => 1200,  // email验证码过期时间：20分钟
	'login_error_chkcode_time' => 1200,  // 登录失败次数的间隔，在这个间隔内出错多次就显示验证码  20分钟
	'login_error_chkcode_count' => 3,  // 登录失败最大的次数，超过这个次数就显示验证码     3次
	/************* 购物车在COOKIE中的过期时间 ******************/
	'CART_COOKIE_EXPIRE' => 30,  // 默认30天
	'CART_COOKIE_DOMAIN' => '.38s.com',  // 项目的域名
);