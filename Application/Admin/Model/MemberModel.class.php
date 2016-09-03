<?php
namespace Admin\Model;
use Think\Model;
class MemberModel extends Model 
{
	protected $insertFields = array('email','name','password','cpassword','gender','must_click','chkcode');
	protected $updateFields = array('id','email','name','password','cpassword','gender');
	// 注册和修改时的验证
	protected $_validate = array(
		array('must_click', 'require', '必须同意注册协议！', 1, 'regex', 3),
		array('chkcode', 'require', '验证码不能为空！', 1, 'regex', 3),
		array('chkcode', 'chk_code', '验证码不正确！', 1, 'callback', 3),
		array('email', 'require', 'Email不能为空！', 1, 'regex', 3),
		array('email', 'email', 'Email格式不正确！', 1, 'regex', 3),
		array('email', '1,150', 'Email的值最长不能超过 150 个字符！', 1, 'length', 3),
		array('name', '1,30', '昵称的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('password', 'require', '密码不能为空！', 1, 'regex', 3),
		array('password', '6,20', '密码必须是6-20位字符！', 1, 'length', 3),
		array('cpassword', 'password', '两次密码不一致！', 1, 'confirm', 3),
		array('gender', '男,女,保密', "性别的值只能是在 '男,女,保密' 中的一个值！", 2, 'in', 3),
		array('email', 'chk_email', 'Email已经存在！', 1, 'callback', 3),
	);
	// 登录的验证规则
	public $_login_validate = array(
		array('email', 'require', 'Email不能为空！', 1, 'regex', 3),
		array('password', 'require', '密码不能为空！', 1, 'regex', 3),
		// 表单中有这个字段时才验证
		array('chkcode', 'require', '验证码不能为空！', 0, 'regex', 3),
		array('chkcode', 'chk_code_with_error_count', '验证码不正确！', 1, 'callback', 3),
	);
	protected function chk_code_with_error_count($code)
	{
		// 如果标记为需要验证并且这个标记没有过期就验证否则 直接 返回TRUE
		if(session('need_chk_code')==1 && ((time()-session('need_chk_code_time'))<C('login_error_chkcode_time')))
		{
			$verify = new \Think\Verify();
    		return $verify->check($code);
		}
		else 
			return TRUE;
	}
	// 判断一个EMAIL是否可以被注册
	protected function chk_email($chkemail)
	{
		$email = $this->field('id,status')->where(array(
			'email' => array('eq', $chkemail),
		))->find();
		if($email)
		{
			// 还没有通过验证
			if($email['status'] == 0)
			{
				// 判断验证码有没有过期
				$model = D('email_chk_code');
				$chktime = $model->field('chk_email_code_time')->where(array(
					'member_id' => array('eq', $email['id']),
				))->find();
				// 判断有没有过期
				$ece = C('email_chkcode_expire');
				if((time() - $chktime['chk_email_code_time']) > $ece)
				{
					// 删除这个过期的验证码和这个账号
					$model->where(array(
						'member_id' => array('eq', $email['id']),
					))->delete();
					$this->delete($email['id']);
					// SESSION中的标记也删除
					session('need_chk_code', null);
					session('need_chk_code_time', null);
					return TRUE;              // 已经过期账号已经失效并删除了，所以可以重新注册
				}
				else 
					return FALSE; // 验证码还没过期不能再注册
			}
			else 
				return FALSE;   // 如果账号已经存在 并且已经验证过了，那么就不能再注册了
		}
		else 
			return TRUE;  // 这个EMAIL还没有注册过可以注册
	}
	public function login($needPassword = TRUE)
	{
		// 从模型中获取用户名和密码
		$email = I('post.email');
		if($needPassword)
			$password = I('post.password');
		$user = $this->field('id,status,password')->where(array(
			'email' => array('eq', $email),
		))->find();
		if($user)
		{
			// 有没有验证过
			if($user['status'] == 1)
			{
				// 如果不需要登录直接登录成功了
				if(!$needPassword)
				{
					session('member_id', $user['id']);
					session('member_email', $email);
					session('member_username', $user['username']);
					/****** 移购物车中的数据到数据库 ************/
					$cartModel = D('Home/Cart');
					$cartModel->moveDataToDb();
					/******* 是否是qq关联 ******/
					if(isset($_SESSION['openid']))
					{
						$this->where(array(
							'id' => $user['id']
						))->setField('qq_openid', $_SESSION['openid']);
						unset($_SESSION['openid']);
					}
					// 删除失败的次数记录
					$leModel = D('login_error');
					$leModel->where(array(
						'ip' => array('eq',  get_client_ip(1, TRUE)),
					))->delete();
					return TRUE;
				}
				// 判断密码
				if($user['password'] == md5($password . C('MD5_KEY')))
				{
					session('member_id', $user['id']);
					session('member_email', $email);
					session('member_username', $user['username']);
					/****** 移购物车中的数据到数据库 ************/
					$cartModel = D('Home/Cart');
					$cartModel->moveDataToDb();
					/******* 是否是qq关联 ******/
					if(isset($_SESSION['openid']))
					{
						$this->where(array(
							'id' => $user['id']
						))->setField('qq_openid', $_SESSION['openid']);
						unset($_SESSION['openid']);
					}
					// 删除失败的次数记录
					$leModel = D('login_error');
					$leModel->where(array(
						'ip' => array('eq',  get_client_ip(1, TRUE)),
					))->delete();
					return TRUE;
				}
				else 
				{
					$leModel = D('login_error');
					$leModel->add(array(
						'ip' => get_client_ip(1, TRUE),
						'logtime' => time(),
					));
					$this->error = '密码错误！';
					return FALSE;
				}
			}
			else 
			{
					$leModel = D('login_error');
					$leModel->add(array(
						'ip' => get_client_ip(1, TRUE),
						'logtime' => time(),
					));
				$this->error = '还没有通过EMAIL验证，不能登录！';
				return FALSE;
			}
		}
		else 
		{
					$leModel = D('login_error');
					$leModel->add(array(
						'ip' => get_client_ip(1, TRUE),
						'logtime' => time(),
					));
			$this->error = '账号不存在！';
			return FALSE;
		}
	}
	/**
	 * 根据验证码实现验证功能
	 *
	 * @param unknown_type $code ： 验证码
	 */
	public function do_email_chk($code)
	{
		if(!$code)
		{
			$this->error = '验证码不能为空！';
			return  FALSE;
		}
		$eccModel = D('email_chk_code');
		$chkcode = $eccModel->field('member_id,chk_email_code_time')->where(array(
			'chk_email_code' => array('eq', $code),
		))->find();
		if($chkcode)
		{
			// 判断有没有过期
			$ece = C('email_chkcode_expire');
			if((time() - $chkcode['chk_email_code_time']) > $ece)
			{
				// 删除验证码
				$eccModel->where(array(
					'member_id' => array('eq', $chkcode['member_id']),
				))->delete();
				// 账号也删除
				$this->delete($chkcode['member_id']);
				$this->error = '验证码已经过期失效！请重新注册！';
				return FALSE;
			}
			else 
			{
				// 设置会员为已验证的状态 
				$this->where(array(
					'id' => array('eq', $chkcode['member_id']),
				))->setField('status', 1);
				// 删除验证码
				$eccModel->where(array(
					'member_id' => array('eq', $chkcode['member_id']),
				))->delete();
				return TRUE;
			}
		}
		else 
		{
			$this->error = '验证码不存在！';
			return FALSE;
		}
	}
	// 验证码是否正确
	protected function chk_code($code)
	{
		$verify = new \Think\Verify();
    	return $verify->check($code);
	}
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		if($email = I('get.email'))
			$where['email'] = array('like', "%$email%");
		if($name = I('get.name'))
			$where['name'] = array('like', "%$name%");
		$st = I('get.st');
		$et = I('get.et');
		if($st && $et)
			$where['regtime'] = array('between', array(strtotime("$st 00:00:00"), strtotime("$et 23:59:59")));
		elseif($st)
			$where['regtime'] = array('egt', strtotime("$st 00:00:00"));
		elseif($et)
			$where['regtime'] = array('elt', strtotime("$et 23:59:59"));
		if($regip = I('get.regip'))
			$where['regip'] = array('eq', $regip);
		$gender = I('get.gender');
		if($gender != '' && $gender != '-1')
			$where['gender'] = array('eq', $gender);
		$status = I('get.status');
		if($status != '' && $status != '-1')
			$where['status'] = array('eq', $status);
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
		return $data;
	}
	// 添加前
	protected function _before_insert(&$data, $option)
	{
		$data['regtime'] = time();
		$data['regip'] = get_client_ip(1, TRUE);
		$data['password'] = md5($data['password'] . C('MD5_KEY'));
		
		if(isset($_FILES['face']) && $_FILES['face']['error'] == 0)
		{
			$ret = uploadOne('face', 'Admin', array(
				array(150, 150, 2),
			));
			if($ret['ok'] == 1)
			{
				$data['face'] = $ret['images'][0];
				$data['face'] = $ret['images'][1];
			}
			else 
			{
				$this->error = $ret['error'];
				return FALSE;
			}
		}
	}
	protected function _after_insert($data, $option)
	{
		/******* 是否是qq关联 ******/
		if(isset($_SESSION['openid']))
		{
			$this->where(array(
				'id' => $data['id']
			))->setField('qq_openid', $_SESSION['openid']);
			unset($_SESSION['openid']);
		}
		// 生成一个email验证码【唯一的】
		$code = md5(uniqid() . C('MD5_KEY'));
		// 把验证码插入到验证码表
		$model = D('email_chk_code');
		$model->add(array(
			'member_id' => $data['id'],
			'chk_email_code' => $code,
			'chk_email_code_time' => $data['regtime'],
		));
		// 先从配置文件读出邮件的标题和内容模板
		$title = C('reg_email_title');
		$content = C('reg_email_content');
		// 把内容模板中的验证码替换进去
		$content = str_replace('#code#', $code, $content);
		// 把验证码发到会员的email中
		sendMail($data['email'], $title, $content);
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
		if(isset($_FILES['face']) && $_FILES['face']['error'] == 0)
		{
			$ret = uploadOne('face', 'Admin', array(
				array(150, 150, 2),
			));
			if($ret['ok'] == 1)
			{
				$data['face'] = $ret['images'][0];
				$data['face'] = $ret['images'][1];
			}
			else 
			{
				$this->error = $ret['error'];
				return FALSE;
			}
			deleteImage(array(
				I('post.old_face'),
				I('post.old_face'),
	
			));
		}
	}
	/**
	 * 判断是否买过这件商品
	 *
	 * @param unknown_type $goodsId
	 */
	public function isBuy($goodsId)
	{
		$id = session('member_id');
		if(!$id)
			return FALSE;
		$ogModel = D('order_goods');
		// 查看这个会员支付的定单中有没有这件商品
		$has = $ogModel->alias('a')
		->join('LEFT JOIN php38_order b ON a.order_id=b.id')
		->where(array(
			'a.member_id' => array('eq', $id),
			'a.goods_id' => array('eq', $goodsId),
			'b.pay_status' => array('eq', 1),
		))->count();
		return ($has > 0);
	}
	public function updateJifenAndJyz($memberId, $jifen, $jyz)
	{
		$this->execute('UPDATE php38_member SET jifen=jifen+'.$jifen.',jyz=jyz+'.$jyz.' WHERE id='.$memberId);
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
		$images = $this->field('face,face')->find($option['where']['id']);
		deleteImage($images);
	}
	/************************************ 其他方法 ********************************************/
}