<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller 
{
	public function qqlogin()
	{
		// 取出这个openid所关联的本地账号
		$memberModel = D('Admin/Member');
		$user = $memberModel->where(array(
			'qq_openid' => array('eq', $_SESSION['openid']),
		))->find();
		if($user)
		{
			// 让这个关联的账号直接登录 
			$_POST['email'] = $user['email'];
			// 不需要密码登录
			if($memberModel->login(FALSE))
			{
				// 判断SESSION中是否设置了要返回的地址
				$return = session('returnUrl');
				if($return)
				{
					session('returnUrl', null);
					$this->success('登录成功！', $return);
					exit;
				}
				else 
				{
					$this->success('登录成功！', U('Home/Index/index'));
					exit;
				}
			}
		}
		else 
		{
			// 跳转到关联页面
			redirect('login');
		}
	}
	public function ajaxChkLogin()
	{
		if(session('member_id'))
		{
			$username = session('member_username');
			die(json_encode(array(
				'ok' => 1,
				'username' => empty($username) ? session('member_email') : $username,
			)));	
		}
		else
		{
			die(json_encode(array(
				'ok' => 0,
			)));
		}
	}
	public function logout()
	{
		session(null);
		redirect('/');
	}
	public function login()
	{
		if(IS_POST)
		{
			$model = D('Admin/Member');
			// 接收表单并且根据规则验证表单
			if($model->validate($model->_login_validate)->create())
			{
				if($model->login())
				{
					// 判断SESSION中是否设置了要返回的地址
					$return = session('returnUrl');
					if($return)
					{
						session('returnUrl', null);
						$this->success('登录成功！', $return);
						exit;
					}
					else 
					{
						$this->success('登录成功！', U('Home/Index/index'));
						exit;
					}
				}
			}
			// 只要失败就获取失败原因
			$error = $model->getError();
			$this->error($error);
		}
		// 先取出失败的次数
		$leModel = D('login_error');
		$errorCount = $leModel->where(array(
			'ip' => array('eq', get_client_ip(1, TRUE)),
			'logtime' => array('egt', time()-C('login_error_chkcode_time')),
		))->count();
		// 设置页面信息
    	$this->assign(array(
    		'errorCount' => $errorCount,
    		'_page_title' => '登录',
    		'_page_keywords' => '登录',
    		'_page_description' => '登录',
    	));
		$this->display();
	}
	// 实现email验证
	public function email_chk()
	{
		$model = D('Admin/Member');
		if($model->do_email_chk(I('get.code')))
		{
			$this->success('EMAIL验证成功，现在可以登录了！', U('login'), 5);
			exit;
		}
		$this->error($model->getError(), U('regist'));
	}
	public function chkcode()
	{
		$Verify = new \Think\Verify(array(
			    'fontSize'    =>    30,    // 验证码字体大小
			    'length'      =>    2,     // 验证码位数
			    'useNoise'    =>    false, // 关闭验证码杂点
			));
		$Verify->entry();
	}
    // 注册
    public function regist()
    {
    	if(IS_POST)
    	{
    		$model = D('Admin/Member');
    		if($model->create(I('post.'), 1))
    		{
    			if($model->add())
    			{
    				// 5秒之后跳转
    				$this->success('注册成功，请登录您的邮件完成验证，之后才可以登录！', U('login'), 5);
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
    	// 设置页面信息
    	$this->assign(array(
    		'_page_title' => '注册',
    		'_page_keywords' => '注册',
    		'_page_description' => '注册',
    	));
		$this->display();
    }
}