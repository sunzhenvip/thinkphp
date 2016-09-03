<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller 
{
	public function __construct()
	{
		// 先调用父类的构造方法
		parent::__construct();
		// 判断登录 
		$id = session('id');
		if(!$id)
			$this->error('必须先登录！', U('Login/login'));
		// 判断权限
		$priModel = D('Privilege');
		if(!$priModel->hasPri())
			$this->error('无权访问！');
	}
}